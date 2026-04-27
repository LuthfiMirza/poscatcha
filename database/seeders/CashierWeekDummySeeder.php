<?php

namespace Database\Seeders;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\DetailSale;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CashierWeekDummySeeder extends Seeder
{
    protected array $productBlueprints = [
        [
            'product_id' => 'B0001',
            'product_name' => 'Bubuk matcha',
            'product_category' => 'BBK001',
            'buy_price' => 78500,
            'product_price' => 92500,
            'final_stock' => 8,
            'opening_stock' => 22,
            'product_expired' => '2027-12-31',
        ],
        [
            'product_id' => 'B0002',
            'product_name' => 'Bubuk thaitea',
            'product_category' => 'BBK001',
            'buy_price' => 52000,
            'product_price' => 61000,
            'final_stock' => 10,
            'opening_stock' => 22,
            'product_expired' => '2027-12-31',
        ],
        [
            'product_id' => 'B0004',
            'product_name' => 'Fresh milk',
            'product_category' => 'BBK001',
            'buy_price' => 21000,
            'product_price' => 25000,
            'final_stock' => 10,
            'opening_stock' => 24,
            'product_expired' => '2027-12-31',
        ],
        [
            'product_id' => 'B0003',
            'product_name' => 'Gula',
            'product_category' => 'BBK001',
            'buy_price' => 16000,
            'product_price' => 18000,
            'final_stock' => 25,
            'opening_stock' => 42,
            'product_expired' => '2027-12-31',
        ],
        [
            'product_id' => 'B0005',
            'product_name' => 'Susu evaporasi',
            'product_category' => 'BBK001',
            'buy_price' => 14500,
            'product_price' => 17500,
            'final_stock' => 12,
            'opening_stock' => 22,
            'product_expired' => '2027-12-31',
        ],
    ];

    protected array $weeklySalesPlan = [
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1]],
            [['product_id' => 'B0005', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
        [
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1]],
            [['product_id' => 'B0004', 'quantity' => 2]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 2]],
        ],
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0002', 'quantity' => 1]],
            [['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1]],
            [['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1]],
            [['product_id' => 'B0005', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0002', 'quantity' => 1]],
            [['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0002', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
        [
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0002', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
            [['product_id' => 'B0005', 'quantity' => 1], ['product_id' => 'B0002', 'quantity' => 1]],
            [['product_id' => 'B0001', 'quantity' => 1], ['product_id' => 'B0004', 'quantity' => 1], ['product_id' => 'B0005', 'quantity' => 1], ['product_id' => 'B0003', 'quantity' => 1]],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $cashiers = $this->ensureCashiers();
            $startDate = now()->subDays(7)->startOfDay();

            $this->cleanupOperationalWeek();
            $products = $this->syncMasterProducts();
            $this->seedOpeningBalances($products, $startDate->copy()->subMinutes(30));
            $this->seedWeeklyOperations($cashiers->values(), $products, $startDate);
        });
    }

    protected function ensureCashiers()
    {
        Role::findOrCreate('cashier');

        $cashiers = collect([
            ['name' => 'cashier', 'email' => 'cashier@gmail.com'],
            ['name' => 'ariz', 'email' => 'ariz@gmail.com'],
            ['name' => 'koko', 'email' => 'koko@gmail.com'],
        ])->map(function (array $cashier) {
            $user = User::updateOrCreate(
                ['email' => $cashier['email']],
                [
                    'name' => $cashier['name'],
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles(['cashier']);

            return $user;
        })->values();

        return $cashiers;
    }

    protected function syncMasterProducts()
    {
        Category::updateOrCreate(
            ['category_id' => 'BBK001'],
            [
                'category_name' => 'Bahan Baku',
                'added_by' => 'system',
            ]
        );

        $products = collect($this->productBlueprints)->mapWithKeys(function (array $productData) {
            $existingProduct = Product::query()
                ->where('product_id', $productData['product_id'])
                ->first();

            $product = Product::updateOrCreate(
                ['product_id' => $productData['product_id']],
                [
                    'product_name' => $productData['product_name'],
                    'product_category' => $productData['product_category'],
                    'product_image' => $existingProduct?->product_image ?: 'demo-product.jpg',
                    'product_price' => $productData['product_price'],
                    'buy_price' => $productData['buy_price'],
                    'product_profit' => $productData['product_price'] - $productData['buy_price'],
                    'product_quantity' => $productData['opening_stock'],
                    'product_expired' => $productData['product_expired'],
                ]
            );

            return [$product->product_id => $product];
        });

        Product::query()
            ->whereNotIn('product_id', array_keys($this->productBlueprintsById()))
            ->delete();

        Category::query()
            ->where('category_id', '!=', 'BBK001')
            ->delete();

        return $products;
    }

    protected function cleanupOperationalWeek(): void
    {
        DetailSale::query()->delete();
        Sale::query()->delete();
        CashierShift::query()->delete();

        StockMovement::query()
            ->where('source', 'sale')
            ->delete();

        StockMovement::query()
            ->where('reason', 'Opening Balance')
            ->whereIn('product_id', array_keys($this->productBlueprintsById()))
            ->delete();
    }

    protected function seedOpeningBalances($products, Carbon $movementAt): void
    {
        foreach ($this->productBlueprints as $productData) {
            $product = $products[$productData['product_id']];

            DB::table('stock_movements')->insert([
                'product_id' => $product->product_id,
                'transaction_id' => 'OPEN-' . $movementAt->format('Ymd'),
                'product_name' => $product->product_name,
                'status' => 1,
                'source' => 'product',
                'reason' => 'Opening Balance',
                'quantity_before' => 0,
                'quantity_after' => $productData['opening_stock'],
                'action_by' => 'admin',
                'created_at' => $movementAt,
                'updated_at' => $movementAt,
            ]);
        }
    }

    protected function seedWeeklyOperations($cashiers, $products, Carbon $startDate): void
    {
        $differencePattern = [-1500, 0, 1000, -500, 1500, -1000, 500];
        $saleTimes = ['09:10', '11:35', '15:20', '18:05'];
        $paymentMethods = ['1', '2', '1', '3'];

        foreach ($this->weeklySalesPlan as $dayIndex => $dailySales) {
            $shiftDate = $startDate->copy()->addDays($dayIndex);
            $dateKey = $shiftDate->toDateString();
            $dailySequence = 0;

            $morningCashier = $cashiers[$dayIndex % $cashiers->count()];
            $eveningCashier = $cashiers[($dayIndex + 1) % $cashiers->count()];

            $shiftDefinitions = [
                [
                    'cashier' => $morningCashier,
                    'start' => $shiftDate->copy()->setTime(8, 0),
                    'end' => $shiftDate->copy()->setTime(14, 0),
                    'opening_cash' => 175000 + ($dayIndex * 5000),
                    'sales' => array_slice($dailySales, 0, 2),
                    'times' => array_slice($saleTimes, 0, 2),
                    'methods' => array_slice($paymentMethods, 0, 2),
                    'difference' => $differencePattern[$dayIndex % count($differencePattern)],
                ],
                [
                    'cashier' => $eveningCashier,
                    'start' => $shiftDate->copy()->setTime(14, 0),
                    'end' => $shiftDate->copy()->setTime(21, 0),
                    'opening_cash' => 225000 + ($dayIndex * 5000),
                    'sales' => array_slice($dailySales, 2, 2),
                    'times' => array_slice($saleTimes, 2, 2),
                    'methods' => array_slice($paymentMethods, 2, 2),
                    'difference' => $differencePattern[($dayIndex + 2) % count($differencePattern)],
                ],
            ];

            foreach ($shiftDefinitions as $shiftIndex => $shiftDefinition) {
                $shiftId = DB::table('cashier_shifts')->insertGetId([
                    'cashier_id' => $shiftDefinition['cashier']->id,
                    'shift_start' => $shiftDefinition['start'],
                    'shift_end' => null,
                    'opening_cash' => $shiftDefinition['opening_cash'],
                    'closing_cash' => null,
                    'notes' => 'Simulasi operasional POS 1 minggu untuk master produk bahan baku.',
                    'status' => 'open',
                    'created_at' => $shiftDefinition['start'],
                    'updated_at' => $shiftDefinition['start'],
                ]);

                $cashSalesTotal = 0;

                foreach ($shiftDefinition['sales'] as $saleIndex => $saleItems) {
                    $saleAt = Carbon::parse($dateKey . ' ' . $shiftDefinition['times'][$saleIndex]);
                    $dailySequence++;
                    $saleId = 'INV-' . $saleAt->format('Ymd') . '-' . str_pad((string) $dailySequence, 4, '0', STR_PAD_LEFT);
                    $total = 0;
                    $detailRows = [];

                    foreach ($saleItems as $saleItem) {
                        $product = $products[$saleItem['product_id']];
                        $quantity = $saleItem['quantity'];
                        $sellPrice = (float) $product->product_price;
                        $buyPrice = (float) $product->buy_price;
                        $quantityBefore = (int) $product->product_quantity;
                        $quantityAfter = $quantityBefore - $quantity;

                        if ($quantityAfter < 0) {
                            throw new \RuntimeException('Simulasi stok menghasilkan nilai negatif untuk produk ' . $product->product_id);
                        }

                        $product->product_quantity = $quantityAfter;
                        $product->save();

                        $subTotal = $sellPrice * $quantity;
                        $profit = ($sellPrice - $buyPrice) * $quantity;

                        $detailRows[] = [
                            'sale_id' => $saleId,
                            'cashier_id' => (string) $shiftDefinition['cashier']->id,
                            'product_id' => $product->product_id,
                            'product_name' => $product->product_name,
                            'product_price' => $sellPrice,
                            'buy_price' => $buyPrice,
                            'product_profit' => $profit,
                            'quantity' => $quantity,
                            'sub_total' => $subTotal,
                            'created_at' => $saleAt,
                            'updated_at' => $saleAt,
                        ];

                        DB::table('stock_movements')->insert([
                            'product_id' => $product->product_id,
                            'transaction_id' => $saleId,
                            'product_name' => $product->product_name,
                            'status' => 4,
                            'source' => 'sale',
                            'reason' => 'Product Sales',
                            'quantity_before' => $quantityBefore,
                            'quantity_after' => $quantityAfter,
                            'action_by' => $shiftDefinition['cashier']->name,
                            'created_at' => $saleAt,
                            'updated_at' => $saleAt,
                        ]);

                        $total += $subTotal;
                    }

                    $paymentMethod = $shiftDefinition['methods'][$saleIndex];
                    $pay = $paymentMethod === '1' ? $total + (5000 * (($saleIndex % 2) + 1)) : $total;
                    $change = $paymentMethod === '1' ? $pay - $total : 0;

                    if ($paymentMethod === '1') {
                        $cashSalesTotal += $total;
                    }

                    DB::table('sales')->insert([
                        'sale_id' => $saleId,
                        'shift_id' => $shiftId,
                        'cashier_id' => (string) $shiftDefinition['cashier']->id,
                        'total' => $total,
                        'payment_method' => $paymentMethod,
                        'pay' => $pay,
                        'change' => $change,
                        'created_at' => $saleAt,
                        'updated_at' => $saleAt,
                    ]);

                    DB::table('detail_sales')->insert($detailRows);
                }

                DB::table('cashier_shifts')
                    ->where('id', $shiftId)
                    ->update([
                        'shift_end' => $shiftDefinition['end'],
                        'closing_cash' => $shiftDefinition['opening_cash'] + $cashSalesTotal + $shiftDefinition['difference'],
                        'notes' => 'Simulasi minggu berjalan. Selisih kas: Rp' . number_format($shiftDefinition['difference'], 2, ',', '.'),
                        'status' => 'closed',
                        'updated_at' => $shiftDefinition['end'],
                    ]);
            }
        }

        foreach ($this->productBlueprints as $productData) {
            Product::query()
                ->where('product_id', $productData['product_id'])
                ->update(['product_quantity' => $productData['final_stock']]);
        }
    }

    protected function productBlueprintsById(): array
    {
        return collect($this->productBlueprints)
            ->keyBy('product_id')
            ->all();
    }
}
