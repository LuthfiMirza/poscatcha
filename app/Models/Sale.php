<?php

namespace App\Models;

use App\Support\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'source',
        'order_id',
        'shift_id',
        'cashier_id',
        'total',
        'payment_method',
        'pay',
        'change',
    ];

    public static function generateInvoiceNumber(?Carbon $date = null): string
    {
        $date = $date ?: now();
        $dateString = $date->format('Ymd');
        $todayCount = self::query()
            ->whereDate('created_at', $date->toDateString())
            ->lockForUpdate()
            ->count();

        return 'INV-'.$dateString.'-'.str_pad((string) ($todayCount + 1), 4, '0', STR_PAD_LEFT);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethodLabel(): string
    {
        return PaymentMethod::label($this->payment_method);
    }
}
