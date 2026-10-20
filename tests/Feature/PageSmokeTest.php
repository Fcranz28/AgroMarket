<?php

use App\Models\Invoice;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductImage;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();
    Category::factory()->create();
});

it('renders the buyer pages', function () {
    $buyer = User::factory()->create();
    $order = Order::factory()->for($buyer)->create();
    OrderItem::factory()->for($order)->create();

    $this->actingAs($buyer);

    $this->get(route('orders.index'))->assertSuccessful();
    $this->get(route('orders.show', $order))->assertSuccessful();
    $this->get(route('checkout.index'))->assertSuccessful();
    $this->getJson(route('addresses.index'))->assertSuccessful();
});

it('renders the farmer pages', function () {
    $farmer = User::factory()->farmer()->verified()->create();
    $product = Product::factory()->forFarmer($farmer)->create();
    $order = OrderItem::factory()->for($product)->create()->order;

    $this->actingAs($farmer);

    $this->get(route('farmer.dashboard'))->assertSuccessful();
    $this->get(route('farmer.orders'))->assertSuccessful();
    $this->get(route('farmer.orders.show', $order))->assertSuccessful();
    $this->get(route('dashboard.productos.index'))->assertSuccessful();
    $this->get(route('dashboard.productos.create'))->assertSuccessful();
    $this->get(route('dashboard.productos.show', $product))->assertSuccessful();
    $this->get(route('dashboard.productos.edit', $product))->assertSuccessful();
});

it('redirects unverified farmers away from product creation', function () {
    $farmer = User::factory()->farmer()->create();

    $this->actingAs($farmer)
        ->get(route('dashboard.productos.create'))
        ->assertRedirect(route('farmer.dashboard'));
});

it('does not let farmers view products of other farmers', function () {
    $product = Product::factory()->create();

    $this->actingAs(User::factory()->farmer()->verified()->create())
        ->get(route('dashboard.productos.show', $product))
        ->assertForbidden();
});

it('renders the admin pages', function () {
    $admin = User::factory()->admin()->create();
    $farmer = User::factory()->farmer()->create();
    $product = Product::factory()->create();

    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))->assertSuccessful();
    $this->get(route('admin.users'))->assertSuccessful();
    $this->get(route('admin.verify.view', $farmer))->assertSuccessful();
    $this->get(route('admin.productos.index'))->assertSuccessful();
    $this->get(route('admin.productos.create'))->assertSuccessful();
    $this->get(route('admin.productos.show', $product))->assertSuccessful();
    $this->get(route('admin.productos.edit', $product))->assertSuccessful();
    $this->get(route('admin.reports.index'))->assertSuccessful();
});

it('lets admins create products', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post(route('admin.productos.store'), [
        'category_id' => Category::first()->id,
        'name' => 'Papa amarilla',
        'description' => 'Papa de Huánuco',
        'unit' => ['kg', 'saco'],
        'stock' => [10, 2],
        'price' => [4.5, 180],
    ])->assertRedirect(route('admin.productos.index'));

    $product = Product::where('name', 'Papa amarilla')->sole();

    expect($product->user_id)->toBe($admin->id)
        ->and($product->units)->toHaveCount(2);
});

it('soft deletes products so order history keeps its images', function () {
    Storage::fake('public');
    Storage::disk('public')->put('products/main.jpg', 'x');
    Storage::disk('public')->put('products/gallery.jpg', 'x');
    $product = Product::factory()->create(['image_path' => 'products/main.jpg']);
    ProductImage::create(['product_id' => $product->id, 'image_path' => 'products/gallery.jpg']);
    $item = OrderItem::factory()->for($product)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->delete(route('admin.productos.destroy', $product))
        ->assertRedirect(route('admin.productos.index'));

    expect($product->fresh()->trashed())->toBeTrue()
        ->and($item->fresh()->product->name)->toBe($product->name);
    Storage::disk('public')->assertExists(['products/main.jpg', 'products/gallery.jpg']);
    $this->get(route('products.show', $product->slug))->assertNotFound();
});

it('generates invoices for orders above one thousand soles', function () {
    Storage::fake('local');
    $order = OrderItem::factory()->create(['price' => 1250.00, 'quantity' => 1])->order;

    $invoice = app(App\Services\InvoiceService::class)->generateInvoice($order);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->status)->toBe('success')
        ->and($invoice->api_request['legends'][0]['value'])->toStartWith('SON MIL');
});
