<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'product_id',
        'product_name',
        'product_profit',
        'product_price',
        'quantity',
        'sub_total',
    ];
}
