    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Artist Dashboard | ArtShelf</title>
        <!-- Bootstrap CSS -->
        <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="../../assets/css/main.css">
        <!-- Custom CSS for Artist Dashboard -->
        <link rel="stylesheet" href="../../assets/css/artist.dashboard.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="../../assets/css/all.min.css">
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

        <style>
            /* Artist Dashboard Styles */
        </style>
    </head>

    <body>

        <!-- Artist Sidebar -->
        <?php require_once VIEWS . "components/artist_sidebar.php"; ?>
        <!-- Main Content -->
        <main class="artist-content">
            <?php $pageTitle = "Artist Dashboard";
            require_once VIEWS . "components/artist_header.php"; ?>

            <?php require_once VIEWS . 'components/error_display.php'; ?>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="stat-value" id="stats-artworks"><?php echo count($artworks); ?></div>
                    <div class="stat-label">Total Artworks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value" id="stats-sales"><?php echo $totalSales; ?></div>
                    <div class="stat-label">Total Sales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="stat-value" id="stats-views"><?php echo $reviewsAvg; ?> ( <?php echo $totalReviews ?> )</div>
                    <div class="stat-label">Avg Reviews</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-value" id="stats-earnings">$<?php echo $totalEarnings; ?></div>
                    <div class="stat-label">Total Earnings</div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">Sales & Views Overview</h2>
                </div>
                <canvas id="salesChart" style="max-height: 300px"></canvas>
            </div>

            <!-- Recent Activity -->

            <!-- <div class="row mt-4">

                <div class="col-lg-6">
                    <div class="table-container">
                        <div class="table-header">
                            <h2 class="table-title">Notifications</h2>
                            <button class="btn btn-outline-primary btn-sm">
                                Mark All Read
                            </button>
                        </div>
                        <ul id="notifications-list" class="notification-list">
                        </ul>
                    </div>
                </div>
            </div> -->

            <div class="row mt-4">
                <div class="col-lg-6">
                    <a id="upload-artwork-btn" href="/artist/new-artwork" class="btn btn-primary w-100 mb-4">
                        <i class="fas fa-plus-circle me-2"></i>Upload New Artwork
                    </a>
                </div>
                <div class="col-lg-6">
                    <a
                        id="create-collection-btn"
                        href="/artist/collections"
                        class="btn btn-outline-primary w-100 mb-4">
                        <i class="fas fa-folder-plus me-2"></i>Create New Collection
                    </a>
                </div>
            </div>

            <!-- Artist Footer -->
            <footer class="artist-footer">
                <p>&copy; 2025 ArtShelf Artist Dashboard. All rights reserved.</p>
            </footer>
        </main>

        <!-- Popper.js (required for Bootstrap dropdowns) -->
        <script src="../assets/js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="../assets/js/bootstrap.min.js"></script>
        <!-- jQuery -->
        <script src="../assets/js/jquery-3.7.1.min.js"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
                    // Initialize sales chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($salesData['labels']); ?>,
                datasets: [{
                    label: 'Sales',
                    data: <?php echo json_encode($salesData['data']); ?>,
                    borderColor: '#C5A992',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
        </script>
    </body>

    </html>