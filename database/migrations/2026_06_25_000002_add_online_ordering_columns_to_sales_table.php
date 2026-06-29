<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'source')) {
                $table->string('source', 20)->default('offline')->after('sale_id')->index();
            }

            if (! Schema::hasColumn('sales', 'order_id')) {
                $table->foreignId('order_id')->nullable()->unique()->after('source')->constrained('orders')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }

            if (Schema::hasColumn('sales', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
