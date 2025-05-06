// Admin Settings Page JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the page
  initSettingsPage();

  // Add event listeners
  setupEventListeners();

  // Check authentication
  //   checkAdminAuthentication();
});

/**
 * Initialize the settings page
 */
function initSettingsPage() {
  // Load settings data from localStorage or API
  loadSettings();

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
 * Load settings from localStorage or API
 */
function loadSettings() {
  // For demo purposes, we're using localStorage
  // In a production environment, this would make API calls to fetch settings

  // Check if settings exist in localStorage
  const settings =
    JSON.parse(localStorage.getItem("artshelfSettings")) ||
    getDefaultSettings();

  // Populate form fields with settings
  populateGeneralSettings(settings.general);
  populateEmailSettings(settings.email);
  populatePaymentSettings(settings.payment);
  populateSystemSettings(settings.system);
}

/**
 * Get default settings if none exist
 */
function getDefaultSettings() {
  return {
    general: {
      siteName: "ArtShelf",
      siteEmail: "support@artshelf.com",
      timezone: "UTC-05:00",
      currency: "USD",
      dateFormat: "MM/DD/YYYY",
      maintenanceMode: false,
    },
    email: {
      smtpServer: "smtp.artshelf.com",
      smtpPort: 587,
      smtpUsername: "notifications@artshelf.com",
      smtpPassword: "••••••••••••",
    },
    payment: {
      stripePublicKey: "pk_test_51Hxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      stripeSecretKey: "••••••••••••••••••••••••••••••••••••••••••••••",
      paypalClientId: "AxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxURs",
      paypalClientSecret: "••••••••••••••••••••••••••••••••••••••••••••••",
      enableTestMode: true,
    },
    system: {
      maxUploadSize: 50,
      sessionTimeout: 30,
      defaultPagination: 20,
      logRetention: 90,
      backupFrequency: "daily",
    },
  };
}

/**
 * Populate general settings form fields
 */
function populateGeneralSettings(generalSettings) {
  document.getElementById("siteName").value = generalSettings.siteName;
  document.getElementById("siteEmail").value = generalSettings.siteEmail;
  document.getElementById("timezone").value = generalSettings.timezone;
  document.getElementById("currency").value = generalSettings.currency;
  document.getElementById("dateFormat").value = generalSettings.dateFormat;
  document.getElementById("maintenanceMode").checked =
    generalSettings.maintenanceMode;
}

/**
 * Populate email settings form fields
 */
function populateEmailSettings(emailSettings) {
  document.getElementById("smtpServer").value = emailSettings.smtpServer;
  document.getElementById("smtpPort").value = emailSettings.smtpPort;
  document.getElementById("smtpUsername").value = emailSettings.smtpUsername;
  document.getElementById("smtpPassword").value = emailSettings.smtpPassword;
}

/**
 * Populate payment settings form fields
 */
function populatePaymentSettings(paymentSettings) {
  document.getElementById("stripePublicKey").value =
    paymentSettings.stripePublicKey;
  document.getElementById("stripeSecretKey").value =
    paymentSettings.stripeSecretKey;
  document.getElementById("paypalClientId").value =
    paymentSettings.paypalClientId;
  document.getElementById("paypalClientSecret").value =
    paymentSettings.paypalClientSecret;
  document.getElementById("enableTestMode").checked =
    paymentSettings.enableTestMode;
}

/**
 * Populate system settings form fields
 */
function populateSystemSettings(systemSettings) {
  document.getElementById("maxUploadSize").value = systemSettings.maxUploadSize;
  document.getElementById("sessionTimeout").value =
    systemSettings.sessionTimeout;
  document.getElementById("defaultPagination").value =
    systemSettings.defaultPagination;
  document.getElementById("logRetention").value = systemSettings.logRetention;
  document.getElementById("backupFrequency").value =
    systemSettings.backupFrequency;
}

/**
 * Set up event listeners for form submissions and buttons
 */
function setupEventListeners() {
  // General Settings Form
  const generalSettingsForm = document.getElementById("generalSettingsForm");
  if (generalSettingsForm) {
    generalSettingsForm.addEventListener("submit", handleGeneralSettingsSubmit);
  }

  // Email Settings Form
  const emailSettingsForm = document.getElementById("emailSettingsForm");
  if (emailSettingsForm) {
    emailSettingsForm.addEventListener("submit", handleEmailSettingsSubmit);
  }

  // Payment Settings Form
  const paymentSettingsForm = document.getElementById("paymentSettingsForm");
  if (paymentSettingsForm) {
    paymentSettingsForm.addEventListener("submit", handlePaymentSettingsSubmit);
  }

  // System Settings Form
  const systemSettingsForm = document.getElementById("systemSettingsForm");
  if (systemSettingsForm) {
    systemSettingsForm.addEventListener("submit", handleSystemSettingsSubmit);
  }

  // Special buttons
  const runBackupBtn = document.getElementById("runBackupBtn");
  if (runBackupBtn) {
    runBackupBtn.addEventListener("click", handleRunBackup);
  }

  const clearCacheBtn = document.getElementById("clearCacheBtn");
  if (clearCacheBtn) {
    clearCacheBtn.addEventListener("click", handleClearCache);
  }

  // Logout button
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", handleLogout);
  }
}

/**
 * Handle submission of general settings form
 */
function handleGeneralSettingsSubmit(event) {
  event.preventDefault();

  const settings =
    JSON.parse(localStorage.getItem("artshelfSettings")) ||
    getDefaultSettings();

  // Update general settings
  settings.general = {
    siteName: document.getElementById("siteName").value,
    siteEmail: document.getElementById("siteEmail").value,
    timezone: document.getElementById("timezone").value,
    currency: document.getElementById("currency").value,
    dateFormat: document.getElementById("dateFormat").value,
    maintenanceMode: document.getElementById("maintenanceMode").checked,
  };

  // Save to localStorage (in production, this would be an API call)
  localStorage.setItem("artshelfSettings", JSON.stringify(settings));

  // Show success message
  showToast("General settings saved successfully!");
}

/**
 * Handle submission of email settings form
 */
function handleEmailSettingsSubmit(event) {
  event.preventDefault();

  const settings =
    JSON.parse(localStorage.getItem("artshelfSettings")) ||
    getDefaultSettings();

  // Update email settings
  settings.email = {
    smtpServer: document.getElementById("smtpServer").value,
    smtpPort: parseInt(document.getElementById("smtpPort").value),
    smtpUsername: document.getElementById("smtpUsername").value,
    smtpPassword: document.getElementById("smtpPassword").value,
  };

  // Save to localStorage (in production, this would be an API call)
  localStorage.setItem("artshelfSettings", JSON.stringify(settings));

  // Show success message
  showToast("Email settings saved successfully!");
}

/**
 * Handle submission of payment settings form
 */
function handlePaymentSettingsSubmit(event) {
  event.preventDefault();

  const settings =
    JSON.parse(localStorage.getItem("artshelfSettings")) ||
    getDefaultSettings();

  // Update payment settings
  settings.payment = {
    stripePublicKey: document.getElementById("stripePublicKey").value,
    stripeSecretKey: document.getElementById("stripeSecretKey").value,
    paypalClientId: document.getElementById("paypalClientId").value,
    paypalClientSecret: document.getElementById("paypalClientSecret").value,
    enableTestMode: document.getElementById("enableTestMode").checked,
  };

  // Save to localStorage (in production, this would be an API call)
  localStorage.setItem("artshelfSettings", JSON.stringify(settings));

  // Show success message
  showToast("Payment settings saved successfully!");
}

/**
 * Handle submission of system settings form
 */
function handleSystemSettingsSubmit(event) {
  event.preventDefault();

  const settings =
    JSON.parse(localStorage.getItem("artshelfSettings")) ||
    getDefaultSettings();

  // Update system settings
  settings.system = {
    maxUploadSize: parseInt(document.getElementById("maxUploadSize").value),
    sessionTimeout: parseInt(document.getElementById("sessionTimeout").value),
    defaultPagination: parseInt(
      document.getElementById("defaultPagination").value
    ),
    logRetention: parseInt(document.getElementById("logRetention").value),
    backupFrequency: document.getElementById("backupFrequency").value,
  };

  // Save to localStorage (in production, this would be an API call)
  localStorage.setItem("artshelfSettings", JSON.stringify(settings));

  // Show success message
  showToast("System settings saved successfully!");
}

/**
 * Handle manual backup button click
 */
function handleRunBackup() {
  // In a real application, this would make an API call to trigger a backup
  console.log("Running manual backup...");

  // Simulate a delay for the backup process
  setTimeout(() => {
    showToast("Manual backup completed successfully!");
  }, 2000);
}

/**
 * Handle clear cache button click
 */
function handleClearCache() {
  // In a real application, this would make an API call to clear the cache
  console.log("Clearing system cache...");

  // Simulate a delay for the cache clearing process
  setTimeout(() => {
    showToast("System cache cleared successfully!");
  }, 1500);
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
function showToast(message) {
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
  const toastHtml = `
    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
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
