"use strict";

// Tab switching functionality
document.querySelectorAll(".auth-tab").forEach((tab) => {
  tab.addEventListener("click", function () {
    // Remove active class from all tabs
    document
      .querySelectorAll(".auth-tab")
      .forEach((t) => t.classList.remove("active"));
    // Add active class to clicked tab
    this.classList.add("active");

    // Hide all forms
    document
      .querySelectorAll(".auth-form")
      .forEach((form) => form.classList.remove("active"));
    // Show the corresponding form
    document
      .getElementById(this.getAttribute("data-tab") + "-form")
      .classList.add("active");

    // Hide any alerts that might be showing
    document
      .querySelectorAll(".alert")
      .forEach((alert) => alert.classList.add("d-none"));

    // Reset all forms
    document.querySelectorAll("form").forEach((form) => {
      form.classList.remove("was-validated");
      form.reset();
    });
  });
});

// Password toggle functionality
document.querySelectorAll(".password-toggle").forEach((toggle) => {
  toggle.addEventListener("click", function () {
    const input = this.closest(".password-input").querySelector("input");
    const type =
      input.getAttribute("type") === "password" ? "text" : "password";
    input.setAttribute("type", type);

    // Toggle icon
    this.querySelector("i").classList.toggle("fa-eye");
    this.querySelector("i").classList.toggle("fa-eye-slash");
  });
});

// Fetch all the forms we want to apply custom Bootstrap validation styles to
const forms = document.querySelectorAll(".needs-validation");

// Validate the password confirmation
const registerPassword = document.getElementById("registerPassword");
const registerFirstName = document.getElementById("registerFirstName");
const registerLastName = document.getElementById("registerLastName");

registerPassword.addEventListener("input", validatePassword);
registerFirstName.addEventListener("input", validateNames);
registerLastName.addEventListener("input", validateNames);

function validatePassword() {
  if (registerPassword.value.length < 8) {
    registerPassword.setCustomValidity(
      "Password must be at least 8 characters long."
    );
  } else if (!/[A-Z]/.test(registerPassword.value)) {
    registerPassword.setCustomValidity(
      "Password must contain at least one uppercase letter."
    );
  } else {
    registerPassword.setCustomValidity("");
  }
}

function validateNames() {
  // Check if first name contains only letters
  if (
    registerFirstName.value &&
    !/^[A-Za-z\s]+$/.test(registerFirstName.value)
  ) {
    registerFirstName.setCustomValidity(
      "First name must contain only letters (no numbers or special characters)."
    );
  } else {
    registerFirstName.setCustomValidity("");
  }

  // Check if last name contains only letters
  if (registerLastName.value && !/^[A-Za-z\s]+$/.test(registerLastName.value)) {
    registerLastName.setCustomValidity(
      "Last name must contain only letters (no numbers or special characters)."
    );
  } else {
    registerLastName.setCustomValidity("");
  }

  // Check if first name and last name are identical
  if (
    registerFirstName.value &&
    registerLastName.value &&
    registerFirstName.value.toLowerCase() ===
      registerLastName.value.toLowerCase()
  ) {
    registerLastName.setCustomValidity(
      "First name and last name cannot be the same."
    );
  }
}

// Loop over them and prevent submission
Array.from(forms).forEach((form) => {
  form.addEventListener(
    "submit",
    (event) => {
      // Re-validate password and names before submission
      if (form.id === "registerForm") {
        validatePassword();
        validateNames();
      }

      if (!form.checkValidity()) {

        event.preventDefault();
        event.stopPropagation();
      }

      form.classList.add("was-validated");
    },
    false
  );
});
