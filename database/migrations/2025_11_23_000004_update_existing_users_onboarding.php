<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mark all existing users as having completed onboarding
        User::query()->update(['onboarding_completed' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to revert this without knowing which users were updated, 
        // but generally we don't need to revert this specific data change.
    }
};
