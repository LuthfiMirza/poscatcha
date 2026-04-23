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

class DetailPendingSellingProduct extends Component
{
    public $cashier_id;
    public $cashier_name;
    public $products;
    public $carts = [];
    public $quantities = [];
    public $total = 0;
    public $cart_id;
    public $detail_pending;
    public $pay = 0;
    public $change = 0;
    public $payment_method = '';
    public $last_sale_id = null;

    public function mount($cart_id)
    {
        $this->cashier_id = Auth::user()->id;
        $this->cashier_name = Auth::user()->name;
        $this->products = Product::all();
        $this->cart_id = $cart_id;
        $this->detail_pending = DetailPendingCart::where('cashier_id', $this->cashier_id)
                                                ->where('cart_id', $this->cart_id)
                                                ->get();
        $this->refreshCarts();
    }

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

        $existing = DetailPendingCart::where('cart_id', $this->cart_id)
            ->where('product_id', $product->product_id)
            ->where('cashier_id', $this->cashier_id)
            ->first();

        if ($existing) {
            $existing->quantity += 1;
            $existing->sub_total = $existing->product_price * $existing->quantity;
            $existing->save();
        } else {
            DetailPendingCart::create([
                'cart_id' => $this->cart_id,
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

        $cart = DetailPendingCart::find($id);
        if ($cart) {
            $cart->quantity = $validated;
            $cart->sub_total = $cart->product_price * $validated;
            $cart->save();

            $this->refreshCarts();
        }
    }

    public function decrementQuantity($id)
    {
        $cart = DetailPendingCart::where('id', $id)
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
        $cart = DetailPendingCart::where('id', $id)
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
        DetailPendingCart::where('id', $id)
            ->where('cashier_id', $this->cashier_id)
            ->delete();

        $this->refreshCarts();
    }

    public function sellProduct()
    {
        $sale_id = DB::transaction(function () {
            $amount = DetailPendingCart::where('cart_id', $this->cart_id)->where('cashier_id', $this->cashier_id)->sum('sub_total');
            $carts = DetailPendingCart::where('cart_id', $this->cart_id)->where('cashier_id', $this->cashier_id)->get();
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
                $product = Product::where('product_id', $cart->product_id)->lockForUpdate()->first();
                $buyPrice = (float) ($product->buy_price ?? 0);
                $sellPrice = (float) $cart->product_price;
                $profitPerItem = ($sellPrice - $buyPrice) * (int) $cart->quantity;

                DetailSale::create([
                    'sale_id' => $saleId,
                    'cashier_id' => $cart->cashier_id,
                    'product_id' => $cart->product_id,
                    'product_name' => $cart->product_name,
                    'product_price' => $cart->product_price,
                    'product_profit' => $profitPerItem,
                    'buy_price' => $buyPrice,
                    'quantity' => $cart->quantity,
                    'sub_total' => $cart->sub_total,
                ]);

                $quantity_before = $product->product_quantity;
                $quantity_after = $product->product_quantity - $cart->quantity;   

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

            PendingCart::where('cart_id', $this->cart_id)->where('cashier_id', $this->cashier_id)->delete();

            return $saleId;
        });
        
        $this->last_sale_id = $sale_id;
        $this->refreshCarts();
        
        $this->dispatch('print-receipt', ['sale_id' => $sale_id]);
        
        session()->flash('transaction_success', 'Transaction completed successfully!');
        
        $this->pay = 0;
        $this->change = 0;
        $this->payment_method = '';

        $this->redirectRoute('pending_selling_product');
    }

    public function refreshCarts()
    {
        $this->carts = DetailPendingCart::where('cashier_id', $this->cashier_id)->get();
        $this->detail_pending = DetailPendingCart::where('cart_id', $this->cart_id)->get();

        foreach ($this->carts as $cart) {
            $this->quantities[$cart->id] = $cart->quantity;
        }

        $this->total = DetailPendingCart::where('cart_id', $this->cart_id)->where('cashier_id', $this->cashier_id)->sum('sub_total');
        $this->calculateChange();
    }

    public function render()
    {
        return view('livewire.detail-pending-selling-product');
    }
}
