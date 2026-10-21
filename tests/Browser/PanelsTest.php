<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Moderation\Enums\ReportStatus;
use App\Modules\Moderation\Models\Report;
use App\Modules\Orders\Models\Order;

it('lets the farmer edit several units of a product', function () {
    Category::factory()->create(['name' => 'Tubérculos']);
    $this->actingAs(User::factory()->farmer()->verified()->create());

    // Guardar el formulario (multipart con imágenes) lo cubren los tests de Feature:
    // el servidor del plugin de navegador todavía no recibe archivos.
    $page = visit('/agricultor/productos/create')
        ->assertCount('input[name="unit[]"]', 1)
        ->click('Agregar unidad')
        ->assertCount('input[name="unit[]"]', 2)
        ->click('Agregar unidad')
        ->assertCount('input[name="unit[]"]', 3);

    $page->click('@remove-unit-1')
        ->assertCount('input[name="unit[]"]', 2)
        ->assertNoJavaScriptErrors();
});

it('lets the admin review a report and change its status', function () {
    $admin = User::factory()->admin()->create();
    $report = Report::create([
        'user_id' => User::factory()->create()->id,
        'product_id' => Product::factory()->create(['name' => 'Miel de abeja'])->id,
        'order_id' => Order::factory()->create()->id,
        'reason' => 'damaged',
        'description' => 'El frasco llegó roto',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($admin);

    visit('/admin/reportes')
        ->assertSee('Producto dañado')
        ->click('Ver detalle')
        ->waitForText('El frasco llegó roto')
        ->select('#report-status', 'resolved')
        ->fill('#report-notes', 'Se reembolsó al cliente')
        ->press('Guardar')
        ->waitForText('Resuelto')
        ->assertNoJavaScriptErrors();

    expect($report->fresh())
        ->status->toBe(ReportStatus::Resolved)
        ->admin_notes->toBe('Se reembolsó al cliente');
});

it('opens the panel menu on mobile', function () {
    $this->actingAs(User::factory()->admin()->create());

    visit('/admin/dashboard')->on()->mobile()
        ->click('@panel-menu')
        ->assertVisible('@panel-sidebar')
        ->assertSee('Reportes');
});
