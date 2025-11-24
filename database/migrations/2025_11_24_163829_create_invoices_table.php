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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Invoice identification
            $table->string('tipo_doc', 2); // 01 = Factura, 03 = Boleta
            $table->string('serie', 10); // F001, B001, etc.
            $table->string('correlativo', 20);
            $table->string('numero_completo', 50)->unique(); // F001-123
            
            // Amounts
            $table->decimal('subtotal', 10, 2); // Monto sin IGV
            $table->decimal('igv', 10, 2); // 18% IGV
            $table->decimal('total', 10, 2); // Total con IGV
            
            // APIs Peru data
            $table->json('api_request')->nullable(); // JSON sent to APIs Peru
            $table->json('api_response')->nullable(); // Response from APIs Peru
            
            // Document URLs from APIs Peru
            $table->string('pdf_url')->nullable();
            $table->string('xml_url')->nullable();
            $table->string('cdr_url')->nullable(); // Constancia de Recepción
            
            // Status tracking
            $table->enum('status', ['pending', 'processing', 'success', 'error'])->default('pending');
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['serie', 'correlativo']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
