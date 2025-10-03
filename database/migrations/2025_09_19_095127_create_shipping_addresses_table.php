<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->bigIncrements('address_id');     // corrected spelling
            $table->unsignedBigInteger('user_id');
            $table->string('recipient_name');        // corrected spelling
            $table->string('phone', 32);
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('postal_code', 32);
            $table->boolean('is_default')->default(false); // corrected spelling
            $table->timestamp('created_at')->useCurrent();  // only created_at (no updated_at)

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('shipping_addresses');
    }
};
