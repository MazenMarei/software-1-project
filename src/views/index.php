<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtShelf - Discover, Buy, and Sell Art</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/css/auth.css">
    <link rel="stylesheet" href="./assets/css/all.min.css">
    <link
        href="./assets/css/font.css"
        rel="stylesheet">
</head>

<body>
    <div class="auth-container">
        <div class="auth-form-container">
            <div class="text-center mb-5">
                <img src="./assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="60">
            </div>
            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login">Login</button>
                <button class="auth-tab" data-tab="register">Register</button>
            </div>

            <div class="auth-form active" id="login-form">
                <h2 class="fs-1 mb-1">Welcome Back</h2>
                <p class="text-muted mb-4">Sign in to discover and purchase exquisite artworks</p>

                <?php require_once VIEWS . 'components/error_display.php'; ?>

                <form id="loginForm" class="needs-validation" novalidate action="/login" method="POST">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label fw-medium">Email</label>
                        <input type="email" class="form-control form-control-lg" id="loginEmail" name="email"
                            placeholder="Enter your email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label fw-medium">Password</label>
                        <div class="password-input">
                            <input type="password" class="form-control form-control-lg" id="loginPassword" name="password"
                                placeholder="Enter your password" required minlength="6">
                            <button type="button" class="password-toggle">
                                <i class="far fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">
                                Password must be at least 6 characters.
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    <div class="alert alert-danger d-none" id="loginAlert">
                        Invalid email or password. Please try again.
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fs-5 fw-medium">Sign In</button>
                </form>
            </div>

            <div class="auth-form" id="register-form">
                <h2 class="fs-1 mb-1">Create an Account</h2>
                <p class="text-muted mb-4">Join our community of art enthusiasts</p>

                <?php require_once VIEWS . 'components/error_display.php'; ?>

                <form id="registerForm" class="needs-validation" novalidate action="/register" method="POST" enctype="multipart/form-data">
                    <div class=" row mb-3">
                        <div class="col-md-6">
                            <label for="registerFirstName" class="form-label fw-medium">First Name</label>
                            <input type="text" class="form-control form-control-lg" id="registerFirstName" name="firstName"
                                placeholder="Enter first name" required minlength="2">
                            <div class="invalid-feedback">
                                Please enter your first name (at least 2 characters).
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="registerLastName" class="form-label fw-medium">Last Name</label>
                            <input type="text" class="form-control form-control-lg" id="registerLastName" name="lastName"
                                placeholder="Enter last name" required minlength="2">
                            <div class="invalid-feedback">
                                Please enter your last name (at least 2 characters).
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="registerUsername" class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control form-control-lg" id="registerUsername" name="username"
                            placeholder="Choose a username" required minlength="3">
                        <div class="invalid-feedback">
                            Please enter a username (at least 3 characters).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label fw-medium">Email</label>
                        <input type="email" class="form-control form-control-lg" id="registerEmail" name="email"
                            placeholder="Enter your email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label fw-medium">Password</label>
                        <div class="password-input">
                            <input type="password" class="form-control form-control-lg" id="registerPassword" name="password"
                                placeholder="Create a password" required minlength="8">
                            <button type="button" class="password-toggle">
                                <i class="far fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">
                                Password must be at least 8 characters with at least one uppercase letter.
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="accountType" class="form-label fw-medium">Account Type</label>
                        <select class="form-select form-select-lg" id="accountType" name="accountType" required>
                            <option value="">Select account type</option>
                            <option value="customer">Customer</option>
                            <option value="artist">Artist</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select an account type.
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="termsAgreement" name="termsAgreement" required>
                        <label class="form-check-label" for="termsAgreement">I agree to the <a href="#">Terms of
                                Service</a> and <a href="#">Privacy Policy</a></label>

                    </div>
                    <div class="alert alert-danger d-none" id="registerAlert">
                        There was an error creating your account. Please try again.
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fs-5 fw-medium">Create Account</button>
                </form>
            </div>

        </div>

        <div class="auth-showcase">
            <div class="showcase-gallery">
                <div class="showcase-image">
                    <div class="showcase-info">
                        <h3>Discover New Artists</h3>
                        <p>Find emerging talents and established masters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery-3.7.1.min.js"></script>

    <script src="./assets/js/main.js"></script>

</body>

</html>