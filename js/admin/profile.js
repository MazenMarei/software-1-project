// Admin Profile Page JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the page
  initProfilePage();

  // Add event listeners
  setupEventListeners();

  // Check authentication
  // checkAdminAuthentication();
});

/**
 * Initialize the profile page
 */
function initProfilePage() {
  // Load profile data from localStorage or API
  loadProfileData();

  // Set up the sidebar toggle for mobile
  const toggleSidebarBtn = document.querySelector(".toggle-sidebar");
  const sidebar = document.querySelector(".admin-sidebar");

  if (toggleSidebarBtn && sidebar) {
    toggleSidebarBtn.addEventListener("click", () => {
      sidebar.classList.toggle("show");
    });
  }
}

/**
 * Load profile data from localStorage or API
 */
function loadProfileData() {
  // For demo purposes, we're using localStorage
  // In a production environment, this would make API calls to fetch profile data
  const adminProfile =
    JSON.parse(localStorage.getItem("artshelfAdminProfile")) ||
    getDefaultProfile();

  // Populate profile form fields
  populateProfileForm(adminProfile);
}

/**
 * Get default profile if none exists
 */
function getDefaultProfile() {
  return {
    firstName: "Admin",
    lastName: "User",
    email: "admin@artshelf.com",
    phone: "+1 (555) 123-4567",
    position: "System Administrator",
    department: "administration",
    bio: "Experienced administrator with a background in fine arts and gallery management. Responsible for maintaining the ArtShelf platform and ensuring smooth operations.",
    avatarUrl:
      "https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&w=200",
    notificationPreferences: {
      email: true,
      sms: false,
      app: true,
      digest: true,
    },
  };
}

/**
 * Populate profile form fields with data
 */
function populateProfileForm(profileData) {
  // Personal info
  document.getElementById("firstName").value = profileData.firstName;
  document.getElementById("lastName").value = profileData.lastName;
  document.getElementById("email").value = profileData.email;
  document.getElementById("phone").value = profileData.phone;
  document.getElementById("position").value = profileData.position;
  document.getElementById("department").value = profileData.department;
  document.getElementById("bio").value = profileData.bio;

  // Avatar
  const avatarElement = document.getElementById("profileAvatar");
  if (avatarElement && profileData.avatarUrl) {
    avatarElement.src = profileData.avatarUrl;
  }

  // Notification preferences
  document.getElementById("emailNotifications").checked =
    profileData.notificationPreferences.email;
  document.getElementById("smsNotifications").checked =
    profileData.notificationPreferences.sms;
  document.getElementById("appNotifications").checked =
    profileData.notificationPreferences.app;
  document.getElementById("activityDigest").checked =
    profileData.notificationPreferences.digest;
}

/**
 * Set up event listeners for form submissions and buttons
 */
function setupEventListeners() {
  // Profile Form
  const profileForm = document.getElementById("profileForm");
  if (profileForm) {
    profileForm.addEventListener("submit", handleProfileFormSubmit);
  }

  // Password Form
  const passwordForm = document.getElementById("passwordForm");
  if (passwordForm) {
    passwordForm.addEventListener("submit", handlePasswordFormSubmit);
  }

  // Notification Form
  const notificationForm = document.getElementById("notificationForm");
  if (notificationForm) {
    notificationForm.addEventListener("submit", handleNotificationFormSubmit);
  }

  // Avatar Upload
  const avatarUpload = document.getElementById("avatarUpload");
  if (avatarUpload) {
    avatarUpload.addEventListener("change", handleAvatarUpload);
  }

  // Remove Photo button
  const removePhotoBtn = document.getElementById("removePhotoBtn");
  if (removePhotoBtn) {
    removePhotoBtn.addEventListener("click", handleRemovePhoto);
  }

  // 2FA button
  const enable2FABtn = document.getElementById("enable2FABtn");
  if (enable2FABtn) {
    enable2FABtn.addEventListener("click", handle2FASetup);
  }

  // Sessions button
  const sessionBtn = document.getElementById("sessionBtn");
  if (sessionBtn) {
    sessionBtn.addEventListener("click", handleViewSessions);
  }

  // Logout button
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", handleLogout);
  }
}

/**
 * Handle profile form submission
 */
function handleProfileFormSubmit(event) {
  event.preventDefault();

  // Get current profile data
  const profileData =
    JSON.parse(localStorage.getItem("artshelfAdminProfile")) ||
    getDefaultProfile();

  // Update profile data with form values
  profileData.firstName = document.getElementById("firstName").value;
  profileData.lastName = document.getElementById("lastName").value;
  profileData.email = document.getElementById("email").value;
  profileData.phone = document.getElementById("phone").value;
  profileData.position = document.getElementById("position").value;
  profileData.department = document.getElementById("department").value;
  profileData.bio = document.getElementById("bio").value;

  // Save to localStorage
  localStorage.setItem("artshelfAdminProfile", JSON.stringify(profileData));

  // Show success notification
  showToast("Profile information updated successfully!");
}

/**
 * Handle password form submission
 */
function handlePasswordFormSubmit(event) {
  event.preventDefault();

  const currentPassword = document.getElementById("currentPassword").value;
  const newPassword = document.getElementById("newPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;

  // Validation
  if (!currentPassword || !newPassword || !confirmPassword) {
    showToast("All password fields are required", "error");
    return;
  }

  if (newPassword !== confirmPassword) {
    showToast("New passwords do not match", "error");
    return;
  }

  // In a real app, this would verify the current password and update with the new one
  // For now, we'll just simulate success

  // Clear the form
  document.getElementById("currentPassword").value = "";
  document.getElementById("newPassword").value = "";
  document.getElementById("confirmPassword").value = "";

  // Show success notification
  showToast("Password changed successfully!");
}

/**
 * Handle notification preferences form submission
 */
function handleNotificationFormSubmit(event) {
  event.preventDefault();

  // Get current profile data
  const profileData =
    JSON.parse(localStorage.getItem("artshelfAdminProfile")) ||
    getDefaultProfile();

  // Update notification preferences
  profileData.notificationPreferences = {
    email: document.getElementById("emailNotifications").checked,
    sms: document.getElementById("smsNotifications").checked,
    app: document.getElementById("appNotifications").checked,
    digest: document.getElementById("activityDigest").checked,
  };

  // Save to localStorage
  localStorage.setItem("artshelfAdminProfile", JSON.stringify(profileData));

  // Show success notification
  showToast("Notification preferences updated successfully!");
}

/**
 * Handle avatar upload
 */
function handleAvatarUpload(event) {
  const file = event.target.files[0];

  if (file) {
    // In a real application, this would upload the file to a server
    // For now, we'll just show a preview using FileReader
    const reader = new FileReader();

    reader.onload = function (e) {
      // Update avatar preview
      const avatarElement = document.getElementById("profileAvatar");
      if (avatarElement) {
        avatarElement.src = e.target.result;
      }

      // Update profile data
      const profileData =
        JSON.parse(localStorage.getItem("artshelfAdminProfile")) ||
        getDefaultProfile();
      profileData.avatarUrl = e.target.result;
      localStorage.setItem("artshelfAdminProfile", JSON.stringify(profileData));

      // Show success notification
      showToast("Profile picture updated successfully!");
    };

    reader.readAsDataURL(file);
  }
}

/**
 * Handle remove photo button click
 */
function handleRemovePhoto() {
  // Get default avatar URL (could be a placeholder image)
  const defaultAvatarUrl = "https://via.placeholder.com/150?text=Admin";

  // Update avatar preview
  const avatarElement = document.getElementById("profileAvatar");
  if (avatarElement) {
    avatarElement.src = defaultAvatarUrl;
  }

  // Update profile data
  const profileData =
    JSON.parse(localStorage.getItem("artshelfAdminProfile")) ||
    getDefaultProfile();
  profileData.avatarUrl = defaultAvatarUrl;
  localStorage.setItem("artshelfAdminProfile", JSON.stringify(profileData));

  // Show success notification
  showToast("Profile picture removed successfully!");
}

/**
 * Handle 2FA setup button click
 */
function handle2FASetup() {
  // In a real application, this would open a modal with 2FA setup options
  // For now, we'll just simulate it with an alert
  showToast(
    "2FA management functionality will be implemented in the next update!",
    "info"
  );
}

/**
 * Handle view sessions button click
 */
function handleViewSessions() {
  // In a real application, this would open a modal showing active sessions
  // For now, we'll just simulate it with an alert
  showToast(
    "Session management functionality will be implemented in the next update!",
    "info"
  );
}

/**
 * Handle admin logout
 */
function handleLogout(event) {
  event.preventDefault();

  // Clear any auth tokens
  localStorage.removeItem("artshelfAdminToken");

  // Redirect to login page
  window.location.href = "../login.html";
}

/**
 * Check if the user is authenticated as an admin
 * In a real application, this would verify the auth token with the backend
 */
function checkAdminAuthentication() {
  const token = localStorage.getItem("artshelfAdminToken");

  // If no token exists, redirect to login
  if (!token) {
    window.location.href = "../login.html";
  }
}

/**
 * Show a toast notification
 */
function showToast(message, type = "success") {
  // Check if toast container exists, if not create it
  let toastContainer = document.querySelector(".toast-container");

  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.className =
      "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(toastContainer);
  }

  // Create toast element
  const toastId = "toast-" + Date.now();
  const toastHeaderClass =
    type === "error"
      ? "bg-danger text-white"
      : type === "info"
      ? "bg-info text-white"
      : "bg-success text-white";

  const toastHtml = `
    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header ${toastHeaderClass}">
        <strong class="me-auto">ArtShelf Admin</strong>
        <small>Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        ${message}
      </div>
    </div>
  `;

  // Add toast to container
  toastContainer.innerHTML += toastHtml;

  // Initialize and show the toast
  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement);
  toast.show();

  // Remove toast after it's hidden
  toastElement.addEventListener("hidden.bs.toast", () => {
    toastElement.remove();
  });
}
