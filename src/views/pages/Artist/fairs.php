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
        $pageTitle = 'Manage Artworks';
        require_once VIEWS . 'components/artist_header.php';
        ?>

        <!-- Display error/success messages -->
        <?php require_once VIEWS . 'components/error_display.php'; ?>

        <!-- Artwork Form -->
        <div class="form-container" id="artwork-form-container">
            <form id="new-artwork-form" class="needs-validation" novalidate action="/artist/add-artwork" method="POST" enctype="multipart/form-data">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="form-section-title">Basic Information</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="artwork-title" class="form-label">
                                Artwork Title <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="artwork-title"
                                name="title"
                                required
                                placeholder="Enter the title of your artwork" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="artwork-category" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="artwork-category" required name="category">
                                <option value="" selected disabled>Select a category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="artwork-description" class="form-label">
                            Description <span class="text-danger">*</span>
                            <i
                                class="fas fa-info-circle tooltip-icon"
                                data-bs-toggle="tooltip"
                                title="Provide a detailed description of your artwork, including inspiration, meaning, and techniques used."></i>
                        </label>
                        <textarea
                            class="form-control"
                            id="artwork-description"
                            rows="4"
                            required

                            name="description"
                            placeholder="Describe your artwork in detail..."></textarea>
                    </div>
                </div>

                <!-- Images -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        Image <span class="text-danger">*</span>
                        <i class="fas fa-info-circle tooltip-icon"
                            data-bs-toggle="tooltip"
                            title="Upload high-quality images of your artwork. The first image will be used as the main display image."></i>
                    </h3>
                    <div class="mb-3">
                        <div class="row">
                            <div class="row">
                                <div class="col-9 col-md-3 mb-3">
                                    <img src="https://developers.elementor.com/docs/assets/img/elementor-placeholder-image.png" alt="artwor k preview" id="profileAvatar" class="img-thumbnail rounded" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="avatarUpload" class="btn btn-outline-primary">
                                        Upload Artwork
                                    </label>
                                    <input
                                        type="file"
                                        id="avatarUpload"
                                        name="image"
                                        style="display: none"
                                        accept="image/*" required />
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            Accepted formats: JPG, PNG, WEBP. Max size: 5MB.
                        </small>
                        <div class="invalid-feedback">
                            Please upload an image of your artwork.
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="form-section">
                    <h3 class="form-section-title">Artwork Details</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="artwork-medium" class="form-label">
                                Medium <span class="text-danger">*</span>
                                <i
                                    class="fas fa-info-circle tooltip-icon"
                                    data-bs-toggle="tooltip"
                                    title="Specify the materials used to create your artwork (e.g., Oil on canvas, Digital print, Bronze sculpture)."></i>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="artwork-medium"
                                name="medium"
                                required
                                placeholder="e.g., Oil on canvas, Acrylic, Digital print" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Dimensions
                                <i
                                    class="fas fa-info-circle tooltip-icon"
                                    data-bs-toggle="tooltip"
                                    title="Provide the dimensions of your artwork (height x width x depth, if applicable)."></i>
                            </label>
                            <div class="dimension-inputs">
                                <div class="dimension-input-group">
                                    <label for="artwork-height" class="form-label">Height</label>
                                    <span class="text-danger">*</span>
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        class="form-control"
                                        id="artwork-height"
                                        required
                                        name="height"
                                        placeholder="Height" />
                                    <span class="dimension-unit">in</span>
                                </div>
                                <div class="dimension-input-group">
                                    <label for="artwork-width" class="form-label">Width</label>
                                    <span class="text-danger">*</span>
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        class="form-control"
                                        required
                                        id="artwork-width"
                                        name="width"
                                        placeholder="Width" />
                                    <span class="dimension-unit">in</span>
                                </div>
                                <div class="dimension-input-group">
                                    <label for="artwork-depth" class="form-label">Depth (optional)</label>
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        class="form-control"
                                        id="artwork-depth"
                                        name="depth"
                                        placeholder="Depth" />
                                    <span class="dimension-unit">in</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="artwork-price" class="form-label">
                                    Price <span class="text-danger">*</span>
                                </label>
                                <div class="price-input-group">
                                    <span class="currency-symbol">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="1"
                                        class="form-control"
                                        name="price"
                                        id="artwork-price"
                                        required
                                        placeholder="Enter price" />
                                </div>
                                <small class="text-muted">
                                    Enter the price in USD. We charge a 20% commission on each
                                    sale.
                                </small>
                            </div>
                        </div>


                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="window.location.href='/artist/artworks'">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Artwork
                        </button>
                    </div>
            </form>
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

    <script src="../assets/js/admin.js"></script>



</body>

</html>