<?php

namespace App\Support;

use Illuminate\Support\Str;

class AdminChatbotIntentParser
{
    public function parse(string $message, array $context = []): array
    {
        $original = trim($message);
        $normalized = $this->normalize($original);

        if ($normalized === '') {
            return $this->buildPayload('unknown', [], $original, $normalized);
        }

        if ($this->isHelpIntent($normalized)) {
            return $this->buildPayload('bantuan_chatbot', [], $original, $normalized);
        }

        if ($this->isCashierComparisonIntent($normalized)) {
            return $this->buildPayload('perbandingan_kasir', $this->extractComparisonContext($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isProductTrendIntent($normalized)) {
            return $this->buildPayload('tren_penjualan_produk', $this->extractComparisonContext($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isSalesComparisonIntent($normalized)) {
            return $this->buildPayload('perbandingan_penjualan', $this->extractComparisonContext($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isShiftDifferenceIntent($normalized)) {
            return $this->buildPayload('selisih_shift_kasir', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isLatestCashierTransactionsIntent($normalized)) {
            return $this->buildPayload('transaksi_terakhir_kasir', array_merge(
                $this->extractCashierContext($normalized),
                $this->extractTimeFilters($normalized, 'current_month')
            ), $original, $normalized);
        }

        if ($this->isPaymentMethodIntent($normalized)) {
            return $this->buildPayload('penjualan_per_metode_pembayaran', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isProfitPerProductIntent($normalized)) {
            return $this->buildPayload('profit_per_produk', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isDeadStockIntent($normalized)) {
            return $this->buildPayload('stok_mati', [
                'days' => $this->extractDays($normalized, 30),
            ], $original, $normalized);
        }

        if ($this->isLeastSellingIntent($normalized)) {
            return $this->buildPayload('produk_paling_jarang_laku', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isTopCategoryIntent($normalized)) {
            return $this->buildPayload('top_kategori', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isLowStockIntent($normalized)) {
            return $this->buildPayload('produk_low_stock', array_merge(
                $this->extractTimeFilters($normalized, 'all_time'),
                ['threshold' => $this->extractThreshold($normalized)]
            ), $original, $normalized);
        }

        if ($this->isExpiringSoonIntent($normalized)) {
            return $this->buildPayload('produk_akan_expired', [
                'days' => $this->extractDays($normalized, 30),
            ], $original, $normalized);
        }

        if ($this->isSalesPerCashierIntent($normalized)) {
            return $this->buildPayload('sales_per_cashier', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isStockFlowIntent($normalized)) {
            return $this->buildPayload('stok_masuk_keluar_periode', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isTopSellingIntent($normalized)) {
            return $this->buildPayload('produk_terlaris', $this->extractTimeFilters($normalized, 'all_time'), $original, $normalized);
        }

        if ($this->isSalesSummaryIntent($normalized)) {
            return $this->buildPayload('ringkasan_penjualan', $this->extractTimeFilters($normalized, 'current_month'), $original, $normalized);
        }

        if ($this->isStockMovementIntent($normalized)) {
            return $this->buildPayload('riwayat_stock_movement', array_merge(
                $this->extractProductContext($normalized),
                $this->extractTimeFilters($normalized, 'all_time')
            ), $original, $normalized);
        }

        if ($this->isStockCheckIntent($normalized)) {
            return $this->buildPayload('cek_stok_produk', array_merge(
                $this->extractProductContext($normalized),
                ['stock_target' => $this->extractStockTarget($normalized)],
                $this->extractTimeFilters($normalized, 'all_time')
            ), $original, $normalized);
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
        $message = preg_replace('/[^\pL\pN\s\/-]/u', ' ', $message) ?? $message;

        return trim(preg_replace('/\s+/', ' ', $message) ?? $message);
    }

    protected function isHelpIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'bantuan',
            'help',
            'fitur chatbot',
            'fitur apa saja',
            'contoh pertanyaan',
            'kamu bisa apa',
        ]);
    }

    protected function isStockCheckIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'cek stok',
            'stok produk',
            'stock produk',
            'qty',
            'quantity',
            'sisa stok',
            'stoknya',
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

    protected function isLeastSellingIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'paling jarang laku',
            'kurang laku',
            'paling sepi',
            'least selling',
            'produk paling jarang',
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
            'omzet total',
        ]);
    }

    protected function isSalesComparisonIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'dibanding',
            'bandingkan',
            'vs',
            'versus',
            'compare',
        ]) && $this->containsAny($message, [
            'penjualan',
            'omzet',
            'transaksi',
        ]);
    }

    protected function isCashierComparisonIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'kasir mana yang naik',
            'kasir mana yang turun',
            'omzet kasir naik',
            'penjualan kasir naik',
            'kasir terbaik dibanding',
            'kasir dibanding',
        ]) || (
            $this->containsAny($message, ['kasir']) &&
            $this->containsAny($message, ['naik', 'turun', 'dibanding', 'bandingkan'])
        );
    }

    protected function isProductTrendIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'produk mana yang penjualannya turun',
            'produk yang turun',
            'penjualan produk turun',
            'produk yang naik',
            'tren produk',
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
            'riwayatnya',
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

    protected function isLatestCashierTransactionsIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'transaksi terakhir kasir',
            'penjualan terakhir kasir',
            'invoice terakhir kasir',
            'transaksi kasir terakhir',
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

    protected function isPaymentMethodIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'metode pembayaran',
            'pembayaran cash',
            'pembayaran transfer',
            'pembayaran qris',
            'cash vs transfer',
            'cash vs qris',
            'penjualan per metode',
        ]);
    }

    protected function isProfitPerProductIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'profit per produk',
            'laba per produk',
            'produk paling untung',
            'produk paling menguntungkan',
            'margin produk',
        ]);
    }

    protected function isDeadStockIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'stok mati',
            'dead stock',
            'tidak terjual',
            'belum terjual',
            'produk tidak laku',
        ]);
    }

    protected function isTopCategoryIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'top kategori',
            'kategori terlaris',
            'kategori paling laku',
            'kategori terbaik',
        ]);
    }

    protected function isShiftDifferenceIntent(string $message): bool
    {
        return $this->containsAny($message, [
            'selisih shift',
            'selisih kasir',
            'selisih kas shift',
            'beda kas shift',
            'audit shift',
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

    protected function extractDays(string $message, int $default = 30): int
    {
        if (preg_match('/(\d+)\s*hari/', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        return $default;
    }

    protected function extractComparisonContext(string $message, string $defaultCurrent): array
    {
        if ($this->containsAny($message, ['minggu ini']) && $this->containsAny($message, ['minggu lalu'])) {
            return [
                'period' => 'current_week',
                'compare_period' => 'previous_week',
            ];
        }

        if ($this->containsAny($message, ['bulan ini']) && $this->containsAny($message, ['bulan lalu'])) {
            return [
                'period' => 'current_month',
                'compare_period' => 'previous_month',
            ];
        }

        if ($this->containsAny($message, ['hari ini']) && $this->containsAny($message, ['kemarin'])) {
            return [
                'period' => 'today',
                'compare_period' => 'yesterday',
            ];
        }

        return array_merge(
            $this->extractTimeFilters($message, $defaultCurrent),
            ['compare_period' => 'previous_equivalent']
        );
    }

    protected function extractTimeFilters(string $message, string $default): array
    {
        if (preg_match('/tanggal\s+(\d{1,2})\s*(?:-|sampai|sd|s\/d)\s*(\d{1,2})/', $message, $matches)) {
            return [
                'period' => 'custom_day_range',
                'day_from' => (int) $matches[1],
                'day_to' => (int) $matches[2],
            ];
        }

        if (preg_match('/(\d+)\s*hari\s*terakhir/', $message, $matches)) {
            return [
                'period' => 'rolling_days',
                'days' => max(1, (int) $matches[1]),
            ];
        }

        if ($this->containsAny($message, ['kemarin', 'yesterday'])) {
            return ['period' => 'yesterday'];
        }

        if ($this->containsAny($message, ['hari ini', 'harian', 'daily'])) {
            return ['period' => 'today'];
        }

        if ($this->containsAny($message, ['minggu lalu'])) {
            return ['period' => 'previous_week'];
        }

        if ($this->containsAny($message, ['minggu ini', 'mingguan', 'weekly'])) {
            return ['period' => 'current_week'];
        }

        if ($this->containsAny($message, ['bulan lalu'])) {
            return ['period' => 'previous_month'];
        }

        if ($this->containsAny($message, ['bulan ini', 'bulanan', 'monthly'])) {
            return ['period' => 'current_month'];
        }

        if ($this->containsAny($message, ['tahun ini', 'tahunan', 'yearly'])) {
            return ['period' => 'current_year'];
        }

        if ($this->containsAny($message, ['semua waktu', 'all time', 'seluruh data'])) {
            return ['period' => 'all_time'];
        }

        return ['period' => $default];
    }

    protected function extractProductContext(string $message): array
    {
        return [
            'product_id' => $this->extractProductId($message),
            'product_query' => $this->extractFallbackProductQuery($message),
        ];
    }

    protected function extractStockTarget(string $message): string
    {
        if ($this->containsAny($message, ['stok produk', 'stok barang', 'stok menu'])) {
            return 'product';
        }

        return 'raw_material';
    }

    protected function extractCashierContext(string $message): array
    {
        return [
            'cashier_query' => $this->extractFallbackCashierQuery($message),
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
            'produk', 'barang', 'menu', 'untuk', 'yang', 'dari', 'dengan', 'detail',
            'tolong', 'riwayat', 'mutasi', 'pergerakan', 'movement', 'history', 'penjualan',
            'ringkasan', 'harian', 'mingguan', 'bulanan', 'hari', 'minggu', 'bulan',
            'ini', 'paling', 'laku', 'terlaris', 'show', 'data', 'akan', 'mendekati',
            'expired', 'kadaluarsa', 'dalam', 'sales', 'per', 'cashier', 'kasir',
            'masuk', 'keluar', 'periode', 'omzet', 'transaksi', 'riwayatnya', 'gimana',
            'bagaimana', 'nya', 'selisih', 'shift', 'bisa', 'bantu', 'dong',
        ];

        return $this->extractQueryByStopWords($message, $stopWords);
    }

    protected function extractFallbackCashierQuery(string $message): ?string
    {
        $stopWords = [
            'transaksi', 'terakhir', 'kasir', 'penjualan', 'invoice', 'lihat',
            'tolong', 'yang', 'dengan', 'untuk', 'di', 'periode', 'hari',
            'minggu', 'bulan', 'tahun', 'ini', 'lalu', 'terbaru',
        ];

        return $this->extractQueryByStopWords($message, $stopWords);
    }

    protected function extractQueryByStopWords(string $message, array $stopWords): ?string
    {
        $words = explode(' ', $message);
        $cleaned = array_filter($words, function ($word) use ($stopWords) {
            return $word !== '' && !in_array($word, $stopWords, true) && !preg_match('/^\d+$/', $word);
        });

        $cleaned = trim(preg_replace('/\s+/', ' ', implode(' ', $cleaned)) ?? '');

        return $cleaned !== '' ? $cleaned : null;
    }

    protected function containsAny(string $message, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($message, $needle) || $this->isApproximatePhraseMatch($message, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function isApproximatePhraseMatch(string $message, string $needle): bool
    {
        $messageTokens = array_values(array_filter(explode(' ', $message)));
        $needleTokens = array_values(array_filter(explode(' ', $needle)));

        if ($messageTokens === [] || $needleTokens === []) {
            return false;
        }

        foreach ($needleTokens as $needleToken) {
            $matched = false;

            foreach ($messageTokens as $messageToken) {
                if ($messageToken === $needleToken) {
                    $matched = true;
                    break;
                }

                if (Str::contains($messageToken, $needleToken) || Str::contains($needleToken, $messageToken)) {
                    $matched = true;
                    break;
                }

                $distance = levenshtein($messageToken, $needleToken);
                $threshold = strlen($needleToken) >= 7 ? 2 : 1;

                if ($distance <= $threshold) {
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                return false;
            }
        }

        return true;
    }
}
