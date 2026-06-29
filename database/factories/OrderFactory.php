<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_code' => 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'status' => Order::STATUS_PENDING,
            'payment_method' => Order::PAYMENT_CASH,
            'payment_status' => 'unpaid',
            'fulfillment_type' => 'pickup',
            'total_price' => 0,
        ];
    }
}
