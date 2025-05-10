<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | ArtShelf</title>
    
    <!-- Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/main.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/css/all.min.css">
    
    <style>
        .error-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1;
        }
        
        .error-divider {
            height: 4px;
            width: 60px;
            background-color: #dc3545;
            margin: 20px auto;
        }
        
        .error-message {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
            color: #343a40;
        }
        
        .error-description {
            font-size: 16px;
            max-width: 600px;
            margin: 0 auto 30px;
            color: #6c757d;
        }
        
        .error-artwork {
            max-width: 300px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="../assets/images/404-art.png" alt="404 Artwork" class="error-artwork">
        <h1 class="error-code">404</h1>
        <div class="error-divider"></div>
        <h2 class="error-message">Masterpiece Not Found</h2>
        <p class="error-description">
            The artwork you're looking for seems to have been moved, sold, or never existed in our gallery.
            Perhaps you'd like to browse our collection for other masterpieces?
        </p>
        <div class="d-flex gap-3">
            <a href="/" class="btn btn-primary">Back to Home</a>
            <!-- <a href="/contact" class="btn btn-outline-secondary">Contact Us</a> -->
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.7.1.min.js"></script>
</body>
</html>
