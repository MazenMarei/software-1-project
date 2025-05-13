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
    <?php

    use App\models\Customer;

    require_once VIEWS . "components/admin_sidebar.php"; ?>
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
                <div class="stat-value" id="stats-earnings"><?php echo $totalResponses; ?></div>
                <div class="stat-label">Total Requests</div>
            </div>

        </div>


        <div class="withdrawal-history-container mt-4">
            <h2 class="fs-1 mb-1">Advice Requests</h2>
            <p class="text-muted mb-4">View customers' advice requests History</p>

            <div class="table-container">
                <div class="table-header">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover search-table" id="artworksTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Budget</th>
                                <th>Art Type</th>
                                <th>Art Style</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="artworksTableBody">
                            <?php if (empty($questionnaires)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No Requests found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($questionnaires as $questionnaire): ?>
                                    <?php $customer = (new Customer())->getCustomerById($questionnaire->getCustomerID()); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="/uploads/profiles/<?php echo $customer->getProfilePic(); ?>" alt="Customer Image" class="rounded-circle me-2" width="30" height="30" />
                                                <span class="customer-name"><?php echo htmlspecialchars($customer->getFirstName() . " " . $customer->getLastName()); ?></span>
                                            </div>
                                        </td>
                                        <td>$<?php echo ($questionnaire->getBudget()); ?></td>
                                        <td><?php echo htmlspecialchars($questionnaire->getArtType()); ?></td>
                                        <td><?php echo htmlspecialchars($questionnaire->getArtStyle()); ?></td>
                                        <td><?php echo htmlspecialchars($questionnaire->getSubmitDate()); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo strtolower($questionnaire->getStatus()); ?>">
                                                <?php echo $questionnaire->getStatus(); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($questionnaire->getStatus() === 'pending'): ?>
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#responseModal<?php echo $questionnaire->getQuestionnaireID(); ?>">Response</button>
                                            <?php endif; ?>
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

    <!-- Response Modal -->
    <?php foreach ($questionnaires as $questionnaire): ?>
        <div class="modal fade" id="responseModal<?php echo $questionnaire->getQuestionnaireID(); ?>" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="responseModalLabel">Response to <?php echo htmlspecialchars($customer->getFirstName() . " " . $customer->getLastName()); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="responseForm<?php echo $questionnaire->getQuestionnaireID(); ?>" method="POST" action="/admin/questionnaire/response">
                        <input type="hidden" name="questionnaireID" value="<?php echo $questionnaire->getQuestionnaireID(); ?>">
                        <input type="hidden" name="customerID" value="<?php echo $questionnaire->getCustomerID  (); ?>">
                        <div class="mb-3">
                            <label for="response" class="form-label">Response</label>
                            <textarea class="form-control" id="response" name="response" rows="4" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Response</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

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