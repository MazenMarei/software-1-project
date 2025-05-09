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
    <!-- Artist Sidebar -->
    <?php require_once VIEWS . "components/artist_sidebar.php"; ?>
    <!-- Main Content -->
    <main class="artist-content">
        <?php $pageTitle = "Artist Dashboard";
        require_once VIEWS . "components/artist_header.php"; ?>

        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value" id="stats-earnings">$ <?php echo $artist->getBalance(); ?></div>
                <div class="stat-label">Balance </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-value" id="stats-artworks">0</div>
                <div class="stat-label">Pending Withdraws</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-value" id="stats-sales">0</div>
                <div class="stat-label">Accepted Withdraws</div>
            </div>

        </div>

        <!-- Include error display component -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <div class="withdrawal-history-container mt-4">
            <h2 class="fs-1 mb-1">Selling History</h2>
            <p class="text-muted mb-4">View your sold Artworks</p>

            <div class="table-container">
                <div class="table-header">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover search-table" id="artworksTable">
                        <thead>
                            <tr>
                                <th>Artwok</th>
                                <th>User</th>
                                <th>Price</th>
                                <th>Selling Date</th>
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
                                            <div class="d-flex align-items-center">

                                                <div class="d-flex align-items-center">
                                                    <img src="/uploads/artworks/<?php echo $order['images']; ?>" alt="Artwork Image" class="img-thumbnail me-4" style="width: 170px; height: 120px; object-fit: cover;" />
                                                    <div>
                                                        <div class="fw-bold d-none d-md-flex"><?= htmlspecialchars($order['title']) ?></div>
                                                    </div>
                                                </div>

                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center my-4">
                                                <img src="/uploads/profiles/<?php echo $order["customerProfilePic"]; ?>" alt="Customer Avatar" class="small-avatar me-3" />
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($order['customerUsername']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center mt-5">
                                                <div class="fw-bold">$<?php echo $order['price']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center mt-5">
                                                <div class="fw-bold"><?php echo $order['orderDate']; ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge mt-5 <?php echo $order["status"]; ?>">
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