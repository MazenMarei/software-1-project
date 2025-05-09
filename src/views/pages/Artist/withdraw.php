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
                <div class="stat-value" id="stats-artworks"><?php echo $pendingWithdrawals ?></div>
                <div class="stat-label">Pending Withdraws</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-value" id="stats-sales"><?php echo $acceptedWithdraw ?></div>
                <div class="stat-label">Accepted Withdraws</div>
            </div>

        </div>
        <div class="withdrawal-form-container">
            <h2 class="fs-1 mb-1">Withdraw Funds</h2>
            <p class="text-muted mb-4">Request a withdrawal of your earnings</p>

            <!-- Include error display component -->
            <?php require_once VIEWS . 'components/error_display.php'; ?>

            <form id="withdrawForm" class="needs-validation stat-card" action="/artist/withdraw" method="POST">
                <div class="mb-3">
                    <label for="amount" class="form-label fw-medium">Amount</label>
                    <input type="number" class="form-control form-control-lg" id="amount" name="amount"
                        step="1" value="<?php echo $artist->getBalance(); ?>"
                        placeholder="Enter amount to withdraw" required min="1">
                    <div class="invalid-feedback">
                        Please enter a valid amount.
                    </div>


                </div>
                <button type="submit" class="btn btn-primary btn-lg" <?php echo $artist->getBalance() > 30 ? "" : "disabled"; ?>>Request Withdrawal</button>
                <div class="mt-3">
                    <p class="text-muted">Note: Minimum withdrawal amount is $30.</p>
                    <p class="text-muted">Normal withdrawal takes 1 week to be accepted <strong>You can request an urgent withdrawal with $5 fees.</strong></p>
                </div>
            </form>
        </div>

        <div class="withdrawal-history-container mt-4">
            <h2 class="fs-1 mb-1">Withdrawal History</h2>
            <p class="text-muted mb-4">View your past withdrawal requests</p>

            <div class="table-container">
                <div class="table-header">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover search-table" id="artworksTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
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
                                        <td><?php echo htmlspecialchars($withdrawal['date']); ?></td>
                                        <td>$<?php echo ($withdrawal['amount']); ?></td>
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