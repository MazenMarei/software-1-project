<?php

/**
 * Admin Header Component
 * This component provides a consistent header for all admin pages
 * It includes the sidebar toggle button for mobile view and the admin dropdown menu
 */
?>
<div class="admin-header">
    <button class="btn btn-outline-primary d-lg-none toggle-sidebar">
        <i class="fas fa-bars"></i>
    </button>
    <div class="dropdown order-0 order-md-1">
        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <img src="/uploads/profiles/<?php echo $admin->getProfilePic(); ?>" alt="Admin" class="rounded-circle me-2" width="30" height="30">
            <?php echo $admin->getFullName(); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/admin/profile"><i class="fas fa-user me-2"></i> Profile</a></li>
            <li><a class="dropdown-item" href="/admin/settings"><i class="fas fa-cog me-2"></i> Settings</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item text-danger" href="/logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a></li>
        </ul>
    </div>
    <h1 class="admin-title"><?php echo $pageTitle ?? 'Admin Dashboard'; ?></h1>
</div>