<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/admin.dashboard.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Admin Sidebar -->
    <?php require_once VIEWS . 'components/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">
        <?php
        // Set page title for admin header
        $pageTitle = 'Dashboard Overview';
        // Include admin header component
        require_once VIEWS . 'components/admin_header.php';
        ?>

        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['totalArtworks']); ?></div>
                <div class="stat-label">Total Artworks</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['activeUsers']); ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['ordersThisMonth']); ?></div>
                <div class="stat-label">Orders This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value">$<?php echo number_format($stats['monthlyRevenue'], 2); ?></div>
                <div class="stat-label">Monthly Revenue</div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h2 class="chart-title">Sales Overview</h2>
            </div>
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>



        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h5 class="card-title">Special Offer Settings</h5>
            </div>
            <div class="card-body">
                <form action="/admin/updateOffer" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="discount" class="form-label">Discount Percentage:</label>
                            <div class="input-group">
                                <input type="number" min="0" max="20" class="form-control" id="discount"
                                    name="discount" value="<?php echo htmlspecialchars($offer->getDiscount()); ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Set the discount percentage for all artworks (0-20%)</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="enableOffer" name="enableOffer" value="1"
                                    <?php echo $offer->isEnabled() ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enableOffer">Enable Special Offer</label>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Offer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> ArtShelf Admin Dashboard. All rights reserved.</p>
        </footer>
    </main>

    <!-- Popper.js (required for Bootstrap dropdowns) -->
    <script src="/assets/js/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="/assets/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <script src="/assets/js/jquery-3.7.1.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Admin JS (handles common admin UI functionality) -->
    <script src="/assets/js/admin.js"></script>
    <!-- Custom Scripts -->
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