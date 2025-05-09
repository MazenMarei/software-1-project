<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Collections | ArtShelf Admin</title>
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
    <main class="admin-content">

        <?php $pageTitle = "Manage Collections";
        require_once VIEWS . 'components/artist_header.php'; ?>
        <?php require_once VIEWS . 'components/error_display.php'; ?>



        <!-- Collections Grid View -->
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
        </div>

        <!-- Collection Details Modal -->
        <div class="modal fade" id="collectionDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Collection Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="collectionDetailsContent">
                        <!-- Will be populated by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="editCollectionBtn">Edit Collection</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Collection Modal -->
        <div class="modal fade" id="editCollectionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Add Collection</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="collectionForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="collectionTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="collectionTitle" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="collectionType" class="form-label">Type</label>
                                    <select class="form-select" id="collectionType" required>
                                        <option value="regular">Regular</option>
                                        <option value="featured">Featured</option>
                                        <option value="seasonal">Seasonal</option>
                                        <option value="curated">Curated</option>
                                        <option value="thematic">Thematic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="collectionDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="collectionDescription" rows="3" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="collectionImage" class="form-label">Cover Image</label>
                                    <input type="file" class="form-control" id="collectionImage">
                                    <div class="form-text">Recommended size: 1200 x 800 pixels</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="collectionStatus" class="form-label">Status</label>
                                    <select class="form-select" id="collectionStatus" required>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Featured Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="collectionFeatured">
                                    <label class="form-check-label" for="collectionFeatured">
                                        Display in featured collections
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="collectionHomepage">
                                    <label class="form-check-label" for="collectionHomepage">
                                        Show on homepage
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Manage Artworks</label>
                                <div class="d-flex mb-2">
                                    <input type="text" class="form-control" id="artworkSearch" placeholder="Search artworks...">
                                    <button type="button" class="btn btn-outline-primary ms-2" id="searchArtworksBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">Available Artworks</div>
                                            <div class="card-body" style="height: 200px; overflow-y: auto;">
                                                <ul class="list-group" id="availableArtworks">
                                                    <!-- Will be populated by JavaScript -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">Selected Artworks</div>
                                            <div class="card-body" style="height: 200px; overflow-y: auto;">
                                                <ul class="list-group" id="selectedArtworks">
                                                    <!-- Will be populated by JavaScript -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="collectionId">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveCollectionBtn">Save Collection</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Footer -->
        <footer class="admin-footer">
            <p>&copy; 2025 ArtShelf Admin Dashboard. All rights reserved.</p>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom Scripts -->
    <script src="../../js/config.js"></script>
    <script src="../../js/main.js"></script>
    <!-- <script src="../../js/auth.js"></script> -->
    <script src="../../js/models/Artwork.js"></script>
    <script src="../../js/services/ArtworkService.js"></script>
    <script src="../../js/admin/collections.js"></script>
</body>

</html>