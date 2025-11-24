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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'farmer', 'admin'])->default('user')->after('email');
            $table->enum('verification_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->after('role');
            $table->string('verification_document')->nullable()->after('verification_status');
            $table->boolean('is_banned')->default(false)->after('verification_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'verification_status', 'verification_document', 'is_banned']);
        });
    }
};
