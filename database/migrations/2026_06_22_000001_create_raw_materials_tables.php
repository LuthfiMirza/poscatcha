<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('unit', 20);
            $table->decimal('stock', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('quantity_required', 12, 2);
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete();
            $table->unique(['product_id', 'raw_material_id']);
        });

        Schema::create('raw_material_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->string('transaction_id', 35)->nullable();
            $table->string('type', 20);
            $table->string('reason', 80);
            $table->decimal('quantity', 12, 2);
            $table->decimal('quantity_before', 12, 2);
            $table->decimal('quantity_after', 12, 2);
            $table->string('action_by', 40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_stock_movements');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
    }
};
