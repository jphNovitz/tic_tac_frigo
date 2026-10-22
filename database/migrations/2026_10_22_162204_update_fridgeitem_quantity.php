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
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->renameColumn('quantity', 'unit_count');
        });

        Schema::table('fridge_items', function (Blueprint $table) {
            $table->unsignedInteger('unit_count')->default(1)->change();
            $table->string('unit_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->dropColumn('unit_label');
        });

        Schema::table('fridge_items', function (Blueprint $table) {
            $table->renameColumn('unit_count', 'quantity');
        });

        Schema::table('fridge_items', function (Blueprint $table) {
            $table->string('quantity')->change()->nullable();
        });

    }
};
