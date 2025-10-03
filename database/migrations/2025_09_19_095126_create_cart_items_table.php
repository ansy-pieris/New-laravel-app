<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('cart_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_image')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('added_at')->useCurrent();
            $table->decimal('total_price', 12, 2)->default(0);

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('carts');
    }
};
