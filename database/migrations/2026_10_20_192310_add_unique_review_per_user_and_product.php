<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Keeps only the latest review of each user per product before adding the constraint.
     */
    public function up(): void
    {
        $latestIds = DB::table('reviews')
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id', 'product_id')
            ->pluck('id');

        DB::table('reviews')->whereNotIn('id', $latestIds)->delete();

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id']);
        });
    }
};
