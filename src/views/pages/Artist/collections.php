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
        $pageTitle = 'Manage Collections';
        require_once VIEWS . 'components/artist_header.php';
        ?>

        <!-- Display error/success messages -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>





        <!-- collection grid view -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="table-title">Collections</h2>
                <div>
                    <button type="button" class="btn btn-primary" id="addCollectionBtn">
                        <i class="fas fa-plus me-1"></i> Add Collection
                    </button>
                </div>
            </div>

            <div id="gridView" class="collection-grid">
                <!-- Grid items will be populated by JavaScript -->
                <!-- Example grid item for reference -->
                <div class="collection-card">
                    <div class="collection-image">
                        <img src="https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Abstract Art Collection">
                        <span class="collection-badge featured-badge">Featured</span>
                    </div>
                    <div class="collection-content">
                        <h3 class="collection-title">Abstract Expressionism</h3>
                        <div class="collection-meta">
                            <div class="collection-meta-item">
                                <i class="fas fa-calendar"></i> Created: Jan 15, 2025
                            </div>
                            <div class="collection-meta-item">
                                <i class="fas fa-palette"></i> 12 Artworks
                            </div>
                            <div class="collection-meta-item">
                                <i class="fas fa-eye"></i> 340 Views
                            </div>
                        </div>
                        <div class="collection-stats">
                            <div class="collection-stat">
                                <div class="collection-stat-value">12</div>
                                <div class="collection-stat-label">Artworks</div>
                            </div>
                            <div class="collection-stat">
                                <div class="collection-stat-value">5</div>
                                <div class="collection-stat-label">Artists</div>
                            </div>
                            <div class="collection-stat">
                                <div class="collection-stat-value">8</div>
                                <div class="collection-stat-label">Sales</div>
                            </div>
                        </div>
                        <div class="collection-actions">
                            <div>
                                <span class="status-badge active">Published</span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-outline-primary btn-sm action-btn view-collection" data-id="coll123">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-secondary btn-sm action-btn edit-collection" data-id="coll123">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm action-btn delete-collection" data-id="coll123">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div id="listView" class="table-container" style="display: none;">
                <div class="table-responsive">
                    <table class="admin-table" id="collectionsTable">
                        <thead>
                            <tr>
                                <th>Collection</th>
                                <th>Type</th>
                                <th>Artworks</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="collectionsTableBody">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-container">
                <nav aria-label="Collection pagination">
                    <ul class="pagination" id="collectionPagination">
                        <!-- Will be populated by JavaScript -->
                    </ul>
                </nav>
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