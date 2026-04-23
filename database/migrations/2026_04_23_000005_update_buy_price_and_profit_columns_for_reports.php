<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'buy_price')) {
            DB::table('products')
                ->whereNull('buy_price')
                ->update(['buy_price' => 0]);

            DB::statement('ALTER TABLE products MODIFY buy_price DECIMAL(10,2) NOT NULL DEFAULT 0');
        } else {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('buy_price', 10, 2)->default(0)->after('product_price');
            });
        }

        Schema::table('detail_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_sales', 'buy_price')) {
                $table->decimal('buy_price', 10, 2)->default(0)->after('product_price');
            }
        });

        if (Schema::hasColumn('detail_sales', 'product_profit')) {
            DB::table('detail_sales')
                ->whereNull('product_profit')
                ->update(['product_profit' => 0]);

            DB::statement('ALTER TABLE detail_sales MODIFY product_profit DECIMAL(12,2) NOT NULL DEFAULT 0');
        }

        if (Schema::hasColumn('detail_sales', 'buy_price')) {
            DB::table('detail_sales')
                ->whereNull('buy_price')
                ->update(['buy_price' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('detail_sales', 'buy_price')) {
            Schema::table('detail_sales', function (Blueprint $table) {
                $table->dropColumn('buy_price');
            });
        }

        if (Schema::hasColumn('detail_sales', 'product_profit')) {
            DB::statement('ALTER TABLE detail_sales MODIFY product_profit INT NOT NULL DEFAULT 0');
        }

        if (Schema::hasColumn('products', 'buy_price')) {
            DB::statement('ALTER TABLE products MODIFY buy_price INT NULL');
        }
    }
};
