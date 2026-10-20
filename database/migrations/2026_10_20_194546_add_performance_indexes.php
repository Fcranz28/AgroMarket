<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for the filters and sorts the application runs most often.
     *
     * Every product query filters deleted_at (soft deletes), so it leads the catalog indexes.
     *
     * @var array<string, list<list<string>>>
     */
    private const INDEXES = [
        'products' => [
            ['deleted_at', 'created_at'],          // catálogo y búsqueda: orden por defecto (más nuevos)
            ['deleted_at', 'price'],               // orden por precio
            ['deleted_at', 'name'],                // orden por nombre
            ['category_id', 'deleted_at'],         // filtro por categoría y productos relacionados
            ['user_id', 'deleted_at', 'created_at'], // productos del agricultor
        ],
        'product_units' => [
            ['product_id', 'unit'],                // syncUnits() busca la unidad por nombre
        ],
        'orders' => [
            ['user_id', 'payment_status', 'created_at'], // "Mis pedidos"
            ['payment_status', 'status', 'created_at'],  // pedidos y ventas del agricultor
            ['status'],                                   // ingresos del dashboard admin
        ],
        'order_items' => [
            ['product_id', 'order_id'],            // pedidos que contienen productos de un agricultor
        ],
        'users' => [
            ['role', 'created_at'],                // conteos por rol y listado de usuarios
            ['verification_status'],               // agricultores pendientes de verificación
        ],
        'reports' => [
            ['created_at'],                        // listado de reportes del admin
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            Schema::table($tableName, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $columns) {
                    $table->index($columns);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::INDEXES as $tableName => $indexes) {
            Schema::table($tableName, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $columns) {
                    $table->dropIndex($columns);
                }
            });
        }
    }
};
