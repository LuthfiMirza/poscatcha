<?php

namespace App\Services\OnlineOrdering;

use App\Models\BuyerCart;
use App\Models\BuyerCartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BuyerCartService
{
    public function cartFor(User $buyer): BuyerCart
    {
        return BuyerCart::firstOrCreate(['user_id' => $buyer->id]);
    }

    public function add(User $buyer, Product $product, int $quantity = 1, array $customization = []): BuyerCartItem
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Quantity minimal 1.']);
        }

        return DB::transaction(function () use ($buyer, $product, $quantity, $customization) {
            $cart = $this->cartFor($buyer);
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $item = BuyerCartItem::where('buyer_cart_id', $cart->id)
                ->where('product_id', $product->product_id)
                ->lockForUpdate()
                ->first();

            $newQuantity = ($item?->quantity ?? 0) + $quantity;
            $this->assertAvailableStock($product, $newQuantity);

            if ($item) {
                $item->update([
                    'quantity' => $newQuantity,
                    'customization' => $this->normalizeCustomization($customization),
                ]);

                return $item;
            }

            return BuyerCartItem::create([
                'buyer_cart_id' => $cart->id,
                'product_id' => $product->product_id,
                'quantity' => $newQuantity,
                'customization' => $this->normalizeCustomization($customization),
            ]);
        });
    }

    public function update(User $buyer, BuyerCartItem $item, int $quantity): BuyerCartItem
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Quantity minimal 1.']);
        }

        return DB::transaction(function () use ($buyer, $item, $quantity) {
            $cart = $this->cartFor($buyer);
            $item = BuyerCartItem::whereKey($item->id)
                ->where('buyer_cart_id', $cart->id)
                ->lockForUpdate()
                ->firstOrFail();
            $product = Product::where('product_id', $item->product_id)->lockForUpdate()->firstOrFail();

            $this->assertAvailableStock($product, $quantity);
            $item->update(['quantity' => $quantity]);

            return $item;
        });
    }

    public function remove(User $buyer, BuyerCartItem $item): void
    {
        $cart = $this->cartFor($buyer);

        BuyerCartItem::whereKey($item->id)
            ->where('buyer_cart_id', $cart->id)
            ->delete();
    }

    public function count(User $buyer): int
    {
        $cart = $buyer->buyerCart;

        if (! $cart) {
            return 0;
        }

        return (int) $cart->items()->sum('quantity');
    }

    protected function assertAvailableStock(Product $product, int $quantity): void
    {
        if (! $product->hasAvailableStock($quantity)) {
            throw ValidationException::withMessages([
                'stock' => 'Stok bahan untuk '.$product->product_name.' tidak cukup.',
            ]);
        }
    }

    protected function normalizeCustomization(array $customization): array
    {
        return [
            'ice_level' => $customization['ice_level'] ?? 'Normal Ice',
            'sugar_level' => $customization['sugar_level'] ?? 'Normal Sugar',
            'add_ons' => array_values(array_filter($customization['add_ons'] ?? [])),
        ];
    }
}
