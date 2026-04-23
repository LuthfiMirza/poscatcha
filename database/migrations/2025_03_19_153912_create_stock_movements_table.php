<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 5)->nullable();
            $table->string('transaction_id', 35)->nullable();
            $table->string('product_name', 50);
            $table->integer('status');
            $table->string('reason', 20);
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('action_by', 40);
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
