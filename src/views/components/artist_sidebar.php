    <!-- Artist Sidebar -->
    <aside class="artist-sidebar">
      <div class="artist-logo">
        <img
          src="../../assets/images/artshelf-logo.png"
          alt="ArtShelf Logo"
          height="40" />
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
          <a href="/artist/collections" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/collections') ? 'active' : ''; ?>">
            <i class="fas fa-layer-group"></i>
            My Collections
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="/artist/selling-history" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/selling-history') ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            Selling History
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="/artist/withdraw" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/withdraw') ? 'active' : ''; ?>">
            <i class="fas fa-money-bill-wave"></i>
            Withdraw
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="/artist/followers" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/followers') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i>
            Followers
          </a>
        </li>
        <li class="artist-nav-item">
          <a href="/artist/fairs" class="artist-nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/artist/fairs') ? 'active' : ''; ?>">
            <i class="fas fa-map-marker-alt"></i>
            Art Fairs
          </a>
        </li>
      </ul>
    </aside>