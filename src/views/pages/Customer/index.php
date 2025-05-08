<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link
        href="../../assets/css/bootstrap.min.css"
        rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css" />
    <link rel="stylesheet" href="../../assets/css/admin.dashboard.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/css/all.min.css" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.html">
                <img src="../../assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.html">Discover</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="artists.html">Artists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="collections.html">Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fairs.html">Art Fairs</a>
                    </li>
                </ul>
                <div class="navbar-right d-flex align-items-center">
                    <div class="search-container me-3">
                        <button class="btn-search">
                            <i class="fa fa-search"></i>
                        </button>
                        <div class="search-dropdown">
                            <form class="search-form">
                                <input type="text" class="form-control" placeholder="Search artworks, artists...">
                                <button type="submit" class="btn-search-submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="dropdown me-3">
                        <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-heart"></i>
                            <span class="badge bg-accent">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Favorites</h6>
                            <div class="favorites-preview">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="favorites.html">View All Favorites</a>
                        </div>
                    </div>
                    <div class="dropdown me-3">
                        <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="badge bg-accent">1</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Shopping Cart</h6>
                            <div class="cart-preview">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="cart.html">View Cart</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-icon profile-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://images.pexels.com/photos/1681010/pexels-photo-1681010.jpeg?auto=compress&cs=tinysrgb&w=200" alt="Profile Picture" class="profile-picture">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">John Smith</h6>
                            <a class="dropdown-item" href="profile.html">
                                <i class="fa fa-user me-2"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="orders.html">
                                <i class="fa fa-shopping-bag me-2"></i> My Orders
                            </a>
                            <a class="dropdown-item" href="following.html">
                                <i class="fa fa-users me-2"></i> Following
                            </a>
                            <a class="dropdown-item" href="settings.html">
                                <i class="fa fa-cog me-2"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" onclick="window.auth.logout(); return false;">
                                <i class="fa fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <h1 class="hero-title">Discover Extraordinary Art</h1>
                            <p class="hero-subtitle">Find and collect unique artworks from emerging and established artists worldwide.</p>
                            <div class="hero-buttons">
                                <a href="#featured" class="btn btn-primary btn-lg">Explore Artworks</a>
                                <a href="#advisor" class="btn btn-outline-primary btn-lg ms-3">Get Art Advice</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-image" style="background-image: url('https://images.pexels.com/photos/1674049/pexels-photo-1674049.jpeg?auto=compress&cs=tinysrgb&w=1600');">
                <div class="overlay"></div>
            </div>
        </section>

        <!-- Featured Section -->
        <section id="featured" class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Featured Artworks</h2>
                    <a href="artworks.html" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row artwork-grid" id="featuredArtworks">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </section>

        <!-- Collections Section -->
        <section class="section bg-secondary">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Weekly Collections</h2>
                    <a href="collections.html" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row collections-slider" id="weeklyCollections">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </section>

        <!-- Artists Section -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Featured Artists</h2>
                    <a href="artists.html" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row artists-grid" id="featuredArtists">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </section>

        <!-- View in Room Feature -->
        <section class="section bg-secondary">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="feature-content">
                            <h2 class="feature-title">View Art in Your Space</h2>
                            <p class="feature-description">Our innovative "View in Room" feature lets you visualize how artwork will look on your walls before you purchase. Simply upload a photo of your room and try different pieces to find the perfect match.</p>
                            <a href="view-in-room.html" class="btn btn-primary">Try It Now</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="feature-image">
                            <img src="https://images.pexels.com/photos/1457842/pexels-photo-1457842.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="View in Room feature" class="img-fluid rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Art Advisor Section -->
        <section id="advisor" class="section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="feature-content">
                            <h2 class="feature-title">Personalized Art Advisory</h2>
                            <p class="feature-description">Not sure where to start? Our expert art advisors will help you find pieces that match your style, space, and budget. Complete a quick questionnaire and receive personalized recommendations.</p>
                            <a href="art-advisor.html" class="btn btn-primary">Get Art Advice</a>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="feature-image">
                            <img src="https://images.pexels.com/photos/7578989/pexels-photo-7578989.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Art Advisory service" class="img-fluid rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gift Cards Section -->
        <section class="section bg-secondary">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Give the Gift of Art</h2>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="gift-card-content">
                            <p class="lead">ArtShelf gift cards are the perfect present for art lovers. Choose from four elegant designs and denominations of $50, $100, $200, or $350.</p>
                            <ul class="gift-card-features">
                                <li><i class="fas fa-check text-accent"></i> Never expires</li>
                                <li><i class="fas fa-check text-accent"></i> Digital delivery</li>
                                <li><i class="fas fa-check text-accent"></i> Personalized message</li>
                                <li><i class="fas fa-check text-accent"></i> Redeemable for any artwork</li>
                            </ul>
                            <a href="gift-cards.html" class="btn btn-primary">Purchase Gift Card</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="gift-card-showcase">
                            <div class="gift-card">
                                <img src="https://images.pexels.com/photos/1092364/pexels-photo-1092364.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="ArtShelf Gift Card" class="img-fluid rounded-lg shadow-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="footer-logo">
                        <img src="../../assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40">
                    </div>
                    <p class="footer-description">Discover, buy, and sell extraordinary art from artists around the world.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-heading">Explore</h5>
                    <ul class="footer-links">
                        <li><a href="artworks.html">Artworks</a></li>
                        <li><a href="artists.html">Artists</a></li>
                        <li><a href="collections.html">Collections</a></li>
                        <li><a href="fairs.html">Art Fairs</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-heading">Account</h5>
                    <ul class="footer-links">
                        <li><a href="profile.html">My Profile</a></li>
                        <li><a href="favorites.html">Favorites</a></li>
                        <li><a href="orders.html">Orders</a></li>
                        <li><a href="settings.html">Settings</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-heading">Support</h5>
                    <ul class="footer-links">
                        <li><a href="../help/faq.html">FAQ</a></li>
                        <li><a href="../help/contact.html">Contact Us</a></li>
                        <li><a href="../help/shipping.html">Shipping</a></li>
                        <li><a href="../help/returns.html">Returns</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="footer-heading">Legal</h5>
                    <ul class="footer-links">
                        <li><a href="../legal/terms.html">Terms of Service</a></li>
                        <li><a href="../legal/privacy.html">Privacy Policy</a></li>
                        <li><a href="../legal/copyright.html">Copyright</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-lg-6">
                        <p class="copyright">&copy; 2023 ArtShelf. All rights reserved.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="payment-methods">
                            <span>Secured by Stripe</span>
                            <img src="../../assets/images/payment-methods.png" alt="Payment Methods" height="24">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->

    <!-- Stripe.js -->
    <!-- Custom Scripts -->

</body>

</html>