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
                <h1 class="mb-3">Live preview Page</h1>
                <p class="text-muted">Preview The Artworks and add to your collection</p>
            </div>

            <div class="row justify-content-between p-3">
                <div class="col-md-7 settings-section">
                    <h3>
                        Live preview
                    </h3>
                    <div id="konvaContainer" class="rounded-3 row justify-content-center"></div>
                </div>
                <div class="col-md-4 settings-section">
                    <h3>
                        Select Artworks
                    </h3>
                    <input type="text" hidden id="selectedArtworks">
                    <div class="row overflow-y-auto" style="max-height: 400px;">
                        <!-- All Artwork -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="SelectedArtworksTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
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
                                            <?php $isSelected = in_array($artwork->getArtworkID(), $selectedArtworks); ?>
                                            <tr>
                                                <td>
                                                    <img
                                                        src="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                                                        alt="<?php echo htmlspecialchars($artwork->getTitle()); ?>"
                                                        class="img-thumbnail"
                                                        style="width: 130px; height: 110px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <div class="my-5">
                                                        <?php echo htmlspecialchars($artwork->getTitle()); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm my-5">
                                                        <button
                                                            type="button"
                                                            class="btn <?php echo $isSelected ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                                            onclick="ArtPreview(this)"
                                                            data-image="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                                                            data-width="<?php echo htmlspecialchars($artwork->getDimensions()['width']); ?>"
                                                            data-height="<?php echo htmlspecialchars($artwork->getDimensions()['height']); ?>"
                                                            data-id="<?php echo $artwork->getArtworkID(); ?>">

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
                </div>
            </div>

            <div class="row p-3">
                <div class="col settings-section">
                    <h3>Setting</h3>

                    <div class="row my-3 justify-content-between">
                        <div class="row">
                            <h5>Choose a Room
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 justify-content-center d-flex my-2">
                                <img
                                    src="data:image/jpeg;base64,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"
                                    alt="Room 1"
                                    class="img-thumbnail room-select"
                                    style="width: 230px; height: 190px; object-fit: cover;"
                                    data-room="room1">
                            </div>
                            <div class="col-12 col-md-3 justify-content-center d-flex my-2">
                                <img
                                    src="data:image/jpeg;base64,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"
                                    alt="Room 1"
                                    class="img-thumbnail room-select"
                                    style="width: 230px; height: 190px; object-fit: cover;"
                                    data-room="room1">
                            </div>
                            <div class="col-12 col-md-3 justify-content-center d-flex my-2">
                                <img
                                    src="data:image/jpeg;base64,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"
                                    alt="Room 1"
                                    class="img-thumbnail room-select"
                                    style="width: 230px; height: 190px; object-fit: cover;"
                                    data-room="room1">
                            </div>
                            <div class="col-12 col-md-3 justify-content-center d-flex my-2">
                                <img
                                    src="https://www.housedigest.com/img/gallery/the-affordable-way-nate-berkus-fills-empty-wall-space/l-intro-1688412309.jpg"
                                    alt="Room 1"
                                    class="img-thumbnail room-select"
                                    style="width: 230px; height: 190px; object-fit: cover;"
                                    data-room="room1">
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-between p-3">
                        <label for="roomImage">
                            <h5>Upload Your Room Image</h5>
                        </label>
                        <input type="file" class="form-control" id="roomImage" name="roomImage" accept="image/*">
                    </div>
                    <div class="row">
                        <button class="btn btn-outline-primary" id="downloadButton">
                            <i class="fas fa-download me-2"></i> Download
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include_once VIEWS . 'components/customer_footer.php'; ?>

    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="../../../assets/js/bootstrap.min.js"></script>

    <script src="../../../assets/js/konva.min.js"></script>
    <script src="../../../assets/js/live-preview.js"></script>

</body>

</html>