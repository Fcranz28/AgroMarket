<?php

namespace App\Modules\Identity\Livewire;

use App\Modules\Identity\Models\Address;
use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Saved shipping addresses of the signed-in user ("Mi cuenta" → Direcciones).
 *
 * @property-read Collection<int, Address> $addresses
 */
class AddressBook extends Component
{
    use AuthorizesRequests;

    #[Validate('required|string|min:5|max:500', message: [
        'required' => 'Escribe la dirección.',
        'min' => 'La dirección es demasiado corta.',
        'max' => 'La dirección puede tener como máximo 500 caracteres.',
    ])]
    public string $newAddress = '';

    /**
     * @return Collection<int, Address>
     */
    #[Computed]
    public function addresses(): Collection
    {
        return $this->user()->addresses()->orderByDesc('is_default')->latest()->get();
    }

    public function add(): void
    {
        $this->validate();

        if ($this->addresses->count() >= Address::MAX_PER_USER) {
            $this->addError('newAddress', 'Solo puedes tener un máximo de '.Address::MAX_PER_USER.' direcciones.');

            return;
        }

        $this->user()->addresses()->create([
            'address' => $this->newAddress,
            'is_default' => $this->addresses->isEmpty(),
        ]);

        $this->reset('newAddress');
        unset($this->addresses);
        $this->dispatch('notify', type: 'success', message: 'Dirección guardada.');
    }

    public function makeDefault(int $addressId): void
    {
        $address = $this->findOwnAddress($addressId);

        DB::transaction(function () use ($address) {
            $this->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        unset($this->addresses);
    }

    public function remove(int $addressId): void
    {
        $address = $this->findOwnAddress($addressId);
        $this->authorize('delete', $address);

        $address->delete();

        if ($address->is_default) {
            $this->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        unset($this->addresses);
        $this->dispatch('notify', type: 'success', message: 'Dirección eliminada.');
    }

    public function render(): View
    {
        return view('livewire.identity.address-book', ['maxAddresses' => Address::MAX_PER_USER]);
    }

    private function findOwnAddress(int $addressId): Address
    {
        return $this->user()->addresses()->findOrFail($addressId);
    }

    private function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
