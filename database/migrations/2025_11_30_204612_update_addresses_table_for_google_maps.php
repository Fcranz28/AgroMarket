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
        Schema::table('addresses', function (Blueprint $table) {
            // Check if columns exist before dropping/renaming to avoid errors if running on fresh DB vs existing
            if (Schema::hasColumn('addresses', 'address_line')) {
                $table->renameColumn('address_line', 'address');
            } else {
                $table->string('address');
            }
            
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Drop unused columns if they exist
            $table->dropColumn(['city', 'country', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->renameColumn('address', 'address_line');
            $table->dropColumn(['latitude', 'longitude']);
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
        });
    }
};
