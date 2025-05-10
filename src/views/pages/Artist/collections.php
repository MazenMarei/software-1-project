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


        <!-- statistic section -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-content">
                        <h3> <?php echo count($collections); ?> </h3>
                        <p>Total Collections</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3> <?php echo ($usedArtworks) ?> </h3>
                        <p>Used Artworks</p>
                    </div>
                </div>
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
                <h3 class="table-title">Your Collections</h3>
                <div class="table-filters">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover search-table">
                    <thead>
                        <tr>
                            <th>Cover Image</th>
                            <th>Title</th>
                            <th>Artworks</th>
                            <th>Date</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="artworksTableBody">
                        <?php if (empty($collections)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No collections found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($collections as $collection): ?>
                                <tr>
                                    <td>
                                        <img
                                            src="/uploads/collections/<?php echo htmlspecialchars($collection->getCoverImage()); ?>"
                                            alt="<?php echo htmlspecialchars($collection->getName()); ?>"
                                            class="img-thumbnail"
                                            style="width: 80px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="my-3">
                                            <?php echo htmlspecialchars($collection->getName()); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="my-3">
                                            <?php echo count($collection->getCollectionArtworks() ?? []); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="my-3">
                                            <?php echo ($collection->getCreateDate()); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm my-2">
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger reject-btn"
                                                onclick="rejectionFun(this)"
                                                data-id="<?php echo $collection->getCollectionID(); ?>"
                                                data-bs-toggle="modal"
                                                data-name="<?php echo htmlspecialchars($collection->getName()); ?>"
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

        <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalTitle">Add Collection</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="collectionForm" enctype="multipart/form-data" action="/artist/create-collection" method="POST">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6" style="margin-top:-24px;">
                                    <label for="collectionTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="collectionTitle" name="collectionTitle" required>
                                </div>
                                <div class="col-md-6 my-3 my-md-0">
                                    <div class="row align-items-center">
                                        <div class="col-9 col-md-6  my-3 my-md-0">
                                            <img src="https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png" alt="artwor k preview" id="profileAvatar" class="img-thumbnail rounded">
                                        </div>
                                        <div class="col">
                                            <label for="avatarUpload" class="btn btn-outline-primary">
                                                Cover Image
                                            </label>
                                            <input type="file" id="avatarUpload" name="coverImage" style="display: none" accept="image/*" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="collectionDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="collectionDescription" rows="3" name="collectionDescription" required></textarea>
                            </div>
                            <div class="mb-3">
                                <div class="row  my-3 my-md-0">
                                    <div class="col-md-4">
                                        <label class="form-label">Manage Artworks</label>
                                        <div class="d-flex mb-2">
                                            <input type="text" class="form-control" id="artworkSearch" placeholder="Search artworks...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" id="collectionStatus">
                                            <option value="">All</option>
                                            <option value="accepted">Accepted</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" id="ArtworkCategory">
                                            <option value="">All</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo htmlspecialchars($category); ?>">
                                                    <?php echo htmlspecialchars($category); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">Available Artworks</div>
                                            <div class="card-body" style="height: 200px; overflow-y: auto;">
                                                <!-- All Artwork -->
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="SelectedArtworksTable">
                                                        <thead>
                                                            <tr>
                                                                <th width="100">Image</th>
                                                                <th width="120">Title</th>
                                                                <th>Category</th>
                                                                <th width="80">Status</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="artworksTableBody">
                                                            <?php if (empty($artworks)): ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center">No artworks found</td>
                                                                </tr>
                                                            <?php else: ?>
                                                                <?php foreach ($artworks as $artwork): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <img
                                                                                src="/uploads/artworks/<?php echo htmlspecialchars($artwork['images']); ?>"
                                                                                alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                                                                class="img-thumbnail"
                                                                                style="width: 100px; height: 80px; object-fit: cover;">
                                                                        </td>
                                                                        <td>
                                                                            <div class="my-4">
                                                                                <?php echo htmlspecialchars($artwork['title']); ?>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="my-4">
                                                                                <?php echo htmlspecialchars($artwork['category']); ?>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="my-4">
                                                                                <span class="status-badge rejected">Rejected</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group btn-group-sm my-3">
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn btn-outline-success"
                                                                                    onclick="ArtToggle(this)"
                                                                                    data-id="<?php echo $artwork['artworkID']; ?>">
                                                                                    <i class="fa-solid fa-plus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <input type="hidden" id="selectedArtworks" name="selectedArtworks">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="collectionId" required>
                            <button type="submit" class="btn btn-primary" id="saveCollectionBtn">Save Collection</button>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <form action="/artist/delete-collection" method="POST">
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


        <!-- Artist Footer -->
        <footer class="artist-footer">
            <p>&copy; 2025 ArtShelf Artist Dashboard. All rights reserved.</p>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <!-- Custom Scripts -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="../assets/js/admin.js"></script>
    <script src="../assets/js/artist/collections.js"></script>



</body>

</html>