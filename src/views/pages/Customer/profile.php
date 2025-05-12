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
                <h1 class="mb-3">Account Settings</h1>
                <p class="text-muted">Manage your account settings and preferences</p>
            </div>

            <!-- Settings Navigation -->
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
                        role="tab">Addresses</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="notifications-tab"
                        data-bs-toggle="tab"
                        href="#notifications"
                        role="tab">Notifications</a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link"
                        id="privacy-tab"
                        data-bs-toggle="tab"
                        href="#privacy"
                        role="tab">Privacy</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="settingsTabsContent">
                <!-- General Settings Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Profile Information</h3>
                        <form class="settings-form" id="profile-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="firstName"
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
                                    value="<?php echo ($customer->getEmail()); ?>" />
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    value="<?php echo ($customer->getPhone()); ?>" />
                            </div>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <div class="d-flex align-items-center">
                                    <img
                                        src="/uploads/profiles/<?php echo ($customer->getProfilePic()); ?>"
                                        alt="Profile Picture"
                                        class="rounded-circle me-3"
                                        style="width: 80px; height: 80px; object-fit: cover" />
                                    <div>
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary mb-2">
                                            Change Picture
                                        </button>
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

                    <div class="settings-section">
                        <h3 class="section-title">Account Preferences</h3>
                        <form class="settings-form" id="preferences-form">
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select class="form-control" id="currency">
                                    <option value="usd" selected>USD ($)</option>
                                    <option value="eur">EUR (€)</option>
                                    <option value="gbp">GBP (£)</option>
                                    <option value="jpy">JPY (¥)</option>
                                </select>
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
                        <form id="passwordForm" action="/admin/changePassword" method="POST" novalidate>
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
                        <h3 class="section-title">Payment Methods</h3>
                        <div class="payment-method default">
                            <img
                                src="https://cdn-icons-png.flaticon.com/512/179/179457.png"
                                alt="Visa"
                                class="payment-logo" />
                            <div class="payment-details">
                                <p class="payment-title">Visa ending in 4242</p>
                                <p class="payment-info">Expires 05/2026</p>
                            </div>
                            <div class="payment-actions">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                        <div class="payment-method">
                            <img
                                src="https://cdn-icons-png.flaticon.com/512/5968/5968299.png"
                                alt="MasterCard"
                                class="payment-logo" />
                            <div class="payment-details">
                                <p class="payment-title">MasterCard ending in 5555</p>
                                <p class="payment-info">Expires 03/2025</p>
                            </div>
                            <div class="payment-actions">
                                <button class="btn btn-sm btn-outline-primary">
                                    Set as Default
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary" id="addPaymentMethodBtn">
                                <i class="fas fa-plus me-2"></i> Add Payment Method
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div class="tab-pane fade" id="address" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Your Addresses</h3>
                        <div class="address-item default">
                            <div class="address-title">
                                <span>Home</span>
                                <span class="address-badge">Default</span>
                            </div>
                            <p class="address-text">
                                John Smith<br />
                                123 Main Street, Apt 4B<br />
                                New York, NY 10001<br />
                                United States<br />
                                Phone: +1 (555) 123-4567
                            </p>
                            <div class="address-actions">
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                        <div class="address-item">
                            <div class="address-title">
                                <span>Office</span>
                            </div>
                            <p class="address-text">
                                John Smith<br />
                                456 Business Ave, Suite 201<br />
                                New York, NY 10002<br />
                                United States<br />
                                Phone: +1 (555) 987-6543
                            </p>
                            <div class="address-actions">
                                <button class="btn btn-sm btn-outline-primary">
                                    Set as Default
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary" id="addAddressBtn">
                                <i class="fas fa-plus me-2"></i> Add Address
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Notification Preferences</h3>

                        <div class="preferences-section">
                            <h4>Email Notifications</h4>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>New Artwork Alerts</h5>
                                    <p>Get notified when artists you follow add new artworks</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="newArtworkNotification"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Special Offers</h5>
                                    <p>Receive discounts and special promotions</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="specialOffersNotification"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Order Updates</h5>
                                    <p>Get notified about order status changes</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="orderUpdatesNotification"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Newsletter</h5>
                                    <p>Receive our weekly newsletter with art insights</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="newsletterNotification" />
                                </div>
                            </div>
                        </div>

                        <div class="preferences-section">
                            <h4>Push Notifications</h4>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>New Messages</h5>
                                    <p>Get notified when you receive new messages</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="newMessagesNotification"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Artist Updates</h5>
                                    <p>Get notified about updates from artists you follow</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="artistUpdatesNotification"
                                        checked />
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="saveNotificationSettings">
                                Save Preferences
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Privacy Tab -->
                <div class="tab-pane fade" id="privacy" role="tabpanel">
                    <div class="settings-section">
                        <h3 class="section-title">Privacy Settings</h3>

                        <div class="preferences-section">
                            <h4>Profile Visibility</h4>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Public Profile</h5>
                                    <p>Allow other users to view your profile</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="publicProfileSetting"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Show Artwork Collection</h5>
                                    <p>Display your purchased artworks on your profile</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="showCollectionSetting"
                                        checked />
                                </div>
                            </div>
                        </div>

                        <div class="preferences-section">
                            <h4>Data Usage</h4>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Personalized Recommendations</h5>
                                    <p>
                                        Allow us to use your browsing history to recommend
                                        artworks
                                    </p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="personalRecommendationsSetting"
                                        checked />
                                </div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-info">
                                    <h5>Browsing Data</h5>
                                    <p>
                                        Allow us to collect browsing data to improve your
                                        experience
                                    </p>
                                </div>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="browsingDataSetting"
                                        checked />
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="savePrivacySettings">
                                Save Privacy Settings
                            </button>
                        </div>

                        <div class="delete-account-section">
                            <h4>Delete Account</h4>
                            <p class="text-muted">
                                Once you delete your account, there is no going back. Please
                                be certain.
                            </p>
                            <button
                                type="button"
                                class="btn btn-danger"
                                id="deleteAccountBtn">
                                Delete My Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include_once VIEWS . 'components/customer_footer.php'; ?>

    <!-- Bootstrap and jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../../js/config.js"></script>
    <script src="../../js/customer/settings.js"></script>
</body>

</html>