<?php

namespace App\Support;

use InvalidArgumentException;

class PaymentMethod
{
    public const CASH = 'cash';

    public const TRANSFER = 'transfer';

    public const QRIS = 'qris';

    public const SALES_CASH = 'C';

    public const SALES_TRANSFER = 'T';

    public const SALES_QRIS = 'Q';

    private const SALES_MAP = [
        self::CASH => self::SALES_CASH,
        self::TRANSFER => self::SALES_TRANSFER,
        self::QRIS => self::SALES_QRIS,
    ];

    private const LABELS = [
        self::CASH => 'Cash',
        self::TRANSFER => 'Transfer',
        self::QRIS => 'QRIS',
        self::SALES_CASH => 'Cash',
        self::SALES_TRANSFER => 'Transfer',
        self::SALES_QRIS => 'QRIS',
    ];

    public static function onlineValues(): array
    {
        return array_keys(self::SALES_MAP);
    }

    public static function toSales(string $paymentMethod): string
    {
        if (! isset(self::SALES_MAP[$paymentMethod])) {
            throw new InvalidArgumentException('Metode pembayaran tidak valid.');
        }

        return self::SALES_MAP[$paymentMethod];
    }

    public static function label(?string $paymentMethod): string
    {
        if ($paymentMethod === null || $paymentMethod === '') {
            return '-';
        }

        return self::LABELS[$paymentMethod] ?? strtoupper($paymentMethod);
    }
}
