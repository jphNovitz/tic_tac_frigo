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
        Schema::create('fridge_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->date('expires_at');
            $table->string('quantity')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('alerted_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['user_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fridge_items');
    }
};
