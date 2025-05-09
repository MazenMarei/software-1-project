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
        <?php $pageTitle = "Artist Notifications";
        require_once VIEWS . "components/artist_header.php"; ?>

        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- Notifications Table -->
        <h3>Total Notifications: <?php echo $totalNotifications; ?></h3>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">All Notifications</h2>

            </div>
            <div class="table-responsive">
                <table class="table table-hover search-table" id="customersTable">

                    <thead>
                        <tr>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (isset($notifications) && !empty($notifications)) : ?>
                            <?php foreach ($notifications as $notification) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center my-2 w-100">
                                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                <i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($notification['Message']); ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex my-4"><?php echo htmlspecialchars($notification['datesent']); ?></div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center">No Followers found.</td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
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