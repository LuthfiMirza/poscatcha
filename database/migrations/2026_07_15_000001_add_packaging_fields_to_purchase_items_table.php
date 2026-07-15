<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('package_quantity', 12, 2)->default(1)->after('product_id');
            $table->decimal('package_size', 12, 2)->default(1)->after('package_quantity');
            $table->string('package_label', 50)->nullable()->after('package_size');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['package_quantity', 'package_size', 'package_label']);
        });
    }
};
