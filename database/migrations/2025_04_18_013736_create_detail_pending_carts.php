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
        Schema::create('detail_pending_carts', function (Blueprint $table) {
            $table->id();
            $table->string('cart_id', 35);
            $table->string('cashier_id', 20);
            $table->string('product_id', 5);
            $table->string('product_name', 50);
            $table->integer('product_profit');
            $table->integer('product_price');
            $table->integer('quantity')->default(1);
            $table->integer('sub_total');
            $table->timestamps();

            $table->foreign('cart_id')->references('cart_id')->on('pending_carts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pending_carts');
    }
};
