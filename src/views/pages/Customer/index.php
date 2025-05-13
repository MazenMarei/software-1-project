<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../../assets/css/main.css" rel="stylesheet">
    <link href="../../../assets/css/customer.dashboard.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <?php require_once VIEWS . 'components/customer_navbard.php'; ?>

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
        <?php require_once VIEWS . 'components/error_display.php'; ?>
        <!-- Featured Section -->
        <section id="featured" class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Featured Artworks</h2>
                    <a href="artworks.html" class="btn btn-link">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row artwork-grid" id="featuredArtworks">
                    <?php if (empty($featuredArtworks)) : ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <h3 class="empty-state-title">No Artworks Found</h3>
                                <p class="empty-state-message">We're currently updating our featured artworks. Please check back soon!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($featuredArtworks as $artwork) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="artwork-card">
                                <div class="artwork-image">
                                    <img src="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                                        alt="<?php echo htmlspecialchars($artwork->getTitle()); ?>"
                                        class="img-fluid">

                                    <div class="artwork-actions">
                                        <form action="/" method="post">
                                            <button href="#" class="btn-circle btn-favorite" type="submit">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        </form>
                                        <button class="btn-circle btn-quickview"
                                            data-id="<?php echo $artwork->getArtworkId(); ?>"
                                            onclick="showQuickView(<?php echo $artwork->getArtworkId(); ?>); return false;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (in_array($artwork, $cartItems)) : ?>
                                            <form action="/customer/remove-from-cart/<?php echo $artwork->getArtworkId(); ?>" method="post">
                                                <button class="btn-circle btn-report" type="submit">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <form action="/customer/add-to-cart/<?php echo $artwork->getArtworkId(); ?>" method="post">
                                                <button class="btn-circle btn-report" type="submit">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Collections Section -->
        <section class="section bg-secondary">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title"><?php echo $specialCollections->getName(); ?></h2>
                </div>
                <div class="row collections-slider" id="weeklyCollections">
                    <?php if (empty($specialCollections->getArtworks())) : ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <h3 class="empty-state-title">No Artworks Found</h3>
                                <p class="empty-state-message">We're currently updating our featured collection. Please check back soon!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($specialCollections->getArtworks() as $artwork) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="artwork-card">
                                <div class="artwork-image">
                                    <img src="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                                        alt="<?php echo htmlspecialchars($artwork->getTitle()); ?>"
                                        class="img-fluid">

                                    <div class="artwork-actions">
                                        <form action="/" method="post">
                                            <button href="#" class="btn-circle btn-favorite" type="submit">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        </form>

                                        <form action="/" method="post">
                                            <button href="#" class="btn-circle btn-quickview" type="submit">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="artwork-info">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="/uploads/profiles/<?php echo htmlspecialchars($artwork->getArtist()->getProfilePic()); ?>"
                                                alt="<?php echo htmlspecialchars($artwork->getArtist()->getFirstName()); ?>"
                                                class="small-avatar" />
                                        </div>
                                        <div class="col">
                                            <h3 class="artwork-title"><?php echo htmlspecialchars($artwork->getTitle()); ?></h3>
                                            <p class="artwork-artist"><?php echo htmlspecialchars($artwork->getArtist()->getLastName()); ?></p>
                                        </div>
                                    </div>
                                    <div class="artwork-price">$<?php echo number_format($artwork->getPrice(), 2); ?></div>

                                    <div class="artwork-actions-bottom d-flex justify-content-start gap-3">
                                        <a href="artworks/<?php echo $artwork->getArtworkId(); ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                        <?php if (in_array($artwork, $cartItems)) : ?>
                                            <form action="/customer/remove-from-cart/<?php echo $artwork->getArtworkId(); ?>" method="post">
                                                <button class="btn btn-primary btn-sm" type="submit">Remove from Cart</button>
                                            </form>
                                        <?php else : ?>
                                            <form action="/customer/add-to-cart/<?php echo $artwork->getArtworkId(); ?>" method="post">
                                                <button class="btn btn-primary btn-sm" type="submit">Add to Cart</button>
                                            </form>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                    <?php if (empty($featuredArtists)) : ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fa-solid fa-people-group"></i>
                                </div>
                                <h3 class="empty-state-title">No Artists Found</h3>
                                <p class="empty-state-message">We're currently updating our featured artists. Please check back soon!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($featuredArtists as $artist) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="artist-card">
                                <img src="/uploads/profiles/<?php echo htmlspecialchars($artist->getProfilePic()); ?>"
                                    alt="<?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()); ?>"
                                    class="img-fluid artist-image">
                                <h3 class="artist-name"><?php echo htmlspecialchars($artist->getFirstName() . ' ' . $artist->getLastName()); ?></h3>
                                <p class="artist-bio"><?php echo htmlspecialchars($artist->getBio()); ?></p>
                                <div class="d-flex flex-row justify-content-center gap-3">
                                    <form action="/" method="post" class=""><button class="btn btn-primary">View Profile</button></form>
                                    <form action="/" method="post" class=""><button class="btn btn-outline-primary">Follow</button></form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                            <a href="live-preview" class="btn btn-primary">Try It Now</a>
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
                            <a href="art-advisor" class="btn btn-primary">Get Art Advice</a>
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
                                <img src="../../assets/images/Egift-2.webp" alt="ArtShelf Gift Card" class="img-fluid rounded-lg shadow-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main> <!-- footer -->
    <?php require_once VIEWS . 'components/customer_footer.php'; ?> <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="../../../assets/js/bootstrap.min.js"></script>
    <!-- Custom Scripts -->
    <script src="../../../assets/js/customer-dropdown.js"></script>
</body>

</html>