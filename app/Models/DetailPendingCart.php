<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPendingCart extends Model
{
    protected $fillable = [
        'cart_id',
        'cashier_id',
        'product_id',
        'product_name',
        'product_profit',
        'product_price',
        'quantity',
        'sub_total',
    ];
}
