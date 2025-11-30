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

      // Calculate subtotal and tax (18% IGV)
      const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.cantidad), 0);
      const tax = subtotal * 0.18;
      const total = subtotal + tax;

      // Create payment intent
      const response = await fetch('/payment/create-intent', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
         },
         body: JSON.stringify({
            amount: total,
            cart: cart,
            tax: tax
         })
      });

      const data = await response.json();

      if (!response.ok) {
         throw new Error(data.error || 'Error al crear la intención de pago');
      }

      // Check for dark mode
      const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';

      // Initialize Elements
      elements = stripe.elements({
         clientSecret: data.clientSecret,
         appearance: {
            theme: isDarkMode ? 'night' : 'stripe',
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

      // Use SweetAlert for better error presentation
      if (typeof Swal !== 'undefined') {
         Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: error.message,
            confirmButtonColor: '#4caf50',
            confirmButtonText: 'Entendido'
         });
      } else {
         alert('Error: ' + error.message);
      }
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
      const guestName = document.getElementById('guest_name').value;
      const guestLastname = document.getElementById('guest_lastname').value;
      const guestEmail = document.getElementById('guest_email').value;
      const documentType = document.getElementById('document_type').value;
      const documentNumber = document.getElementById('document_number').value;

      // Confirm payment
      const { error, paymentIntent } = await stripe.confirmPayment({
         elements,
         confirmParams: {
            return_url: window.location.origin + '/checkout',
            payment_method_data: {
               billing_details: {
                  name: `${guestName} ${guestLastname}`,
                  email: guestEmail,
                  phone: phone,
                  address: {
                     line1: shippingAddress
                  }
               }
            }
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
               phone: phone,
               guest_name: guestName,
               guest_lastname: guestLastname,
               guest_email: guestEmail,
               document_type: documentType,
               document_number: documentNumber
            })
         });

         const data = await response.json();

         if (!response.ok) {
            throw new Error(data.error || 'Error al procesar el pago');
         }

         // Clear cart
         localStorage.removeItem('carrito');

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
      const cart = localStorage.getItem('carrito');
      return cart ? JSON.parse(cart) : [];
   } catch (e) {
      console.error('Error reading cart:', e);
      return [];
   }
}

// Initialize on page load
if (document.readyState === 'loading') {
   document.addEventListener('DOMContentLoaded', initializeStripeCheckout);
} else {
   initializeStripeCheckout();
}
