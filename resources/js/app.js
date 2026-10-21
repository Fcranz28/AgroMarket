import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import stripePayment from './checkout/stripe-payment';

// Livewire (y Alpine) arrancan desde este bundle para registrar antes los componentes Alpine.
// El carrito, el buscador, el catálogo y el checkout son componentes Livewire.
Alpine.data('stripePayment', stripePayment);

Livewire.start();
