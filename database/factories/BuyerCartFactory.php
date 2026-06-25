<?php

namespace Database\Factories;

use App\Models\BuyerCart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuyerCartFactory extends Factory
{
    protected $model = BuyerCart::class;

    public function definition(): array
    {
        return ['user_id' => User::factory()];
    }
}
