<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('slug')->unique(); // NEW
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('image')->nullable();
            $table->unsignedBigInteger('added_by')->nullable(); // users.id
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->foreign('category_id')
                  ->references('category_id')->on('categories')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('added_by')
                  ->references('id')->on('users')
                  ->nullOnDelete()->cascadeOnUpdate();
        });
    }
    public function down(): void {
        Schema::dropIfExists('products');
    }
};
