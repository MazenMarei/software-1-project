<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Profile | ArtShelf Admin</title>
  <!-- Bootstrap CSS -->
  <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/admin.dashboard.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
  <!-- Admin Sidebar -->
  <?php require_once VIEWS . 'components/admin_sideBar.php'; ?>

  <!-- Main Content -->
  <main class="admin-content">
    <?php
    // Set page title for admin header
    $pageTitle = 'Admin Profile';
    // Include admin header component
    require_once VIEWS . 'components/admin_header.php';
    ?>

    <?php require_once VIEWS . 'components/error_display.php'; ?>


    <div class="container-fluid">
      <div class="row">
        <!-- Profile Info Section -->
        <div class="col-lg-8">
          <div class="profile-section">
            <h2 class="profile-section-title">Profile Information</h2>
            <form id="profileForm" action="/admin/profileUpdate" method="POST">
              <div class="row mb-4">
                <div class="col">

                  <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input
                      type="text"
                      value="<?php echo $admin->getFirstName(); ?>"
                      class="form-control"
                      id="firstName"
                      required
                      name="firstName" />
                  </div>
                  <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input
                      type="text"
                      value="<?php echo $admin->getLastName(); ?>"
                      class="form-control"
                      id="lastName"
                      required
                      name="lastName" />
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                      type="email"
                      value="<?php echo $admin->getEmail(); ?>"
                      class="form-control"
                      id="email"
                      required
                      name="email" />
                  </div>
                </div>

              </div>
              <button type="submit" class="btn btn-primary">
                Update Profile
              </button>
            </form>
          </div>

          <!-- Security Section -->
          <div class="profile-section">
            <h2 class="profile-section-title">Security</h2>
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

        <!-- Sidebar Content -->
        <div class="col-lg-4">
          <!-- Profile Avatar Section -->
          <div class="profile-section">
            <h2 class="profile-section-title">Profile Picture</h2>
            <div class="profile-avatar-container">
              <img
                src="/uploads/profiles/<?php echo $admin->getProfilePic(); ?>"
                alt="Admin Avatar"
                class="profile-avatar"
                id="profileAvatar" />
              <div class="mt-3">
                <form id="photoForm" action="/admin/profilePicUpdate" method="POST" enctype="multipart/form-data">
                  <label for="avatarUpload" class="btn btn-outline-primary">
                    Change Photo
                  </label>
                  <input
                    type="file"
                    id="avatarUpload"
                    name="profilePic"
                    style="display: none"
                    accept="image/*" />

                  <button type="submit" class="btn btn-primary ms-2" id="uploadBtn">
                    Upload
                  </button>
                </form>
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>

    <!-- Admin Footer -->
    <footer class="admin-footer">
      <p>&copy; 2025 ArtShelf Admin Dashboard. All rights reserved.</p>
    </footer>
  </main>

  <!-- Popper.js (required for Bootstrap dropdowns) -->
  <script src="/assets/js/popper.min.js"></script>
  <!-- jQuery -->
  <script src="../../assets/js/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../../assets/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

  <!-- Custom JS -->
  <script src="../../assets/js/admin/artists.js"></script>
  <script src="../../assets/js/admin.js"></script>
  <script>
    $(document).ready(function() {
      // Password validation
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

      // For avatar upload
      $("#avatarUpload").on("change", function() {
        const file = this.files[0];
        if (file) {
          // Show preview
          const reader = new FileReader();
          reader.onload = function(e) {
            $("#profileAvatar").attr("src", e.target.result);
          };
          reader.readAsDataURL(file);

        
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