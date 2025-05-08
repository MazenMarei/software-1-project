    <!-- Artist Sidebar -->
    <aside class="artist-sidebar">
      <div class="artist-logo">
        <img
          src="../../assets/images/artshelf-logo.png"
          alt="ArtShelf Logo"
          height="40"
        />
      </div>
      <ul class="artist-nav">
        <li class="artist-nav-item">
          <a href="/artist/dashboard" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            Dashboard
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="/artist/artworks" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/artworks') ? 'active' : ''; ?>">
            <i class="fas fa-palette"></i>
            My Artworks
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="new-artwork" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/new-artwork') ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i>
            Add New Artwork
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="collections.html" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/collections') ? 'active' : ''; ?>">
            <i class="fas fa-layer-group"></i>
            My Collections
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="sales.html" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/sales') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            Sales
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="earnings.html" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/earnings') ? 'active' : ''; ?>">
            <i class="fas fa-money-bill-wave"></i>
            Earnings
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="reviews.html" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/reviews') ? 'active' : ''; ?>">
            <i class="fas fa-star"></i>
            Reviews
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="fairs.html" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/fairs') ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i>
            Art Fairs
          </a>
        </li>
      </ul>
    </aside>