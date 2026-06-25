<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('buyer_cart_items') && ! Schema::hasColumn('buyer_cart_items', 'customization')) {
            Schema::table('buyer_cart_items', function (Blueprint $table) {
                $table->json('customization')->nullable()->after('quantity');
            });
        }

        if (Schema::hasTable('order_items') && ! Schema::hasColumn('order_items', 'customization')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->json('customization')->nullable()->after('subtotal');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('buyer_cart_items') && Schema::hasColumn('buyer_cart_items', 'customization')) {
            Schema::table('buyer_cart_items', function (Blueprint $table) {
                $table->dropColumn('customization');
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'customization')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('customization');
            });
        }
    }
};
