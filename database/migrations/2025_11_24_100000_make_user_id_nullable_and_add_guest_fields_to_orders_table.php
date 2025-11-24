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
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add guest fields
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_lastname')->nullable()->after('guest_name');
            $table->string('guest_email')->nullable()->after('guest_lastname');
            $table->string('document_type')->nullable()->after('guest_email'); // DNI, RUC, Pasaporte, Carnet Extranjeria
            $table->string('document_number')->nullable()->after('document_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert user_id to not null (CAUTION: this might fail if there are null values)
            // We won't strictly enforce not null on rollback to avoid data loss issues during dev, 
            // or we could delete guest orders. For now, just dropping columns.
            
            $table->dropColumn([
                'guest_name',
                'guest_lastname',
                'guest_email',
                'document_type',
                'document_number'
            ]);
            
            // $table->unsignedBigInteger('user_id')->nullable(false)->change(); 
        });
    }
};
