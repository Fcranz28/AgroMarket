import { loadStripe } from '@stripe/stripe-js';

// Initialize Stripe (will be set in initializeStripeCheckout)
let stripe = null;
let elements = null;
let paymentElement = null;

/**
 * Initialize Stripe checkout
 */
export async function initializeStripeCheckout() {
   const checkoutForm = document.getElementById('checkoutForm');
   if (!checkoutForm) return;

   try {
      // Initialize Stripe
      const stripeKey = document.querySelector('meta[name="stripe-key"]')?.content;
      if (!stripeKey) {
         console.error('Stripe key not found');
         return;
      }

      stripe = await loadStripe(stripeKey);

      // Get cart data
      const cart = getCartFromStorage();
      if (!cart || cart.length === 0) {
         alert('El carrito está vacío');
         window.location.href = '/productos';
         return;
      }

      // Calculate total
      const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

      // Create payment intent
      const response = await fetch('/payment/create-intent', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
         },
         body: JSON.stringify({
            amount: total,
            cart: cart
         })
      });

      const data = await response.json();

      if (!response.ok) {
         throw new Error(data.error || 'Error al crear la intención de pago');
      }

      // Initialize Elements
      elements = stripe.elements({
         clientSecret: data.clientSecret,
         appearance: {
            theme: 'stripe',
            variables: {
               colorPrimary: '#4caf50',
            }
         }
      });

      // Create and mount Payment Element
      paymentElement = elements.create('payment');
      paymentElement.mount('#payment-element');

      // Show payment section
      document.getElementById('payment-section').style.display = 'block';

      // Handle form submission
      checkoutForm.addEventListener('submit', handleSubmit);

   } catch (error) {
      console.error('Error initializing Stripe:', error);
      alert('Error al cargar el sistema de pagos: ' + error.message);
   }
}

/**
 * Handle form submission
 */
async function handleSubmit(e) {
   e.preventDefault();

   const submitButton = document.getElementById('submit-payment');
   const spinner = document.getElementById('payment-spinner');
   const buttonText = document.getElementById('button-text');

   // Disable button
   submitButton.disabled = true;
   spinner.style.display = 'inline-block';
   buttonText.textContent = 'Procesando...';

   try {
      // Get form data
      const shippingAddress = document.getElementById('shipping_address').value;
      const phone = document.getElementById('phone').value;

      // Confirm payment
      const { error, paymentIntent } = await stripe.confirmPayment({
         elements,
         confirmParams: {
            return_url: window.location.origin + '/checkout',
         },
         redirect: 'if_required'
      });

      if (error) {
         throw new Error(error.message);
      }

      if (paymentIntent.status === 'succeeded') {
         // Process payment on backend
         const cart = getCartFromStorage();
         const response = await fetch('/payment/process', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
               payment_intent_id: paymentIntent.id,
               cart: cart,
               shipping_address: shippingAddress,
               phone: phone
            })
         });

         const data = await response.json();

         if (!response.ok) {
            throw new Error(data.error || 'Error al procesar el pago');
         }

         // Clear cart
         localStorage.removeItem('cart');

         // Redirect to order confirmation
         window.location.href = data.redirect;
      }

   } catch (error) {
      console.error('Payment error:', error);
      alert('Error al procesar el pago: ' + error.message);

      // Re-enable button
      submitButton.disabled = false;
      spinner.style.display = 'none';
      buttonText.textContent = 'Pagar ahora';
   }
}

/**
 * Get cart from localStorage
 */
function getCartFromStorage() {
   try {
      const cart = localStorage.getItem('cart');
      return cart ? JSON.parse(cart) : [];
   } catch (e) {
      console.error('Error reading cart:', e);
      return [];
   }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initializeStripeCheckout);
