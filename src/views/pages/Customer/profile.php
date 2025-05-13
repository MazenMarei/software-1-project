<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Settings | ArtShelf</title>
    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../assets/css/main.css" rel="stylesheet">
    <link href="../../../assets/css/customer.dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/all.min.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
        rel="stylesheet" />

</head>

<body>
    <?php include_once VIEWS . '/components/customer_navbard.php'; ?>

    <main class="main-content">
        <div class="container">

            <div class="settings-header">
                <h1 class="mb-3">Account Settings</h1>
                <p class="text-muted">Manage your account settings and preferences</p>
            </div>
            <?php require_once VIEWS . 'components/error_display.php'; ?>

            <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <a
                        class="nav-link active"
                        id="general-tab"
                        data-bs-toggle="tab"
                        href="#general"
                        role="tab">General</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="security-tab"
                        data-bs-toggle="tab"
                        href="#security"
                        role="tab">Security</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="payment-tab"
                        data-bs-toggle="tab"
                        href="#payment"
                        role="tab">Payment Methods</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="address-tab"
                        data-bs-toggle="tab"
                        href="#address"
                        role="tab">Address</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="notifications-tab"
                        data-bs-toggle="tab"
                        href="#notifications"
                        role="tab">Notifications</a>
                </li>
            </ul>


            <div class="tab-content" id="settingsTabsContent">

                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Profile Information</h3>
                        <form class="settings-form" id="profile-form" action="/customer/updateProfile" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="firstName"
                                            name="firstName"
                                            required
                                            minlength="2"
                                            value="<?php echo ($customer->getFirstName()); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="lastName"
                                            name="lastName"
                                            required
                                            minlength="2"
                                            value="<?php echo ($customer->getLastName()); ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    required
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                    title="Please enter a valid email address"
                                    name="email"
                                    value="<?php echo ($customer->getEmail()); ?>" />
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    required
                                    name="phone"
                                    value="<?php echo ($customer->getPhone()); ?>" />
                            </div>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <div class="d-flex align-items-center">
                                    <img
                                        src="/uploads/profiles/<?php echo ($customer->getProfilePic()); ?>"
                                        alt="Profile Picture"
                                        class="rounded-circle me-3"
                                        id="profileAvatar"
                                        style="width: 80px; height: 80px; object-fit: cover" />
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary mb-2"
                                            onclick="document.getElementById('avatarUpload').click()">
                                            Change Picture
                                        </button>
                                        <input
                                            type="file"
                                            class="form-control-file"
                                            id="avatarUpload"
                                            name="profilePic"
                                            hidden
                                            required
                                            accept="image/*" />
                                        <p class="text-muted small">
                                            Recommended size: 400×400 pixels
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>


                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="settings-section">
                        <h2 class="settings-section-title">Security</h2>
                        <form id="passwordForm" action="/customer/changePassword" method="POST" novalidate>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="currentPassword"
                                            name="currentPassword"
                                            required />
                                        <div class="invalid-feedback">
                                            Please enter your current password
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="newPassword"
                                            name="newPassword"
                                            required
                                            pattern="(?=.*[A-Z])(?=.*[0-9]).{8,}" />
                                        <div class="invalid-feedback" id="newPasswordFeedback">
                                            Password must be at least 8 characters with at least one uppercase letter and one number
                                        </div>
                                        <div class="mt-2">
                                            <div class="progress" style="height: 5px;">
                                                <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small id="passwordStrengthText" class="form-text text-muted">Password strength</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="confirmPassword"
                                            name="confirmPassword"
                                            required />
                                        <div class="invalid-feedback">
                                            Passwords do not match
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="passwordReqs" class="mb-3">
                                <p class="mb-1 small">Password requirements:</p>
                                <ul class="small ps-3">
                                    <li id="length-check" class="text-muted">At least 8 characters</li>
                                    <li id="uppercase-check" class="text-muted">At least one uppercase letter</li>
                                    <li id="number-check" class="text-muted">At least one number</li>
                                </ul>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Payment Methods Tab -->
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <div class="settings-section">
                        <h2 class="profile-section-title">Payment Information</h2>
                        <form id="paymentForm" action="/customer/updatePayment" method="POST" novalidate>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="cardNumber" class="form-label">Card Number</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="cardNumber"
                                            name="cardNumber"
                                            pattern="^\d{4}-\d{4}-\d{4}-\d{4}$"
                                            value="<?php echo (!$payment) ? "" : $payment->getCardNumber(); ?>"
                                            maxlength="19"
                                            minlength="19"
                                            required />
                                        <div class="invalid-feedback">
                                            Please enter a valid card number (format: 1234-5678-9012-3456)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cvvNumber" class="form-label">CVV</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="cvvNumber"
                                            name="cvvNumber"
                                            maxlength="3"
                                            minlength="3"
                                            value="<?php echo $payment->getCvv(); ?>"
                                            required
                                            pattern="^\d{3}$" />
                                        <div class="invalid-feedback" id="newPasswordFeedback">
                                            Please enter a valid CVV number
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="expMonth" class="form-label">Exp Month</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="12"
                                            step="1"
                                            class="form-control"
                                            id="expMonth"
                                            name="expMonth"
                                            maxlength="2"
                                            value="<?php echo (!$payment) ? "" : $payment->getExpiryMonth(); ?>"
                                            minlength="2"
                                            pattern="^\d{2}$"
                                            required />
                                        <div class="invalid-feedback">
                                            Please enter a valid expiration month
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="expYear" class="form-label">Exp Year</label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="expYear"
                                            name="expYear"
                                            min="<?php echo date('Y'); ?>"
                                            max="<?php echo date('Y') + 20; ?>"
                                            value="<?php echo (!$payment) ? "" : $payment->getExpiryYear(); ?>"
                                            required />
                                        <div class="invalid-feedback">
                                            Please enter a valid expiration year
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary">
                                Save Card
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div class="tab-pane fade" id="address" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Your Address</h3>
                        <form id="addressForm" action="/customer/updateProfile" method="POST">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="city" class="form-label">City</label>
                                            <select
                                                class="form-control"
                                                id="city"
                                                name="city"
                                                required>
                                                <option value="" disabled selected>Select your city</option>
                                                <?php foreach ($governments as $government): ?>
                                                    <option value="<?php echo htmlspecialchars($government); ?>" <?php echo ($customer->getCity() == $government) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($government); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label for="postal_code">Postal Code</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="postal_code"
                                                name="postal_code"
                                                required
                                                pattern="^\d{5}$"
                                                value="<?php echo $customer->getPostalCode(); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea
                                            class="form-control"
                                            id="address"
                                            name="address"
                                            rows="3"
                                            required><?php echo $customer->getAddress(); ?></textarea>
                                        <div class="invalid-feedback">
                                            Please enter your address
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    Save Address
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="settings-section">

                        <div class="table-container">
                            <div class="table-header">
                                <h2 class="table-title">All Notifications</h2>

                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover search-table" id="customersTable">

                                    <thead>
                                        <tr>
                                            <th>Message</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php if (isset($notifications) && !empty($notifications)) : ?>
                                            <?php foreach ($notifications as $notification) : ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center my-2 w-100">
                                                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                                <i class="fas fa-info-circle me-2"></i><?php echo htmlspecialchars($notification['Message']); ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="d-flex my-4"><?php echo htmlspecialchars($notification['datesent']); ?></div>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="8" class="text-center">No Followers found.</td>
                                            </tr>

                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include_once VIEWS . 'components/customer_footer.php'; ?>

    <!-- Bootstrap and jQuery JS -->
    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="../../../assets/js/bootstrap.min.js"></script>
    <!-- Custom JS -->
    <script src="../../../assets/js/admin.js"></script>


    <script>
        $(document).ready(function() {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const currentPassword = document.getElementById('currentPassword');
            const passwordForm = document.getElementById('passwordForm');

            // Form validation
            passwordForm.addEventListener('submit', function(event) {
                if (!passwordForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                // Check if passwords match
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }

                passwordForm.classList.add('was-validated');
            });

            // Clear confirm password validation when typing
            confirmPassword.addEventListener('input', function() {
                if (confirmPassword.value !== newPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });

            // Password strength meter
            newPassword.addEventListener('input', function() {
                const password = newPassword.value;
                updatePasswordStrength(password);

                // Validate password format
                let isValid = true;

                // Check length
                if (password.length >= 8) {
                    document.getElementById('length-check').classList.replace('text-muted', 'text-success');
                } else {
                    document.getElementById('length-check').classList.replace('text-success', 'text-muted');
                    isValid = false;
                }

                // Check uppercase
                if (/[A-Z]/.test(password)) {
                    document.getElementById('uppercase-check').classList.replace('text-muted', 'text-success');
                } else {
                    document.getElementById('uppercase-check').classList.replace('text-success', 'text-muted');
                    isValid = false;
                }

                // Check number
                if (/[0-9]/.test(password)) {
                    document.getElementById('number-check').classList.replace('text-muted', 'text-success');
                } else {
                    document.getElementById('number-check').classList.replace('text-success', 'text-muted');
                    isValid = false;
                }

                if (!isValid) {
                    newPassword.setCustomValidity('Password does not meet requirements');
                } else {
                    newPassword.setCustomValidity('');
                }
            });
            /// make the card number input accept only numbers and dashes
            $("#cardNumber").on("input", function() {
                this.value = this.value.replace(/[^0-9-]/g, '');
                this.value = this.value.replace(/(\d{4})(?=\d)/g, '$1-');
            });
            // make the cvv input accept only numbers
            $("#cvvNumber").on("input", function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            /// valiate exp month and year
            $("#expMonth").on("input", function() {
                if (new Date().getMonth() + 1 > this.value && new Date().getFullYear() == $("#expYear").val()) {
                    this.setCustomValidity("Expiration month is in the past");
                } else {
                    this.setCustomValidity("");
                }
            });

            $("#expYear").on("input", function() {
                if (new Date().getFullYear() > this.value) {
                    this.setCustomValidity("Expiration year is in the past");
                } else {
                    this.setCustomValidity("");
                }
            });

            function updatePasswordStrength(password) {
                let strength = 0;
                let feedback = "Password strength";

                // Empty password
                if (password.length === 0) {
                    $("#passwordStrengthBar").css("width", "0%").removeClass().addClass("progress-bar");
                    $("#passwordStrengthText").text(feedback);
                    return;
                }

                // Length check (up to 25 points)
                const lengthScore = Math.min(25, Math.floor(password.length * 3));
                strength += lengthScore;

                // Uppercase letters (25 points)
                if (/[A-Z]/.test(password)) {
                    strength += 25;
                }

                // Numbers (25 points)
                if (/[0-9]/.test(password)) {
                    strength += 25;
                }

                // Special characters (25 points)
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 25;
                }

                // Trim to max 100
                strength = Math.min(100, strength);

                // Update progress bar
                $("#passwordStrengthBar").css("width", strength + "%");

                // Set color and text based on strength
                if (strength < 50) {
                    $("#passwordStrengthBar").removeClass().addClass("progress-bar bg-danger");
                    feedback = "Weak password";
                } else if (strength < 75) {
                    $("#passwordStrengthBar").removeClass().addClass("progress-bar bg-warning");
                    feedback = "Moderate password";
                } else {
                    $("#passwordStrengthBar").removeClass().addClass("progress-bar bg-success");
                    feedback = "Strong password";
                }

                $("#passwordStrengthText").text(feedback);
            }
        });
    </script>

</body>

</html>