<?php

use App\Models\Invoice;
use App\Modules\Identity\Models\User;

it('throttles account registration', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post('/register', [
            'name' => "Usuario {$attempt}",
            'email' => "usuario{$attempt}@example.com",
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ]);
        auth()->logout();
    }

    $this->post('/register', [
        'name' => 'Usuario 6',
        'email' => 'usuario6@example.com',
        'password' => 'secreto123',
        'password_confirmation' => 'secreto123',
    ])->assertTooManyRequests();
});

it('no longer exposes the unused contact submit endpoint', function () {
    $this->post('/contacto', ['email' => 'a@example.com', 'message' => 'Hola, ¿tienen paltas?'])
        ->assertMethodNotAllowed();
});

it('updates the profile without a password when the email does not change', function () {
    $user = User::factory()->create(['email' => 'ana@example.com']);

    $this->actingAs($user)->put(route('profile.update'), [
        'name' => 'Ana María',
        'email' => 'ANA@example.com',
        'dni' => '12345678',
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->name)->toBe('Ana María');
});

it('requires the current password to change the email', function () {
    $user = User::factory()->create(['email' => 'ana@example.com', 'password' => 'secreto123']);

    $this->actingAs($user)->put(route('profile.update'), [
        'name' => $user->name,
        'email' => 'atacante@example.com',
    ])->assertSessionHasErrorsIn('updateProfile', 'current_password');

    expect($user->fresh()->email)->toBe('ana@example.com');

    $this->actingAs($user)->put(route('profile.update'), [
        'name' => $user->name,
        'email' => 'nueva@example.com',
        'current_password' => 'secreto123',
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->email)->toBe('nueva@example.com');
});

it('rejects an invalid dni', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'dni' => '12ab',
    ])->assertSessionHasErrorsIn('updateProfile', 'dni');
});

it('addresses invoices by uuid instead of the sequential id', function () {
    $invoice = Invoice::factory()->create();

    expect($invoice->uuid)->not->toBeEmpty()
        ->and(route('invoice.show', $invoice))->toEndWith("/factura/{$invoice->uuid}");

    $this->actingAs($invoice->order->user)
        ->get("/factura/{$invoice->id}")
        ->assertNotFound();

    $this->actingAs($invoice->order->user)
        ->get(route('invoice.show', $invoice))
        ->assertSuccessful();
});
