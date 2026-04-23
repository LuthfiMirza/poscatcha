<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'cashier_id',
        'product_id',
        'product_name',
        'product_price',
        'buy_price',
        'product_profit',
        'quantity',
        'sub_total',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'product_profit' => 'decimal:2',
        ];
    }
}
