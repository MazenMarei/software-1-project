<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="../../public/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../public/assets/css/main.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../public/assets/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/customer/dashboard">
                <img src="../../public/assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/customer/dashboard">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/discover">Discover</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/collections">Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/fairs">Art Fairs</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">New artwork from artists you follow</a></li>
                            <li><a class="dropdown-item" href="#">Special collection released today</a></li>
                            <li><a class="dropdown-item" href="#">Your order has been shipped</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-center" href="/customer/notifications">View all</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary rounded-pill d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2">
                                <?php echo isset($_SESSION['user']['email']) ? explode('@', $_SESSION['user']['email'])[0] : 'Account'; ?>
                            </span>
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/customer/profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="/customer/favorites">Favorites</a></li>
                            <li><a class="dropdown-item" href="/customer/orders">Order History</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="/logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-5">
        <!-- Display errors/success messages -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <div class="row mb-5">
            <div class="col-md-8">
                <h1 class="display-4 mb-4">Welcome back, <?php echo isset($_SESSION['user']['first_name']) ? $_SESSION['user']['first_name'] : 'Art Lover'; ?>!</h1>
                <p class="lead text-muted">Discover curated artworks handpicked for your collection.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex justify-content-md-end align-items-center">
                    <a href="/customer/cart" class="btn btn-outline-accent position-relative me-3">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-accent">
                            2
                        </span>
                    </a>
                    <button class="btn btn-primary rounded-pill">
                        <i class="fas fa-search me-2"></i> Find Art
                    </button>
                </div>
            </div>
        </div>

        <!-- Weekly Featured Collection -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>This Week's Featured Collection</h2>
                <a href="/customer/collections/featured" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x400" class="card-img-top" alt="Artwork">
                        <div class="card-body">
                            <h5 class="card-title">Summer Breeze</h5>
                            <p class="card-text text-muted">Jane Doe</p>
                            <p class="card-text fw-bold">$1,200</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary"><i class="far fa-heart"></i> Save</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
                <!-- Additional artwork cards would go here -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x400" class="card-img-top" alt="Artwork">
                        <div class="card-body">
                            <h5 class="card-title">Urban Landscape</h5>
                            <p class="card-text text-muted">John Smith</p>
                            <p class="card-text fw-bold">$950</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary"><i class="far fa-heart"></i> Save</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x400" class="card-img-top" alt="Artwork">
                        <div class="card-body">
                            <h5 class="card-title">Abstract Thoughts</h5>
                            <p class="card-text text-muted">Maria Garcia</p>
                            <p class="card-text fw-bold">$1,500</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary"><i class="far fa-heart"></i> Save</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x400" class="card-img-top" alt="Artwork">
                        <div class="card-body">
                            <h5 class="card-title">Nature's Whisper</h5>
                            <p class="card-text text-muted">Robert Chen</p>
                            <p class="card-text fw-bold">$2,100</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary"><i class="far fa-heart"></i> Save</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Artists You Follow -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Artists You Follow</h2>
                <a href="/customer/following" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mt-4" width="100" height="100" alt="Artist">
                            <span class="position-absolute top-0 end-0 p-2">
                                <i class="fas fa-certificate text-primary"></i>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Jane Doe</h5>
                            <p class="card-text text-muted">Abstract Expressionism</p>
                            <p class="card-text"><small>125 Artworks • 3.2k Followers</small></p>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">Following</button>
                        </div>
                    </div>
                </div>
                <!-- More artist cards would go here -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mt-4" width="100" height="100" alt="Artist">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">John Smith</h5>
                            <p class="card-text text-muted">Contemporary Realism</p>
                            <p class="card-text"><small>87 Artworks • 2.1k Followers</small></p>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">Following</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center">
                        <div class="position-relative">
                            <img src="https://via.placeholder.com/150" class="rounded-circle mt-4" width="100" height="100" alt="Artist">
                            <span class="position-absolute top-0 end-0 p-2">
                                <i class="fas fa-certificate text-primary"></i>
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Maria Garcia</h5>
                            <p class="card-text text-muted">Minimalism</p>
                            <p class="card-text"><small>54 Artworks • 4.7k Followers</small></p>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">Following</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-dashed">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="fas fa-search fa-2x mb-3 text-muted"></i>
                            <h5>Discover New Artists</h5>
                            <p class="text-muted text-center">Find more artists based on your preferences</p>
                            <a href="/customer/discover/artists" class="btn btn-primary mt-2">Explore</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Art Advisor -->
        <section class="bg-light p-4 rounded-lg mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3>Need help finding the perfect piece?</h3>
                    <p class="mb-md-0">Our art advisors can help you discover artwork tailored to your space and style preferences.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="/customer/art-advisor" class="btn btn-outline-dark">Consult an Art Advisor</a>
                </div>
            </div>
        </section>

        <!-- Recently Viewed -->
        <section>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Recently Viewed</h2>
                <a href="/customer/history" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                <!-- Recently viewed artwork cards would go here -->
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/300x400" class="card-img-top" alt="Artwork">
                        <div class="card-body">
                            <h5 class="card-title">Moonlit River</h5>
                            <p class="card-text text-muted">Sarah Johnson</p>
                            <p class="card-text fw-bold">$890</p>
                        </div>
                        <div class="card-footer bg-white border-0 d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary"><i class="far fa-heart"></i> Save</button>
                            <button class="btn btn-sm btn-primary">View</button>
                        </div>
                    </div>
                </div>
                <!-- More recently viewed items would go here -->
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <img src="../../public/assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40" class="mb-4">
                    <p>Discover, buy, and sell exceptional art from around the world.</p>
                    <div class="social-links mt-3">
                        <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Explore</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">New Arrivals</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Collections</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Artists</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Art Fairs</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>About</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Our Story</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Team</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Careers</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Press</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5>Support</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">FAQs</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Contact Us</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Shipping</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Returns</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5>Legal</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Terms</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Privacy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Cookies</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Accessibility</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">&copy; 2025 ArtShelf. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">
                        <img src="https://via.placeholder.com/30x20" alt="Visa" class="me-2">
                        <img src="https://via.placeholder.com/30x20" alt="Mastercard" class="me-2">
                        <img src="https://via.placeholder.com/30x20" alt="American Express" class="me-2">
                        <img src="https://via.placeholder.com/30x20" alt="PayPal">
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Popper.js (required for Bootstrap dropdowns) -->
    <script src="./assets/js/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="./assets/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <script src="./assets/js/jquery-3.7.1.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/main.js"></script>
</body>

</html>