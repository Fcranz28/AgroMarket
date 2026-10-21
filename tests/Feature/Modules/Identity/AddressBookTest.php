<?php

use App\Modules\Identity\Livewire\AddressBook;
use App\Modules\Identity\Models\Address;
use App\Modules\Identity\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('adds addresses and makes the first one the default', function () {
    Livewire::actingAs($this->user)->test(AddressBook::class)
        ->set('newAddress', 'Av. Arequipa 200, Lima')
        ->call('add')
        ->assertHasNoErrors()
        ->assertSet('newAddress', '')
        ->assertDispatched('notify')
        ->set('newAddress', 'Jr. Cusco 15, Lima')
        ->call('add')
        ->assertSee(['Av. Arequipa 200, Lima', 'Jr. Cusco 15, Lima']);

    expect($this->user->addresses()->where('is_default', true)->pluck('address')->all())->toBe(['Av. Arequipa 200, Lima']);
});

it('validates the new address', function () {
    Livewire::actingAs($this->user)->test(AddressBook::class)
        ->set('newAddress', 'Av')
        ->call('add')
        ->assertHasErrors(['newAddress' => 'min']);
});

it('keeps at most the allowed number of addresses', function () {
    foreach (range(1, Address::MAX_PER_USER) as $number) {
        $this->user->addresses()->create(['address' => "Calle {$number}"]);
    }

    Livewire::actingAs($this->user)->test(AddressBook::class)
        ->set('newAddress', 'Una dirección más')
        ->call('add')
        ->assertHasErrors('newAddress');

    expect($this->user->addresses()->count())->toBe(Address::MAX_PER_USER);
});

it('changes the default address', function () {
    $first = $this->user->addresses()->create(['address' => 'Calle 1', 'is_default' => true]);
    $second = $this->user->addresses()->create(['address' => 'Calle 2']);

    Livewire::actingAs($this->user)->test(AddressBook::class)->call('makeDefault', $second->id);

    expect($first->fresh()->is_default)->toBeFalse()
        ->and($second->fresh()->is_default)->toBeTrue();
});

it('moves the default to another address when the default one is removed', function () {
    $default = $this->user->addresses()->create(['address' => 'Calle 1', 'is_default' => true]);
    $other = $this->user->addresses()->create(['address' => 'Calle 2']);

    Livewire::actingAs($this->user)->test(AddressBook::class)
        ->call('remove', $default->id)
        ->assertDontSee('Calle 1');

    expect(Address::find($default->id))->toBeNull()
        ->and($other->fresh()->is_default)->toBeTrue();
});

it('cannot touch the addresses of another user', function (string $action) {
    $foreign = User::factory()->create()->addresses()->create(['address' => 'Calle ajena', 'is_default' => false]);

    Livewire::actingAs($this->user)->test(AddressBook::class)
        ->call($action, $foreign->id)
        ->assertNotFound();

    expect($foreign->fresh())->not->toBeNull()
        ->and($foreign->fresh()->is_default)->toBeFalse();
})->with(['remove', 'makeDefault']);
