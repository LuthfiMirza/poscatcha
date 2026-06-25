<?php

namespace Database\Factories;

use App\Models\BuyerCart;
use App\Models\BuyerCartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyerCartItemFactory extends Factory
{
    protected $model = BuyerCartItem::class;

    public function definition(): array
    {
        return [
            'buyer_cart_id' => BuyerCart::factory(),
            'product_id' => fn () => Product::factory()->create()->product_id,
            'quantity' => 1,
        ];
    }
}
