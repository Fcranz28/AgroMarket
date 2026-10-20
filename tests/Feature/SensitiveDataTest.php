<?php

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Modules\Identity\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
    $this->withoutVite();
});

it('stores farmer verification documents on the private disk', function () {
    $user = User::factory()->farmer()->pendingOnboarding()->create();

    $this->actingAs($user)->post(route('onboarding.verification'), [
        'face_photo' => UploadedFile::fake()->image('selfie.jpg'),
        'dni_front' => UploadedFile::fake()->image('front.jpg'),
        'dni_back' => UploadedFile::fake()->image('back.jpg'),
    ]);

    $user->refresh();

    Storage::disk('local')->assertExists([$user->face_photo, $user->dni_front, $user->dni_back]);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();
});

it('serves verification documents only to admins', function () {
    Storage::disk('local')->put('verification/dni/front.jpg', 'image');
    $farmer = User::factory()->farmer()->create(['dni_front' => 'verification/dni/front.jpg']);
    $url = route('admin.verification.document', [$farmer, 'dni_front']);

    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    $this->actingAs($farmer)->get($url)->assertForbidden();
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertSuccessful();
});

it('does not expose arbitrary user attributes through the document route', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get("/admin/usuarios/{$admin->id}/documentos/password")
        ->assertNotFound();
});

it('stores generated invoice json on the private disk', function () {
    $order = Order::factory()->guest()->create();

    $invoice = app(InvoiceService::class)->generateInvoice($order);

    Storage::disk('local')->assertExists($invoice->jsonPath());
    expect(Storage::disk('public')->allFiles())->toBeEmpty()
        ->and($invoice->pdf_url)->toBeNull();
});

it('does not let other users see guest invoices', function () {
    $invoice = Invoice::factory()->for(Order::factory()->guest())->create();

    $this->actingAs(User::factory()->create())
        ->get(route('invoice.download', $invoice))
        ->assertForbidden();
});

it('does not let users see invoices of other customers', function () {
    $invoice = Invoice::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('invoice.show', $invoice))
        ->assertForbidden();
});

it('lets the owner download their invoice', function () {
    $order = OrderItem::factory()->create()->order;
    $invoice = app(InvoiceService::class)->generateInvoice($order);

    $this->actingAs($order->user)
        ->get(route('invoice.download', $invoice))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

it('lets guests download their invoice only through a signed url', function () {
    $order = OrderItem::factory()->for(Order::factory()->guest())->create()->order;
    $invoice = app(InvoiceService::class)->generateInvoice($order);

    $this->get(route('invoice.guest-download', $invoice))->assertForbidden();

    $this->get(URL::temporarySignedRoute('invoice.guest-download', now()->addHour(), $invoice))
        ->assertSuccessful();
});

it('looks up a dni with the configured token', function () {
    config(['services.decolecta.token' => 'test-token']);
    Http::fake([
        'api.decolecta.com/*' => Http::response(['document_number' => '12345678', 'first_name' => 'ANA']),
    ]);
    $farmer = User::factory()->farmer()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->postJson(route('admin.check.dni'), ['dni' => '12345678', 'user_id' => $farmer->id])
        ->assertSuccessful()
        ->assertJson(['success' => true]);

    Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    expect($farmer->fresh()->dni)->toBe('12345678');
});

it('does not call the dni api without a configured token', function () {
    config(['services.decolecta.token' => null]);
    Http::fake();

    $this->actingAs(User::factory()->admin()->create())
        ->postJson(route('admin.check.dni'), ['dni' => '12345678', 'user_id' => User::factory()->create()->id])
        ->assertJson(['success' => false]);

    Http::assertNothingSent();
});

it('moves legacy public files to the private disk', function () {
    Storage::disk('public')->put('verification/dni/old.jpg', 'image');
    Storage::disk('public')->put('invoices/B001-000001.json', '{}');
    Storage::disk('public')->put('products/apple.jpg', 'image');

    $this->artisan('storage:move-sensitive')->assertSuccessful();

    Storage::disk('local')->assertExists(['verification/dni/old.jpg', 'invoices/B001-000001.json']);
    Storage::disk('public')->assertMissing(['verification/dni/old.jpg', 'invoices/B001-000001.json']);
    Storage::disk('public')->assertExists('products/apple.jpg');
});

it('seeds an admin with a random password and the admin role', function () {
    $this->seed(Database\Seeders\AdminSeeder::class);

    $admin = User::where('email', 'admin@agromarket.com')->sole();

    expect($admin->isAdmin())->toBeTrue()
        ->and(Illuminate\Support\Facades\Hash::check('admin123', $admin->password))->toBeFalse()
        ->and(Illuminate\Support\Facades\Hash::check('password', $admin->password))->toBeFalse();
});
