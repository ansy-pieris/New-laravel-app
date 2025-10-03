<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_image')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 10, 2); // unit price at purchase time

            $table->foreign('order_id')
                  ->references('order_id')->on('orders')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('order_items');
    }
};
