<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreignId('raw_material_id')->nullable()->after('purchase_id')->constrained('raw_materials')->nullOnDelete();
            $table->string('product_id', 5)->nullable()->change();
            $table->decimal('quantity', 12, 2)->change();
            $table->decimal('buy_price', 12, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('raw_material_id');
            $table->string('product_id', 5)->nullable(false)->change();
            $table->integer('quantity')->change();
            $table->integer('buy_price')->change();
        });
    }
};
