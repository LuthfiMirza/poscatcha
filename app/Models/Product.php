<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_name',
        'product_category',
        'product_image',
        'product_price',
        'buy_price',
        'product_profit',
        'product_quantity',
        'product_expired',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
        ];
    }

    public function stock_movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'product_id', 'product_id');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class, 'product_id', 'product_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'product_category', 'category_id');
    }

    public function buyerCartItems(): HasMany
    {
        return $this->hasMany(BuyerCartItem::class, 'product_id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function imageExists(): bool
    {
        if (blank($this->product_image)) {
            return false;
        }

        return Storage::disk('public')->exists('assets/product/'.$this->product_image)
            || file_exists(public_path('assets/product/'.$this->product_image));
    }

    public function imageUrl(): ?string
    {
        if (! $this->imageExists()) {
            return null;
        }

        return Storage::disk('public')->exists('assets/product/'.$this->product_image)
            ? asset('storage/assets/product/'.$this->product_image)
            : asset('assets/product/'.$this->product_image);
    }

    public function availableQuantity(): int
    {
        $this->loadMissing('recipes.rawMaterial');

        if ($this->recipes->isEmpty()) {
            return max(0, (int) $this->product_quantity);
        }

        return (int) $this->recipes
            ->map(function (ProductRecipe $recipe): int {
                $required = (float) $recipe->quantity_required;

                if ($required <= 0 || ! $recipe->rawMaterial) {
                    return 0;
                }

                return (int) floor((float) $recipe->rawMaterial->stock / $required);
            })
            ->min();
    }

    public function hasAvailableStock(int $quantity = 1): bool
    {
        return $this->availableQuantity() >= $quantity;
    }

    public static function postProduct($product_id, $product_name, $product_category, $product_image, $product_price, $buy_price, $product_profit, $product_quantity, $product_expired, $transaction_id, $status, $reason, $quantity_before, $user)
    {
        return DB::transaction(function () use ($product_id, $product_name, $product_category, $product_image, $product_price, $buy_price, $product_profit, $product_quantity, $product_expired, $transaction_id, $status, $reason, $quantity_before, $user) {
            $product = self::create([
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_category' => $product_category,
                'product_image' => $product_image,
                'product_price' => $product_price,
                'buy_price' => $buy_price,
                'product_profit' => $product_profit,
                'product_quantity' => $product_quantity,
                'product_expired' => $product_expired,
            ]);

            StockMovement::create([
                'product_id' => $product_id,
                'transaction_id' => $transaction_id,
                'product_name' => $product_name,
                'status' => $status,
                'source' => 'product',
                'reason' => $reason,
                'quantity_before' => $quantity_before,
                'quantity_after' => $product_quantity,
                'action_by' => $user,
            ]);

            return $product;
        });
    }

    public static function updateProduct($id, $product_id, $product_name, $product_category, $product_image, $product_price, $buy_price, $product_profit, $product_quantity, $product_expired, $transaction_id, $status, $reason, $quantity_before, $user)
    {
        return DB::transaction(function () use ($id, $product_id, $product_name, $product_category, $product_image, $product_price, $buy_price, $product_profit, $product_quantity, $product_expired, $transaction_id, $status, $reason, $quantity_before, $user) {
            $product = self::find($id);
            $product->product_name = $product_name;
            $product->product_category = $product_category;
            $product->product_image = $product_image;
            $product->product_price = $product_price;
            $product->buy_price = $buy_price;
            $product->product_profit = $product_profit;
            $product->product_quantity = $product_quantity;
            $product->product_expired = $product_expired;
            $product->save();

            StockMovement::create([
                'product_id' => $product_id,
                'transaction_id' => $transaction_id,
                'product_name' => $product_name,
                'status' => $status,
                'source' => 'product',
                'reason' => $reason,
                'quantity_before' => $quantity_before,
                'quantity_after' => $product_quantity,
                'action_by' => $user,
            ]);

            return $product;
        });
    }

    public static function deleteProduct($id, $transaction_id, $status, $reason, $quantity_after, $user)
    {
        return DB::transaction(function () use ($id, $transaction_id, $status, $reason, $quantity_after, $user) {
            $product = self::find($id);

            StockMovement::create([
                'product_id' => $product->product_id,
                'transaction_id' => $transaction_id,
                'product_name' => $product->product_name,
                'status' => $status,
                'source' => 'product',
                'reason' => $reason,
                'quantity_before' => $product->product_quantity,
                'quantity_after' => $quantity_after,
                'action_by' => $user,
            ]);

            $product->delete();

            return $product;
        });
    }
}
