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
                <div class="row">
                    <!-- All Artwork -->
                    <div class="table-container">
                        <div class="table-header">
                            <h3 class="table-title">All Artworks</h3>
                            <div class="table-filters">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover search-table" id="artworksTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Artist</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date Added</th>
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
                                                        src="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                                                        alt="<?php echo htmlspecialchars($artwork->getTitle()); ?>"
                                                        class="img-thumbnail"
                                                        style="width: 80px; height: 60px; object-fit: cover;">
                                                </td>
                                                <td><a href="artworks/<?php echo $artwork->getArtworkID(); ?>"><?php echo htmlspecialchars($artwork->getTitle()); ?></a></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img
                                                            src="/uploads/profiles/<?php echo htmlspecialchars($artwork->getArtist()->getProfilePic()); ?>"
                                                            alt="Artist"
                                                            class="rounded-circle me-2"
                                                            width="30"
                                                            height="30">
                                                        <?php echo htmlspecialchars($artwork->getArtist()->getFirstName() . ' ' . $artwork->getArtist()->getLastName()); ?>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($artwork->getCategory()); ?></td>
                                                <td>$<?php echo ($artwork->getPrice()); ?></td>
                                                <td>
                                                    <span class="status-badge <?php echo strtolower($artwork->getStatus()); ?>">
                                                        <?php echo $artwork->getStatus(); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo ($artwork->getDateCreated()); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../assets/js/admin/artists.js"></script>
    <script src="../../assets/js/admin.js"></script>


</body>

</html>