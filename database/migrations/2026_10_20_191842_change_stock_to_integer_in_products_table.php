<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The stock was changed to a string column; it must be numeric to compare and decrement it.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('stock')->change();
        });
    }
};
