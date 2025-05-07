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

        <!-- Recent Activity -->
        <!-- <div class="row">
            <div class="col-lg-6">
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Pending Approvals</h2>
                        <a href="/admin/approvals" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <?php if (empty($pendingApprovals)): ?>
                        <div class="alert alert-info mt-3">No pending approvals at this time.</div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Submitted By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingApprovals as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['type']); ?></td>
                                        <td><?php echo htmlspecialchars($item['submittedBy']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-success btn-sm action-btn approve-btn"
                                                    data-id="<?php echo $item['id']; ?>"
                                                    data-type="<?php echo $item['type']; ?>"
                                                    data-name="<?php echo htmlspecialchars($item['name']); ?>">
                                                    Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm action-btn reject-btn"
                                                    data-id="<?php echo $item['id']; ?>"
                                                    data-type="<?php echo $item['type']; ?>"
                                                    data-name="<?php echo htmlspecialchars($item['name']); ?>">
                                                    Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Recent Orders</h2>
                        <a href="/admin/orders" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <?php if (empty($recentOrders)): ?>
                        <div class="alert alert-info mt-3">No recent orders at this time.</div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                        <td>$<?php echo number_format($order['amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div> -->

        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> ArtShelf Admin Dashboard. All rights reserved.</p>
        </footer>
    </main>

    <!-- Approval Modal -->
    <!-- <div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalTitle">Approve Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/admin/approve-item" method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to approve <strong id="approvalItemName"></strong>?</p>
                        <input type="hidden" name="id" id="approvalItemId">
                        <input type="hidden" name="type" id="approvalItemType">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

    <!-- Rejection Modal -->
    <!-- <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalTitle">Reject Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/admin/reject-item" method="POST">
                    <div class="modal-body">
                        <p>Are you sure you want to reject <strong id="rejectionItemName"></strong>?</p>
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                            <textarea class="form-control" id="rejectionReason" name="reason" rows="3" required></textarea>
                            <div class="form-text">This reason will be sent to the submitter.</div>
                        </div>
                        <input type="hidden" name="id" id="rejectionItemId">
                        <input type="hidden" name="type" id="rejectionItemType">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->

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

        // Approval button handler
        $('.approve-btn').on('click', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const name = $(this).data('name');

            $('#approvalItemId').val(id);
            $('#approvalItemType').val(type);
            $('#approvalItemName').text(name);

            const approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
            approvalModal.show();
        });

        // Rejection button handler
        $('.reject-btn').on('click', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const name = $(this).data('name');

            $('#rejectionItemId').val(id);
            $('#rejectionItemType').val(type);
            $('#rejectionItemName').text(name);

            const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
            rejectionModal.show();
        });

    </script>
</body>

</html>