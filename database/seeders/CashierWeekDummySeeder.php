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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $cashiers = $this->ensureCashiers();
            $products = $this->ensureDemoProducts();

            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->subDay()->endOfDay();

            $this->cleanupExistingDummyWeek($cashiers->pluck('id')->all(), $startDate, $endDate);

            $dailySequences = [];

            for ($dayOffset = 7; $dayOffset >= 1; $dayOffset--) {
                $shiftDate = now()->subDays($dayOffset)->startOfDay();
                $dateKey = $shiftDate->toDateString();
                $dailySequences[$dateKey] = Sale::query()
                    ->whereDate('created_at', $dateKey)
                    ->count();

                foreach ($cashiers as $cashierIndex => $cashier) {
                    $shiftStart = $shiftDate->copy()->setTime($cashierIndex === 0 ? 8 : 12, 0);
                    $shiftEnd = $shiftStart->copy()->addHours(8);
                    $openingCash = 150000 + ($cashierIndex * 25000) + ((7 - $dayOffset) * 5000);

                    $shiftId = DB::table('cashier_shifts')->insertGetId([
                        'cashier_id' => $cashier->id,
                        'shift_start' => $shiftStart,
                        'shift_end' => null,
                        'opening_cash' => $openingCash,
                        'closing_cash' => null,
                        'notes' => 'Dummy shift data 1 minggu untuk simulasi operasional kasir.',
                        'status' => 'open',
                        'created_at' => $shiftStart,
                        'updated_at' => $shiftStart,
                    ]);

                    $cashSalesTotal = 0;
                    $salesCount = 4 + (($cashierIndex + $dayOffset) % 2);

                    for ($saleIndex = 0; $saleIndex < $salesCount; $saleIndex++) {
                        $saleAt = $shiftStart->copy()->addMinutes(60 + ($saleIndex * 85));
                        $dateString = $saleAt->format('Ymd');
                        $dailySequences[$dateKey]++;
                        $saleId = 'INV-' . $dateString . '-' . str_pad((string) $dailySequences[$dateKey], 4, '0', STR_PAD_LEFT);

                        $itemCount = 2 + (($saleIndex + $cashierIndex) % 2);
                        $pickedProducts = [];
                        $total = 0;
                        $detailRows = [];

                        for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++) {
                            $product = $products[($dayOffset + $saleIndex + $itemIndex + $cashierIndex) % count($products)];

                            if (in_array($product->product_id, $pickedProducts, true)) {
                                continue;
                            }

                            $pickedProducts[] = $product->product_id;

                            $quantity = (($dayOffset + $saleIndex + $itemIndex) % 3) + 1;
                            $sellPrice = (float) $product->product_price;
                            $buyPrice = (float) $product->buy_price;
                            $subTotal = $sellPrice * $quantity;
                            $profit = ($sellPrice - $buyPrice) * $quantity;

                            $quantityBefore = (int) $product->product_quantity;
                            $quantityAfter = $quantityBefore - $quantity;

                            $product->product_quantity = $quantityAfter;
                            $product->save();

                            $detailRows[] = [
                                'sale_id' => $saleId,
                                'cashier_id' => (string) $cashier->id,
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
                                'action_by' => $cashier->name,
                                'created_at' => $saleAt,
                                'updated_at' => $saleAt,
                            ]);

                            $total += $subTotal;
                        }

                        $paymentMethod = (string) (((($saleIndex + $cashierIndex + $dayOffset) % 3) + 1));
                        $pay = $paymentMethod === '1' ? $total + (5000 * ((($saleIndex + $dayOffset) % 3) + 1)) : $total;
                        $change = $paymentMethod === '1' ? $pay - $total : 0;

                        if ($paymentMethod === '1') {
                            $cashSalesTotal += $total;
                        }

                        DB::table('sales')->insert([
                            'sale_id' => $saleId,
                            'shift_id' => $shiftId,
                            'cashier_id' => (string) $cashier->id,
                            'total' => $total,
                            'payment_method' => $paymentMethod,
                            'pay' => $pay,
                            'change' => $change,
                            'created_at' => $saleAt,
                            'updated_at' => $saleAt,
                        ]);

                        DB::table('detail_sales')->insert($detailRows);
                    }

                    $differencePattern = [-2500, 0, 1500, -1000, 2000, -500, 1000];
                    $difference = $differencePattern[(($dayOffset + $cashierIndex) - 1) % count($differencePattern)];
                    $closingCash = $openingCash + $cashSalesTotal + $difference;

                    DB::table('cashier_shifts')
                        ->where('id', $shiftId)
                        ->update([
                            'shift_end' => $shiftEnd,
                            'closing_cash' => $closingCash,
                            'notes' => 'Dummy shift data 1 minggu. Selisih kas simulasi: Rp' . number_format($difference, 2, ',', '.'),
                            'status' => 'closed',
                            'updated_at' => $shiftEnd,
                        ]);
                }
            }
        });
    }

    protected function ensureCashiers()
    {
        Role::findOrCreate('cashier');

        $cashiers = collect([
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

    protected function ensureDemoProducts(): array
    {
        $categories = [
            ['category_id' => 'DM001', 'category_name' => 'Minuman Demo'],
            ['category_id' => 'DM002', 'category_name' => 'Snack Demo'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['category_id' => $category['category_id']],
                [
                    'category_name' => $category['category_name'],
                    'added_by' => 'system',
                ]
            );
        }

        $products = [
            ['product_id' => 'D0001', 'product_name' => 'Matcha Latte', 'product_category' => 'DM001', 'buy_price' => 12000, 'product_price' => 22000, 'product_quantity' => 280],
            ['product_id' => 'D0002', 'product_name' => 'Americano', 'product_category' => 'DM001', 'buy_price' => 8000, 'product_price' => 18000, 'product_quantity' => 280],
            ['product_id' => 'D0003', 'product_name' => 'Cappuccino', 'product_category' => 'DM001', 'buy_price' => 10000, 'product_price' => 23000, 'product_quantity' => 280],
            ['product_id' => 'D0004', 'product_name' => 'Croissant Butter', 'product_category' => 'DM002', 'buy_price' => 9000, 'product_price' => 17000, 'product_quantity' => 240],
            ['product_id' => 'D0005', 'product_name' => 'Cheese Cake Slice', 'product_category' => 'DM002', 'buy_price' => 15000, 'product_price' => 28000, 'product_quantity' => 220],
            ['product_id' => 'D0006', 'product_name' => 'Banana Bread', 'product_category' => 'DM002', 'buy_price' => 11000, 'product_price' => 21000, 'product_quantity' => 230],
        ];

        return collect($products)->map(function (array $product) {
            $buyPrice = (float) $product['buy_price'];
            $sellPrice = (float) $product['product_price'];

            return Product::updateOrCreate(
                ['product_id' => $product['product_id']],
                [
                    'product_name' => $product['product_name'],
                    'product_category' => $product['product_category'],
                    'product_image' => 'demo-product.jpg',
                    'product_price' => $sellPrice,
                    'buy_price' => $buyPrice,
                    'product_profit' => $sellPrice - $buyPrice,
                    'product_quantity' => $product['product_quantity'],
                    'product_expired' => now()->addYear()->toDateString(),
                ]
            );
        })->all();
    }

    protected function cleanupExistingDummyWeek(array $cashierIds, Carbon $startDate, Carbon $endDate): void
    {
        $sales = Sale::query()
            ->whereIn('cashier_id', array_map('strval', $cashierIds))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['sale_id']);

        if ($sales->isNotEmpty()) {
            $saleIds = $sales->pluck('sale_id')->all();

            $restoreStocks = DetailSale::query()
                ->whereIn('sale_id', $saleIds)
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->get();

            foreach ($restoreStocks as $restoreStock) {
                DB::table('products')
                    ->where('product_id', $restoreStock->product_id)
                    ->increment('product_quantity', (int) $restoreStock->total_quantity);
            }

            StockMovement::query()
                ->where('source', 'sale')
                ->whereIn('transaction_id', $saleIds)
                ->delete();

            DetailSale::query()
                ->whereIn('sale_id', $saleIds)
                ->delete();

            Sale::query()
                ->whereIn('sale_id', $saleIds)
                ->delete();
        }

        CashierShift::query()
            ->whereIn('cashier_id', $cashierIds)
            ->whereBetween('shift_start', [$startDate, $endDate])
            ->delete();
    }
}
