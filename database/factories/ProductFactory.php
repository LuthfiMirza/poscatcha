<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_id' => strtoupper(fake()->bothify('P####')),
            'product_name' => fake()->words(2, true),
            'product_category' => 'CAT001',
            'product_image' => 'demo-product.jpg',
            'product_price' => 15000,
            'buy_price' => 8000,
            'product_profit' => 7000,
            'product_quantity' => 10,
            'product_expired' => now()->addMonth()->format('Y-m-d'),
        ];
    }
}
