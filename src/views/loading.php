<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading... | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/main.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./assets/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
            background-color: #F3F2EC;
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100%;
        }

        .loading-logo {
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }

        .loading-spinner {
            position: relative;
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        .loading-spinner div {
            position: absolute;
            border: 4px solid #C5A992;
            opacity: 1;
            border-radius: 50%;
            animation: loading-spinner 1.5s cubic-bezier(0, 0.2, 0.8, 1) infinite;
        }

        .loading-spinner div:nth-child(2) {
            animation-delay: -0.5s;
        }

        .loading-text {
            font-family: 'Playfair Display', serif;
            color: #73663B;
            font-size: 1.2rem;
            letter-spacing: 2px;
            margin-top: 20px;
        }

        .loading-progress {
            width: 200px;
            height: 4px;
            background-color: #EDEBE4;
            border-radius: 4px;
            margin-top: 20px;
            overflow: hidden;
        }

        .loading-progress-bar {
            height: 100%;
            width: 0%;
            background-color: #C5A992;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        @keyframes loading-spinner {
            0% {
                top: 28px;
                left: 28px;
                width: 0;
                height: 0;
                opacity: 1;
            }

            100% {
                top: -1px;
                left: -1px;
                width: 58px;
                height: 58px;
                opacity: 0;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="loading-container">
        <div class="loading-logo">
            <img src="./assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="80">
        </div>
        <div class="loading-spinner">
            <div></div>
            <div></div>
        </div>
        <div class="loading-text">Curating your experience...</div>
        <div class="loading-progress">
            <div class="loading-progress-bar" id="progressBar"></div>
        </div>
    </div>

    <script>
        // Simulate loading progress
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('progressBar');
            let width = 0;

            // Increment progress
            const interval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(interval);
                } else {
                    width += 5;
                    progressBar.style.width = width + '%';
                }
            }, 150); // Adjust speed as needed (about 3 seconds total)
        });
    </script>
</body>

</html>