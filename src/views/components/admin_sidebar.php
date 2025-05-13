<aside class="admin-sidebar">
    <div class="admin-logo">
        <img
            src="/assets/images/artshelf-logo.png"
            alt="ArtShelf Logo"
            height="40" />
    </div>
    <ul class="admin-nav">
        <li class="admin-nav-item">
            <a href="/admin/dashboard" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/artworks" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/artworks') ? 'active' : ''; ?>">
                <i class="fas fa-palette"></i>
                Artworks
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/artists" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/artists') ? 'active' : ''; ?>">
                <i class="fa-solid fa-paintbrush"></i>
                Artists
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/customers" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/customers') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Customers
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/collections" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/collections') ? 'active' : ''; ?>">
                <i class="fas fa-layer-group"></i>
                Collections
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/fairs" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/fairs') ? 'active' : ''; ?>">
                <i class="fas fa-map-marker-alt"></i>
                Art Fairs
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/orders" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/orders') ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>
                Orders
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/withdrawals" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/withdrawals') ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                Withdrawals
            </a>
        </li>
        <li class="admin-nav-item">
            <a href="/admin/questionnaire" class="admin-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/questionnaire') ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                Questionnaire
            </a>
        </li>
   
    </ul>
</aside>