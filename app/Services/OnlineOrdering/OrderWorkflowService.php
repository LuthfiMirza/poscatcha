<?php

namespace App\Services\OnlineOrdering;

use App\Models\CashierShift;
use App\Models\DetailSale;
use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function confirm(Order $order, User $actor): Order
    {
        return DB::transaction(function () use ($order, $actor) {
            $order = Order::whereKey($order->id)->with('items')->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::STATUS_PENDING) {
                throw ValidationException::withMessages(['status' => 'Pesanan tidak berada pada status pending.']);
            }

            if ($order->stock_deducted_at) {
                throw ValidationException::withMessages(['stock' => 'Stok pesanan ini sudah pernah dikurangi.']);
            }

            foreach ($order->items->sortBy('product_id') as $item) {
                $product = Product::where('product_id', $item->product_id)
                    ->with('recipes.rawMaterial')
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->deductRecipeMaterials($product, (int) $item->quantity, $order->order_code, $actor);
            }

            $order->update([
                'status' => Order::STATUS_CONFIRMED,
                'confirmed_by' => $actor->id,
                'confirmed_at' => now(),
                'stock_deducted_at' => now(),
            ]);

            return $order->fresh(['items', 'buyer']);
        });
    }

    public function startProcessing(Order $order, User $actor): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::STATUS_CONFIRMED) {
                throw ValidationException::withMessages(['status' => 'Hanya pesanan confirmed yang dapat diproses.']);
            }

            $order->update([
                'status' => Order::STATUS_PROCESSING,
                'processing_at' => now(),
            ]);

            return $order->fresh(['items', 'buyer']);
        });
    }

    public function cancel(Order $order, User $actor, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $actor, $reason) {
            $order = Order::whereKey($order->id)->with('items')->lockForUpdate()->firstOrFail();

            if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)) {
                throw ValidationException::withMessages(['status' => 'Pesanan tidak dapat dibatalkan.']);
            }

            if ($order->status === Order::STATUS_PROCESSING && blank($reason)) {
                throw ValidationException::withMessages(['cancel_reason' => 'Alasan wajib diisi untuk membatalkan pesanan processing.']);
            }

            if ($order->stock_deducted_at) {
                foreach ($order->items->sortBy('product_id') as $item) {
                    $product = Product::where('product_id', $item->product_id)
                        ->with('recipes.rawMaterial')
                        ->lockForUpdate()
                        ->firstOrFail();

                    $this->restoreRecipeMaterials($product, (int) $item->quantity, $order->order_code, $actor);
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancelled_by' => $actor->id,
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            return $order->fresh(['items', 'buyer']);
        });
    }

    public function complete(Order $order, User $actor): Order
    {
        return DB::transaction(function () use ($order, $actor) {
            $order = Order::whereKey($order->id)->with(['items', 'sale'])->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::STATUS_PROCESSING) {
                throw ValidationException::withMessages(['status' => 'Hanya pesanan processing yang dapat diselesaikan.']);
            }

            if (! $order->stock_deducted_at) {
                throw ValidationException::withMessages(['stock' => 'Stok belum dikurangi untuk pesanan ini.']);
            }

            if ($order->sale) {
                throw ValidationException::withMessages(['sale' => 'Pesanan ini sudah memiliki data penjualan.']);
            }

            $activeShift = CashierShift::query()
                ->open()
                ->where('cashier_id', $actor->id)
                ->lockForUpdate()
                ->first();

            if (! $activeShift && $actor->hasRole('cashier')) {
                throw ValidationException::withMessages(['shift' => 'Kasir harus membuka shift sebelum menyelesaikan pesanan online.']);
            }

            $saleId = Sale::generateInvoiceNumber();
            $sale = Sale::create([
                'sale_id' => $saleId,
                'source' => 'online',
                'order_id' => $order->id,
                'shift_id' => $activeShift?->id,
                'cashier_id' => $actor->id,
                'total' => $order->total_price,
                'payment_method' => PaymentMethod::toSales($order->payment_method),
                'pay' => $order->total_price,
                'change' => 0,
            ]);

            foreach ($order->items as $item) {
                $product = Product::where('product_id', $item->product_id)->first();
                $buyPrice = (float) ($product?->buy_price ?? 0);
                $profit = ((float) $item->price - $buyPrice) * (int) $item->quantity;

                DetailSale::create([
                    'sale_id' => $sale->sale_id,
                    'cashier_id' => $actor->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->price,
                    'buy_price' => $buyPrice,
                    'product_profit' => $profit,
                    'quantity' => $item->quantity,
                    'sub_total' => $item->subtotal,
                ]);
            }

            $order->update([
                'status' => Order::STATUS_COMPLETED,
                'completed_by' => $actor->id,
                'completed_at' => now(),
                'payment_status' => 'paid',
            ]);

            return $order->fresh(['items', 'buyer', 'sale']);
        });
    }

    protected function recordProductMovement(Product $product, string $transactionId, int $status, string $reason, int $quantityBefore, int $quantityAfter, User $actor): void
    {
        StockMovement::create([
            'product_id' => $product->product_id,
            'transaction_id' => $transactionId,
            'product_name' => $product->product_name,
            'status' => $status,
            'source' => 'online_order',
            'reason' => $reason,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'action_by' => $actor->name,
        ]);
    }

    protected function deductRecipeMaterials(Product $product, int $quantity, string $transactionId, User $actor): void
    {
        if ($product->recipes->isEmpty()) {
            $quantityBefore = (int) $product->product_quantity;
            $quantityAfter = $quantityBefore - $quantity;

            if ($quantityAfter < 0) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok '.$product->product_name.' tidak cukup. Tersedia '.$quantityBefore.', dibutuhkan '.$quantity.'.',
                ]);
            }

            $product->update(['product_quantity' => $quantityAfter]);
            $this->recordProductMovement($product, $transactionId, 4, 'Online Order', $quantityBefore, $quantityAfter, $actor);

            return;
        }

        foreach ($product->recipes->sortBy('raw_material_id') as $recipe) {
            $material = RawMaterial::whereKey($recipe->raw_material_id)->lockForUpdate()->firstOrFail();
            $requiredQuantity = (float) $recipe->quantity_required * $quantity;
            $quantityBefore = (float) $material->stock;
            $quantityAfter = $quantityBefore - $requiredQuantity;

            if ($quantityAfter < 0) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok bahan "'.$material->name.'" tidak cukup untuk '.$product->product_name.'. Butuh '.number_format($requiredQuantity, 2, ',', '.').' '.$material->unit.', sisa '.number_format($quantityBefore, 2, ',', '.').' '.$material->unit.'.',
                ]);
            }

            $material->update(['stock' => $quantityAfter]);
            $this->recordRawMaterialMovement($material, $transactionId, 'out', 'Online Order - '.$product->product_name, $requiredQuantity, $quantityBefore, $quantityAfter, $actor);
        }
    }

    protected function restoreRecipeMaterials(Product $product, int $quantity, string $transactionId, User $actor): void
    {
        if ($product->recipes->isEmpty()) {
            $quantityBefore = (int) $product->product_quantity;
            $quantityAfter = $quantityBefore + $quantity;

            $product->update(['product_quantity' => $quantityAfter]);
            $this->recordProductMovement($product, $transactionId, 2, 'Online Order Cancel', $quantityBefore, $quantityAfter, $actor);

            return;
        }

        foreach ($product->recipes->sortBy('raw_material_id') as $recipe) {
            $material = RawMaterial::whereKey($recipe->raw_material_id)->lockForUpdate()->firstOrFail();
            $restoredQuantity = (float) $recipe->quantity_required * $quantity;
            $quantityBefore = (float) $material->stock;
            $quantityAfter = $quantityBefore + $restoredQuantity;

            $material->update(['stock' => $quantityAfter]);
            $this->recordRawMaterialMovement($material, $transactionId, 'in', 'Online Order Cancel - '.$product->product_name, $restoredQuantity, $quantityBefore, $quantityAfter, $actor);
        }
    }

    protected function recordRawMaterialMovement(RawMaterial $material, string $transactionId, string $type, string $reason, float $quantity, float $quantityBefore, float $quantityAfter, User $actor): void
    {
        RawMaterialStockMovement::create([
            'raw_material_id' => $material->id,
            'transaction_id' => $transactionId,
            'type' => $type,
            'reason' => $reason,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'action_by' => $actor->name,
        ]);
    }
}
