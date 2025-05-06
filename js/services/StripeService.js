/**
 * Stripe Service
 * Handles Stripe payment integration
 */
class StripeService {
    /**
     * Create a new StripeService instance
     */
    constructor() {
        this.stripe = null;
        this.elements = null;
        this.card = null;
        this.initialized = false;
    }
    
    /**
     * Initialize Stripe with publishable key
     * @returns {Promise<boolean>} Success flag
     */
    async initialize() {
        if (this.initialized) return true;
        
        try {
            // Load Stripe.js dynamically
            await this.loadStripeScript();
            
            // Initialize Stripe with publishable key
            this.stripe = Stripe(AppConfig.stripe.publishableKey);
            this.initialized = true;
            return true;
        } catch (error) {
            console.error('Failed to initialize Stripe:', error);
            return false;
        }
    }
    
    /**
     * Load Stripe.js script dynamically
     * @returns {Promise<void>}
     */
    loadStripeScript() {
        return new Promise((resolve, reject) => {
            if (window.Stripe) {
                resolve();
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load Stripe.js'));
            document.head.appendChild(script);
        });
    }
    
    /**
     * Create card elements
     * @param {string} elementId - Container element ID
     * @returns {Object} Card element
     */
    createCardElement(elementId) {
        if (!this.initialized) {
            throw new Error('Stripe not initialized. Call initialize() first.');
        }
        
        const elements = this.stripe.elements();
        const style = {
            base: {
                color: '#32325d',
                fontFamily: '"Poppins", sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        
        this.elements = elements;
        this.card = elements.create('card', { style });
        this.card.mount(`#${elementId}`);
        
        return this.card;
    }
    
    /**
     * Process payment with card element
     * @param {number} amount - Payment amount in cents
     * @param {string} currency - Payment currency
     * @param {Object} metadata - Additional payment metadata
     * @returns {Promise<Object>} Payment result
     */
    async processCardPayment(amount, currency = 'usd', metadata = {}) {
        if (!this.initialized || !this.card) {
            throw new Error('Card element not initialized. Call createCardElement() first.');
        }
        
        try {
            // Create payment method
            const { paymentMethod, error: paymentMethodError } = await this.stripe.createPaymentMethod({
                type: 'card',
                card: this.card,
                billing_details: metadata.billingDetails || {}
            });
            
            if (paymentMethodError) {
                throw new Error(paymentMethodError.message);
            }
            
            // In a real app, this would be a server call to create a payment intent
            // For demo purposes, we'll simulate a successful payment
            const paymentResult = await this.simulatePaymentIntentCall(
                amount, 
                currency, 
                paymentMethod.id, 
                metadata
            );
            
            return paymentResult;
        } catch (error) {
            console.error('Payment processing error:', error);
            throw error;
        }
    }
    
    /**
     * Simulate a call to create a payment intent on the server
     * In a real app, this would be an API call to your backend
     * @param {number} amount - Payment amount in cents
     * @param {string} currency - Payment currency
     * @param {string} paymentMethodId - Payment method ID
     * @param {Object} metadata - Additional payment metadata
     * @returns {Promise<Object>} Simulated payment result
     */
    async simulatePaymentIntentCall(amount, currency, paymentMethodId, metadata) {
        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 1500));
        
        // Create a fake payment intent ID
        const paymentIntentId = 'pi_' + Math.random().toString(36).substring(2, 15);
        
        // Simulate successful payment
        return {
            success: true,
            paymentIntentId,
            amount,
            currency,
            paymentMethodId,
            metadata,
            created: new Date().toISOString()
        };
    }
    
    /**
     * Format amount for Stripe (converts dollars to cents)
     * @param {number} amount - Amount in dollars
     * @returns {number} Amount in cents
     */
    formatAmountForStripe(amount) {
        return Math.round(amount * 100);
    }
}

// Create a singleton instance
const stripeService = new StripeService();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = stripeService;
} else {
    window.stripeService = stripeService;
}