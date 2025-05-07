<?php

// Function to display a single error message
function displayError($message) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

// Function to display a success message
function displaySuccess($message) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

// Display any error messages from the session
if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    displayError($_SESSION['error']);

    unset($_SESSION['error']);
}

// Display multiple error messages if they exist
if (isset($_SESSION['errors']) && is_array($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        displayError($error);
    }

    unset($_SESSION['errors']);
}

// Display authentication errors
if (isset($_SESSION['auth_error']) && !empty($_SESSION['auth_error'])) {
    displayError($_SESSION['auth_error']);

    unset($_SESSION['auth_error']);
}

// Display success messages
if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    displaySuccess($_SESSION['success']);

    unset($_SESSION['success']);
}


// Display general message 
if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
    echo '<div class="alert alert-info alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-info-circle me-2"></i>' . htmlspecialchars($_SESSION['message']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';

    unset($_SESSION['message']);
}
?>