// ArtShelf - Main JavaScript File
// Author: GitHub Copilot
// Date: April 29, 2025

// Base User class for authentication and user management
class User {
  constructor(id, name, email, password, role) {
    this.id = id;
    this.name = name;
    this.email = email;
    this.password = password; // In a real app, this would be hashed
    this.role = role; // 'customer', 'artist', or 'admin'
    this.createdAt = new Date();
    this.isActive = true;
  }

  login() {
    // Simulate authentication
    console.log(`User ${this.name} logged in`);
    return true;
  }

  logout() {
    console.log(`User ${this.name} logged out`);
    return true;
  }

  updateProfile(data) {
    Object.assign(this, data);
    console.log("Profile updated successfully");
    return this;
  }

  changePassword(oldPassword, newPassword) {
    if (this.password === oldPassword) {
      this.password = newPassword;
      console.log("Password changed successfully");
      return true;
    }
    console.log("Incorrect old password");
    return false;
  }
}

// Customer specific functionalities
class Customer extends User {
  constructor(id, name, email, password) {
    super(id, name, email, password, "customer");
    this.cart = [];
    this.favorites = [];
    this.followedArtists = [];
    this.orderHistory = [];
    this.balance = 0;
    this.notifications = [];
  }

  addToCart(artwork) {
    this.cart.push(artwork);
    console.log(`${artwork.title} added to cart`);
    return this.cart;
  }

  removeFromCart(artworkId) {
    this.cart = this.cart.filter((item) => item.id !== artworkId);
    console.log("Item removed from cart");
    return this.cart;
  }

  addToFavorites(artwork) {
    if (!this.favorites.some((fav) => fav.id === artwork.id)) {
      this.favorites.push(artwork);
      console.log(`${artwork.title} added to favorites`);
    }
    return this.favorites;
  }

  removeFromFavorites(artworkId) {
    this.favorites = this.favorites.filter((item) => item.id !== artworkId);
    console.log("Item removed from favorites");
    return this.favorites;
  }

  followArtist(artist) {
    if (!this.followedArtists.some((a) => a.id === artist.id)) {
      this.followedArtists.push(artist);
      console.log(`Now following ${artist.name}`);
    }
    return this.followedArtists;
  }

  unfollowArtist(artistId) {
    this.followedArtists = this.followedArtists.filter(
      (artist) => artist.id !== artistId
    );
    console.log("Artist unfollowed");
    return this.followedArtists;
  }

  checkout() {
    if (this.cart.length === 0) {
      console.log("Cart is empty");
      return false;
    }

    const order = {
      id: `order-${Date.now()}`,
      items: [...this.cart],
      total: this.cart.reduce((sum, item) => sum + item.price, 0),
      date: new Date(),
      status: "pending",
    };

    this.orderHistory.push(order);
    this.cart = [];
    console.log(`Order placed: ${order.id}`);
    return order;
  }

  addBalance(amount) {
    this.balance += amount;
    console.log(`$${amount} added to balance. New balance: $${this.balance}`);
    return this.balance;
  }

  useBalance(amount) {
    if (this.balance >= amount) {
      this.balance -= amount;
      console.log(
        `$${amount} used from balance. New balance: $${this.balance}`
      );
      return true;
    }
    console.log("Insufficient balance");
    return false;
  }
}

// Artist specific functionalities
class Artist extends User {
  constructor(id, name, email, password) {
    super(id, name, email, password, "artist");
    this.artworks = [];
    this.collections = [];
    this.followers = [];
    this.isApproved = false;
    this.sales = [];
    this.fairs = [];
    this.withdrawals = [];
    this.balance = 0;
  }

  createArtwork(artwork) {
    this.artworks.push(artwork);
    console.log(`New artwork created: ${artwork.title}`);
    return artwork;
  }

  updateArtwork(artworkId, data) {
    const index = this.artworks.findIndex((art) => art.id === artworkId);
    if (index !== -1) {
      this.artworks[index] = { ...this.artworks[index], ...data };
      console.log(`Artwork updated: ${this.artworks[index].title}`);
      return this.artworks[index];
    }
    console.log("Artwork not found");
    return null;
  }

  deleteArtwork(artworkId) {
    this.artworks = this.artworks.filter((art) => art.id !== artworkId);
    console.log("Artwork deleted");
    return this.artworks;
  }

  createCollection(collection) {
    this.collections.push(collection);
    console.log(`New collection created: ${collection.name}`);
    return collection;
  }

  registerFair(fair) {
    this.fairs.push(fair);
    console.log(`New fair registered: ${fair.name}`);
    return fair;
  }

  requestWithdrawal(amount, isUrgent = false) {
    if (this.balance >= amount) {
      const withdrawal = {
        id: `withdrawal-${Date.now()}`,
        amount,
        date: new Date(),
        status: "pending",
        isUrgent,
      };
      this.withdrawals.push(withdrawal);
      console.log(`Withdrawal requested: $${amount}`);
      return withdrawal;
    }
    console.log("Insufficient balance");
    return null;
  }

  getSalesReport() {
    return {
      totalSales: this.sales.length,
      totalRevenue: this.sales.reduce((sum, sale) => sum + sale.price, 0),
      sales: this.sales,
    };
  }
}

// Admin specific functionalities
class Admin extends User {
  constructor(id, name, email, password) {
    super(id, name, email, password, "admin");
  }

  approveArtwork(artwork) {
    artwork.isApproved = true;
    console.log(`Artwork approved: ${artwork.title}`);
    return artwork;
  }

  rejectArtwork(artwork, reason) {
    artwork.isApproved = false;
    artwork.rejectionReason = reason;
    console.log(`Artwork rejected: ${artwork.title}`);
    return artwork;
  }

  approveFair(fair) {
    fair.isApproved = true;
    console.log(`Fair approved: ${fair.name}`);
    return fair;
  }

  rejectFair(fair, reason) {
    fair.isApproved = false;
    fair.rejectionReason = reason;
    console.log(`Fair rejected: ${fair.name}`);
    return fair;
  }

  approveArtist(artist) {
    artist.isApproved = true;
    console.log(`Artist approved: ${artist.name}`);
    return artist;
  }

  banUser(user, reason) {
    user.isActive = false;
    user.banReason = reason;
    console.log(`User banned: ${user.name}`);
    return user;
  }

  createSpecialCollection(collection) {
    collection.isSpecial = true;
    collection.createdBy = "admin";
    console.log(`Special collection created: ${collection.name}`);
    return collection;
  }

  processWithdrawal(withdrawal, isApproved) {
    withdrawal.status = isApproved ? "approved" : "rejected";
    console.log(`Withdrawal ${withdrawal.status}: $${withdrawal.amount}`);
    return withdrawal;
  }

  createOffer(percentage, startDate, endDate) {
    const offer = {
      id: `offer-${Date.now()}`,
      percentage,
      startDate,
      endDate,
      isActive: true,
    };
    console.log(`New offer created: ${percentage}% off`);
    return offer;
  }
}

// Artwork class to manage art pieces
class Artwork {
  constructor(
    id,
    title,
    artist,
    description,
    price,
    category,
    dimensions,
    images
  ) {
    this.id = id;
    this.title = title;
    this.artist = artist;
    this.description = description;
    this.price = price;
    this.category = category; // painting, photography, sculpture, etc.
    this.dimensions = dimensions;
    this.images = images;
    this.createdAt = new Date();
    this.isApproved = false;
    this.likes = 0;
    this.views = 0;
  }

  like() {
    this.likes++;
    console.log(`${this.title} liked. Total likes: ${this.likes}`);
    return this.likes;
  }

  view() {
    this.views++;
    return this.views;
  }

  calculateShipping(location) {
    // Simple shipping calculation based on artwork size and destination
    const baseRate = 10;
    const sizeMultiplier = this.getSizeMultiplier();
    const distanceMultiplier = this.getDistanceMultiplier(location);

    return baseRate * sizeMultiplier * distanceMultiplier;
  }

  getSizeMultiplier() {
    // Calculate size multiplier based on dimensions
    const volume =
      this.dimensions.width * this.dimensions.height * this.dimensions.depth;
    if (volume < 1000) return 1;
    if (volume < 5000) return 1.5;
    if (volume < 10000) return 2;
    return 3;
  }

  getDistanceMultiplier(location) {
    // This would normally use the user's location and the artwork's location
    // For demo purposes, we'll return a random value between 1 and 2
    return 1 + Math.random();
  }
}

// Collection class to manage groups of artworks
class Collection {
  constructor(id, name, description, curator, artworks = []) {
    this.id = id;
    this.name = name;
    this.description = description;
    this.curator = curator;
    this.artworks = artworks;
    this.createdAt = new Date();
    this.isSpecial = false;
  }

  addArtwork(artwork) {
    this.artworks.push(artwork);
    console.log(`Added ${artwork.title} to ${this.name} collection`);
    return this.artworks;
  }

  removeArtwork(artworkId) {
    this.artworks = this.artworks.filter((art) => art.id !== artworkId);
    console.log(`Removed artwork from ${this.name} collection`);
    return this.artworks;
  }

  getTotalValue() {
    return this.artworks.reduce((sum, art) => sum + art.price, 0);
  }
}

// Fair class to manage art fairs
class Fair {
  constructor(id, name, location, startDate, endDate, organizer) {
    this.id = id;
    this.name = name;
    this.location = location;
    this.startDate = startDate;
    this.endDate = endDate;
    this.organizer = organizer;
    this.artists = [];
    this.isApproved = false;
    this.reviews = [];
  }

  addArtist(artist) {
    this.artists.push(artist);
    console.log(`${artist.name} added to ${this.name} fair`);
    return this.artists;
  }

  addReview(user, rating, comment) {
    const review = {
      user,
      rating,
      comment,
      date: new Date(),
    };
    this.reviews.push(review);
    console.log(`Review added to ${this.name} fair`);
    return this.reviews;
  }

  getAverageRating() {
    if (this.reviews.length === 0) return 0;
    const sum = this.reviews.reduce(
      (total, review) => total + review.rating,
      0
    );
    return sum / this.reviews.length;
  }
}

// Gift card class
class GiftCard {
  constructor(id, amount, code, style) {
    this.id = id;
    this.amount = amount;
    this.code = code;
    this.style = style; // one of four styles
    this.createdAt = new Date();
    this.isRedeemed = false;
    this.redeemedBy = null;
    this.redeemedAt = null;
  }

  redeem(user) {
    if (this.isRedeemed) {
      console.log("Gift card already redeemed");
      return false;
    }

    this.isRedeemed = true;
    this.redeemedBy = user.id;
    this.redeemedAt = new Date();

    console.log(`Gift card redeemed by ${user.name} for $${this.amount}`);
    return true;
  }
}

// Auth Service for handling authentication
class AuthService {
  constructor() {
    this.users = [];
    this.currentUser = null;
  }

  register(name, email, password, role) {
    // Check if email is already registered
    if (this.users.some((user) => user.email === email)) {
      console.log("Email already registered");
      return null;
    }

    const id = `user-${Date.now()}`;
    let user;

    // Create appropriate user type based on role
    switch (role) {
      case "customer":
        user = new Customer(id, name, email, password);
        break;
      case "artist":
        user = new Artist(id, name, email, password);
        break;
      case "admin":
        user = new Admin(id, name, email, password);
        break;
      default:
        console.log("Invalid role");
        return null;
    }

    this.users.push(user);
    console.log(`New ${role} registered: ${name}`);
    return user;
  }

  login(email, password) {
    const user = this.users.find(
      (u) => u.email === email && u.password === password
    );

    if (user) {
      this.currentUser = user;
      user.login();
      return user;
    }

    console.log("Invalid credentials");
    return null;
  }

  logout() {
    if (this.currentUser) {
      this.currentUser.logout();
      this.currentUser = null;
      return true;
    }

    console.log("No user is currently logged in");
    return false;
  }

  getCurrentUser() {
    return this.currentUser;
  }

  isLoggedIn() {
    return !!this.currentUser;
  }
}

// DataService for handling data operations (simulating a backend)
class DataService {
  constructor() {
    this.artworks = [];
    this.collections = [];
    this.fairs = [];
    this.giftCards = [];
    this.offers = [];
  }

  // Artwork methods
  addArtwork(artwork) {
    this.artworks.push(artwork);
    return artwork;
  }

  getArtworks(filters = {}) {
    let filteredArtworks = [...this.artworks];

    // Apply filters
    if (filters.category) {
      filteredArtworks = filteredArtworks.filter(
        (art) => art.category === filters.category
      );
    }

    if (filters.minPrice !== undefined) {
      filteredArtworks = filteredArtworks.filter(
        (art) => art.price >= filters.minPrice
      );
    }

    if (filters.maxPrice !== undefined) {
      filteredArtworks = filteredArtworks.filter(
        (art) => art.price <= filters.maxPrice
      );
    }

    if (filters.artist) {
      filteredArtworks = filteredArtworks.filter(
        (art) => art.artist.id === filters.artist.id
      );
    }

    // Sort results
    if (filters.sortBy) {
      switch (filters.sortBy) {
        case "price-asc":
          filteredArtworks.sort((a, b) => a.price - b.price);
          break;
        case "price-desc":
          filteredArtworks.sort((a, b) => b.price - a.price);
          break;
        case "date-asc":
          filteredArtworks.sort((a, b) => a.createdAt - b.createdAt);
          break;
        case "date-desc":
          filteredArtworks.sort((a, b) => b.createdAt - a.createdAt);
          break;
        case "popularity":
          filteredArtworks.sort((a, b) => b.likes - a.likes);
          break;
      }
    }

    return filteredArtworks;
  }

  searchArtworks(query) {
    const searchTerm = query.toLowerCase();
    return this.artworks.filter(
      (art) =>
        art.title.toLowerCase().includes(searchTerm) ||
        art.description.toLowerCase().includes(searchTerm) ||
        art.artist.name.toLowerCase().includes(searchTerm) ||
        art.category.toLowerCase().includes(searchTerm)
    );
  }

  // Collection methods
  addCollection(collection) {
    this.collections.push(collection);
    return collection;
  }

  getCollections(filters = {}) {
    let filteredCollections = [...this.collections];

    if (filters.curator) {
      filteredCollections = filteredCollections.filter(
        (col) => col.curator.id === filters.curator.id
      );
    }

    if (filters.isSpecial !== undefined) {
      filteredCollections = filteredCollections.filter(
        (col) => col.isSpecial === filters.isSpecial
      );
    }

    return filteredCollections;
  }

  // Fair methods
  addFair(fair) {
    this.fairs.push(fair);
    return fair;
  }

  getFairs(filters = {}) {
    let filteredFairs = [...this.fairs];

    if (filters.location) {
      filteredFairs = filteredFairs.filter(
        (fair) =>
          fair.location.city === filters.location.city ||
          fair.location.country === filters.location.country
      );
    }

    if (filters.isApproved !== undefined) {
      filteredFairs = filteredFairs.filter(
        (fair) => fair.isApproved === filters.isApproved
      );
    }

    // Filter for upcoming fairs
    if (filters.upcoming) {
      const now = new Date();
      filteredFairs = filteredFairs.filter(
        (fair) => new Date(fair.startDate) > now
      );
    }

    return filteredFairs;
  }

  // Gift card methods
  createGiftCard(amount, style) {
    const id = `giftcard-${Date.now()}`;
    const code = this.generateGiftCardCode();
    const giftCard = new GiftCard(id, amount, code, style);

    this.giftCards.push(giftCard);
    return giftCard;
  }

  redeemGiftCard(code, user) {
    const giftCard = this.giftCards.find(
      (card) => card.code === code && !card.isRedeemed
    );

    if (giftCard) {
      return giftCard.redeem(user);
    }

    console.log("Invalid or already redeemed gift card");
    return false;
  }

  generateGiftCardCode() {
    // Generate a random 16-character code
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let code = "";
    for (let i = 0; i < 16; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
  }

  // Offer methods
  createOffer(percentage, startDate, endDate) {
    if (percentage > 15) {
      console.log("Offers cannot exceed 15%");
      percentage = 15;
    }

    const offer = {
      id: `offer-${Date.now()}`,
      percentage,
      startDate,
      endDate,
      isActive: true,
    };

    this.offers.push(offer);
    return offer;
  }

  getActiveOffers() {
    const now = new Date();
    return this.offers.filter(
      (offer) =>
        offer.isActive &&
        new Date(offer.startDate) <= now &&
        new Date(offer.endDate) >= now
    );
  }
}

// StripeService to handle Stripe payments
class StripeService {
  constructor(apiKey) {
    this.apiKey = apiKey;
    console.log("Stripe service initialized");
  }

  processPayment(amount, currency, cardDetails) {
    // Simulate Stripe API call
    console.log(`Processing payment of ${amount} ${currency}`);

    // In a real app, this would make an API call to Stripe
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        // Simulate successful payment 90% of the time
        if (Math.random() < 0.9) {
          resolve({
            success: true,
            transactionId: `txn_${Date.now()}`,
            amount,
            currency,
          });
        } else {
          reject({
            success: false,
            error: "Payment failed",
            code: "card_declined",
          });
        }
      }, 1000);
    });
  }

  createCustomer(user, cardDetails) {
    // Simulate creating a Stripe customer
    console.log(`Creating Stripe customer for ${user.name}`);

    return {
      customerId: `cus_${Date.now()}`,
      user: user.id,
      createdAt: new Date(),
    };
  }
}

// Initialize services (this would normally be done in an initialization file)
const authService = new AuthService();
const dataService = new DataService();
const stripeService = new StripeService("fake-api-key");

// Add some demo users
authService.register("John Doe", "john@example.com", "password123", "customer");
authService.register(
  "Jane Artist",
  "jane@example.com",
  "password123",
  "artist"
);
authService.register("Admin User", "admin@artshelf.com", "admin123", "admin");

// Export services for use in other files
// In a real application, you'd use module exports
window.authService = authService;
window.dataService = dataService;
window.stripeService = stripeService;

// Document ready function (using jQuery)
$(document).ready(function () {
  console.log("ArtShelf application initialized");

  // Initialize UI components
  initializeUI();

  // Setup event listeners
  setupEventListeners();
});

// Initialize UI components
function initializeUI() {
  // Check if user is logged in and update UI accordingly
  updateAuthUI();

  // Initialize tooltips, popovers, etc.
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover();

  // Smooth scrolling for anchor links
  $('a[href*="#"]').on("click", function (e) {
    if (this.hash !== "") {
      e.preventDefault();
      const hash = this.hash;
      $("html, body").animate(
        {
          scrollTop: $(hash).offset().top,
        },
        800,
        function () {
          window.location.hash = hash;
        }
      );
    }
  });
}

// Setup event listeners
function setupEventListeners() {
  // Login form submission
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    const email = $("#email").val();
    const password = $("#password").val();

    const user = authService.login(email, password);
    if (user) {
      // Redirect based on user role
      switch (user.role) {
        case "customer":
          window.location.href = "customer-dashboard.html";
          break;
        case "artist":
          window.location.href = "artist-dashboard.html";
          break;
        case "admin":
          window.location.href = "admin-dashboard.html";
          break;
      }
    } else {
      showError("Invalid email or password");
    }
  });

  // Registration form submission
  $("#registerForm").on("submit", function (e) {
    e.preventDefault();
    const name = $("#name").val();
    const email = $("#email").val();
    const password = $("#password").val();
    const role = $('input[name="role"]:checked').val();

    const user = authService.register(name, email, password, role);
    if (user) {
      showSuccess("Registration successful! Please log in.");
      setTimeout(() => {
        window.location.href = "login.html";
      }, 2000);
    } else {
      showError("Registration failed. Email may already be in use.");
    }
  });

  // Logout button
  $(".logout-btn").on("click", function (e) {
    e.preventDefault();
    authService.logout();
    window.location.href = "index.html";
  });
}

// Update UI based on authentication state
function updateAuthUI() {
  const isLoggedIn = authService.isLoggedIn();

  if (isLoggedIn) {
    $(".logged-in").show();
    $(".logged-out").hide();

    const user = authService.getCurrentUser();
    $(".user-name").text(user.name);
  } else {
    $(".logged-in").hide();
    $(".logged-out").show();
  }
}

// Helper function to show errors
function showError(message) {
  const alertHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

  $("#alerts").html(alertHTML);

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    $(".alert").alert("close");
  }, 5000);
}

// Helper function to show success messages
function showSuccess(message) {
  const alertHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

  $("#alerts").html(alertHTML);

  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    $(".alert").alert("close");
  }, 5000);
}
