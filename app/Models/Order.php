<?php

namespace App\Models;

use App\Support\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_CASH = 'cash';

    public const PAYMENT_TRANSFER = 'transfer';

    public const PAYMENT_QRIS = 'qris';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_WAITING_VERIFICATION = 'waiting_verification';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_REJECTED = 'payment_rejected';

    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'payment_method',
        'payment_status',
        'fulfillment_type',
        'total_price',
        'note',
        'confirmed_by',
        'completed_by',
        'cancelled_by',
        'cancel_reason',
        'stock_deducted_at',
        'confirmed_at',
        'processing_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'stock_deducted_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'processing_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function isStockDeducted(): bool
    {
        return $this->stock_deducted_at !== null;
    }

    public function paymentMethodLabel(): string
    {
        return PaymentMethod::label($this->payment_method);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED => 'Diterima',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_UNPAID => 'Belum Dibayar',
            self::PAYMENT_STATUS_WAITING_VERIFICATION => 'Menunggu Verifikasi',
            self::PAYMENT_STATUS_PAID => 'Lunas',
            self::PAYMENT_STATUS_REJECTED => 'Pembayaran Ditolak',
            default => ucfirst(str_replace('_', ' ', (string) $this->payment_status)),
        };
    }
}
