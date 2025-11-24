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
            $table->boolean('onboarding_completed')->default(false)->after('is_banned');
            $table->string('dni_front')->nullable()->after('onboarding_completed');
            $table->string('dni_back')->nullable()->after('dni_front');
            $table->string('face_photo')->nullable()->after('dni_back');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed', 'dni_front', 'dni_back', 'face_photo']);
        });
    }
};
