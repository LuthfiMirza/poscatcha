<?php

namespace App\Support;

use Illuminate\Support\Str;

class AdminChatbotIntentParser
{
    public function parse(string $message): array
    {
        $original = trim($message);
        $normalized = $this->normalize($original);

        if ($normalized === '') {
            return $this->buildPayload('unknown', [], $original, $normalized);
        }

        if ($this->isLowStockIntent($normalized)) {
            return $this->buildPayload('produk_low_stock', [
                'threshold' => $this->extractThreshold($normalized),
            ], $original, $normalized);
        }

        if ($this->isExpiringSoonIntent($normalized)) {
            return $this->buildPayload('produk_akan_expired', [
                'days' => $this->extractDays($normalized),
            ], $original, $normalized);
        }

        if ($this->isSalesPerCashierIntent($normalized)) {
            return $this->buildPayload('sales_per_cashier', [
                'period' => $this->extractPeriod($normalized, 'monthly'),
            ], $original, $normalized);
        }

        if ($this->isStockFlowIntent($normalized)) {
            return $this->buildPayload('stok_masuk_keluar_periode', [
                'period' => $this->extractPeriod($normalized, 'monthly'),
            ], $original, $normalized);
        }

        if ($this->isTopSellingIntent($normalized)) {
            return $this->buildPayload('produk_terlaris', [
                'period' => $this->extractPeriod($normalized, 'all_time'),
            ], $original, $normalized);
        }

        if ($this->isSalesSummaryIntent($normalized)) {
            return $this->buildPayload('ringkasan_penjualan', [
                'period' => $this->extractPeriod($normalized, 'monthly'),
            ], $original, $normalized);
        }

        if ($this->isStockMovementIntent($normalized)) {
            return $this->buildPayload('riwayat_stock_movement', $this->extractProductContext($normalized), $original, $normalized);
        }

        if ($this->isStockCheckIntent($normalized)) {
            return $this->buildPayload('cek_stok_produk', $this->extractProductContext($normalized), $original, $normalized);
        }

        return $this->buildPayload('unknown', [], $original, $normalized);
    }

    protected function buildPayload(string $intent, array $parameters, string $original, string $normalized): array
    {
        return [
            'intent' => $intent,
            'parameters' => $parameters,
            'original_message' => $original,
            'normalized_message' => $normalized,
        ];
    }

    protected function normalize(string $message): string
    {
        $message = Str::lower($message);
        $message = preg_replace('/[^\pL\pN\s]/u', ' ', $message) ?? $message;

        return trim(preg_replace('/\s+/', ' ', $message) ?? $message);
    }

    protected function isStockCheckIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'cek stok',
            'stok produk',
            'stock produk',
            'qty ',
            'quantity ',
            'sisa stok',
        ]);
    }

    protected function isLowStockIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'stok menipis',
            'stock menipis',
            'stok rendah',
            'low stock',
            'stok hampir habis',
        ]);
    }

    protected function isTopSellingIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'produk paling laku',
            'produk terlaris',
            'barang paling laku',
            'barang terlaris',
            'best seller',
            'paling laku',
            'terlaris',
        ]);
    }

    protected function isSalesSummaryIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'ringkasan penjualan',
            'summary penjualan',
            'penjualan harian',
            'penjualan mingguan',
            'penjualan bulanan',
            'total penjualan',
            'laporan penjualan',
        ]);
    }

    protected function isStockMovementIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'stock movement',
            'stok movement',
            'riwayat stok',
            'mutasi stok',
            'pergerakan stok',
            'history stok',
        ]);
    }

    protected function isExpiringSoonIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'akan expired',
            'mendekati expired',
            'produk expired',
            'produk yang expired',
            'kadaluarsa',
            'expired dalam',
        ]);
    }

    protected function isSalesPerCashierIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'sales per cashier',
            'sales per kasir',
            'penjualan per cashier',
            'penjualan per kasir',
            'omzet per kasir',
            'transaksi per kasir',
        ]);
    }

    protected function isStockFlowIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'stok masuk keluar',
            'stock masuk keluar',
            'stok masuk',
            'stok keluar',
            'mutasi periode',
            'pergerakan stok periode',
        ]);
    }

    protected function extractThreshold(string $message): int
    {
        if (preg_match('/(?:di bawah|dibawah|kurang dari|under)\s+(\d+)/', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        if (preg_match('/(?:<=|<)\s*(\d+)/', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        return 5;
    }

    protected function extractDays(string $message): int
    {
        if (preg_match('/(\d+)\s*hari/', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        return 30;
    }

    protected function extractPeriod(string $message, string $default): string
    {
        if ($this->containsAny($message, ['hari ini', 'harian', 'daily'])) {
            return 'daily';
        }

        if ($this->containsAny($message, ['minggu ini', 'mingguan', 'weekly'])) {
            return 'weekly';
        }

        if ($this->containsAny($message, ['bulan ini', 'bulanan', 'monthly'])) {
            return 'monthly';
        }

        return $default;
    }

    protected function extractProductContext(string $message): array
    {
        return [
            'product_id' => $this->extractProductId($message),
            'product_query' => $this->extractFallbackProductQuery($message),
        ];
    }

    protected function extractProductId(string $message): ?string
    {
        if (preg_match('/\b([a-z]\d{4})\b/i', $message, $matches)) {
            return strtoupper($matches[1]);
        }

        return null;
    }

    protected function extractFallbackProductQuery(string $message): ?string
    {
        $stopWords = [
            'cek', 'lihat', 'berapa', 'stok', 'stock', 'qty', 'quantity', 'sisa',
            'produk', 'barang', 'untuk', 'yang', 'dari', 'dengan', 'detail',
            'tolong', 'riwayat', 'mutasi', 'pergerakan', 'movement', 'history', 'penjualan',
            'ringkasan', 'harian', 'mingguan', 'bulanan', 'hari', 'minggu', 'bulan',
            'ini', 'paling', 'laku', 'terlaris', 'show', 'data', 'akan', 'mendekati',
            'expired', 'kadaluarsa', 'dalam', 'sales', 'per', 'cashier', 'kasir',
            'masuk', 'keluar', 'periode', 'omzet', 'transaksi',
        ];

        $words = explode(' ', $message);
        $cleaned = array_filter($words, fn ($word) => !in_array($word, $stopWords, true));
        $cleaned = trim(preg_replace('/\s+/', ' ', implode(' ', $cleaned)) ?? '');

        return $cleaned !== '' ? $cleaned : null;
    }

    protected function containsAny(string $message, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }
}
