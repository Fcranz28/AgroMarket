// Payment tab switching
function initPaymentTabs() {
    const tabs = document.querySelectorAll('.checkout-payment-tab');
    const methods = document.querySelectorAll('.checkout-payment-method');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const method = tab.getAttribute('data-method');

            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Update active method
            methods.forEach(m => m.classList.remove('active'));
            document.querySelector(`.checkout-payment-method[data-method="${method}"]`).classList.add('active');
        });
    });
}

// Run on DOM ready or immediately if already loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initPaymentTabs();
    });
} else {
    initPaymentTabs();
}
