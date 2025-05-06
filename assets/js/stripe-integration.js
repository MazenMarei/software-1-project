// ArtShelf - Stripe Payment Integration
// Author: GitHub Copilot
// Date: April 29, 2025

class StripePaymentProcessor {
  constructor(publishableKey) {
    this.stripe = Stripe(publishableKey);
    this.elements = null;
    this.card = null;
    this.paymentIntentId = null;
    this.clientSecret = null;
    this.baseUrl = window.location.origin; // For callbacks
  }

  // Initialize Stripe Elements
  initializeElements(elementId, options = {}) {
    // Create Elements instance
    this.elements = this.stripe.elements();

    // Customize the card Element
    const cardElementOptions = {
      style: {
        base: {
          color: "#32325d",
          fontFamily: '"Poppins", Helvetica, sans-serif',
          fontSmoothing: "antialiased",
          fontSize: "16px",
          "::placeholder": {
            color: "#aab7c4",
          },
          ...options.style?.base,
        },
        invalid: {
          color: "#fa755a",
          iconColor: "#fa755a",
          ...options.style?.invalid,
        },
      },
      hidePostalCode: options.hidePostalCode || false,
    };

    // Create the card Element
    this.card = this.elements.create("card", cardElementOptions);

    // Add the card Element to the page
    this.card.mount(`#${elementId}`);

    // Handle validation errors
    this.card.addEventListener("change", (event) => {
      const displayError = document.getElementById("card-errors");
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = "";
      }
    });

    return this.card;
  }

  // Create a payment intent on the server
  async createPaymentIntent(amount, currency = "usd", metadata = {}) {
    try {
      // In a real implementation, this would be a server call
      // For demo purposes, we'll simulate the response
      console.log(`Creating payment intent for ${amount} ${currency}`);

      // Simulate server response
      const response = {
        clientSecret: "pi_mock_secret_" + Date.now(),
        id: "pi_" + Date.now(),
      };

      this.clientSecret = response.clientSecret;
      this.paymentIntentId = response.id;

      return response;
    } catch (error) {
      console.error("Error creating payment intent:", error);
      throw error;
    }
  }

  // Process the payment
  async processPayment(customerData = {}) {
    try {
      if (!this.clientSecret) {
        throw new Error(
          "No payment intent created. Please create a payment intent first."
        );
      }

      const result = await this.stripe.confirmCardPayment(this.clientSecret, {
        payment_method: {
          card: this.card,
          billing_details: {
            name: customerData.name || "",
            email: customerData.email || "",
            address: customerData.address || {},
            phone: customerData.phone || "",
          },
        },
      });

      if (result.error) {
        throw result.error;
      }

      return result.paymentIntent;
    } catch (error) {
      console.error("Error processing payment:", error);
      throw error;
    }
  }

  // Create a setup intent for saving payment methods
  async createSetupIntent(customerEmail) {
    try {
      // In a real implementation, this would be a server call
      // For demo purposes, we'll simulate the response
      console.log(`Creating setup intent for customer: ${customerEmail}`);

      // Simulate server response
      const response = {
        clientSecret: "seti_mock_secret_" + Date.now(),
        id: "seti_" + Date.now(),
      };

      this.clientSecret = response.clientSecret;

      return response;
    } catch (error) {
      console.error("Error creating setup intent:", error);
      throw error;
    }
  }

  // Save a payment method for future use
  async savePaymentMethod(customerData = {}) {
    try {
      if (!this.clientSecret) {
        throw new Error(
          "No setup intent created. Please create a setup intent first."
        );
      }

      const result = await this.stripe.confirmCardSetup(this.clientSecret, {
        payment_method: {
          card: this.card,
          billing_details: {
            name: customerData.name || "",
            email: customerData.email || "",
            address: customerData.address || {},
            phone: customerData.phone || "",
          },
        },
      });

      if (result.error) {
        throw result.error;
      }

      return result.setupIntent;
    } catch (error) {
      console.error("Error saving payment method:", error);
      throw error;
    }
  }

  // Create a payment element with multiple payment methods
  initializePaymentElement(elementId, options = {}) {
    // Create Elements instance if not already created
    if (!this.elements) {
      this.elements = this.stripe.elements({
        clientSecret: this.clientSecret,
      });
    }

    const paymentElementOptions = {
      layout: {
        type: "tabs",
        defaultCollapsed: false,
        ...options.layout,
      },
    };

    // Create the Payment Element
    const paymentElement = this.elements.create(
      "payment",
      paymentElementOptions
    );

    // Add the Payment Element to the page
    paymentElement.mount(`#${elementId}`);

    return paymentElement;
  }

  // Handle 3D Secure authentication if needed
  async handle3DSecure(paymentIntentId) {
    try {
      const result = await this.stripe.retrievePaymentIntent(this.clientSecret);

      if (result.error) {
        throw result.error;
      }

      const paymentIntent = result.paymentIntent;

      // Check if authentication is required
      if (
        paymentIntent.status === "requires_action" &&
        paymentIntent.next_action?.type === "use_stripe_sdk"
      ) {
        const cardActionResult = await this.stripe.handleCardAction(
          this.clientSecret
        );

        if (cardActionResult.error) {
          throw cardActionResult.error;
        }

        return cardActionResult.paymentIntent;
      }

      return paymentIntent;
    } catch (error) {
      console.error("Error handling 3D Secure:", error);
      throw error;
    }
  }

  // Process a payment using a saved payment method
  async chargeUsingPaymentMethod(
    paymentMethodId,
    amount,
    currency = "usd",
    customerData = {}
  ) {
    try {
      // In a real implementation, this would be a server call
      // For demo purposes, we'll simulate the response
      console.log(
        `Charging ${amount} ${currency} using payment method: ${paymentMethodId}`
      );

      // Simulate payment success
      return {
        id: "pi_" + Date.now(),
        amount: amount,
        currency: currency,
        status: "succeeded",
        created: Date.now(),
        customer: customerData.customerId,
        payment_method: paymentMethodId,
      };
    } catch (error) {
      console.error("Error charging with saved payment method:", error);
      throw error;
    }
  }

  // Create a subscription
  async createSubscription(customerId, priceId, paymentMethodId) {
    try {
      // In a real implementation, this would be a server call
      // For demo purposes, we'll simulate the response
      console.log(
        `Creating subscription for customer ${customerId} with price ${priceId}`
      );

      // Simulate subscription creation
      return {
        id: "sub_" + Date.now(),
        customer: customerId,
        status: "active",
        current_period_end: Date.now() + 30 * 24 * 60 * 60 * 1000, // 30 days from now
        items: {
          data: [
            {
              price: {
                id: priceId,
              },
            },
          ],
        },
      };
    } catch (error) {
      console.error("Error creating subscription:", error);
      throw error;
    }
  }

  // Create a checkout session for a simple redirect flow
  async createCheckoutSession(
    lineItems,
    successUrl,
    cancelUrl,
    mode = "payment"
  ) {
    try {
      // In a real implementation, this would be a server call
      // For demo purposes, we'll simulate the response
      console.log("Creating checkout session with items:", lineItems);

      // Simulate checkout session creation
      const sessionId = "cs_" + Date.now();

      // In a real implementation, this would redirect to Stripe
      // For demo purposes, we'll simulate the flow
      console.log(
        `Checkout session created. Would redirect to Stripe checkout with session ID: ${sessionId}`
      );

      // Simulate immediate success (in real app this would redirect to Stripe)
      setTimeout(() => {
        window.location.href = successUrl + "?session_id=" + sessionId;
      }, 2000);

      return { id: sessionId };
    } catch (error) {
      console.error("Error creating checkout session:", error);
      throw error;
    }
  }

  // Process art purchase
  async processArtPurchase(artwork, customer, shippingDetails) {
    try {
      // Create a payment intent for the artwork price
      await this.createPaymentIntent(
        artwork.price * 100, // Convert to cents
        "usd",
        {
          artworkId: artwork.id,
          artworkTitle: artwork.title,
          artistId: artwork.artist.id,
          customerId: customer.id,
        }
      );

      // Process the payment
      const paymentResult = await this.processPayment({
        name: customer.name,
        email: customer.email,
        address: shippingDetails.address,
        phone: shippingDetails.phone,
      });

      // If payment succeeded, return the result
      if (paymentResult.status === "succeeded") {
        return {
          success: true,
          paymentId: paymentResult.id,
          amount: paymentResult.amount / 100, // Convert from cents
          artwork: artwork,
          date: new Date(),
          shippingDetails: shippingDetails,
        };
      } else {
        throw new Error(`Payment failed with status: ${paymentResult.status}`);
      }
    } catch (error) {
      console.error("Error processing art purchase:", error);
      throw error;
    }
  }
}

// Expose to global scope
window.StripePaymentProcessor = StripePaymentProcessor;

/**
 * ArtShelf Stripe Integration
 * Handles payment processing for artwork purchases
 */

// Initialize Stripe with the publishable key
let stripe;
let elements;

// Initialize Stripe when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize Stripe only on pages with payment forms
  const paymentForm = document.getElementById("payment-form");
  if (paymentForm) {
    initializeStripe();
  }
});

// Initialize Stripe with the publishable key
async function initializeStripe() {
  // This key should be replaced with your actual Stripe publishable key
  const STRIPE_PUBLISHABLE_KEY =
    "pk_test_51ArTsHGkIJgWzMQRICr59CQ2xLqBPYCwq06R4EkKBX7c5J5IsRw8lBPJnriCKLcnEEi0NIGHwrLyRz0wGLvCLaW800ZNhGUnhk";

  stripe = Stripe(STRIPE_PUBLISHABLE_KEY);
  elements = stripe.elements();

  // Create and mount the card element
  const cardElement = elements.create("card", {
    style: {
      base: {
        color: "#32325d",
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: "antialiased",
        fontSize: "16px",
        "::placeholder": {
          color: "#aab7c4",
        },
      },
      invalid: {
        color: "#fa755a",
        iconColor: "#fa755a",
      },
    },
  });

  cardElement.mount("#card-element");

  // Add event listener for form submission
  const paymentForm = document.getElementById("payment-form");
  paymentForm.addEventListener("submit", handlePaymentSubmission);

  // Handle real-time validation errors
  cardElement.addEventListener("change", function (event) {
    const displayError = document.getElementById("card-errors");
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = "";
    }
  });
}

// Handle payment form submission
async function handlePaymentSubmission(event) {
  event.preventDefault();

  const paymentForm = document.getElementById("payment-form");
  const submitButton = paymentForm.querySelector('button[type="submit"]');
  const loadingSpinner = document.getElementById("loading-spinner");

  // Disable form submission during processing
  setPaymentFormLoading(true, submitButton, loadingSpinner);

  // Get artwork details from the form
  const artworkId = paymentForm.getAttribute("data-artwork-id");
  const amount = parseFloat(paymentForm.getAttribute("data-amount"));
  const currency = paymentForm.getAttribute("data-currency") || "usd";

  try {
    // Create a payment intent on the server
    const paymentIntentResponse = await createPaymentIntent(
      artworkId,
      amount,
      currency
    );

    if (paymentIntentResponse.error) {
      showPaymentError(paymentIntentResponse.error.message);
      setPaymentFormLoading(false, submitButton, loadingSpinner);
      return;
    }

    // Confirm the card payment with Stripe
    const { paymentIntent, error } = await stripe.confirmCardPayment(
      paymentIntentResponse.clientSecret,
      {
        payment_method: {
          card: elements.getElement("card"),
          billing_details: {
            name: document.getElementById("customer-name").value,
            email: document.getElementById("customer-email").value,
          },
        },
      }
    );

    if (error) {
      showPaymentError(error.message);
      setPaymentFormLoading(false, submitButton, loadingSpinner);
    } else if (paymentIntent.status === "succeeded") {
      // Payment succeeded, redirect to success page
      window.location.href = `/pages/payment-success.html?payment_id=${paymentIntent.id}&artwork_id=${artworkId}`;
    } else {
      // Unexpected status
      showPaymentError("Payment processing error. Please try again.");
      setPaymentFormLoading(false, submitButton, loadingSpinner);
    }
  } catch (error) {
    console.error("Payment processing error:", error);
    showPaymentError("An unexpected error occurred.");
    setPaymentFormLoading(false, submitButton, loadingSpinner);
  }
}

// Create a payment intent on the server
async function createPaymentIntent(artworkId, amount, currency) {
  try {
    // In a real application, this would be a server endpoint
    // For demo purposes, we'll simulate a successful response
    // TODO: Replace with actual API call in production

    // Simulate network delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Simulate a successful response
    return {
      clientSecret: "pi_mock_secret_" + Math.random().toString(36).substring(2),
      amount: amount,
      currency: currency,
    };

    /* Example of actual API call:
        const response = await fetch('/api/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                artworkId: artworkId,
                amount: amount,
                currency: currency
            }),
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        return await response.json();
        */
  } catch (error) {
    console.error("Error creating payment intent:", error);
    return {
      error: {
        message: "Unable to process payment. Please try again later.",
      },
    };
  }
}

// Display payment error message
function showPaymentError(message) {
  const errorElement = document.getElementById("card-errors");
  errorElement.textContent = message;
  errorElement.classList.add("visible");

  // Scroll to error message
  errorElement.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

// Set loading state for payment form
function setPaymentFormLoading(isLoading, submitButton, loadingSpinner) {
  if (isLoading) {
    submitButton.disabled = true;
    submitButton.textContent = "Processing...";
    if (loadingSpinner) {
      loadingSpinner.classList.remove("hidden");
    }
  } else {
    submitButton.disabled = false;
    submitButton.textContent = "Complete Purchase";
    if (loadingSpinner) {
      loadingSpinner.classList.add("hidden");
    }
  }
}

// Calculate tax and shipping costs
function calculateOrderTotal(subtotal, shippingMethod, country) {
  // Sample tax rates by country (in a real app, would come from a database)
  const taxRates = {
    US: 0.0725, // 7.25%
    CA: 0.13, // 13%
    UK: 0.2, // 20%
    default: 0.1, // Default for other countries
  };

  // Sample shipping rates (in a real app, would be based on dimensions and weight)
  const shippingRates = {
    standard: 15,
    express: 35,
    overnight: 60,
  };

  // Calculate tax
  const taxRate = taxRates[country] || taxRates.default;
  const taxAmount = subtotal * taxRate;

  // Calculate shipping
  const shippingCost = shippingRates[shippingMethod] || shippingRates.standard;

  // Calculate total
  const total = subtotal + taxAmount + shippingCost;

  return {
    subtotal: subtotal,
    tax: taxAmount,
    taxRate: taxRate,
    shipping: shippingCost,
    total: total,
  };
}

// Update order summary on the checkout page
function updateOrderSummary(subtotal, shippingMethod, country) {
  const orderSummary = document.getElementById("order-summary");
  if (!orderSummary) return;

  const totals = calculateOrderTotal(subtotal, shippingMethod, country);

  // Format currency values
  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  });

  // Update the order summary HTML
  orderSummary.querySelector(".subtotal").textContent = formatter.format(
    totals.subtotal
  );
  orderSummary.querySelector(".tax").textContent = formatter.format(totals.tax);
  orderSummary.querySelector(".shipping").textContent = formatter.format(
    totals.shipping
  );
  orderSummary.querySelector(".total").textContent = formatter.format(
    totals.total
  );

  // Update the payment form amount attribute
  const paymentForm = document.getElementById("payment-form");
  if (paymentForm) {
    paymentForm.setAttribute("data-amount", totals.total * 100); // Convert to cents for Stripe
  }
}

// Event listeners for shipping method and country changes
document.addEventListener("DOMContentLoaded", function () {
  const shippingMethod = document.getElementById("shipping-method");
  const country = document.getElementById("shipping-country");
  const subtotalElement = document.getElementById("artwork-price");

  if (shippingMethod && country && subtotalElement) {
    const updateTotals = () => {
      const subtotal = parseFloat(
        subtotalElement.getAttribute("data-price") || 0
      );
      updateOrderSummary(subtotal, shippingMethod.value, country.value);
    };

    shippingMethod.addEventListener("change", updateTotals);
    country.addEventListener("change", updateTotals);

    // Initial update
    updateTotals();
  }
});
