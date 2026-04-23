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
        Schema::create('detail_sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_id', 30);
            $table->string('cashier_id', 20);
            $table->string('product_id', 5);
            $table->string('product_name', 50);
            $table->integer('product_profit');
            $table->integer('product_price');
            $table->integer('quantity')->default(1);
            $table->integer('sub_total');
            $table->timestamps();

            $table->foreign('sale_id')->references('sale_id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_sales');
    }
};
