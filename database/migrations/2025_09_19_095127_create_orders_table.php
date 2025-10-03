<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending|paid|shipped|delivered|cancelled
            $table->string('product_image')->nullable(); // kept for parity with old schema
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('orders');
    }
};
