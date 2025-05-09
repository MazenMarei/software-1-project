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
    <?php require_once VIEWS . "components/admin_sidebar.php"; ?>
    <!-- Main Content -->
    <main class="artist-content">
        <?php $pageTitle = "Admin Dashboard";
        require_once VIEWS . "components/admin_header.php"; ?>

        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-value" id="stats-earnings">$ <?php echo $totalWithdrawals; ?></div>
                <div class="stat-label">Total Withdrawals</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-value" id="stats-artworks"><?php echo $pending ?></div>
                <div class="stat-label">Pending Withdraws</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-value" id="stats-sales"><?php echo $accepted ?></div>
                <div class="stat-label">Accepted Withdraws</div>
            </div>

        </div>


        <div class="withdrawal-history-container mt-4">
            <h2 class="fs-1 mb-1">Withdrawal History</h2>
            <p class="text-muted mb-4">View System withdrawal requests History</p>

            <div class="table-container">
                <div class="table-header">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover search-table" id="artworksTable">
                        <thead>
                            <tr>
                                <th>Artist</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="artworksTableBody">
                            <?php if (empty($trnasactions)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No Withdraws found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($trnasactions as $withdrawal): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/uploads/profiles/<?php echo $withdrawal['artistProfilePic']; ?>" alt="Artist Image" class="rounded-circle me-2" width="30" height="30" />
                                                <span class="artist-name"><?php echo htmlspecialchars($withdrawal['artistFirstName'] . " " . $withdrawal["artistLastName"]); ?></span>
                                            </div>
                                        </td>
                                        <td>$<?php echo ($withdrawal['amount']); ?></td>
                                        <td><?php echo htmlspecialchars($withdrawal['transactionDate']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($withdrawal['status']); ?>">
                                                <?php echo $withdrawal['status']; ?>
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