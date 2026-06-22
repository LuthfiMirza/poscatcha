<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
                'product_expired' => $product_expired
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
        return DB::transaction(function () use ($id, $product_id, $product_name, $product_category, $product_image, $product_price, $buy_price, $product_profit, $product_quantity, $product_expired, $transaction_id, $status, $reason, $quantity_before, $user,) {
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
