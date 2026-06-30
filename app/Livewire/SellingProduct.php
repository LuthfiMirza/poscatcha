<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Cart;
use App\Models\DetailSale;
use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\RawMaterialStockMovement;
use App\Models\Sale;
use App\Models\CashierShift;
use Illuminate\Validation\ValidationException;

class SellingProduct extends Component
{
    public $cashier_id;
    public $cashier_name;
    public $quantities = [];
    public $total = 0;
    public $pay = 0;
    public $change = 0;
    public $payment_method = '';
    public $last_sale_id = null;
    public $selected_category = '';
    public $pending_online_order_count = 0;
    public $pending_online_orders = [];
    public $show_online_order_popup = false;

    public function mount()
    {
        $this->cashier_id = Auth::user()->id;
        $this->cashier_name = Auth::user()->name;
        $this->refreshCarts(); 
        $this->refreshOnlineOrderNotifications();
    }

    // Method untuk menghitung change secara otomatis
    public function updatedPay()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        if ($this->pay <= 0) {
            $this->change = 0;
        } else {
            $this->change = max(0, $this->pay - $this->total);
        }
    }

    public function filterByCategory($categoryId = '')
    {
        $this->selected_category = (string) $categoryId;
    }

    public function toggleOnlineOrderPopup()
    {
        $this->show_online_order_popup = ! $this->show_online_order_popup;
        $this->refreshOnlineOrderNotifications();
    }

    public function closeOnlineOrderPopup()
    {
        $this->show_online_order_popup = false;
    }

    public function refreshOnlineOrderNotifications()
    {
        $previousPendingCount = (int) $this->pending_online_order_count;

        $pendingOrders = Order::query()
            ->with('buyer')
            ->withCount('items')
            ->where('status', Order::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get();

        $this->pending_online_order_count = Order::query()
            ->where('status', Order::STATUS_PENDING)
            ->count();

        $this->pending_online_orders = $pendingOrders
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'buyer_name' => $order->buyer?->name ?? 'Pembeli',
                'items_count' => $order->items_count,
                'total_price' => $order->total_price,
                'created_at' => $order->created_at?->diffForHumans(),
                'url' => route('online-orders.show', $order),
            ])
            ->all();

        if ($this->pending_online_order_count > $previousPendingCount) {
            $this->dispatch('online-order-received');
        }
    }

    public function addToCart($id)
    {
        $product = Product::query()->find($id);

        if (!$product) return;

        $existing = Cart::where('product_id', $product->product_id)
            ->where('cashier_id', $this->cashier_id)
            ->first();

        if ($existing) {
            $existing->quantity += 1;
            $existing->sub_total = $existing->product_price * $existing->quantity;
            $existing->save();
        } else {
            Cart::create([
                'cashier_id' => $this->cashier_id,
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_profit' => $product->product_profit,
                'product_price' => $product->product_price,
                'quantity' => 1,
                'sub_total' => $product->product_price,
            ]);
        }

        $this->refreshCarts();
    }

    public function updateQuantityManual($id)
    {
        $validated = max(1, (int) $this->quantities[$id]);

        $cart = Cart::where('id', $id)
            ->where('cashier_id', $this->cashier_id)
            ->first();

        if ($cart) {
            $cart->quantity = $validated;
            $cart->sub_total = $cart->product_price * $validated;
            $cart->save();

            $this->refreshCarts();
        }
    }

    public function decrementQuantity($id)
    {
        $cart = Cart::where('id', $id)
            ->where('cashier_id', $this->cashier_id)
            ->first();

        if (!$cart) return;

        if ($cart->quantity <= 1) {
            $cart->delete();
        } else {
            $cart->quantity -= 1;
            $cart->sub_total = $cart->product_price * $cart->quantity;
            $cart->save();
        }

        $this->refreshCarts();
    }

    public function incrementQuantity($id)
    {
        $cart = Cart::where('id', $id)
            ->where('cashier_id', $this->cashier_id)
            ->first();

        if (!$cart) return;

        $cart->quantity += 1;
        $cart->sub_total = $cart->product_price * $cart->quantity;
        $cart->save();

        $this->refreshCarts();
    }

    public function removeFromCart($id)
    {
        Cart::where('id', $id)
            ->where('cashier_id', $this->cashier_id)
            ->delete();

        $this->refreshCarts();
    }

    public function sellProduct()
    {
        try {
            $sale_id = DB::transaction(function () {
                $amount = Cart::where('cashier_id', $this->cashier_id)->sum('sub_total');
                $carts = Cart::where('cashier_id', $this->cashier_id)->get();
                $status = 4;
                $reason = "Product Sales";
                $saleId = Sale::generateInvoiceNumber();
                $activeShift = CashierShift::query()
                    ->open()
                    ->where('cashier_id', $this->cashier_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                Sale::create([
                    'sale_id' => $saleId,
                    'shift_id' => $activeShift->id,
                    'cashier_id' => $this->cashier_id,
                    'total' => $amount,
                    'payment_method' => $this->payment_method,
                    'pay' => $this->pay,
                    'change' => $this->change,
                ]); 

                foreach ($carts as $cart) {
                    $product = Product::where('product_id', $cart->product_id)
                        ->with('recipes.rawMaterial')
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($product->recipes->isEmpty()) {
                        throw ValidationException::withMessages([
                            'recipe' => 'Produk "' . $cart->product_name . '" belum punya resep bahan baku. Admin perlu mengisi resep dulu.',
                        ]);
                    }

                    $buyPrice = (float) ($product->buy_price ?? 0);
                    $sellPrice = (float) $cart->product_price;
                    $profitPerItem = ($sellPrice - $buyPrice) * (int) $cart->quantity;

                    DetailSale::create([
                        'sale_id' => $saleId,
                        'cashier_id' => $cart->cashier_id,
                        'product_id' => $cart->product_id,
                        'product_name' => $cart->product_name,
                        'product_profit' => $profitPerItem,
                        'product_price' => $cart->product_price,
                        'buy_price' => $buyPrice,
                        'quantity' => $cart->quantity,
                        'sub_total' => $cart->sub_total,
                    ]);

                    foreach ($product->recipes as $recipe) {
                        $material = RawMaterial::whereKey($recipe->raw_material_id)->lockForUpdate()->firstOrFail();
                        $requiredQuantity = (float) $recipe->quantity_required * (int) $cart->quantity;
                        $quantityBefore = (float) $material->stock;
                        $quantityAfter = $quantityBefore - $requiredQuantity;

                        if ($quantityAfter < 0) {
                            throw ValidationException::withMessages([
                                'stock' => 'Stok bahan "' . $material->name . '" tidak cukup untuk ' . $cart->product_name . '. Butuh ' . number_format($requiredQuantity, 2, ',', '.') . ' ' . $material->unit . ', sisa ' . number_format($quantityBefore, 2, ',', '.') . ' ' . $material->unit . '.',
                            ]);
                        }

                        $material->stock = $quantityAfter;
                        $material->save();

                        RawMaterialStockMovement::create([
                            'raw_material_id' => $material->id,
                            'transaction_id' => $saleId,
                            'type' => 'out',
                            'reason' => $reason . ' - ' . $cart->product_name,
                            'quantity' => $requiredQuantity,
                            'quantity_before' => $quantityBefore,
                            'quantity_after' => $quantityAfter,
                            'action_by' => $this->cashier_name,
                        ]);
                    }
                }

                Cart::where('cashier_id', $this->cashier_id)->delete();

                return $saleId;
            });
        } catch (ValidationException $exception) {
            session()->flash('error', collect($exception->errors())->flatten()->first() ?? 'Stok bahan tidak mencukupi.');
            $this->refreshCarts();

            return;
        }

        $this->last_sale_id = $sale_id;
        $this->refreshCarts();

        $this->dispatch('print-receipt', ['sale_id' => $sale_id]);
    }

    public function refreshCarts()
    {
        $carts = Cart::query()
            ->where('cashier_id', $this->cashier_id)
            ->orderBy('id')
            ->get();

        $this->quantities = [];
        foreach ($carts as $cart) {
            $this->quantities[$cart->id] = $cart->quantity;
        }

        $this->total = Cart::where('cashier_id', $this->cashier_id)->sum('sub_total');
        
        // Recalculate change when total changes
        $this->calculateChange();
    }

    public function render()
    {
        return view('livewire.selling_product', [
            'carts' => Cart::query()
                ->where('cashier_id', $this->cashier_id)
                ->orderBy('id')
                ->get(),
            'products' => Product::query()
                ->with('recipes.rawMaterial')
                ->orderBy('product_id')
                ->get(),
        ]);
    }
}
