/**
 * Card payment of the Livewire checkout (deferred intent): the Payment Element is created
 * with the amount, the server creates the intent only once the buyer data is valid
 * ($wire.prepare) and records the order once Stripe confirmed the payment ($wire.complete).
 * Stripe.js is only downloaded on the checkout page.
 */
export default function stripePayment({ key, amount, currency }) {
    return {
        stripe: null,
        elements: null,
        ready: false,
        processing: false,
        error: null,

        async init() {
            if (!key) {
                this.error = 'Los pagos con tarjeta no están configurados.';
                return;
            }

            try {
                const { loadStripe } = await import('@stripe/stripe-js');
                this.stripe = await loadStripe(key);
                const isDark = document.documentElement.dataset.theme === 'dark';

                this.elements = this.stripe.elements({
                    mode: 'payment',
                    amount,
                    currency,
                    appearance: {
                        theme: isDark ? 'night' : 'stripe',
                        variables: { colorPrimary: '#7aa537', borderRadius: '12px' },
                    },
                });

                const paymentElement = this.elements.create('payment');
                paymentElement.on('ready', () => (this.ready = true));
                paymentElement.on('loaderror', ({ error }) => {
                    console.error(error);
                    this.error = error?.message ?? 'No se pudo cargar el formulario de pago.';
                });
                paymentElement.mount(this.$refs.paymentElement);
            } catch (error) {
                console.error(error);
                this.error = 'No se pudo cargar el formulario de pago. Recarga la página e inténtalo de nuevo.';
            }
        },

        async pay() {
            if (!this.ready || this.processing) {
                return;
            }

            this.processing = true;
            this.error = null;

            try {
                // Stripe marks the card fields that need fixing
                const { error: cardError } = await this.elements.submit();
                if (cardError) {
                    return;
                }

                // Livewire shows the buyer data errors next to each field
                const clientSecret = await this.$wire.prepare();
                if (!clientSecret) {
                    return;
                }

                const { error, paymentIntent } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    clientSecret,
                    confirmParams: {
                        return_url: window.location.href,
                        payment_method_data: {
                            billing_details: {
                                name: `${this.$wire.guest_name} ${this.$wire.guest_lastname}`.trim(),
                                email: this.$wire.guest_email,
                                phone: this.$wire.phone,
                            },
                        },
                    },
                    redirect: 'if_required',
                });

                if (error) {
                    this.error = error.message;
                    return;
                }

                if (paymentIntent.status !== 'succeeded') {
                    this.error = 'El pago no se completó. Inténtalo de nuevo o usa otra tarjeta.';
                    return;
                }

                await this.$wire.complete();
            } catch (error) {
                console.error(error);
                this.error = 'Ocurrió un error al procesar el pago. Inténtalo de nuevo.';
            } finally {
                this.processing = false;
            }
        },
    };
}
