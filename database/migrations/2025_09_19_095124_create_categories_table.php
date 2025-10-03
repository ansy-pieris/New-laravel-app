<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('category_id');
            $table->string('name')->unique();
            $table->string('slug')->unique(); // NEW
            $table->timestamps(); // created_at, updated_at
        });
    }
    public function down(): void {
        Schema::dropIfExists('categories');
    }
};
