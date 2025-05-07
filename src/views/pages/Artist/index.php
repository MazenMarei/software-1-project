<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard | ArtShelf</title>
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
    <style>
        .border-dashed {
            border: 2px dashed #dee2e6;
        }

        .stats-card {
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .approval-pending {
            background-color: #fffff0;
            border-left: 4px solid #ebe898;
        }
    </style>
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/artist/dashboard">
                <img src="../../public/assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/artist/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/artist/artwork">My Artwork</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/artist/collections">Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/artist/fairs">Art Fairs</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                2
                                <span class="visually-hidden">unread notifications</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">New follower: Sarah J.</a></li>
                            <li><a class="dropdown-item" href="#">Your artwork "Ocean Dreams" was sold!</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-center" href="/artist/notifications">View all</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary rounded-pill d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2">
                                <?php echo isset($_SESSION['user']['email']) ? explode('@', $_SESSION['user']['email'])[0] : 'Artist'; ?>
                            </span>
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/artist/profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="/artist/sales">Sales History</a></li>
                            <li><a class="dropdown-item" href="/artist/earnings">Earnings</a></li>
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

        <?php
        // Check if artist account is pending approval
        $isPending = false;
        if (isset($_SESSION['user']) && isset($_SESSION['user']['status']) && $_SESSION['user']['status'] === 'Pending') {
            $isPending = true;
        }

        if ($isPending): ?>
            <div class="alert approval-pending p-4 mb-4">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h4>Account Pending Approval</h4>
                        <p>Your artist account is currently under review by our admin team. Once approved, you'll be able to publish artwork and access all artist features. This process usually takes 1-2 business days.</p>
                        <a href="#" class="btn btn-sm btn-outline-warning">Check Status</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-4 mb-3">Artist Dashboard</h1>
                <p class="lead text-muted">Manage your artwork, track sales, and grow your audience.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="/artist/artwork/new" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Upload New Artwork
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stats-card h-100 bg-light border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Total Artworks</h6>
                        <h2 class="mb-0">12</h2>
                        <p class="small text-success mt-2 mb-0">
                            <i class="fas fa-arrow-up me-1"></i> 2 this month
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100 bg-light border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Total Sales</h6>
                        <h2 class="mb-0">8</h2>
                        <p class="small text-success mt-2 mb-0">
                            <i class="fas fa-arrow-up me-1"></i> 3 this month
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100 bg-light border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Followers</h6>
                        <h2 class="mb-0">245</h2>
                        <p class="small text-success mt-2 mb-0">
                            <i class="fas fa-arrow-up me-1"></i> 18 this month
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card h-100 bg-light border-0">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-3">Revenue</h6>
                        <h2 class="mb-0">$5,210</h2>
                        <p class="small text-success mt-2 mb-0">
                            <i class="fas fa-arrow-up me-1"></i> $1,200 this month
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Recent Sales</h2>
                <a href="/artist/sales" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Artwork</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/50" class="me-3" alt="Artwork">
                                        <div>
                                            <h6 class="mb-0">Ocean Dreams</h6>
                                            <small class="text-muted">Oil on Canvas</small>
                                        </div>
                                    </div>
                                </td>
                                <td>May 5, 2025</td>
                                <td>Emily W.</td>
                                <td>$950</td>
                                <td><span class="badge bg-success">Delivered</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/50" class="me-3" alt="Artwork">
                                        <div>
                                            <h6 class="mb-0">City Lights</h6>
                                            <small class="text-muted">Acrylic on Canvas</small>
                                        </div>
                                    </div>
                                </td>
                                <td>April 28, 2025</td>
                                <td>Michael T.</td>
                                <td>$1,200</td>
                                <td><span class="badge bg-info">Shipped</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/50" class="me-3" alt="Artwork">
                                        <div>
                                            <h6 class="mb-0">Sunset Valley</h6>
                                            <small class="text-muted">Watercolor</small>
                                        </div>
                                    </div>
                                </td>
                                <td>April 15, 2025</td>
                                <td>Sarah J.</td>
                                <td>$780</td>
                                <td><span class="badge bg-success">Delivered</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Recent Activity -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Recent Activity</h2>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item d-flex mb-4">
                            <div class="timeline-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <p class="mb-1"><strong>Sarah J.</strong> started following you</p>
                                <p class="text-muted small mb-0">2 hours ago</p>
                            </div>
                        </div>
                        <div class="timeline-item d-flex mb-4">
                            <div class="timeline-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <p class="mb-1">Your artwork <strong>"Ocean Dreams"</strong> was sold!</p>
                                <p class="text-muted small mb-0">1 day ago</p>
                            </div>
                        </div>
                        <div class="timeline-item d-flex mb-4">
                            <div class="timeline-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="fas fa-comment"></i>
                            </div>
                            <div>
                                <p class="mb-1"><strong>John D.</strong> commented on your artwork <strong>"City Lights"</strong></p>
                                <p class="text-muted small mb-0">2 days ago</p>
                            </div>
                        </div>
                        <div class="timeline-item d-flex">
                            <div class="timeline-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <i class="fas fa-star"></i>
                            </div>
                            <div>
                                <p class="mb-1">Your artwork <strong>"Sunset Valley"</strong> was featured in the <strong>Weekly Highlights</strong> collection</p>
                                <p class="text-muted small mb-0">4 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upcoming Fairs -->
        <section>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Upcoming Art Fairs</h2>
                <a href="/artist/fairs" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/500x300" class="card-img-top" alt="Art Fair">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">Registered</span>
                                <small class="text-muted">May 15-20, 2025</small>
                            </div>
                            <h5 class="card-title">Spring Art Expo</h5>
                            <p class="card-text">New York Convention Center, NY</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/500x300" class="card-img-top" alt="Art Fair">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary">Open for Registration</span>
                                <small class="text-muted">June 10-15, 2025</small>
                            </div>
                            <h5 class="card-title">Contemporary Art Fair</h5>
                            <p class="card-text">Miami Beach Convention Center, FL</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="#" class="btn btn-sm btn-primary">Register Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-dashed">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center h-100 p-4">
                            <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                            <h5>Register Your Own Fair</h5>
                            <p class="text-muted text-center">Hosting a local art event? Register it on ArtShelf to get more visibility</p>
                            <a href="/artist/fairs/register" class="btn btn-primary mt-2">Register Fair</a>
                        </div>
                    </div>
                </div>
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
                    <h5>For Artists</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Why ArtShelf</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Pricing</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Resources</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Success Stories</a></li>
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
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Artist Guidelines</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Payments</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5>Legal</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Terms</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Privacy</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Copyright</a></li>
                        <li class="nav-item"><a href="#" class="nav-link px-0 text-muted">Seller Terms</a></li>
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
    <script src="./public/assets/js/main.js"></script>
</body>

</html>