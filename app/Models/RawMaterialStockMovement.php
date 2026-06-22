<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'transaction_id',
        'type',
        'reason',
        'quantity',
        'quantity_before',
        'quantity_after',
        'action_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'quantity_before' => 'decimal:2',
            'quantity_after' => 'decimal:2',
        ];
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
