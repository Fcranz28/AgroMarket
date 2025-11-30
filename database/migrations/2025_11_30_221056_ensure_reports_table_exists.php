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
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reporter
                $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Reported Product
                $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Context
                $table->string('reason');
                $table->text('description');
                $table->json('evidence')->nullable(); // Paths to images
                $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop it automatically if it was created by this safety migration
        // Schema::dropIfExists('reports');
    }
};
