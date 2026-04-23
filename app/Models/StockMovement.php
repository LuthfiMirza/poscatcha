<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'transaction_id',
        'product_name',
        'status',
        'source',
        'reason',
        'quantity_before',
        'quantity_after',
        'action_by', 
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->source) {
            'purchase' => 'Barang Masuk',
            'sale' => 'Barang Keluar',
            default => 'Penyesuaian',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'purchase' => 'Restock',
            'sale' => 'Penjualan',
            default => 'Produk',
        };
    }
}
