<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Review;
use App\Modules\Identity\Models\User;
use App\Modules\Moderation\Enums\ReportStatus;
use App\Modules\Moderation\Models\Report;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;

beforeEach(function () {
    $this->withoutVite();
    $this->farmer = User::factory()->farmer()->verified()->create();
    $this->admin = User::factory()->admin()->create();
});

it('shows the farmer real sales, rating and monthly revenue', function () {
    $product = Product::factory()->for($this->farmer)->create();
    $order = Order::factory()->create(['payment_status' => PaymentStatus::Paid, 'status' => OrderStatus::Delivered]);
    OrderItem::factory()->for($order)->for($product)->create(['price' => '10.00', 'quantity' => 3]);
    Review::create(['product_id' => $product->id, 'user_id' => User::factory()->create()->id, 'rating' => 4]);

    $this->actingAs($this->farmer)
        ->get(route('farmer.dashboard'))
        ->assertSuccessful()
        ->assertSee('S/. 30.00')
        ->assertSee('4.0 ★')
        ->assertViewHas('monthlyRevenue', fn (array $months) => count($months) === 12 && end($months)['cents'] === 3000)
        ->assertDontSee('apexcharts');
});

it('lists only the farmer share of each order, also for guest orders', function () {
    $own = Product::factory()->for($this->farmer)->create(['name' => 'Papa amarilla']);
    $foreign = Product::factory()->create(['name' => 'Miel ajena']);
    $order = Order::factory()->guest()->create(['payment_status' => PaymentStatus::Paid, 'guest_name' => 'Ana', 'guest_lastname' => 'Quispe']);
    OrderItem::factory()->for($order)->for($own)->create(['price' => '5.00', 'quantity' => 2]);
    OrderItem::factory()->for($order)->for($foreign)->create(['price' => '20.00', 'quantity' => 1]);

    $this->actingAs($this->farmer)
        ->get(route('farmer.orders'))
        ->assertSuccessful()
        ->assertSee('Ana Quispe')
        ->assertSee('Papa amarilla')
        ->assertDontSee('Miel ajena')
        ->assertSee('Tu parte: <strong>S/. 10.00</strong>', false)
        ->assertDontSee('@js(', false);
});

it('lists the real products in the admin panel with their farmer', function () {
    Product::factory()->for($this->farmer)->create(['name' => 'Quinua blanca']);

    $this->actingAs($this->admin)
        ->get(route('admin.productos.index'))
        ->assertSuccessful()
        ->assertSee('Quinua blanca')
        ->assertSee($this->farmer->name);
});

it('lets the admin create a product with several units', function () {
    $category = Product::factory()->create()->category;

    $this->actingAs($this->admin)
        ->post(route('admin.productos.store'), [
            'name' => 'Maíz morado',
            'description' => 'Para chicha',
            'category_id' => $category->id,
            'unit' => ['kg', 'saco'],
            'stock' => [40, 3],
            'price' => ['7.80', '300.00'],
        ])
        ->assertRedirect(route('admin.productos.index'));

    expect(Product::where('name', 'Maíz morado')->sole()->units()->pluck('unit')->all())->toEqualCanonicalizing(['kg', 'saco']);
});

it('prefills the units editor when editing a product', function () {
    $product = Product::factory()->for($this->farmer)->create();
    $product->syncUnits([['unit' => 'arroba', 'price' => '140.00', 'stock' => 6]]);

    $this->actingAs($this->farmer)
        ->get(route('dashboard.productos.edit', $product))
        ->assertSuccessful()
        ->assertSee('arroba')
        ->assertSee('JSON.parse', false)
        ->assertDontSee('@js(', false);
});

it('shows the admin what needs attention', function () {
    User::factory()->farmer()->create(['name' => 'Rosa Mamani']);
    Report::create([
        'user_id' => User::factory()->create()->id,
        'product_id' => Product::factory()->create()->id,
        'order_id' => Order::factory()->create()->id,
        'reason' => 'damaged',
        'description' => 'Llegó aplastado',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Rosa Mamani')
        ->assertSee(route('admin.reports.index'))
        ->assertViewHas('pendingReportsCount', 1);
});

it('shows reports with the reason label and their detail data', function () {
    Report::create([
        'user_id' => User::factory()->create()->id,
        'product_id' => Product::factory()->create()->id,
        'order_id' => Order::factory()->create()->id,
        'reason' => 'expired',
        'description' => 'Fecha vencida',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful()
        ->assertSee('Producto vencido')
        ->assertSee('report-detail')
        ->assertDontSee('@js(', false);
});

it('renders the panel pages with the Tailwind layout', function (string $role, string $route) {
    $this->actingAs($role === 'admin' ? $this->admin : $this->farmer)
        ->get(route($route))
        ->assertSuccessful()
        ->assertSee('Menú del panel')
        ->assertDontSee('dashboard.css');
})->with([
    ['farmer', 'farmer.dashboard'],
    ['farmer', 'dashboard.productos.index'],
    ['farmer', 'dashboard.productos.create'],
    ['farmer', 'farmer.orders'],
    ['admin', 'admin.dashboard'],
    ['admin', 'admin.users'],
    ['admin', 'admin.productos.create'],
    ['admin', 'admin.reports.index'],
]);
