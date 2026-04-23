<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
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

        return 'INV-' . $dateString . '-' . str_pad((string) ($todayCount + 1), 4, '0', STR_PAD_LEFT);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }
}
