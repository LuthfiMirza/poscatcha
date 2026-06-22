<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock',
        'minimum_stock',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'minimum_stock' => 'decimal:2',
        ];
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(RawMaterialStockMovement::class);
    }
}
