<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Invoicing\Services\InvoiceService;
use App\Modules\Moderation\Models\Report;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Support\Facades\Storage;

it('returns only the public product fields in the catalog api', function () {
    Product::factory()->create();

    $product = $this->getJson('/api/productos')->assertSuccessful()->json('products.0');

    expect(array_keys($product))->toBe(['id', 'category_id', 'name', 'slug', 'price', 'unit', 'stock', 'image_path']);
});

it('keeps the search response the javascript expects', function () {
    Product::factory()->create(['name' => 'Papa amarilla']);

    $this->getJson('/api/search?q=papa')
        ->assertSuccessful()
        ->assertJsonStructure(['products' => [['id', 'slug', 'name', 'price', 'unit', 'image_path', 'image_url', 'category_name']], 'total']);
});

it('does not expose the tax payload or the buyer document in the invoice json', function () {
    Storage::fake('local');
    $order = OrderItem::factory()->create()->order;
    $invoice = app(InvoiceService::class)->generateInvoice($order);

    $json = $this->actingAs($order->user)->getJson(route('invoice.show', $invoice))->assertSuccessful();

    $json->assertJsonPath('invoice.numero_completo', $invoice->numero_completo)
        ->assertJsonMissingPath('invoice.api_request')
        ->assertJsonMissingPath('order.document_number')
        ->assertJsonMissingPath('order.guest_email');
});

it('returns the report detail the admin panel uses', function () {
    $item = OrderItem::factory()->create();
    $report = Report::create([
        'user_id' => $item->order->user_id,
        'order_id' => $item->order_id,
        'product_id' => $item->product_id,
        'reason' => 'Producto en mal estado',
        'description' => 'Llegó malogrado',
        'status' => 'pending',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->getJson(route('admin.reports.show', $report))
        ->assertSuccessful()
        ->assertJsonPath('status_label', 'Pendiente')
        ->assertJsonStructure(['id', 'reason', 'description', 'evidence', 'status', 'admin_notes', 'user' => ['name', 'email'], 'product' => ['name', 'farmer'], 'order' => ['uuid']])
        ->assertJsonMissingPath('user.password')
        ->assertJsonMissingPath('user.dni');
});

it('returns the addresses as a plain list', function () {
    $user = User::factory()->create();
    $user->addresses()->create(['address' => 'Jr. Lima 100']);

    $this->actingAs($user)->getJson(route('addresses.index'))
        ->assertSuccessful()
        ->assertJsonPath('0.address', 'Jr. Lima 100');
});
