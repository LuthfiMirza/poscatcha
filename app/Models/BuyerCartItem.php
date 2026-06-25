<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_cart_id',
        'product_id',
        'quantity',
        'customization',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
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

    public function cart(): BelongsTo
    {
        return $this->belongsTo(BuyerCart::class, 'buyer_cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
