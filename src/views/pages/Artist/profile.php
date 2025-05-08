<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Artist Dashboard | ArtShelf</title>
  <!-- Bootstrap CSS -->
  <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/main.css">
  <!-- Custom CSS for Artist Dashboard -->
  <link rel="stylesheet" href="../../assets/css/artist.dashboard.css">
  <link rel="stylesheet" href="/assets/css/admin.dashboard.css">


  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../assets/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

</head>

<body>

  <!-- Artist Sidebar -->
  <?php require_once VIEWS . "components/artist_sidebar.php"; ?>
  <!-- Main Content -->
  <main class="artist-content">
    <?php $pageTitle = "Artist Dashboard";
    require_once VIEWS . "components/artist_header.php"; ?>

    <?php require_once VIEWS . 'components/error_display.php'; ?>



    <div class="container-fluid">
      <div class="row">
        <!-- Profile Info Section -->
        <div class="col-lg-8">
          <div class="profile-section">
            <h2 class="profile-section-title">Profile Information</h2>
            <form id="profileForm" action="/artist/profileUpdate" method="POST" enctype="multipart/form-data" novalidate>
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input
                      type="text"
                      class="form-control"
                      id="firstName"
                      value="<?php echo $artist->getFirstName(); ?>"
                      required
                      minlength="4"
                      name="firstName" />
                  </div>
                  <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input
                      type="text"
                      class="form-control"
                      id="lastName"
                      value="<?php echo $artist->getLastName(); ?>"
                      required
                      minlength="4"
                      name="lastName" />
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      required
                      pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                      value="<?php echo $artist->getEmail(); ?>"
                      name="email" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input
                      type="tel"
                      class="form-control"
                      id="phone"
                      pattern="^\+?[0-9]{10,15}$"
                      value="<?php echo $artist->getPhone(); ?>"
                      name="phone" />
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input
                      type="text"
                      class="form-control"
                      id="address"
                      value="<?php echo $artist->getAddress(); ?>"
                      name="address" />
                  </div>
                  <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input
                      type="date"
                      class="form-control"
                      id="dob"
                      value="<?php echo $artist->getBDate(); ?>"
                      name="dob" />
                  </div>
                </div>
                <div class="col-12">
                  <div class="mb-3">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea
                      class="form-control"
                      id="bio"
                      name="bio"
                      rows="4"><?php echo $artist->getBio(); ?></textarea>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="emailNotifications"
                      name="emailNotifications"
                      value="1"
                      <?php echo ($artist->getEmailNotification() == 1) ? 'checked' : ''; ?> />
                    <label class="form-check-label" for="emailNotifications">
                      Email Notifications
                    </label>
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
            <form id="passwordForm" action="/artist/changePassword" method="POST" novalidate>
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
                src="/uploads/profiles/<?php echo $artist->getProfilePic(); ?>"
                alt="Artist Avatar"
                class="profile-avatar"
                id="profileAvatar" />
              <div class="mt-3">
                <form id="photoForm" action="/artist/profilePicUpdate" method="POST" enctype="multipart/form-data">
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


    <!-- Artist Footer -->
    <footer class="artist-footer">
      <p>&copy; 2025 ArtShelf Artist Dashboard. All rights reserved.</p>
    </footer>
  </main>

  <!-- Popper.js (required for Bootstrap dropdowns) -->
  <script src="../assets/js/popper.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../assets/js/bootstrap.min.js"></script>
  <!-- jQuery -->
  <script src="../assets/js/jquery-3.7.1.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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