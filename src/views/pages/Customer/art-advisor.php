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
            <!-- Settings Header -->
            <div class="settings-header">
                <h1 class="mb-3">Art Advisor</h1>
                <p class="text-muted">Get personalized art recommendations</p>
            </div>

            <div class="row justify-content-between p-3">
                <div class="settings-section">
                    <form action="/customer/art-advisor" method="POST">
                        <div class="mb-3">
                            <label for="art-style" class="form-label">Preferred Art Style</label>
                            <select class="form-select" id="art-style" name="art_style" required>
                                <option value="" disabled selected>Select your preferred art style</option>
                                <option value="abstract">Abstract</option>
                                <option value="realism">Realism</option>
                                <option value="impressionism">Impressionism</option>
                                <option value="modern">Modern</option>
                                <option value="contemporary">Contemporary</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="budget" class="form-label">Budget Range</label>
                            <input type="number" class="form-control" id="budget" name="budget" placeholder="$0.00"
                                required />
                        </div>

                        <div class="mb-3">
                            <label for="art-type" class="form-label">Type of Art</label>
                            <select class="form-select" id="art-type" name="art_type" required>
                                <option value="" disabled selected>Select the type of art you are interested in</option>
                                <option value="painting">Painting</option>
                                <option value="sculpture">Sculpture</option>
                                <option value="photography">Photography</option>
                                <option value="digital">Digital Art</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Get Recommendations</button>
                    </form>
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