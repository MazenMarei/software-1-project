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
    <!-- Admin Sidebar -->
    <?php require_once VIEWS . 'components/admin_sidebar.php'; ?>



    <!-- Main Content -->
    <main class="artist-content">
        <!-- Admin Header -->
        <?php
        $pageTitle = 'Manage Sepecial Collections';
        require_once VIEWS . 'components/admin_header.php';
        ?>

        <!-- Display error/success messages -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>


        <div class="stat-card my-5">
            <form action="/admin/special-collections" method="post" id="specialCollection">
                <div class=" my-3">
                    <label for="collectionName" class="form-label">Collection Name</label>
                    <input type="text" class="form-control" id="collectionName" name="collectionName" value="<?php echo htmlspecialchars($collection->getName()); ?>" required>
                    <input type="hidden" id="selectedArtworks" name="selectedArtworks" value="<?php echo join(',', $selectedArtworks); ?>">
                </div>
                <div class="row  flex-row-reverse">
                    <div class="col-md-6 col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg start-0" data-bs-toggle="modal">Save Special Collection</button>
                    </div>
                </div>
            </form>
        </div>


        <!-- Filter Section -->

        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="h5 mb-0">Filter Artworks</h2>
            </div>
            <div class="card-body">
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
            </div>
        </div>




        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">All Artworks</h3>
                <div class="table-filters">
                </div>
            </div>
            <!-- All Artwork -->
            <div class="table-responsive">
                <table class="table table-hover" id="SelectedArtworksTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Artist</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
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
                                <?php $isSelected = in_array($artwork['artworkID'], $selectedArtworks); ?>
                                <tr>
                                    <td>
                                        <img
                                            src="/uploads/artworks/<?php echo htmlspecialchars($artwork['images']); ?>"
                                            alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                                            class="img-thumbnail"
                                            style="width: 130px; height: 110px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center my-4">
                                            <img
                                                src="/uploads/profiles/<?php echo htmlspecialchars($artwork['profilePic']); ?>"
                                                alt="<?php echo htmlspecialchars($artwork['Fname'] . ' ' . $artwork['Lname']); ?>"
                                                class="small-avatar me-3">
                                            <span><?php echo htmlspecialchars($artwork['Fname'] . ' ' . $artwork['Lname']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="my-5">
                                            <?php echo htmlspecialchars($artwork['title']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="my-5">
                                            <?php echo htmlspecialchars($artwork['category']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="my-5">
                                            <span class="status-badge <?php echo $isSelected ? 'accepted' : 'rejected' ?>"><?php echo $isSelected ? 'Accepted' : 'Rejected' ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm my-5">
                                            <button
                                                type="button"
                                                class="btn <?php echo $isSelected ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                                onclick="ArtToggle(this)"
                                                data-id="<?php echo $artwork['artworkID']; ?>">
                                                <i class="fa-solid <?php echo $isSelected ? 'fa-trash' : 'fa-plus'; ?>"></i>
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