<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
        'customization',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'quantity' => 'integer',
            'subtotal' => 'integer',
            'customization' => 'array',
        ];
    }

    public function customizationSummary(): string
    {
        $customization = $this->customization ?? [];
        $parts = [];

        if (! empty($customization['ice_level'])) {
            $parts[] = 'Ice: '.$customization['ice_level'];
        }

        if (! empty($customization['sugar_level'])) {
            $parts[] = 'Sugar: '.$customization['sugar_level'];
        }

        if (! empty($customization['add_ons'])) {
            $parts[] = 'Add-ons: '.implode(', ', $customization['add_ons']);
        }

        return implode(' • ', $parts);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
