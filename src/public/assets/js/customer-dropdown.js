// Function to initialize all dropdowns
function initDropdowns() {
  console.log("Initializing dropdowns...");

  // Get all dropdown toggles
  const dropdownToggles = document.querySelectorAll(
    '[data-bs-toggle="dropdown"]'
  );
  console.log("Found", dropdownToggles.length, "dropdown toggles");

  // Initialize each dropdown with Bootstrap
  dropdownToggles.forEach((toggle, index) => {
    console.log("Initializing dropdown", index);
    try {
      new bootstrap.Dropdown(toggle);
    } catch (e) {
      console.error("Error initializing dropdown:", e);
    }
  });

  // Add click event listeners for debugging
  dropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      console.log("Dropdown toggle clicked:", this);
    });
  });
}

// Initialize when the DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM fully loaded");

  // Check if Bootstrap is available
  if (typeof bootstrap === "undefined") {
    console.error("Bootstrap JavaScript is not loaded!");
  } else {
    console.log("Bootstrap version:", bootstrap.Dropdown.VERSION);
    initDropdowns();
  }

  // Check for jQuery since some Bootstrap components might need it
  if (typeof jQuery === "undefined") {
    console.warn("jQuery is not loaded!");
  } else {
    console.log("jQuery version:", jQuery.fn.jquery);
  }
});
