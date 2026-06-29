<?php

namespace App\Services;

use App\Models\AdminChatbotLog;
use App\Models\CashierShift;
use App\Models\Category;
use App\Models\DetailSale;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Support\AdminChatbotIntentParser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class AdminChatbotService
{
    public function __construct(
        protected AdminChatbotIntentParser $intentParser = new AdminChatbotIntentParser()
    ) {
    }

    public function handle(string $message, array $context = [], ?int $userId = null, ?string $sessionId = null): array
    {
        $startedAt = microtime(true);
        $parsed = $this->applyContextFallbacks($this->intentParser->parse($message, $context), $context);

        $response = match ($parsed['intent']) {
            'bantuan_chatbot' => $this->helpResponse(),
            'cek_stok_produk' => $this->handleStockCheck($parsed),
            'produk_low_stock' => $this->handleLowStock($parsed),
            'produk_terlaris' => $this->handleTopSelling($parsed),
            'ringkasan_penjualan' => $this->handleSalesSummary($parsed),
            'riwayat_stock_movement' => $this->handleStockMovementHistory($parsed),
            'produk_akan_expired' => $this->handleExpiringSoon($parsed),
            'sales_per_cashier' => $this->handleSalesPerCashier($parsed),
            'stok_masuk_keluar_periode' => $this->handleStockFlowByPeriod($parsed),
            'penjualan_per_metode_pembayaran' => $this->handleSalesByPaymentMethod($parsed),
            'profit_per_produk' => $this->handleProfitPerProduct($parsed),
            'produk_paling_jarang_laku' => $this->handleLeastSellingProducts($parsed),
            'stok_mati' => $this->handleDeadStock($parsed),
            'transaksi_terakhir_kasir' => $this->handleLatestCashierTransactions($parsed),
            'top_kategori' => $this->handleTopCategories($parsed),
            'selisih_shift_kasir' => $this->handleShiftDifferences($parsed),
            'perbandingan_penjualan' => $this->handleSalesComparison($parsed),
            'perbandingan_kasir' => $this->handleCashierComparison($parsed),
            'tren_penjualan_produk' => $this->handleProductTrend($parsed),
            default => $this->unknownIntentResponse($parsed),
        };

        $response['intent'] = $response['intent'] ?? $parsed['intent'];
        $response['parameters'] = $response['parameters'] ?? $parsed['parameters'];
        $response['actions'] = $response['actions'] ?? $this->buildActions($response['intent']);
        $response['meta'] = array_merge(
            $response['meta'] ?? [],
            $this->buildResponseMeta($response['intent'])
        );
        $response['context'] = $this->buildNextContext($context, $parsed, $response);

        $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);
        $response['latency_ms'] = $latencyMs;

        $log = $this->logInteraction($parsed, $response, $userId, $sessionId, $latencyMs);
        $response['log_id'] = $log?->id;

        return $response;
    }

    public function submitFeedback(int $logId, string $feedback): bool
    {
        if (!in_array($feedback, ['helpful', 'not_helpful'], true)) {
            return false;
        }

        try {
            if (!Schema::hasTable('admin_chatbot_logs')) {
                return false;
            }

            $log = AdminChatbotLog::query()->find($logId);

            if (!$log) {
                return false;
            }

            $log->update([
                'feedback' => $feedback,
                'feedback_at' => now(),
            ]);

            return true;
        } catch (Throwable $exception) {
            Log::warning('admin_chatbot_feedback_failed', [
                'log_id' => $logId,
                'feedback' => $feedback,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function helpResponse(): array
    {
        $primaryInsights = [
            'Ringkasan penjualan per periode.',
            'Produk terlaris dan penjualan per kasir.',
            'Penjualan per metode pembayaran.',
            'Selisih shift kasir.',
            'Riwayat stock movement produk tertentu.',
        ];

        $secondaryInsights = [
            'Profit per produk.',
            'Perbandingan penjualan antar periode.',
            'Perbandingan performa kasir.',
            'Transaksi terakhir kasir tertentu.',
            'Tren penjualan produk.',
        ];

        $conditionalInsights = [
            'Cek stok produk tertentu.',
            'Produk low stock.',
            'Produk akan expired.',
            'Stok mati atau produk tidak terjual.',
            'Top kategori saat data kategori sudah lebih beragam.',
        ];

        $examples = [
            'Ringkasan penjualan minggu ini',
            'Produk terlaris bulan ini',
            'Penjualan per metode pembayaran bulan ini',
            'Sales per kasir bulan ini',
            'Selisih shift kasir bulan ini',
            'penjualan minggu ini dibanding minggu lalu',
            'kasir mana yang naik omzetnya bulan ini',
            'riwayat stock movement gula',
        ];

        $primarySummary = collect($primaryInsights)
            ->map(fn ($item, $index) => ($index + 1) . '. ' . $item)
            ->implode(' ');
        $secondarySummary = collect($secondaryInsights)
            ->map(fn ($item, $index) => ($index + 1) . '. ' . $item)
            ->implode(' ');
        $conditionalSummary = collect($conditionalInsights)
            ->map(fn ($item, $index) => ($index + 1) . '. ' . $item)
            ->implode(' ');

        $message = 'Fokus utama saya sekarang adalah insight operasional yang datanya paling kuat di POS Anda. '
            . 'Insight utama: ' . $primarySummary
            . ' Analisis lanjutan: ' . $secondarySummary
            . ' Insight tambahan saat data mendukung: ' . $conditionalSummary
            . ' Contoh pertanyaan prioritas: ' . implode('; ', $examples) . '.';

        return [
            'success' => true,
            'intent' => 'bantuan_chatbot',
            'parameters' => [],
            'data' => [
                'primary_insights' => $primaryInsights,
                'secondary_insights' => $secondaryInsights,
                'conditional_insights' => $conditionalInsights,
                'examples' => $examples,
            ],
            'message' => $message,
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleStockCheck(array $parsed): array
    {
        $stockTarget = $parsed['parameters']['stock_target'] ?? 'raw_material';

        if (empty($parsed['parameters']['product_id']) && empty($parsed['parameters']['product_query'])) {
            if ($stockTarget === 'raw_material') {
                return $this->rawMaterialStockListResponse($parsed['parameters']);
            }

            return $this->productStockListResponse($parsed['parameters']);
        }

        if ($stockTarget === 'product') {
            $productLookup = $this->resolveProductLookup($parsed['parameters']);

            if ($productLookup['status'] === 'single') {
                return $this->productStockResponse($parsed['parameters'], $productLookup['product']);
            }

            if ($productLookup['status'] === 'multiple') {
                return $this->productCandidatesResponse('cek_stok_produk', $parsed['parameters'], $productLookup['candidates']);
            }

            return [
                'success' => false,
                'intent' => 'cek_stok_produk',
                'parameters' => $parsed['parameters'],
                'data' => null,
                'message' => 'Produk yang Anda tanyakan tidak ditemukan. Coba pakai nama produk atau product ID yang lebih spesifik.',
                'actions' => [
                    $this->makeAction('Lihat Produk', 'admin.products.index'),
                ],
            ];
        }

        $rawMaterialLookup = $this->resolveRawMaterialLookup($parsed['parameters']);

        if ($rawMaterialLookup['status'] === 'single') {
            return $this->rawMaterialStockResponse($parsed['parameters'], $rawMaterialLookup['raw_material']);
        }

        if ($rawMaterialLookup['status'] === 'multiple') {
            return $this->rawMaterialCandidatesResponse('cek_stok_produk', $parsed['parameters'], $rawMaterialLookup['candidates']);
        }

        $productLookup = $this->resolveProductLookup($parsed['parameters']);

        if ($productLookup['status'] === 'single') {
            return $this->productStockResponse(
                $parsed['parameters'],
                $productLookup['product'],
                'Bahan yang Anda tanyakan tidak ditemukan. Saya menemukan produk yang cocok: '
            );
        }

        if ($productLookup['status'] === 'multiple') {
            $response = $this->productCandidatesResponse('cek_stok_produk', $parsed['parameters'], $productLookup['candidates']);
            $response['message'] = 'Bahan yang Anda tanyakan tidak ditemukan. ' . $response['message'] . ' Jika ingin cek menu/produk, gunakan format "cek stok produk nama".';

            return $response;
        }

        return [
            'success' => false,
            'intent' => 'cek_stok_produk',
            'parameters' => $parsed['parameters'],
            'data' => null,
            'message' => 'Bahan yang Anda tanyakan tidak ditemukan. Jika ingin cek menu/produk, gunakan format "cek stok produk nama".',
            'actions' => [
                $this->makeAction('Restock', 'purchases.create'),
                $this->makeAction('Lihat Produk', 'admin.products.index'),
            ],
        ];
    }

    protected function productStockListResponse(array $parameters): array
    {
            $products = Product::query()
                ->select('product_id', 'product_name', 'product_quantity')
                ->orderBy('product_name')
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return [
                    'success' => false,
                    'intent' => 'cek_stok_produk',
                    'parameters' => $parameters,
                    'data' => null,
                    'message' => 'Belum ada data produk untuk dicek stoknya.',
                    'actions' => [
                        $this->makeAction('Lihat Produk', 'admin.products.index'),
                    ],
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
                'intent' => 'cek_stok_produk',
                'parameters' => $parameters,
                'data' => [
                    'products' => $products->map(fn ($product) => [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'product_quantity' => $product->product_quantity,
                    ])->toArray(),
                ],
                'message' => 'Daftar stok produk: ' . $lines . '.',
                'actions' => [
                    $this->makeAction('Lihat Produk', 'admin.products.index'),
                ],
            ];
    }

    protected function productStockResponse(array $parameters, Product $product, string $messagePrefix = ''): array
    {
        return [
            'success' => true,
            'intent' => 'cek_stok_produk',
            'parameters' => $parameters,
            'data' => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'product_quantity' => $product->product_quantity,
                'product_price' => $product->product_price,
                'product_profit' => $product->product_profit,
                'product_expired' => $product->product_expired,
            ],
            'message' => $messagePrefix . sprintf(
                'Stok produk %s (%s) saat ini %s unit. Harga jual %s, profit %s, expired %s.',
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->product_quantity),
                $this->formatRupiah($product->product_price),
                $this->formatRupiah($product->product_profit),
                $this->formatDate($product->product_expired)
            ),
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                $this->makeAction('Restock', 'purchases.create'),
            ],
        ];
    }

    protected function rawMaterialStockListResponse(array $parameters): array
    {
        $materials = RawMaterial::query()
            ->select('id', 'name', 'stock', 'unit', 'minimum_stock')
            ->orderBy('name')
            ->limit(10)
            ->get();

        if ($materials->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'cek_stok_produk',
                'parameters' => $parameters,
                'data' => null,
                'message' => 'Belum ada data bahan baku untuk dicek stoknya.',
                'actions' => [
                    $this->makeAction('Restock', 'purchases.create'),
                ],
            ];
        }

        $lines = $materials->map(function ($material, $index) {
            return sprintf(
                '%d. %s - stok %s %s',
                $index + 1,
                $material->name,
                $this->formatNumber($material->stock),
                $material->unit
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'cek_stok_produk',
            'parameters' => $parameters,
            'data' => [
                'raw_materials' => $materials->map(fn ($material) => [
                    'raw_material_id' => $material->id,
                    'raw_material_name' => $material->name,
                    'stock' => $material->stock,
                    'unit' => $material->unit,
                    'minimum_stock' => $material->minimum_stock,
                ])->toArray(),
            ],
            'message' => 'Daftar stok bahan baku: ' . $lines . '.',
            'actions' => [
                $this->makeAction('Restock', 'purchases.create'),
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
            ],
        ];
    }

    protected function rawMaterialStockResponse(array $parameters, RawMaterial $material): array
    {
        return [
            'success' => true,
            'intent' => 'cek_stok_produk',
            'parameters' => $parameters,
            'data' => [
                'raw_material_id' => $material->id,
                'raw_material_name' => $material->name,
                'stock' => $material->stock,
                'unit' => $material->unit,
                'minimum_stock' => $material->minimum_stock,
            ],
            'message' => sprintf(
                'Stok bahan %s saat ini %s %s. Minimum stok %s %s.',
                $material->name,
                $this->formatNumber($material->stock),
                $material->unit,
                $this->formatNumber($material->minimum_stock),
                $material->unit
            ),
            'actions' => [
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                $this->makeAction('Restock', 'purchases.create'),
            ],
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
                'actions' => [
                    $this->makeAction('Lihat Produk', 'admin.products.index'),
                ],
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
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Restock', 'purchases.create'),
            ],
        ];
    }

    protected function handleTopSelling(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'all_time');

        $query = DetailSale::query()
            ->select(
                'detail_sales.product_id',
                'detail_sales.product_name',
                'products.product_category',
                DB::raw('SUM(detail_sales.quantity) as total_quantity'),
                DB::raw('SUM(detail_sales.sub_total) as total_revenue')
            )
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id')
            ->leftJoin('products', 'products.product_id', '=', 'detail_sales.product_id');

        $this->applySalesWindow($query, 'sales.created_at', $window);

        $topProducts = $query
            ->groupBy('detail_sales.product_id', 'detail_sales.product_name', 'products.product_category')
            ->orderByDesc('total_quantity')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'produk_terlaris',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
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
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'products' => $topProducts->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'product_category' => $product->product_category,
                        'total_quantity' => (int) $product->total_quantity,
                        'total_revenue' => (int) $product->total_revenue,
                    ];
                })->toArray(),
            ],
            'message' => 'Produk terlaris ' . $window['label'] . ': ' . $summary . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
        ];
    }

    protected function handleSalesSummary(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $salesQuery = Sale::query();
        $detailQuery = DetailSale::query()->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        $this->applySalesWindow($salesQuery, 'created_at', $window);
        $this->applySalesWindow($detailQuery, 'sales.created_at', $window);

        $transactionCount = $salesQuery->count();
        $totalSales = (int) $salesQuery->sum('total');
        $totalItems = (int) $detailQuery->sum('detail_sales.quantity');
        $averageSale = $transactionCount > 0 ? (int) round($totalSales / $transactionCount) : 0;

        if ($transactionCount === 0) {
            return [
                'success' => false,
                'intent' => 'ringkasan_penjualan',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
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
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'transaction_count' => $transactionCount,
                'total_sales' => $totalSales,
                'total_items' => $totalItems,
                'average_sale' => $averageSale,
            ],
            'message' => sprintf(
                'Ringkasan penjualan %s: %s transaksi, %s item terjual, total omzet %s, rata-rata per transaksi %s.',
                $window['label'],
                $this->formatNumber($transactionCount),
                $this->formatNumber($totalItems),
                $this->formatRupiah($totalSales),
                $this->formatRupiah($averageSale)
            ),
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
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
                'actions' => [
                    $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                ],
            ];
        }

        $product = $productLookup['product'];
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'all_time');

        $movements = StockMovement::query()
            ->select(
                'product_id',
                'transaction_id',
                'product_name',
                'status',
                'source',
                'reason',
                'quantity_before',
                'quantity_after',
                'action_by',
                'created_at'
            )
            ->where('product_id', $product->product_id)
            ->orderByDesc('created_at');

        $this->applySalesWindow($movements, 'created_at', $window);

        $movements = $movements->limit(10)->get();

        if ($movements->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'riwayat_stock_movement',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'product_name' => $product->product_name,
                    'movements' => [],
                ],
                'message' => 'Tidak ada riwayat stock movement untuk produk yang diminta pada periode tersebut.',
                'actions' => [
                    $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                ],
            ];
        }

        $lines = $movements->map(function ($movement, $index) {
            return sprintf(
                '%d. %s | %s/%s | %s -> %s | %s',
                $index + 1,
                $this->formatDateTime($movement->created_at),
                $movement->source,
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
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'period' => $window['period'],
                'movements' => $movements->map(function ($movement) {
                    return [
                        'product_id' => $movement->product_id,
                        'transaction_id' => $movement->transaction_id,
                        'product_name' => $movement->product_name,
                        'status' => $movement->status,
                        'source' => $movement->source,
                        'reason' => $movement->reason,
                        'quantity_before' => $movement->quantity_before,
                        'quantity_after' => $movement->quantity_after,
                        'action_by' => $movement->action_by,
                        'created_at' => $movement->created_at,
                    ];
                })->toArray(),
            ],
            'message' => '10 riwayat stock movement terbaru untuk ' . $product->product_name . ' ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                $this->makeAction('Lihat Produk', 'admin.products.index'),
            ],
        ];
    }

    protected function handleExpiringSoon(array $parsed): array
    {
        $days = (int) ($parsed['parameters']['days'] ?? 30);
        $today = now()->startOfDay()->toDateString();
        $deadline = now()->copy()->addDays($days)->endOfDay()->toDateString();

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
                'actions' => [
                    $this->makeAction('Lihat Produk', 'admin.products.index'),
                ],
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
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Restock', 'purchases.create'),
            ],
        ];
    }

    protected function handleSalesPerCashier(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $query = Sale::query()
            ->select(
                'sales.cashier_id',
                'users.name as cashier_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(sales.total) as total_sales')
            )
            ->leftJoin('users', 'users.id', '=', 'sales.cashier_id');

        $this->applySalesWindow($query, 'sales.created_at', $window);

        $cashiers = $query
            ->groupBy('sales.cashier_id', 'users.name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        if ($cashiers->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'sales_per_cashier',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
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
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'cashiers' => $cashiers->map(function ($cashier) {
                    return [
                        'cashier_id' => $cashier->cashier_id,
                        'cashier_name' => $cashier->cashier_name,
                        'transaction_count' => (int) $cashier->transaction_count,
                        'total_sales' => (int) $cashier->total_sales,
                    ];
                })->toArray(),
            ],
            'message' => 'Penjualan per kasir ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleStockFlowByPeriod(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $movements = StockMovement::query()
            ->select(
                'product_id',
                'product_name',
                'quantity_before',
                'quantity_after'
            )
            ->orderByDesc('created_at');

        $this->applySalesWindow($movements, 'created_at', $window);

        $movements = $movements->get();

        if ($movements->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'stok_masuk_keluar_periode',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
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
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'products' => $grouped->toArray(),
                'total_in' => $totalIn,
                'total_out' => $totalOut,
            ],
            'message' => sprintf(
                'Pergerakan stok %s: total masuk %s unit, total keluar %s unit. Rinciannya: %s.',
                $window['label'],
                $this->formatNumber($totalIn),
                $this->formatNumber($totalOut),
                $lines
            ),
            'actions' => [
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
                $this->makeAction('Restock', 'purchases.index'),
            ],
        ];
    }

    protected function handleSalesByPaymentMethod(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $query = Sale::query()
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total) as total_sales')
            );

        $this->applySalesWindow($query, 'created_at', $window);

        $rows = $query
            ->groupBy('payment_method')
            ->orderByDesc('total_sales')
            ->get();

        if ($rows->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'penjualan_per_metode_pembayaran',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
                    'methods' => [],
                ],
                'message' => 'Belum ada data penjualan per metode pembayaran pada periode tersebut.',
            ];
        }

        $lines = $rows->map(function ($row, $index) {
            return sprintf(
                '%d. %s - %s transaksi, omzet %s',
                $index + 1,
                $this->paymentMethodLabel($row->payment_method),
                $this->formatNumber($row->transaction_count),
                $this->formatRupiah($row->total_sales)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'penjualan_per_metode_pembayaran',
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'methods' => $rows->map(function ($row) {
                    return [
                        'payment_method' => (string) $row->payment_method,
                        'payment_method_label' => $this->paymentMethodLabel($row->payment_method),
                        'transaction_count' => (int) $row->transaction_count,
                        'total_sales' => (int) $row->total_sales,
                    ];
                })->toArray(),
            ],
            'message' => 'Penjualan per metode pembayaran ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleProfitPerProduct(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $query = DetailSale::query()
            ->select(
                'detail_sales.product_id',
                'detail_sales.product_name',
                DB::raw('SUM(detail_sales.quantity) as total_quantity'),
                DB::raw('SUM(detail_sales.sub_total) as total_revenue'),
                DB::raw('SUM(detail_sales.buy_price * detail_sales.quantity) as total_cost'),
                DB::raw('SUM(detail_sales.product_profit) as total_profit')
            )
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        $this->applySalesWindow($query, 'sales.created_at', $window);

        $rows = $query
            ->groupBy('detail_sales.product_id', 'detail_sales.product_name')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();

        if ($rows->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'profit_per_produk',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
                    'products' => [],
                ],
                'message' => 'Belum ada data profit produk untuk periode yang diminta.',
            ];
        }

        $lines = $rows->map(function ($row, $index) {
            $margin = (float) $row->total_revenue > 0
                ? ((float) $row->total_profit / (float) $row->total_revenue) * 100
                : 0;

            return sprintf(
                '%d. %s - laba %s, omzet %s, margin %s%%',
                $index + 1,
                $row->product_name,
                $this->formatRupiah($row->total_profit),
                $this->formatRupiah($row->total_revenue),
                number_format($margin, 1, ',', '.')
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'profit_per_produk',
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'products' => $rows->map(function ($row) {
                    return [
                        'product_id' => $row->product_id,
                        'product_name' => $row->product_name,
                        'total_quantity' => (int) $row->total_quantity,
                        'total_revenue' => (int) $row->total_revenue,
                        'total_cost' => (int) $row->total_cost,
                        'total_profit' => (int) $row->total_profit,
                    ];
                })->toArray(),
            ],
            'message' => 'Produk paling menguntungkan ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Laporan Profit', 'reports.profit'),
                $this->makeAction('Lihat Penjualan', 'sales_data'),
            ],
        ];
    }

    protected function handleLeastSellingProducts(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $salesSubquery = DetailSale::query()
            ->select(
                'detail_sales.product_id',
                DB::raw('SUM(detail_sales.quantity) as qty_sold'),
                DB::raw('SUM(detail_sales.sub_total) as revenue')
            )
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        $this->applySalesWindow($salesSubquery, 'sales.created_at', $window);

        $salesSubquery = $salesSubquery->groupBy('detail_sales.product_id');

        $products = Product::query()
            ->leftJoinSub($salesSubquery, 'sales_agg', function ($join) {
                $join->on('products.product_id', '=', 'sales_agg.product_id');
            })
            ->select(
                'products.product_id',
                'products.product_name',
                DB::raw('COALESCE(sales_agg.qty_sold, 0) as qty_sold'),
                DB::raw('COALESCE(sales_agg.revenue, 0) as revenue')
            )
            ->orderBy('qty_sold')
            ->orderBy('products.product_name')
            ->limit(5)
            ->get();

        if ($products->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'produk_paling_jarang_laku',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
                    'products' => [],
                ],
                'message' => 'Belum ada data produk untuk dianalisis.',
            ];
        }

        $lines = $products->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s) - terjual %s item, omzet %s',
                $index + 1,
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->qty_sold),
                $this->formatRupiah($product->revenue)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'produk_paling_jarang_laku',
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'products' => $products->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'qty_sold' => (int) $product->qty_sold,
                        'revenue' => (int) $product->revenue,
                    ];
                })->toArray(),
            ],
            'message' => 'Produk paling jarang laku ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Lihat Penjualan', 'sales_data'),
            ],
        ];
    }

    protected function handleDeadStock(array $parsed): array
    {
        $days = (int) ($parsed['parameters']['days'] ?? 30);
        $start = now()->copy()->subDays($days)->startOfDay();
        $end = now()->copy()->endOfDay();

        $products = Product::query()
            ->whereNotExists(function ($query) use ($start, $end) {
                $query->selectRaw('1')
                    ->from('detail_sales')
                    ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id')
                    ->whereColumn('detail_sales.product_id', 'products.product_id')
                    ->whereBetween('sales.created_at', [$start, $end]);
            })
            ->orderByDesc('product_quantity')
            ->orderBy('product_name')
            ->limit(10)
            ->get(['product_id', 'product_name', 'product_quantity', 'product_expired']);

        if ($products->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'stok_mati',
                'parameters' => ['days' => $days],
                'data' => [
                    'days' => $days,
                    'products' => [],
                ],
                'message' => "Tidak ada stok mati. Semua produk punya penjualan dalam {$this->formatNumber($days)} hari terakhir.",
            ];
        }

        $lines = $products->map(function ($product, $index) {
            return sprintf(
                '%d. %s (%s) - stok %s unit, expired %s',
                $index + 1,
                $product->product_name,
                $product->product_id,
                $this->formatNumber($product->product_quantity),
                $this->formatDate($product->product_expired)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'stok_mati',
            'parameters' => ['days' => $days],
            'data' => [
                'days' => $days,
                'products' => $products->toArray(),
            ],
            'message' => "Produk yang tidak terjual dalam {$this->formatNumber($days)} hari terakhir: {$lines}.",
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
        ];
    }

    protected function handleLatestCashierTransactions(array $parsed): array
    {
        $cashierLookup = $this->resolveCashierLookup($parsed['parameters']);

        if ($cashierLookup['status'] === 'multiple') {
            return $this->cashierCandidatesResponse($parsed['parameters'], $cashierLookup['candidates']);
        }

        if ($cashierLookup['status'] === 'none') {
            return [
                'success' => false,
                'intent' => 'transaksi_terakhir_kasir',
                'parameters' => $parsed['parameters'],
                'data' => null,
                'message' => 'Nama kasir yang dimaksud belum jelas. Coba sebutkan nama kasir yang lebih spesifik.',
                'actions' => [
                    $this->makeAction('Lihat Penjualan', 'sales_data'),
                    $this->makeAction('Lihat User', 'user_data'),
                ],
            ];
        }

        $cashier = $cashierLookup['cashier'];
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $sales = Sale::query()
            ->where('cashier_id', $cashier->id)
            ->latest()
            ->limit(5);

        $this->applySalesWindow($sales, 'created_at', $window);

        $sales = $sales->get();

        if ($sales->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'transaksi_terakhir_kasir',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'cashier_id' => $cashier->id,
                    'cashier_name' => $cashier->name,
                    'transactions' => [],
                ],
                'message' => 'Belum ada transaksi untuk kasir tersebut pada periode yang diminta.',
            ];
        }

        $lines = $sales->map(function ($sale, $index) {
            return sprintf(
                '%d. %s | %s | %s',
                $index + 1,
                $sale->sale_id,
                $this->formatDateTime($sale->created_at),
                $this->formatRupiah($sale->total)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'transaksi_terakhir_kasir',
            'parameters' => $parsed['parameters'],
            'data' => [
                'cashier_id' => $cashier->id,
                'cashier_name' => $cashier->name,
                'period' => $window['period'],
                'transactions' => $sales->map(function ($sale) {
                    return [
                        'sale_id' => $sale->sale_id,
                        'total' => (int) $sale->total,
                        'payment_method' => $sale->payment_method,
                        'created_at' => $sale->created_at,
                    ];
                })->toArray(),
            ],
            'message' => 'Transaksi terakhir kasir ' . $cashier->name . ' ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleTopCategories(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $query = Category::query()
            ->leftJoin('products', 'products.product_category', '=', 'categories.category_id')
            ->leftJoin('detail_sales', 'detail_sales.product_id', '=', 'products.product_id')
            ->leftJoin('sales', 'sales.sale_id', '=', 'detail_sales.sale_id')
            ->select(
                'categories.category_id',
                'categories.category_name',
                DB::raw('COALESCE(SUM(detail_sales.quantity), 0) as qty_sold'),
                DB::raw('COALESCE(SUM(detail_sales.sub_total), 0) as revenue'),
                DB::raw('COALESCE(SUM(detail_sales.product_profit), 0) as profit')
            );

        if ($window['start'] && $window['end']) {
            $query->where(function ($inner) use ($window) {
                $inner->whereBetween('sales.created_at', [$window['start'], $window['end']])
                    ->orWhereNull('sales.created_at');
            });
        }

        $categories = $query
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        if ($categories->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'top_kategori',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
                    'categories' => [],
                ],
                'message' => 'Belum ada data kategori untuk periode yang diminta.',
            ];
        }

        $lines = $categories->map(function ($category, $index) {
            return sprintf(
                '%d. %s - terjual %s item, omzet %s, laba %s',
                $index + 1,
                $category->category_name,
                $this->formatNumber($category->qty_sold),
                $this->formatRupiah($category->revenue),
                $this->formatRupiah($category->profit)
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'top_kategori',
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'categories' => $categories->map(function ($category) {
                    return [
                        'category_id' => $category->category_id,
                        'category_name' => $category->category_name,
                        'qty_sold' => (int) $category->qty_sold,
                        'revenue' => (int) $category->revenue,
                        'profit' => (int) $category->profit,
                    ];
                })->toArray(),
            ],
            'message' => 'Top kategori ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Kategori', 'admin.categories.index'),
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
        ];
    }

    protected function handleShiftDifferences(array $parsed): array
    {
        $window = $this->resolvePeriodWindow($parsed['parameters'], 'current_month');

        $shifts = CashierShift::query()
            ->with(['cashier', 'sales'])
            ->latest('shift_start');

        $this->applySalesWindow($shifts, 'shift_start', $window);

        $shifts = $shifts->limit(10)->get()->map(function (CashierShift $shift) {
            $cashTotal = (float) $shift->sales->where('payment_method', '1')->sum('total');
            $expectedCash = (float) $shift->opening_cash + $cashTotal;
            $difference = $shift->closing_cash !== null
                ? (float) $shift->closing_cash - $expectedCash
                : null;

            return [
                'cashier_name' => $shift->cashier?->name ?? 'Kasir',
                'shift_start' => $shift->shift_start,
                'status' => $shift->status,
                'opening_cash' => (float) $shift->opening_cash,
                'cash_total' => $cashTotal,
                'expected_cash' => $expectedCash,
                'closing_cash' => $shift->closing_cash !== null ? (float) $shift->closing_cash : null,
                'difference' => $difference,
            ];
        });

        if ($shifts->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'selisih_shift_kasir',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'period' => $window['period'],
                    'shifts' => [],
                ],
                'message' => 'Belum ada data shift kasir pada periode yang diminta.',
            ];
        }

        $lines = $shifts->map(function ($shift, $index) {
            $differenceLabel = $shift['difference'] === null
                ? 'belum ditutup'
                : $this->formatRupiah($shift['difference']);

            return sprintf(
                '%d. %s | %s | selisih %s',
                $index + 1,
                $shift['cashier_name'],
                $this->formatDateTime($shift['shift_start']),
                $differenceLabel
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'selisih_shift_kasir',
            'parameters' => $parsed['parameters'],
            'data' => [
                'period' => $window['period'],
                'shifts' => $shifts->values()->toArray(),
            ],
            'message' => 'Ringkasan selisih shift kasir ' . $window['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleSalesComparison(array $parsed): array
    {
        [$currentWindow, $comparisonWindow] = $this->resolveComparisonWindows($parsed['parameters'], 'current_month');

        $current = $this->salesAggregate($currentWindow);
        $comparison = $this->salesAggregate($comparisonWindow);

        $salesDiff = $current['total_sales'] - $comparison['total_sales'];
        $transactionDiff = $current['transaction_count'] - $comparison['transaction_count'];
        $currentAov = $this->averagePerTransaction($current['total_sales'], $current['transaction_count']);
        $comparisonAov = $this->averagePerTransaction($comparison['total_sales'], $comparison['transaction_count']);
        $aovDiff = $currentAov - $comparisonAov;
        $salesChangePercent = $this->percentageChange($current['total_sales'], $comparison['total_sales']);
        $transactionChangePercent = $this->percentageChange($current['transaction_count'], $comparison['transaction_count']);
        $direction = $this->comparisonDirection($salesDiff);
        $insight = $this->salesComparisonInsight($current, $comparison, $salesDiff, $transactionDiff, $aovDiff);
        $recommendation = $this->salesComparisonRecommendation($current, $comparison, $salesDiff, $transactionDiff, $aovDiff);
        $periodSummary = sprintf(
            'Periode: %s (%s) vs %s (%s).',
            ucfirst($currentWindow['label']),
            $this->formatWindowRange($currentWindow),
            $comparisonWindow['label'],
            $this->formatWindowRange($comparisonWindow)
        );
        $headline = $salesChangePercent === null
            ? sprintf('Penjualan %s dibanding %s %s %s.', $currentWindow['label'], $comparisonWindow['label'], $direction, $this->formatRupiah(abs($salesDiff)))
            : sprintf('Penjualan %s dibanding %s %s %s (%s).', $currentWindow['label'], $comparisonWindow['label'], $direction, $this->formatRupiah(abs($salesDiff)), $this->formatPercent(abs($salesChangePercent)));

        return [
            'success' => true,
            'intent' => 'perbandingan_penjualan',
            'parameters' => $parsed['parameters'],
            'data' => [
                'current' => $current,
                'comparison' => $comparison,
                'sales_diff' => $salesDiff,
                'transaction_diff' => $transactionDiff,
                'average_order_value' => [
                    'current' => $currentAov,
                    'comparison' => $comparisonAov,
                    'diff' => $aovDiff,
                ],
                'change_percent' => [
                    'sales' => $salesChangePercent,
                    'transactions' => $transactionChangePercent,
                ],
                'insight' => $insight,
                'recommendation' => $recommendation,
            ],
            'message' => implode(' ', [
                $headline,
                $periodSummary,
                sprintf(
                    'Ringkasan: omzet %s vs %s, transaksi %s vs %s, rata-rata/transaksi %s vs %s.',
                    $this->formatRupiah($current['total_sales']),
                    $this->formatRupiah($comparison['total_sales']),
                    $this->formatNumber($current['transaction_count']),
                    $this->formatNumber($comparison['transaction_count']),
                    $this->formatRupiah($currentAov),
                    $this->formatRupiah($comparisonAov)
                ),
                sprintf(
                    'Selisih: omzet %s, transaksi %s, rata-rata/transaksi %s.',
                    $this->formatSignedRupiah($salesDiff),
                    $this->formatSignedNumber($transactionDiff),
                    $this->formatSignedRupiah($aovDiff)
                ),
                'Insight: ' . $insight,
                'Saran: ' . $recommendation,
            ]),
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
        ];
    }

    protected function handleCashierComparison(array $parsed): array
    {
        [$currentWindow, $comparisonWindow] = $this->resolveComparisonWindows($parsed['parameters'], 'current_month');

        $currentRows = $this->cashierAggregates($currentWindow)->keyBy('cashier_id');
        $comparisonRows = $this->cashierAggregates($comparisonWindow)->keyBy('cashier_id');

        $combined = collect($currentRows->keys())
            ->merge($comparisonRows->keys())
            ->unique()
            ->map(function ($cashierId) use ($currentRows, $comparisonRows) {
                $current = $currentRows->get($cashierId);
                $comparison = $comparisonRows->get($cashierId);

                return [
                    'cashier_id' => $cashierId,
                    'cashier_name' => $current['cashier_name'] ?? $comparison['cashier_name'] ?? ('Kasir ID ' . $cashierId),
                    'current_total' => $current['total_sales'] ?? 0,
                    'comparison_total' => $comparison['total_sales'] ?? 0,
                    'delta' => ($current['total_sales'] ?? 0) - ($comparison['total_sales'] ?? 0),
                ];
            })
            ->sortByDesc('delta')
            ->take(5)
            ->values();

        if ($combined->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'perbandingan_kasir',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'cashiers' => [],
                ],
                'message' => 'Belum ada data kasir untuk dibandingkan pada periode tersebut.',
            ];
        }

        $lines = $combined->map(function ($row, $index) {
            $direction = $row['delta'] >= 0 ? 'naik' : 'turun';

            return sprintf(
                '%d. %s - %s %s (%s vs %s)',
                $index + 1,
                $row['cashier_name'],
                $direction,
                $this->formatRupiah(abs($row['delta'])),
                $this->formatRupiah($row['current_total']),
                $this->formatRupiah($row['comparison_total'])
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'perbandingan_kasir',
            'parameters' => $parsed['parameters'],
            'data' => [
                'current_period' => $currentWindow['label'],
                'comparison_period' => $comparisonWindow['label'],
                'cashiers' => $combined->toArray(),
            ],
            'message' => 'Perbandingan omzet kasir ' . $currentWindow['label'] . ' vs ' . $comparisonWindow['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
        ];
    }

    protected function handleProductTrend(array $parsed): array
    {
        [$currentWindow, $comparisonWindow] = $this->resolveComparisonWindows($parsed['parameters'], 'current_month');

        $currentRows = $this->productAggregates($currentWindow)->keyBy('product_id');
        $comparisonRows = $this->productAggregates($comparisonWindow)->keyBy('product_id');

        $combined = collect($currentRows->keys())
            ->merge($comparisonRows->keys())
            ->unique()
            ->map(function ($productId) use ($currentRows, $comparisonRows) {
                $current = $currentRows->get($productId);
                $comparison = $comparisonRows->get($productId);
                $currentQty = $current['quantity'] ?? 0;
                $comparisonQty = $comparison['quantity'] ?? 0;

                return [
                    'product_id' => $productId,
                    'product_name' => $current['product_name'] ?? $comparison['product_name'] ?? $productId,
                    'current_quantity' => $currentQty,
                    'comparison_quantity' => $comparisonQty,
                    'delta_quantity' => $currentQty - $comparisonQty,
                    'current_revenue' => $current['revenue'] ?? 0,
                    'comparison_revenue' => $comparison['revenue'] ?? 0,
                ];
            })
            ->sortBy('delta_quantity')
            ->take(5)
            ->values();

        if ($combined->isEmpty()) {
            return [
                'success' => false,
                'intent' => 'tren_penjualan_produk',
                'parameters' => $parsed['parameters'],
                'data' => [
                    'products' => [],
                ],
                'message' => 'Belum ada data penjualan produk untuk dibandingkan.',
            ];
        }

        $lines = $combined->map(function ($row, $index) {
            $direction = $row['delta_quantity'] >= 0 ? 'naik' : 'turun';

            return sprintf(
                '%d. %s - %s %s item (%s vs %s)',
                $index + 1,
                $row['product_name'],
                $direction,
                $this->formatNumber(abs($row['delta_quantity'])),
                $this->formatNumber($row['current_quantity']),
                $this->formatNumber($row['comparison_quantity'])
            );
        })->implode('; ');

        return [
            'success' => true,
            'intent' => 'tren_penjualan_produk',
            'parameters' => $parsed['parameters'],
            'data' => [
                'current_period' => $currentWindow['label'],
                'comparison_period' => $comparisonWindow['label'],
                'products' => $combined->toArray(),
            ],
            'message' => 'Perbandingan penjualan produk ' . $currentWindow['label'] . ' vs ' . $comparisonWindow['label'] . ': ' . $lines . '.',
            'actions' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
                $this->makeAction('Lihat Produk', 'admin.products.index'),
            ],
        ];
    }

    protected function unknownIntentResponse(array $parsed): array
    {
        return [
            'success' => false,
            'intent' => 'unknown',
            'parameters' => $parsed['parameters'],
            'data' => null,
            'message' => 'Pertanyaan belum dikenali. Coba: "cek stok gula", "produk akan expired 30 hari", "penjualan minggu ini dibanding minggu lalu", "transaksi terakhir kasir vina", atau "bantuan".',
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
                $this->makeAction('Lihat Penjualan', 'sales_data'),
            ],
        ];
    }

    protected function applyContextFallbacks(array $parsed, array $context): array
    {
        $productIntents = ['cek_stok_produk', 'riwayat_stock_movement'];
        $cashierIntents = ['transaksi_terakhir_kasir'];

        if (
            in_array($parsed['intent'], $productIntents, true)
            && empty($parsed['parameters']['product_id'])
            && empty($parsed['parameters']['product_query'])
            && !empty($context['last_product_id'])
        ) {
            $parsed['parameters']['product_id'] = $context['last_product_id'];
            $parsed['parameters']['product_query'] = $context['last_product_name'] ?? null;
        }

        if (
            in_array($parsed['intent'], $cashierIntents, true)
            && empty($parsed['parameters']['cashier_query'])
            && !empty($context['last_cashier_name'])
        ) {
            $parsed['parameters']['cashier_query'] = $context['last_cashier_name'];
        }

        return $parsed;
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

        $fuzzyCandidates = Product::query()
            ->get(['product_id', 'product_name'])
            ->map(function ($product) use ($normalized) {
                $nameScore = $this->similarityScore($normalized, mb_strtolower($product->product_name));
                $idScore = $this->similarityScore($normalized, mb_strtolower($product->product_id));
                $score = max($nameScore, $idScore);

                return [
                    'score' => $score,
                    'product' => $product,
                ];
            })
            ->filter(fn ($row) => $row['score'] >= 55)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('product');

        if ($fuzzyCandidates->count() === 1) {
            return ['status' => 'single', 'product' => $fuzzyCandidates->first(), 'candidates' => []];
        }

        if ($fuzzyCandidates->count() > 1) {
            return ['status' => 'multiple', 'product' => null, 'candidates' => new Collection($fuzzyCandidates->all())];
        }

        return ['status' => 'none', 'product' => null, 'candidates' => []];
    }

    protected function resolveRawMaterialLookup(array $parameters): array
    {
        $materialQuery = trim((string) ($parameters['product_query'] ?? ''));

        if ($materialQuery === '') {
            return ['status' => 'none', 'raw_material' => null, 'candidates' => []];
        }

        $normalized = mb_strtolower($materialQuery);

        $exactCandidates = RawMaterial::query()
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->orderBy('name')
            ->get();

        if ($exactCandidates->count() === 1) {
            return ['status' => 'single', 'raw_material' => $exactCandidates->first(), 'candidates' => []];
        }

        if ($exactCandidates->count() > 1) {
            return ['status' => 'multiple', 'raw_material' => null, 'candidates' => $exactCandidates];
        }

        $partialCandidates = RawMaterial::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $normalized . '%'])
            ->orderByRaw('CASE WHEN LOWER(name) LIKE ? THEN 0 ELSE 1 END', [$normalized . '%'])
            ->orderBy('name')
            ->limit(10)
            ->get();

        if ($partialCandidates->count() === 1) {
            return ['status' => 'single', 'raw_material' => $partialCandidates->first(), 'candidates' => []];
        }

        if ($partialCandidates->count() > 1) {
            return ['status' => 'multiple', 'raw_material' => null, 'candidates' => $partialCandidates];
        }

        $fuzzyCandidates = RawMaterial::query()
            ->get(['id', 'name', 'stock', 'unit', 'minimum_stock'])
            ->map(function ($material) use ($normalized) {
                return [
                    'score' => $this->similarityScore($normalized, mb_strtolower($material->name)),
                    'raw_material' => $material,
                ];
            })
            ->filter(fn ($row) => $row['score'] >= 55)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('raw_material');

        if ($fuzzyCandidates->count() === 1) {
            return ['status' => 'single', 'raw_material' => $fuzzyCandidates->first(), 'candidates' => []];
        }

        if ($fuzzyCandidates->count() > 1) {
            return ['status' => 'multiple', 'raw_material' => null, 'candidates' => new Collection($fuzzyCandidates->all())];
        }

        return ['status' => 'none', 'raw_material' => null, 'candidates' => []];
    }

    protected function resolveCashierLookup(array $parameters): array
    {
        $cashierQuery = trim((string) ($parameters['cashier_query'] ?? ''));

        if ($cashierQuery === '') {
            return ['status' => 'none', 'cashier' => null, 'candidates' => []];
        }

        $normalized = mb_strtolower($cashierQuery);

        $exact = User::query()
            ->role('cashier')
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->orderBy('name')
            ->get();

        if ($exact->count() === 1) {
            return ['status' => 'single', 'cashier' => $exact->first(), 'candidates' => []];
        }

        if ($exact->count() > 1) {
            return ['status' => 'multiple', 'cashier' => null, 'candidates' => $exact];
        }

        $partial = User::query()
            ->role('cashier')
            ->whereRaw('LOWER(name) LIKE ?', ['%' . $normalized . '%'])
            ->orderBy('name')
            ->limit(10)
            ->get();

        if ($partial->count() === 1) {
            return ['status' => 'single', 'cashier' => $partial->first(), 'candidates' => []];
        }

        if ($partial->count() > 1) {
            return ['status' => 'multiple', 'cashier' => null, 'candidates' => $partial];
        }

        $fuzzy = User::query()
            ->role('cashier')
            ->get(['id', 'name'])
            ->map(function ($cashier) use ($normalized) {
                return [
                    'score' => $this->similarityScore($normalized, mb_strtolower($cashier->name)),
                    'cashier' => $cashier,
                ];
            })
            ->filter(fn ($row) => $row['score'] >= 55)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('cashier');

        if ($fuzzy->count() === 1) {
            return ['status' => 'single', 'cashier' => $fuzzy->first(), 'candidates' => []];
        }

        if ($fuzzy->count() > 1) {
            return ['status' => 'multiple', 'cashier' => null, 'candidates' => new Collection($fuzzy->all())];
        }

        return ['status' => 'none', 'cashier' => null, 'candidates' => []];
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
            'actions' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
            ],
        ];
    }

    protected function rawMaterialCandidatesResponse(string $intent, array $parameters, Collection $candidates): array
    {
        $candidateList = $candidates->map(function ($material, $index) {
            return sprintf(
                '%d. %s - stok %s %s',
                $index + 1,
                $material->name,
                $this->formatNumber($material->stock),
                $material->unit
            );
        })->implode('; ');

        return [
            'success' => false,
            'intent' => $intent,
            'parameters' => $parameters,
            'data' => [
                'candidates' => $candidates->map(fn ($material) => [
                    'raw_material_id' => $material->id,
                    'raw_material_name' => $material->name,
                    'stock' => $material->stock,
                    'unit' => $material->unit,
                ])->toArray(),
            ],
            'message' => 'Saya menemukan beberapa bahan yang mirip. Mohon pilih yang lebih spesifik: ' . $candidateList . '.',
            'actions' => [
                $this->makeAction('Restock', 'purchases.create'),
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
            ],
        ];
    }

    protected function cashierCandidatesResponse(array $parameters, Collection $candidates): array
    {
        $candidateList = $candidates->map(function ($cashier, $index) {
            return sprintf(
                '%d. %s',
                $index + 1,
                $cashier->name
            );
        })->implode('; ');

        return [
            'success' => false,
            'intent' => 'transaksi_terakhir_kasir',
            'parameters' => $parameters,
            'data' => [
                'candidates' => $candidates->map(fn ($cashier) => [
                    'cashier_id' => $cashier->id,
                    'cashier_name' => $cashier->name,
                ])->toArray(),
            ],
            'message' => 'Saya menemukan beberapa kasir yang mirip. Mohon pilih yang lebih spesifik: ' . $candidateList . '.',
            'actions' => [
                $this->makeAction('Lihat User', 'user_data'),
            ],
        ];
    }

    protected function buildNextContext(array $context, array $parsed, array $response): array
    {
        $next = $context;
        $next['last_intent'] = $response['intent'] ?? $parsed['intent'] ?? 'unknown';

        if (!empty($response['parameters']['period'])) {
            $next['last_period'] = $response['parameters']['period'];
        }

        if (!empty($response['data']['product_id'])) {
            $next['last_product_id'] = $response['data']['product_id'];
            $next['last_product_name'] = $response['data']['product_name'] ?? null;
        }

        if (!empty($response['data']['cashier_id'])) {
            $next['last_cashier_id'] = $response['data']['cashier_id'];
            $next['last_cashier_name'] = $response['data']['cashier_name'] ?? null;
        }

        return $next;
    }

    protected function resolvePeriodWindow(array $parameters, string $fallback): array
    {
        $period = $parameters['period'] ?? $fallback;
        $now = now();

        return match ($period) {
            'today' => [
                'period' => 'today',
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'hari ini',
            ],
            'yesterday' => [
                'period' => 'yesterday',
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
                'label' => 'kemarin',
            ],
            'current_week' => [
                'period' => 'current_week',
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'label' => 'minggu ini',
            ],
            'previous_week' => [
                'period' => 'previous_week',
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
                'label' => 'minggu lalu',
            ],
            'current_month' => [
                'period' => 'current_month',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'bulan ini',
            ],
            'previous_month' => [
                'period' => 'previous_month',
                'start' => $now->copy()->subMonthNoOverflow()->startOfMonth(),
                'end' => $now->copy()->subMonthNoOverflow()->endOfMonth(),
                'label' => 'bulan lalu',
            ],
            'current_year' => [
                'period' => 'current_year',
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'label' => 'tahun ini',
            ],
            'previous_year' => [
                'period' => 'previous_year',
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
                'label' => 'tahun lalu',
            ],
            'rolling_days' => [
                'period' => 'rolling_days',
                'start' => $now->copy()->subDays(max(1, (int) ($parameters['days'] ?? 7)) - 1)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => max(1, (int) ($parameters['days'] ?? 7)) . ' hari terakhir',
            ],
            'custom_day_range' => $this->resolveCustomDayRange($parameters, $now),
            'all_time' => [
                'period' => 'all_time',
                'start' => null,
                'end' => null,
                'label' => 'sepanjang data yang tersedia',
            ],
            default => $this->resolvePeriodWindow(['period' => $fallback] + $parameters, $fallback),
        };
    }

    protected function resolveCustomDayRange(array $parameters, Carbon $now): array
    {
        $dayFrom = max(1, min(31, (int) ($parameters['day_from'] ?? 1)));
        $dayTo = max(1, min(31, (int) ($parameters['day_to'] ?? $dayFrom)));

        if ($dayFrom > $dayTo) {
            [$dayFrom, $dayTo] = [$dayTo, $dayFrom];
        }

        $start = $now->copy()->startOfMonth()->setDay(min($dayFrom, $now->copy()->endOfMonth()->day))->startOfDay();
        $end = $now->copy()->startOfMonth()->setDay(min($dayTo, $now->copy()->endOfMonth()->day))->endOfDay();

        return [
            'period' => 'custom_day_range',
            'start' => $start,
            'end' => $end,
            'label' => sprintf('tanggal %s-%s %s', $dayFrom, $dayTo, $now->translatedFormat('F Y')),
        ];
    }

    protected function resolveComparisonWindows(array $parameters, string $fallback): array
    {
        $currentWindow = $this->resolvePeriodWindow($parameters, $fallback);
        $comparePeriod = $parameters['compare_period'] ?? 'previous_equivalent';

        if ($comparePeriod !== 'previous_equivalent') {
            return [$currentWindow, $this->resolvePeriodWindow(['period' => $comparePeriod] + $parameters, $fallback)];
        }

        if (!$currentWindow['start'] || !$currentWindow['end']) {
            return [$currentWindow, [
                'period' => 'all_time',
                'start' => null,
                'end' => null,
                'label' => 'periode pembanding tidak tersedia',
            ]];
        }

        $periodDays = $currentWindow['start']->diffInDays($currentWindow['end']) + 1;

        $comparisonWindow = match ($currentWindow['period']) {
            'today' => $this->resolvePeriodWindow(['period' => 'yesterday'], $fallback),
            'current_week' => $this->resolvePeriodWindow(['period' => 'previous_week'], $fallback),
            'current_month' => $this->resolvePeriodWindow(['period' => 'previous_month'], $fallback),
            'current_year' => $this->resolvePeriodWindow(['period' => 'previous_year'], $fallback),
            default => [
                'period' => 'previous_equivalent',
                'start' => $currentWindow['start']->copy()->subDays($periodDays),
                'end' => $currentWindow['start']->copy()->subDay()->endOfDay(),
                'label' => $periodDays . ' hari sebelumnya',
            ],
        };

        return [$currentWindow, $comparisonWindow];
    }

    protected function applySalesWindow($query, string $column, array $window): void
    {
        if (!empty($window['start']) && !empty($window['end'])) {
            $query->whereBetween($column, [$window['start'], $window['end']]);
        }
    }

    protected function salesAggregate(array $window): array
    {
        $salesQuery = Sale::query();
        $detailQuery = DetailSale::query()->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        $this->applySalesWindow($salesQuery, 'created_at', $window);
        $this->applySalesWindow($detailQuery, 'sales.created_at', $window);

        $transactionCount = $salesQuery->count();
        $totalSales = (int) $salesQuery->sum('total');
        $totalItems = (int) $detailQuery->sum('detail_sales.quantity');

        return [
            'period' => $window['period'],
            'label' => $window['label'],
            'transaction_count' => $transactionCount,
            'total_sales' => $totalSales,
            'total_items' => $totalItems,
        ];
    }

    protected function cashierAggregates(array $window): Collection
    {
        $query = Sale::query()
            ->select(
                'sales.cashier_id',
                'users.name as cashier_name',
                DB::raw('SUM(sales.total) as total_sales')
            )
            ->leftJoin('users', 'users.id', '=', 'sales.cashier_id');

        $this->applySalesWindow($query, 'sales.created_at', $window);

        return $query
            ->groupBy('sales.cashier_id', 'users.name')
            ->get()
            ->map(fn ($row) => [
                'cashier_id' => $row->cashier_id,
                'cashier_name' => $row->cashier_name,
                'total_sales' => (int) $row->total_sales,
            ]);
    }

    protected function productAggregates(array $window): Collection
    {
        $query = DetailSale::query()
            ->select(
                'detail_sales.product_id',
                'detail_sales.product_name',
                DB::raw('SUM(detail_sales.quantity) as quantity'),
                DB::raw('SUM(detail_sales.sub_total) as revenue')
            )
            ->join('sales', 'sales.sale_id', '=', 'detail_sales.sale_id');

        $this->applySalesWindow($query, 'sales.created_at', $window);

        return $query
            ->groupBy('detail_sales.product_id', 'detail_sales.product_name')
            ->get()
            ->map(fn ($row) => [
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'quantity' => (int) $row->quantity,
                'revenue' => (int) $row->revenue,
            ]);
    }

    protected function buildActions(string $intent): array
    {
        return match ($intent) {
            'cek_stok_produk', 'produk_low_stock', 'produk_akan_expired', 'stok_mati', 'produk_paling_jarang_laku' => [
                $this->makeAction('Lihat Produk', 'admin.products.index'),
            ],
            'riwayat_stock_movement', 'stok_masuk_keluar_periode' => [
                $this->makeAction('Lihat Stock Movement', 'stock_movement'),
            ],
            'ringkasan_penjualan', 'produk_terlaris', 'sales_per_cashier', 'penjualan_per_metode_pembayaran', 'transaksi_terakhir_kasir', 'perbandingan_penjualan', 'perbandingan_kasir', 'tren_penjualan_produk' => [
                $this->makeAction('Lihat Penjualan', 'sales_data'),
            ],
            'profit_per_produk', 'top_kategori' => [
                $this->makeAction('Laporan Profit', 'reports.profit'),
            ],
            'selisih_shift_kasir' => [
                $this->makeAction('Lihat Shift', 'admin.shifts.index'),
            ],
            default => [],
        };
    }

    protected function buildResponseMeta(string $intent): array
    {
        $primaryIntents = [
            'ringkasan_penjualan',
            'produk_terlaris',
            'penjualan_per_metode_pembayaran',
            'sales_per_cashier',
            'selisih_shift_kasir',
            'riwayat_stock_movement',
        ];

        if (in_array($intent, $primaryIntents, true)) {
            return [
                'insight_label' => 'Insight Utama',
                'insight_tier' => 'primary',
            ];
        }

        return [];
    }

    protected function makeAction(string $label, string $routeName): array
    {
        return [
            'label' => $label,
            'url' => Route::has($routeName) ? route($routeName) : '#',
        ];
    }

    protected function logInteraction(array $parsed, array $response, ?int $userId, ?string $sessionId, int $latencyMs): ?AdminChatbotLog
    {
        try {
            if (!Schema::hasTable('admin_chatbot_logs')) {
                Log::info('admin_chatbot_query', [
                    'user_id' => $userId,
                    'question' => $parsed['original_message'] ?? null,
                    'intent' => $response['intent'] ?? $parsed['intent'] ?? 'unknown',
                    'parameters' => $response['parameters'] ?? $parsed['parameters'] ?? [],
                    'success' => $response['success'] ?? false,
                    'latency_ms' => $latencyMs,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                return null;
            }

            return AdminChatbotLog::query()->create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'question' => Str::limit((string) ($parsed['original_message'] ?? ''), 500, ''),
                'normalized_question' => Str::limit((string) ($parsed['normalized_message'] ?? ''), 500, ''),
                'intent' => $response['intent'] ?? $parsed['intent'] ?? 'unknown',
                'parameters' => $response['parameters'] ?? $parsed['parameters'] ?? [],
                'success' => (bool) ($response['success'] ?? false),
                'response_summary' => Str::limit((string) ($response['message'] ?? ''), 1000),
                'response_meta' => [
                    'actions' => $response['actions'] ?? [],
                ],
                'context_snapshot' => $response['context'] ?? [],
                'latency_ms' => $latencyMs,
            ]);
        } catch (Throwable $exception) {
            Log::warning('admin_chatbot_log_failed', [
                'question' => $parsed['original_message'] ?? null,
                'intent' => $response['intent'] ?? $parsed['intent'] ?? 'unknown',
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    protected function similarityScore(string $left, string $right): int
    {
        similar_text($left, $right, $score);

        return (int) round($score);
    }

    protected function paymentMethodLabel(string|int|null $paymentMethod): string
    {
        return match ((string) $paymentMethod) {
            '1' => 'Cash',
            '2' => 'Transfer',
            '3' => 'QRIS',
            default => 'Lainnya',
        };
    }

    protected function averagePerTransaction(int|float $totalSales, int $transactionCount): int
    {
        if ($transactionCount <= 0) {
            return 0;
        }

        return (int) round($totalSales / $transactionCount);
    }

    protected function percentageChange(int|float $current, int|float $comparison): ?float
    {
        if ((float) $comparison === 0.0) {
            return (float) $current === 0.0 ? 0.0 : null;
        }

        return (($current - $comparison) / abs($comparison)) * 100;
    }

    protected function comparisonDirection(int|float $diff): string
    {
        if ($diff > 0) {
            return 'naik';
        }

        if ($diff < 0) {
            return 'turun';
        }

        return 'tetap';
    }

    protected function salesComparisonInsight(array $current, array $comparison, int|float $salesDiff, int $transactionDiff, int|float $aovDiff): string
    {
        if ($current['transaction_count'] === 0 && $comparison['transaction_count'] > 0) {
            return 'Belum ada transaksi tercatat pada periode ini, sehingga omzet turun penuh dibanding periode pembanding.';
        }

        if ($current['transaction_count'] === 0 && $comparison['transaction_count'] === 0) {
            return 'Kedua periode belum memiliki transaksi, jadi belum ada performa penjualan yang bisa dibandingkan.';
        }

        if ($salesDiff > 0 && $transactionDiff > 0) {
            return 'Kenaikan omzet terutama didorong oleh bertambahnya jumlah transaksi.';
        }

        if ($salesDiff > 0 && $transactionDiff <= 0 && $aovDiff > 0) {
            return 'Omzet naik walaupun transaksi tidak bertambah, artinya nilai rata-rata belanja per transaksi meningkat.';
        }

        if ($salesDiff < 0 && $transactionDiff < 0) {
            return 'Penurunan omzet terutama dipengaruhi oleh jumlah transaksi yang lebih sedikit.';
        }

        if ($salesDiff < 0 && $transactionDiff >= 0 && $aovDiff < 0) {
            return 'Transaksi tidak turun, tetapi rata-rata belanja per transaksi menurun sehingga omzet ikut turun.';
        }

        if ($salesDiff === 0 && $transactionDiff === 0) {
            return 'Omzet dan jumlah transaksi relatif stabil dibanding periode pembanding.';
        }

        return 'Perubahan omzet dipengaruhi kombinasi jumlah transaksi dan nilai rata-rata belanja per transaksi.';
    }

    protected function salesComparisonRecommendation(array $current, array $comparison, int|float $salesDiff, int $transactionDiff, int|float $aovDiff): string
    {
        if ($current['transaction_count'] === 0) {
            return 'Pastikan transaksi periode ini sudah masuk, cek shift kasir aktif, lalu lihat produk terlaris periode pembanding untuk bahan promo.';
        }

        if ($salesDiff < 0 && $transactionDiff < 0) {
            return 'Cek jam ramai dan produk terlaris periode pembanding, lalu dorong promo atau bundling untuk menaikkan jumlah transaksi.';
        }

        if ($salesDiff < 0 && $aovDiff < 0) {
            return 'Evaluasi menu dengan nilai transaksi rendah dan coba upsell add-on atau paket agar rata-rata belanja naik.';
        }

        if ($salesDiff > 0) {
            return 'Pertahankan pola penjualan yang berhasil dan pastikan stok bahan untuk produk terlaris tetap aman.';
        }

        return 'Pantau produk terlaris dan shift kasir agar peluang peningkatan omzet periode berikutnya lebih jelas.';
    }

    protected function formatWindowRange(array $window): string
    {
        if (empty($window['start']) || empty($window['end'])) {
            return '-';
        }

        return Carbon::parse($window['start'])->locale('id')->translatedFormat('d M Y')
            . ' - '
            . Carbon::parse($window['end'])->locale('id')->translatedFormat('d M Y');
    }

    protected function formatSignedRupiah(int|float $amount): string
    {
        if ($amount === 0 || $amount === 0.0) {
            return 'Rp0';
        }

        return ($amount > 0 ? '+' : '-') . $this->formatRupiah(abs($amount));
    }

    protected function formatSignedNumber(int|float $value): string
    {
        if ($value === 0 || $value === 0.0) {
            return '0';
        }

        return ($value > 0 ? '+' : '-') . $this->formatNumber(abs($value));
    }

    protected function formatPercent(int|float $value): string
    {
        return number_format((float) $value, 1, ',', '.') . '%';
    }

    protected function formatRupiah(int|float|string|null $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    protected function formatNumber(int|float|string|null $value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }

    protected function formatDate(Carbon|string|null $date): string
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->translatedFormat('d M Y');
    }

    protected function formatDateTime(Carbon|string|null $date): string
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->locale('id')->translatedFormat('d M Y H:i');
    }
}
