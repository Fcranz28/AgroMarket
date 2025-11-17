<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique(); // Para la página de detalle
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2); // 8 dígitos total, 2 decimales
            $table->string('unit'); // 'Kg', 'Arroba', 'Saco (60kg)'
            $table->integer('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};