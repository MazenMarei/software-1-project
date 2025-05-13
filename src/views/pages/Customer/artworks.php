<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Settings | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../../assets/css/main.css" rel="stylesheet">
    <link href="../../../assets/css/customer.dashboard.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/css/all.min.css">
    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet" />

</head>

<body>
    <!-- Navbar -->
    <?php include_once VIEWS . '/components/customer_navbard.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">

            <div class="settings-header">
                <h1 class="mb-3">ArtWork Page</h1>
                <p class="text-muted">Preview The Artworks and add to your collection</p>
            </div>

            <div class="settings-section fade show active" id="general" role="tabpanel">
                <h3 class="section-title">ArtWork Details</h3>
                <div class="row">
                    <div class="col-md-6">
                        <img
                            src="/uploads/artworks/<?php echo ($artwork->getImages()); ?>"
                            alt="Artwork Image"
                            class="img-fluid" />
                    </div>
                    <div class="col-md-6">
                        <h3 id="modal-artwork-title"><?php echo ($artwork->getTitle()); ?></h3>
                        <p class="text-accent fs-4" id="modal-artwork-price">$ <?php echo ($artwork->getPrice()); ?></p>
                        <div class="mb-3">
                            <strong>Category:</strong>
                            <span id="modal-artwork-category"><?php echo ($artwork->getCategory()); ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Medium:</strong>
                            <span id="modal-artwork-medium"><?php echo ($artwork->getMedium()); ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Dimensions:</strong>
                            <span id="modal-artwork-dimensions"><?php echo (join('x', $artwork->getDimensions()) . ' in'); ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <span id="modal-artwork-date"><?php echo ($artwork->getDateCreated()); ?></span>
                        </div>

                        <div class="mb-3">
                            <p id="modal-artwork-description"><?php echo ($artwork->getDescription()); ?></p>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include_once VIEWS . 'components/customer_footer.php'; ?>

    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="../../../assets/js/bootstrap.min.js"></script>

</body>

</html>