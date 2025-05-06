<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Customers | ArtShelf Admin</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/main.css" />
    <link rel="stylesheet" href="../../css/dashboard.css" />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
      rel="stylesheet"
    />
    <style>
      /* Admin Dashboard Styles */
      .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        background-color: var(--white);
        box-shadow: var(--shadow-md);
        padding: var(--space-lg);
        z-index: 1000;
        overflow-y: auto;
      }

      .admin-logo {
        text-align: center;
        padding: var(--space-lg) 0;
        margin-bottom: var(--space-lg);
        border-bottom: 1px solid var(--secondary-bg);
      }

      .admin-nav {
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .admin-nav-item {
        margin-bottom: var(--space-xs);
      }

      .admin-nav-link {
        display: flex;
        align-items: center;
        padding: var(--space-md);
        color: var(--secondary-text);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
      }

      .admin-nav-link:hover {
        color: var(--accent);
        background-color: var(--secondary-bg);
      }

      .admin-nav-link.active {
        color: var(--accent);
        background-color: var(--secondary-bg);
      }

      .admin-nav-link i {
        width: 20px;
        margin-right: var(--space-md);
      }

      .admin-content {
        margin-left: 250px;
        padding: var(--space-xl);
        min-height: 100vh;
        background-color: var(--main-bg);
      }

      .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-xl);
      }

      .admin-title {
        font-size: 1.75rem;
        margin: 0;
      }

      .table-container {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        padding: var(--space-lg);
        box-shadow: var(--shadow-md);
        margin-bottom: var(--space-xl);
      }

      .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-lg);
      }

      .table-title {
        font-size: 1.2rem;
        margin: 0;
        color: var(--primary-text-dark);
      }

      .admin-table {
        width: 100%;
      }

      .admin-table th {
        font-weight: 500;
        color: var(--primary-text-dark);
        padding: var(--space-md);
        border-bottom: 2px solid var(--secondary-bg);
      }

      .admin-table td {
        padding: var(--space-md);
        border-bottom: 1px solid var(--secondary-bg);
        color: var(--secondary-text);
      }

      .admin-table tr:last-child td {
        border-bottom: none;
      }

      .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-weight: 500;
      }

      .status-badge.active {
        background-color: #d4edda;
        color: #155724;
      }

      .status-badge.inactive {
        background-color: #f8d7da;
        color: #721c24;
      }

      .action-buttons {
        display: flex;
        gap: var(--space-sm);
      }

      .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
        border-radius: var(--radius-sm);
      }

      .admin-footer {
        margin-top: var(--space-xxl);
        padding-top: var(--space-lg);
        border-top: 1px solid var(--secondary-bg);
        color: var(--secondary-text);
        font-size: 0.9rem;
      }

      .filter-container {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        padding: var (--space-lg);
        box-shadow: var(--shadow-md);
        margin-bottom: var(--space-lg);
      }

      .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-md);
        margin-bottom: var(--space-md);
      }

      .filter-group {
        flex: 1;
        min-width: 200px;
      }

      .customer-avatar {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 50%;
      }

      .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: var(--space-lg);
      }

      .customer-activity {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
      }

      .customer-activity.high {
        background-color: #28a745;
      }

      .customer-activity.medium {
        background-color: #ffc107;
      }

      .customer-activity.low {
        background-color: #dc3545;
      }

      .stat-card {
        background-color: var(--white);
        border-radius: var(--radius-lg);
        padding: var(--space-lg);
        box-shadow: var(--shadow-md);
        margin-bottom: var(--space-xl);
        display: flex;
        align-items: center;
      }

      .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: var(--space-lg);
        color: var(--white);
        font-size: 1.5rem;
      }

      .stat-icon.primary {
        background-color: var(--primary);
      }

      .stat-icon.success {
        background-color: #28a745;
      }

      .stat-icon.warning {
        background-color: #ffc107;
      }

      .stat-icon.danger {
        background-color: #dc3545;
      }

      .stat-content h3 {
        font-size: 2rem;
        margin-bottom: 0;
        font-weight: 500;
      }

      .stat-content p {
        margin-bottom: 0;
        color: var(--secondary-text);
      }

      @media (max-width: 992px) {
        .admin-sidebar {
          transform: translateX(-100%);
          transition: transform var(--transition-medium);
        }

        .admin-sidebar.show {
          transform: translateX(0);
        }

        .admin-content {
          margin-left: 0;
        }

        .toggle-sidebar {
          display: block;
        }
      }
    </style>
  </head>
  <body>
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
      <div class="admin-logo">
        <img
          src="../../assets/images/artshelf-logo.png"
          alt="ArtShelf Logo"
          height="40"
        />
      </div>
      <ul class="admin-nav">
        <li class="admin-nav-item">
          <a href="dashboard.html" class="admin-nav-link">
            <i class="fas fa-home"></i>
            Dashboard
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="artworks.html" class="admin-nav-link">
            <i class="fas fa-palette"></i>
            Artworks
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="artists.html" class="admin-nav-link">
            <i class="fas fa-user-artist"></i>
            Artists
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="customers.html" class="admin-nav-link active">
            <i class="fas fa-users"></i>
            Customers
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="collections.html" class="admin-nav-link">
            <i class="fas fa-layer-group"></i>
            Collections
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="fairs.html" class="admin-nav-link">
            <i class="fas fa-map-marker-alt"></i>
            Art Fairs
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="orders.html" class="admin-nav-link">
            <i class="fas fa-shopping-cart"></i>
            Orders
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="withdrawals.html" class="admin-nav-link">
            <i class="fas fa-money-bill-wave"></i>
            Withdrawals
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="offers.html" class="admin-nav-link">
            <i class="fas fa-tag"></i>
            Offers
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="reports.html" class="admin-nav-link">
            <i class="fas fa-chart-bar"></i>
            Reports
          </a>
        </li>
        <li class="admin-nav-item">
          <a href="settings.html" class="admin-nav-link">
            <i class="fas fa-cog"></i>
            Settings
          </a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
      <div class="admin-header">
        <button class="btn btn-outline-primary d-lg-none toggle-sidebar">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="admin-title">Manage Customers</h1>
        <div class="dropdown">
          <button
            class="btn btn-outline-primary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
          >
            <img
              src="https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&w=200"
              alt="Admin"
              class="rounded-circle me-2"
              width="30"
              height="30"
            />
            Admin User
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="profile.html"
                ><i class="fas fa-user me-2"></i> Profile</a
              >
            </li>
            <li>
              <a class="dropdown-item" href="settings.html"
                ><i class="fas fa-cog me-2"></i> Settings</a
              >
            </li>
            <li><hr class="dropdown-divider" /></li>
            <li>
              <a
                class="dropdown-item text-danger"
                href="#"
                onclick="window.auth.logout(); return false;"
              >
                <i class="fas fa-sign-out-alt me-2"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Customer Stats -->
      <div class="row">
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon primary">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <h3 id="totalCustomersCount">0</h3>
              <p>Total Customers</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
              <h3 id="activeCustomersCount">0</h3>
              <p>Active Customers</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon warning">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
              <h3 id="pendingOrdersCount">0</h3>
              <p>Pending Orders</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon danger">
              <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-content">
              <h3 id="inactiveCustomersCount">0</h3>
              <p>Inactive Customers</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="filter-container">
        <div class="filter-header">
          <h2 class="table-title mb-3">Filter Customers</h2>
        </div>
        <form id="customerFilterForm">
          <div class="filter-row">
            <div class="filter-group">
              <label for="statusFilter" class="form-label">Status</label>
              <select class="form-select" id="statusFilter">
                <option value="all">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="filter-group">
              <label for="activityFilter" class="form-label"
                >Activity Level</label
              >
              <select class="form-select" id="activityFilter">
                <option value="all">All Activity</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
              </select>
            </div>
            <div class="filter-group">
              <label for="searchFilter" class="form-label">Search</label>
              <input
                type="text"
                class="form-control"
                id="searchFilter"
                placeholder="Search by name, email, or ID"
              />
            </div>
          </div>
          <div class="text-end">
            <button type="reset" class="btn btn-outline-secondary">
              Reset
            </button>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
          </div>
        </form>
      </div>

      <!-- Customers Container -->
      <div class="table-container">
        <div class="table-header">
          <h2 class="table-title">All Customers</h2>
          <div>
            <button
              type="button"
              class="btn btn-outline-primary me-2"
              id="exportBtn"
            >
              <i class="fas fa-download me-1"></i> Export
            </button>
            <button type="button" class="btn btn-primary" id="addCustomerBtn">
              <i class="fas fa-plus me-1"></i> Add Customer
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="admin-table" id="customersTable">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Email</th>
                <th>Date Joined</th>
                <th>Orders</th>
                <th>Spending</th>
                <th>Activity</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="customersTableBody">
              <!-- Will be populated by JavaScript -->
            </tbody>
          </table>
        </div>

        <div class="pagination-container">
          <nav aria-label="Customer pagination">
            <ul class="pagination" id="customerPagination">
              <!-- Will be populated by JavaScript -->
            </ul>
          </nav>
        </div>
      </div>

      <!-- Customer Details Modal -->
      <div
        class="modal fade"
        id="customerDetailsModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Customer Details</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body" id="customerDetailsContent">
              <!-- Will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
              <button
                type="button"
                class="btn btn-primary"
                id="editCustomerBtn"
              >
                Edit Profile
              </button>
              <button
                type="button"
                class="btn btn-danger"
                id="deactivateCustomerBtn"
              >
                Deactivate Account
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Add/Edit Customer Modal -->
      <div
        class="modal fade"
        id="editCustomerModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalTitle">Add Customer</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <form id="customerForm">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="customerName" class="form-label">Name</label>
                    <input
                      type="text"
                      class="form-control"
                      id="customerName"
                      required
                    />
                  </div>
                  <div class="col-md-6">
                    <label for="customerEmail" class="form-label">Email</label>
                    <input
                      type="email"
                      class="form-control"
                      id="customerEmail"
                      required
                    />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="customerPhone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="customerPhone" />
                  </div>
                  <div class="col-md-6">
                    <label for="customerStatus" class="form-label"
                      >Status</label
                    >
                    <select class="form-select" id="customerStatus" required>
                      <option value="true">Active</option>
                      <option value="false">Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="customerBio" class="form-label">Bio</label>
                  <textarea
                    class="form-control"
                    id="customerBio"
                    rows="3"
                  ></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">Address</label>
                  <div class="row g-2">
                    <div class="col-12">
                      <input
                        type="text"
                        class="form-control"
                        id="customerStreet"
                        placeholder="Street"
                      />
                    </div>
                    <div class="col-md-6">
                      <input
                        type="text"
                        class="form-control"
                        id="customerCity"
                        placeholder="City"
                      />
                    </div>
                    <div class="col-md-4">
                      <input
                        type="text"
                        class="form-control"
                        id="customerState"
                        placeholder="State"
                      />
                    </div>
                    <div class="col-md-2">
                      <input
                        type="text"
                        class="form-control"
                        id="customerZip"
                        placeholder="Zip"
                      />
                    </div>
                    <div class="col-12">
                      <input
                        type="text"
                        class="form-control"
                        id="customerCountry"
                        placeholder="Country"
                      />
                    </div>
                  </div>
                </div>
                <input type="hidden" id="customerId" />
              </form>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-primary"
                id="saveCustomerBtn"
              >
                Save Customer
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin Footer -->
      <footer class="admin-footer">
        <p>&copy; 2025 ArtShelf Admin Dashboard. All rights reserved.</p>
      </footer>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom Scripts -->
    <script src="../../js/config.js"></script>
    <script src="../../js/main.js"></script>
    <!-- <script src="../../js/auth.js"></script> -->
    <script src="../../js/models/User.js"></script>
    <script src="../../js/services/UserService.js"></script>
    <script src="../../js/admin/customers.js"></script>
  </body>
</html>
