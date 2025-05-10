<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Artwork | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/main.css" />
    <link rel="stylesheet" href="/assets/css/artist.dashboard.css" />
    <link rel="stylesheet" href="/assets/css/admin.dashboard.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/css/all.min.css" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet" />
</head>

<body>
    <!-- Artist Sidebar -->
    <?php require_once VIEWS . 'components/artist_sidebar.php'; ?>



    <!-- Main Content -->
    <main class="artist-content">
        <!-- Admin Header -->
        <?php
        $pageTitle = 'Manage Art Fairs';
        require_once VIEWS . 'components/artist_header.php';
        ?>

        <!-- Display error/success messages -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>


        <!-- statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="stat-value" id="stats-earnings"><?php echo $totalFairs; ?></div>
                <div class="stat-label">Total Fairs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-value" id="stats-artworks"><?php echo $pendingFairs; ?></div>
                <div class="stat-label">Pending Fairs</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-value" id="stats-sales"><?php echo $acceptedFairs; ?></div>
                <div class="stat-label">Accepted Fairs</div>
            </div>

        </div>
        <!-- Filter Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="h5 mb-0">Filter Fairs</h2>
            </div>
            <div class="card-body">
                <form id="artworkFilterForm">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="categoryFilter" class="form-label">Category</label>
                            <select class="form-select" id="categoryFilter" name="category">
                                <option value="">All Government</option>
                                <?php foreach ($governments as $government): ?>
                                    <option value="<?php echo htmlspecialchars($government); ?>"><?php echo htmlspecialchars($government); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- local fair listing        -->
        <div class="row  flex-row-reverse mb-3">
            <div class="col-md-6 col-lg-4 justify-content-end d-flex">
                <button data-bs-toggle="modal" data-bs-target="#registerModal" class="btn btn-primary">Register New Art Fair</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Your Fairs</h3>
                <div class="table-filters">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover search-table" id="artworksTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Goverment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="artworksTableBody">
                        <?php if (empty($fairs)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No fairs found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($fairs as $fair): ?>
                                <tr>
                                    <td>
                                        <img
                                            src="/uploads/localfairs/<?php echo htmlspecialchars($fair->getImage()); ?>"
                                            alt="<?php echo htmlspecialchars($fair->getName()); ?>"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($fair->getName()); ?></td>
                                    <td><?php echo (explode(',', $fair->getLocation())[1]); ?></td>
                                    <td><?php echo (explode(',', $fair->getLocation())[0]); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($fair->getStatus()); ?>">
                                            <?php echo $fair->getStatus(); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($fair->getStartDate())); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button
                                                type="button"
                                                onclick="showDetails(this)"
                                                class="btn btn-outline-primary view-artwork-btn"
                                                data-id="<?php echo $fair->getId(); ?>"
                                                data-image="/localfairs/<?php echo htmlspecialchars($fair->getImage()); ?>"
                                                data-title="<?php echo htmlspecialchars($fair->getName()); ?>"
                                                data-description="<?php echo htmlspecialchars($fair->getDescription()); ?>"
                                                data-status="<?php echo htmlspecialchars($fair->getStatus()); ?>"
                                                data-category="<?php echo htmlspecialchars(explode(',', $fair->getLocation())[0]); ?>"
                                                data-medium="<?php echo htmlspecialchars((explode(',', $fair->getLocation())[1])); ?>"
                                                data-date="<?php echo htmlspecialchars($fair->getStartDate()); ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger reject-btn"
                                                onclick="rejectionFun(this)"
                                                data-id="<?php echo $fair->getId(); ?>"
                                                data-bs-toggle="modal"
                                                data-name="<?php echo htmlspecialchars($fair->getName()); ?>"
                                                data-bs-target="#rejectionModal">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>


        <!-- registering model -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register New Art Fair</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="registerArtFairForm" method="POST" action="/artist/register-fair" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="fairName" class="form-label">Fair Name</label>
                                <input type="text" class="form-control" id="fairName" name="fairName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Location
                                    <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip" title="Provide the location of the art fair."></i>
                                </label>

                                <div class="row">
                                    <div class="col">
                                        <select name="fairGovernment" id="fairLocation" class="form-select" required>
                                            <option value="">Select Government</option>
                                            <?php foreach ($governments as $government): ?>
                                                <option value="<?php echo htmlspecialchars($government); ?>">
                                                    <?php echo htmlspecialchars($government); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" id="fairLocationDetails" name="fairLocationDetails" placeholder="Enter location details (e.g., venue name, address)" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="fairDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="fairDate" name="fairDate" required>
                            </div>
                            <div class="mb-3">
                                <label for="fairImage" class="form-label">Fair Image</label>
                                <div class="row">
                                    <div class="row">
                                        <div class="col-9 col-md-3 mb-3">
                                            <img src="https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png" alt="artwor k preview" id="profileAvatar" class="img-thumbnail rounded">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label for="avatarUpload" class="btn btn-outline-primary">
                                                Upload Artwork
                                            </label>
                                            <input type="file" id="avatarUpload" name="fairImage" style="display: none" accept="image/*" required="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="fairDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="fairDescription" name="fairDescription"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Register Fair</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Rejection Modal -->
        <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectionModalTitle">Delete Artwork</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="/artist/delete-fair" method="POST">
                        <div class="modal-body">
                            <p>Are you sure you want to Delete <strong id="rejectionItemName"></strong>?</p>

                            <input type="hidden" name="id" id="rejectionItemId">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- artfair Details Modal -->
        <div
            class="modal fade"
            id="showDetailsModal"
            tabindex="-1"
            aria-labelledby="artwork-detail-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="artwork-detail-modal-label">
                            Art Fair Details
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img
                                    src=""
                                    alt="Artwork Image"
                                    id="modal-artwork-image"
                                    class="modal-artwork-image img-fluid" />
                            </div>
                            <div class="col-md-6">
                                <h3 id="modal-artwork-title"></h3>
                                <p id="modal-artwork-description"></p>
                                <div class="mb-3">
                                    <strong>Status:</strong>
                                    <span id="modal-artwork-status"></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Government:</strong>
                                    <span id="modal-artwork-category"></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Location Description:</strong>
                                    <span id="modal-artwork-medium"></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Start Date:</strong>
                                    <span id="modal-artwork-date"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>




        <!-- Artist Footer -->
        <footer class="artist-footer">
            <p>&copy; 2025 ArtShelf Artist Dashboard. All rights reserved.</p>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom Scripts -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/admin/artists.js"></script>



</body>

</html>