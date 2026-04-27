<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Cart;
use App\Models\DetailPendingCart;
use App\Models\DetailSale;
use App\Models\Product;
use App\Models\PendingCart;
use App\Models\Sale;
use App\Models\CashierShift;
use App\Models\StockMovement;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SellingProduct extends Component
{
    public $cashier_id;
    public $cashier_name;
    public $products;
    public $carts = [];
    public $quantities = [];
    public $total = 0;
    public $pay = 0;
    public $change = 0;
    public $payment_method = '';
    public $last_sale_id = null;

    public function mount()
    {
        $this->cashier_id = Auth::user()->id;
        $this->cashier_name = Auth::user()->name;
        $this->products = Product::all();
        $this->refreshCarts(); 
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

    public function addToCart($id)
    {
        $product = Product::find($id);

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

    public function addPendingOrder()
    {
        $cart_id = date('Y-m-d_H:i:s') . '_' . Str::random(10);
        $amount = Cart::where('cashier_id', $this->cashier_id)->sum('sub_total');
        $carts = Cart::where('cashier_id', $this->cashier_id)->get();
        
        PendingCart::create([
            'cart_id' => $cart_id,
            'cashier_id' => $this->cashier_id,
            'amount' => $amount,
        ]);

        foreach ($carts as $cart) {
            DetailPendingCart::create([
                'cart_id' => $cart_id,
                'cashier_id' => $cart->cashier_id,
                'product_id' => $cart->product_id,
                'product_name' => $cart->product_name,
                'product_profit' => $cart->product_profit,
                'product_price' => $cart->product_price,
                'quantity' => $cart->quantity,
                'sub_total' => $cart->sub_total,
            ]);
        }

        Cart::where('cashier_id', $this->cashier_id)->delete();

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
                    $product = Product::where('product_id', $cart->product_id)->lockForUpdate()->firstOrFail();
                    $buyPrice = (float) ($product->buy_price ?? 0);
                    $sellPrice = (float) $cart->product_price;
                    $profitPerItem = ($sellPrice - $buyPrice) * (int) $cart->quantity;
                    $quantity_before = $product->product_quantity;
                    $quantity_after = $product->product_quantity - $cart->quantity;

                    if ($quantity_after < 0) {
                        throw ValidationException::withMessages([
                            'stock' => 'Stok produk "' . $cart->product_name . '" tidak mencukupi. Sisa stok saat ini: ' . $quantity_before . '.',
                        ]);
                    }

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

                    StockMovement::create([
                        'product_id' => $cart->product_id,
                        'transaction_id' => $saleId,
                        'product_name' => $cart->product_name,
                        'status' => $status,
                        'source' => 'sale',
                        'reason' => $reason,
                        'cashier_id' => $cart->cashier_id,
                        'quantity_before' => $quantity_before,
                        'quantity_after' => $quantity_after,
                        'action_by' => $this->cashier_name,
                    ]);

                    $product->product_quantity = $quantity_after;
                    $product->save();
                }

                Cart::where('cashier_id', $this->cashier_id)->delete();

                return $saleId;
            });
        } catch (ValidationException $exception) {
            session()->flash('error', collect($exception->errors())->flatten()->first() ?? 'Stok produk tidak mencukupi.');
            $this->refreshCarts();

            return;
        }

        $this->last_sale_id = $sale_id;
        $this->refreshCarts();

        $this->dispatch('print-receipt', ['sale_id' => $sale_id]);
    }

    public function refreshCarts()
    {
        $this->carts = Cart::where('cashier_id', $this->cashier_id)->get();

        foreach ($this->carts as $cart) {
            $this->quantities[$cart->id] = $cart->quantity;
        }

        $this->total = Cart::where('cashier_id', $this->cashier_id)->sum('sub_total');
        
        // Recalculate change when total changes
        $this->calculateChange();
    }

    public function render()
    {
        return view('livewire.selling_product');
    }
}
