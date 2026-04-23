<?php

namespace App\Services;

use App\Models\DetailSale;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\AdminChatbotIntentParser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminChatbotService
{
    public function __construct(
        protected AdminChatbotIntentParser $intentParser = new AdminChatbotIntentParser()
    ) {
    }

    public function handle(string $message): array
    {
        $parsed = $this->intentParser->parse($message);

        $response = match ($parsed['intent']) {
            'cek_stok_produk' => $this->handleStockCheck($parsed),
            'produk_low_stock' => $this->handleLowStock($parsed),
            'produk_terlaris' => $this->handleTopSelling($parsed),
            'ringkasan_penjualan' => $this->handleSalesSummary($parsed),
            'riwayat_stock_movement' => $this->handleStockMovementHistory($parsed),
            'produk_akan_expired' => $this->handleExpiringSoon($parsed),
            'sales_per_cashier' => $this->handleSalesPerCashier($parsed),
            'stok_masuk_keluar_periode' => $this->handleStockFlowByPeriod($parsed),
            default => $this->unknownIntentResponse($parsed),
        };

        $this->logInteraction($parsed, $response);

        return $response;
    }

    protected function handleStockCheck(array $parsed): array
    {
        $productLookup = $this->resolveProductLookup($parsed['parameters']);

        if ($productLookup['status'] === 'multiple') {
            return $this->productCandidatesResponse('cek_stok_produk', $parsed['parameters'], $productLookup['candidates']);
        }

        if ($productLookup['status'] === 'none') {
            return [
                'success' => false,
                'intent' => 'cek_stok_produk',
                'parameters' => $parsed['parameters'],
                'data' => null,
                'message' => 'Produk yang Anda tanyakan tidak ditemukan. Coba pakai nama produk atau product ID yang lebih spesifik.',
            ];
        }

        $product = $productLookup['product'];

        return [
            'success' => true,
            'intent' => 'cek_stok_produk',
            'parameters' => $parsed['parameters'],
            'data' => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_quantity' => $product->product_quantity,
                'product_price' => $product->product_price,
                'product_profit' => $product->product_profit,
                'product_expired' => $product->product_expired,
            ],
            'message' => sprintf(
                'Stok %s (%s) saat ini %s unit. Harga jual %s, profit %s, expired %s.',
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->product_quantity),
                $this->formatRupiah($product->product_price),
                $this->formatRupiah($product->product_profit),
                $this->formatDate($product->product_expired)
            ),
        ];
    }

    protected function handleLowStock(array $parsed): array
    {
        $threshold = (int) ($parsed['parameters']['threshold'] ?? 5);

        $products = Product::query()
            ->select('product_id', 'product_name', 'product_quantity', 'product_price')
            ->where('product_quantity', '<=', $threshold)
            ->orderBy('product_quantity')
            ->orderBy('product_name')
            ->limit(10)
            ->get();

        if ($products->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'produk_low_stock',
                'parameters' => ['threshold' => $threshold],
                'data' => [
                    'threshold' => $threshold,
                    'products' => [],
                ],
                'message' => "Tidak ada produk dengan stok <= {$this->formatNumber($threshold)} saat ini.",
            ];
        }

        $lines = $products->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s) - stok %s unit',
                $index + 1,
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->product_quantity)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'produk_low_stock',
            'parameters' => ['threshold' => $threshold],
            'data' => [
                'threshold' => $threshold,
                'products' => $products->toArray(),
            ],
            'message' => "Produk dengan stok menipis (<= {$this->formatNumber($threshold)}) adalah: {$lines}.",
        ];
    }

    protected function handleTopSelling(array $parsed): array
    {
        $period = $parsed['parameters']['period'] ?? 'all_time';
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        $query = DetailSale::query()
            ->select(
                'detail_sales.product_id',
                'detail_sales.product_name',
                DB::raw('SUM(detail_sales.quantity) as total_quantity'),
                DB::raw('SUM(detail_sales.sub_total) as total_revenue')
            )
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        if ($startDate && $endDate) {
            $query->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        $topProducts = $query
            ->groupBy('detail_sales.product_id', 'detail_sales.product_name')
            ->orderByDesc('total_quantity')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'produk_terlaris',
                'parameters' => ['period' => $period],
                'data' => [
                    'period' => $period,
                    'products' => [],
                ],
                'message' => 'Belum ada data penjualan untuk periode yang diminta.',
            ];
        }

        $summary = $topProducts->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s) terjual %s item dengan omzet %s',
                $index + 1,
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->total_quantity),
                $this->formatRupiah($product->total_revenue)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'produk_terlaris',
            'parameters' => ['period' => $period],
            'data' => [
                'period' => $period,
                'products' => $topProducts->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'total_quantity' => (int) $product->total_quantity,
                        'total_revenue' => (int) $product->total_revenue,
                    ];
                })->toArray(),
            ],
            'message' => 'Produk terlaris ' . $this->periodLabel($period) . ': ' . $summary . '.',
        ];
    }

    protected function handleSalesSummary(array $parsed): array
    {
        $period = $parsed['parameters']['period'] ?? 'monthly';
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        $salesQuery = Sale::query();
        $detailQuery = DetailSale::query()->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate, $endDate]);
            $detailQuery->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        $transactionCount = $salesQuery->count();
        $totalSales = (int) $salesQuery->sum('total');
        $totalItems = (int) $detailQuery->sum('detail_sales.quantity');
        $averageSale = $transactionCount > 0 ? (int) round($totalSales / $transactionCount) : 0;

        if ($transactionCount === 0) {
            return [
                'success' => false,
                'intent' => 'ringkasan_penjualan',
                'parameters' => ['period' => $period],
                'data' => [
                    'period' => $period,
                    'transaction_count' => 0,
                    'total_sales' => 0,
                    'total_items' => 0,
                    'average_sale' => 0,
                ],
                'message' => 'Belum ada transaksi penjualan untuk periode yang diminta.',
            ];
        }

        return [
            'success' => true,
            'intent' => 'ringkasan_penjualan',
            'parameters' => ['period' => $period],
            'data' => [
                'period' => $period,
                'transaction_count' => $transactionCount,
                'total_sales' => $totalSales,
                'total_items' => $totalItems,
                'average_sale' => $averageSale,
            ],
            'message' => sprintf(
                'Ringkasan penjualan %s: %s transaksi, %s item terjual, total omzet %s, rata-rata per transaksi %s.',
                $this->periodLabel($period),
                $this->formatNumber($transactionCount),
                $this->formatNumber($totalItems),
                $this->formatRupiah($totalSales),
                $this->formatRupiah($averageSale)
            ),
        ];
    }

    protected function handleStockMovementHistory(array $parsed): array
    {
        $productLookup = $this->resolveProductLookup($parsed['parameters']);

        if ($productLookup['status'] === 'multiple') {
            return $this->productCandidatesResponse('riwayat_stock_movement', $parsed['parameters'], $productLookup['candidates']);
        }

        if ($productLookup['status'] === 'none') {
            return [
                'success' => false,
                'intent' => 'riwayat_stock_movement',
                'parameters' => $parsed['parameters'],
                'data' => null,
                'message' => 'Sebutkan nama produk atau product ID yang lebih spesifik untuk melihat riwayat stock movement.',
            ];
        }

        $product = $productLookup['product'];

        $movements = StockMovement::query()
            ->select(
                'product_id',
                'transaction_id',
                'product_name',
                'status',
                'reason',
                'quantity_before',
                'quantity_after',
                'action_by',
                'created_at'
            )
            ->where('product_id', $product->product_id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        if ($movements->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'riwayat_stock_movement',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'product_name' => $product->product_name,
                    'movements' => [],
                ],
                'message' => 'Tidak ada riwayat stock movement untuk produk yang diminta.',
            ];
        }

        $lines = $movements->map(function ($movement, $index) {
            return sprintf(
                '%d. %s | %s | %s -> %s | %s',
                $index + 1,
                $this->formatDateTime($movement->created_at),
                $movement->reason,
                $this->formatNumber($movement->quantity_before),
                $this->formatNumber($movement->quantity_after),
                $movement->action_by
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'riwayat_stock_movement',
            'parameters' => $parsed['parameters'],
            'data' => [
                'product_name' => $product->product_name,
                'movements' => $movements->map(function ($movement) {
                    return [
                        'product_id' => $movement->product_id,
                        'transaction_id' => $movement->transaction_id,
                        'product_name' => $movement->product_name,
                        'status' => $movement->status,
                        'reason' => $movement->reason,
                        'quantity_before' => $movement->quantity_before,
                        'quantity_after' => $movement->quantity_after,
                        'action_by' => $movement->action_by,
                        'created_at' => $movement->created_at,
                    ];
                })->toArray(),
            ],
            'message' => '10 riwayat stock movement terbaru untuk ' . $product->product_name . ': ' . $lines . '.',
        ];
    }

    protected function handleExpiringSoon(array $parsed): array
    {
        $days = (int) ($parsed['parameters']['days'] ?? 30);
        $today = now()->startOfDay()->toDateString();
        $deadline = now()->addDays($days)->endOfDay()->toDateString();

        $products = Product::query()
            ->select('product_id', 'product_name', 'product_quantity', 'product_expired')
            ->whereBetween('product_expired', [$today, $deadline])
            ->orderBy('product_expired')
            ->limit(10)
            ->get();

        if ($products->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'produk_akan_expired',
                'parameters' => ['days' => $days],
                'data' => [
                    'days' => $days,
                    'products' => [],
                ],
                'message' => "Tidak ada produk yang akan expired dalam {$this->formatNumber($days)} hari ke depan.",
            ];
        }

        $lines = $products->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s) - expired %s, stok %s unit',
                $index + 1,
                $product->product_name,
                $product->product_id,
                $this->formatDate($product->product_expired),
                $this->formatNumber($product->product_quantity)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'produk_akan_expired',
            'parameters' => ['days' => $days],
            'data' => [
                'days' => $days,
                'products' => $products->toArray(),
            ],
            'message' => "Produk yang akan expired dalam {$this->formatNumber($days)} hari ke depan: {$lines}.",
        ];
    }

    protected function handleSalesPerCashier(array $parsed): array
    {
        $period = $parsed['parameters']['period'] ?? 'monthly';
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        $query = Sale::query()
            ->select(
                'sales.cashier_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(sales.total) as total_sales'),
                'users.name as cashier_name'
            )
            ->leftJoin('users', 'users.id', '=', 'sales.cashier_id');

        if ($startDate && $endDate) {
            $query->whereBetween('sales.created_at', [$startDate, $endDate]);
        }

        $cashiers = $query
            ->groupBy('sales.cashier_id', 'users.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        if ($cashiers->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'sales_per_cashier',
                'parameters' => ['period' => $period],
                'data' => [
                    'period' => $period,
                    'cashiers' => [],
                ],
                'message' => 'Belum ada data penjualan kasir untuk periode yang diminta.',
            ];
        }

        $lines = $cashiers->map(function ($cashier, $index) {
            $name = $cashier->cashier_name ?: 'Cashier ID ' . $cashier->cashier_id;

            return sprintf(
                '%d. %s - %s transaksi, omzet %s',
                $index + 1,
                $name,
                $this->formatNumber($cashier->transaction_count),
                $this->formatRupiah($cashier->total_sales)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'sales_per_cashier',
            'parameters' => ['period' => $period],
            'data' => [
                'period' => $period,
                'cashiers' => $cashiers->map(function ($cashier) {
                    return [
                        'cashier_id' => $cashier->cashier_id,
                        'cashier_name' => $cashier->cashier_name,
                        'transaction_count' => (int) $cashier->transaction_count,
                        'total_sales' => (int) $cashier->total_sales,
                    ];
                })->toArray(),
            ],
            'message' => 'Penjualan per kasir ' . $this->periodLabel($period) . ': ' . $lines . '.',
        ];
    }

    protected function handleStockFlowByPeriod(array $parsed): array
    {
        $period = $parsed['parameters']['period'] ?? 'monthly';
        [$startDate, $endDate] = $this->resolvePeriodRange($period);

        $movements = StockMovement::query()
            ->select(
                'product_id',
                'product_name',
                'quantity_before',
                'quantity_after'
            )
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderByDesc('created_at')
            ->get();

        if ($movements->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'stok_masuk_keluar_periode',
                'parameters' => ['period' => $period],
                'data' => [
                    'period' => $period,
                    'products' => [],
                    'total_in' => 0,
                    'total_out' => 0,
                ],
                'message' => 'Belum ada data stock movement untuk periode yang diminta.',
            ];
        }

        $grouped = $movements->groupBy('product_id')->map(function ($items) {
            $first = $items->first();
            $stockIn = 0;
            $stockOut = 0;

            foreach ($items as $item) {
                $delta = (int) $item->quantity_after - (int) $item->quantity_before;

                if ($delta > 0) {
                    $stockIn += $delta;
                } elseif ($delta < 0) {
                    $stockOut += abs($delta);
                }
            }

            return [
                'product_id' => $first->product_id,
                'product_name' => $first->product_name,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
            ];
        })->sortByDesc(fn ($row) => $row['stock_in'] + $row['stock_out'])->take(10)->values();

        $totalIn = array_sum(array_column($grouped->toArray(), 'stock_in'));
        $totalOut = array_sum(array_column($grouped->toArray(), 'stock_out'));

        $lines = $grouped->map(function ($item, $index) {
            return sprintf(
                '%d. %s (%s) - masuk %s, keluar %s',
                $index + 1,
                $item['product_name'],
                $item['product_id'],
                $this->formatNumber($item['stock_in']),
                $this->formatNumber($item['stock_out'])
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'stok_masuk_keluar_periode',
            'parameters' => ['period' => $period],
            'data' => [
                'period' => $period,
                'products' => $grouped->toArray(),
                'total_in' => $totalIn,
                'total_out' => $totalOut,
            ],
            'message' => sprintf(
                'Pergerakan stok %s: total masuk %s unit, total keluar %s unit. Rinciannya: %s.',
                $this->periodLabel($period),
                $this->formatNumber($totalIn),
                $this->formatNumber($totalOut),
                $lines
            ),
        ];
    }

    protected function unknownIntentResponse(array $parsed): array
    {
        return [
            'success' => false,
            'intent' => 'unknown',
            'parameters' => $parsed['parameters'],
            'data' => null,
            'message' => 'Pertanyaan belum dikenali. Coba: "cek stok gula", "produk akan expired 30 hari", "sales per kasir bulan ini", "stok masuk keluar bulan ini", atau "riwayat stock movement bubuk matcha".',
        ];
    }

    protected function resolveProductLookup(array $parameters): array
    {
        if (!empty($parameters['product_id'])) {
            $product = Product::query()
                ->where('product_id', strtoupper($parameters['product_id']))
                ->first();

            if ($product) {
                return ['status' => 'single', 'product' => $product, 'candidates' => []];
            }
        }

        $productQuery = trim((string) ($parameters['product_query'] ?? ''));

        if ($productQuery === '') {
            return ['status' => 'none', 'product' => null, 'candidates' => []];
        }

        $normalized = mb_strtolower($productQuery);

        $exactCandidates = Product::query()
            ->whereRaw('LOWER(product_name) = ?', [$normalized])
            ->orWhereRaw('LOWER(product_id) = ?', [$normalized])
            ->orderBy('product_name')
            ->get();

        if ($exactCandidates->count() === 1) {
            return ['status' => 'single', 'product' => $exactCandidates->first(), 'candidates' => []];
        }

        if ($exactCandidates->count() > 1) {
            return ['status' => 'multiple', 'product' => null, 'candidates' => $exactCandidates];
        }

        $like = '%' . $normalized . '%';

        $partialCandidates = Product::query()
            ->whereRaw('LOWER(product_name) LIKE ?', [$like])
            ->orWhereRaw('LOWER(product_id) LIKE ?', [$like])
            ->orderByRaw('CASE WHEN LOWER(product_name) LIKE ? THEN 0 ELSE 1 END', [$normalized . '%'])
            ->orderBy('product_name')
            ->limit(10)
            ->get();

        if ($partialCandidates->count() === 1) {
            return ['status' => 'single', 'product' => $partialCandidates->first(), 'candidates' => []];
        }

        if ($partialCandidates->count() > 1) {
            return ['status' => 'multiple', 'product' => null, 'candidates' => $partialCandidates];
        }

        return ['status' => 'none', 'product' => null, 'candidates' => []];
    }

    protected function productCandidatesResponse(string $intent, array $parameters, Collection $candidates): array
    {
        $candidateList = $candidates->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s)',
                $index + 1,
                $product->product_name,
                $product->product_id
            );
        })->implode('; ');

        return [
            'success' => false,
            'intent' => $intent,
            'parameters' => $parameters,
            'data' => [
                'candidates' => $candidates->map(fn ($product) => [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                ])->toArray(),
            ],
            'message' => 'Saya menemukan beberapa produk yang mirip. Mohon pilih yang lebih spesifik: ' . $candidateList . '.',
        ];
    }

    protected function logInteraction(array $parsed, array $response): void
    {
        Log::info('admin_chatbot_query', [
            'question' => $parsed['original_message'] ?? null,
            'intent' => $response['intent'] ?? $parsed['intent'] ?? 'unknown',
            'parameters' => $response['parameters'] ?? $parsed['parameters'] ?? [],
            'success' => $response['success'] ?? false,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    protected function resolvePeriodRange(string $period): array
    {
        $now = now();

        return match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default => [null, null],
        };
    }

    protected function periodLabel(string $period): string
    {
        return match ($period) {
            'daily' => 'hari ini',
            'weekly' => 'minggu ini',
            'monthly' => 'bulan ini',
            default => 'sepanjang data yang tersedia',
        };
    }

    protected function formatRupiah(int|float|string|null $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    protected function formatNumber(int|float|string|null $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }

    protected function formatDate(string $date): string
    {
        return Carbon::parse($date)->locale('id')->translatedFormat('d M Y');
    }

    protected function formatDateTime(string $date): string
    {
        return Carbon::parse($date)->locale('id')->translatedFormat('d M Y H:i');
    }
}
