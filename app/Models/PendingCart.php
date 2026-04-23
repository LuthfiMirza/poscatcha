<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendingCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'cashier_id',
        'amount',
    ];
}
