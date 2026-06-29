<?php

namespace App\Services\OnlineOrdering;

use App\Models\BuyerCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Support\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderCheckoutService
{
    public function checkout(User $buyer, string $paymentMethod, ?string $note = null): Order
    {
        if (! in_array($paymentMethod, PaymentMethod::onlineValues(), true)) {
            throw ValidationException::withMessages(['payment_method' => 'Metode pembayaran tidak valid.']);
        }

        return DB::transaction(function () use ($buyer, $paymentMethod, $note) {
            $cart = BuyerCart::query()
                ->where('user_id', $buyer->id)
                ->with('items.product')
                ->lockForUpdate()
                ->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
            }

            $items = [];
            $total = 0;

            foreach ($cart->items as $cartItem) {
                $product = Product::query()
                    ->with('recipes.rawMaterial')
                    ->where('product_id', $cartItem->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($cartItem->quantity < 1) {
                    throw ValidationException::withMessages(['quantity' => 'Quantity tidak valid.']);
                }

                if (! $product->hasAvailableStock((int) $cartItem->quantity)) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stok bahan untuk '.$product->product_name.' tidak cukup untuk checkout.',
                    ]);
                }

                $subtotal = (int) $product->product_price * (int) $cartItem->quantity;
                $total += $subtotal;
                $items[] = compact('product', 'cartItem', 'subtotal');
            }

            $order = Order::create([
                'user_id' => $buyer->id,
                'order_code' => $this->generateOrderCode(),
                'status' => Order::STATUS_PENDING,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === PaymentMethod::CASH ? 'unpaid' : 'waiting_verification',
                'fulfillment_type' => 'pickup',
                'total_price' => $total,
                'note' => $note,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->product_id,
                    'product_name' => $item['product']->product_name,
                    'price' => (int) $item['product']->product_price,
                    'quantity' => (int) $item['cartItem']->quantity,
                    'subtotal' => $item['subtotal'],
                    'customization' => $item['cartItem']->customization,
                ]);
            }

            $cart->items()->delete();

            return $order->load('items');
        });
    }

    protected function generateOrderCode(): string
    {
        do {
            $code = 'ORD-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
