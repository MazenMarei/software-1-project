<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Artist Withdraw | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- Custom CSS for Artist Dashboard -->
    <link rel="stylesheet" href="/assets/css/main.css" />
    <link rel="stylesheet" href="/assets/css/artist.dashboard.css" />
    <link rel="stylesheet" href="/assets/css/admin.dashboard.css" />


    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../assets/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

</head>

<body>
    <!-- Admin Sidebar -->
    <?php require_once VIEWS . 'components/admin_sideBar.php'; ?>

    <!-- Main Content -->
    <main class="admin-content">
        <?php
        // Set page title for admin header
        $pageTitle = 'Admin Logs';
        // Include admin header component
        require_once VIEWS . 'components/admin_header.php';
        ?>

        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- statistics -->
        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value" id="stats-artworks"><?php echo $totalOrders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <div class="stat-value" id="stats-sales"><?php echo $totalItems; ?></div>
                <div class="stat-label">Total Artworks</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value" id="stats-earnings">$ <?php echo $totalPrice ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <!-- Include error display component -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <div class="withdrawal-history-container mt-4">
            <h2 class="fs-1 mb-1">Selling History</h2>
            <p class="text-muted mb-4">All the Selling Logs in the site</p>

            <div class="table-container">
                <div class="table-header">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover search-table" id="artworksTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total Price</th>
                                <th>Total Items</th>
                                <th>Order Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="artworksTableBody">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No Withdraws found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <div class="my-3">
                                                <h3> <?php echo $order['orderID']; ?></h3>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center my-2">
                                                <img src="/uploads/profiles/<?php echo $order['customerPic']; ?>" alt="Customer Avatar" class="small-avatar me-3" />
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($order['Fname'] . ' ' . $order['Lname']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($order['username'])   ?></small>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex align-items-center my-4">
                                                <span class="fw-bold">$<?php echo number_format($order['totalPrice'], 2); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center my-4">
                                                <span class="fw-bold"><?php echo $order['totalItems']; ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center my-4">
                                                <span class="fw-bold"><?php echo $order['orderDate']; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge my-4 <?php echo $order['orderStatus']; ?>">
                                                <?php echo $order['orderStatus']; ?>
                                            </span>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </main>
    <!-- Popper.js (required for Bootstrap dropdowns) -->
    <script src="../../assets/js/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="../../assets/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <script src="../../assets/js/jquery-3.7.1.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom JS -->
    <script src="../../assets/js/admin/artists.js"></script>
    <script src="../../assets/js/admin.js"></script>
</body>

</html>