<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Customers | ArtShelf Admin</title>
  <!-- Bootstrap CSS -->
  <link
    href="../../assets/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/main.css" />
  <link rel="stylesheet" href="../../assets/css/admin.dashboard.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../assets/css/all.min.css" />
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="../../assets/css/all.min.css" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet" />

</head>

<body>
  <?php require_once VIEWS . "components/admin_sidebar.php"; ?>

  <!-- Main Content -->
  <main class="admin-content">


    <?php
    $pageTitle = "Manage Customers";
    require_once VIEWS . "components/admin_header.php"; ?>


    <?php require_once VIEWS . 'components/error_display.php'; ?>

    <!-- statistic section -->
    <div class="row mb-4 justify-content-center">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon primary">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count($customers) ?> </h3>
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
            <h3> <?php echo count(array_filter($customers, function ($artist) {
                    return $artist['status'] === 'Accepted';
                  })) ?> </h3>
            <p>Accepted Customers</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon danger">
            <i class="fas fa-user-slash"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count(array_filter($customers, function ($artist) {
                    return $artist['status'] === 'Rejected';
                  })) ?> </h3>
            <p>Rejected Customers</p>
          </div>
        </div>
      </div>
    </div>

    <div class="admin-header">
      <h2 class="admin-title">Manage Customers</h2>
    </div>
    <!-- Customers Container -->
    <div class="table-container">
      <div class="table-header">
        <h2 class="table-title">All Customers</h2>
        <div class="table-filters">
          <select id="statusFilter" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="Accepted">Accepted</option>
            <option value="Rejected">Rejected</option>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover search-table" id="customersTable">

          <thead>
            <tr>
              <th>Customer</th>
              <th>Email</th>
              <th>Orders</th>
              <th>Spending</th>
              <th>Activity</th>
              <th>Status</th>
              <th>Date Joined</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <?php if (isset($customers) && !empty($customers)) : ?>
              <?php foreach ($customers as $customer) : ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="/uploads/profiles/<?php echo $customer['profilePic']; ?>" alt="Customer Avatar" class="small-avatar me-3" />
                      <div>
                        <div class="fw-bold"><?= htmlspecialchars($customer['Fname'] . ' ' . $customer['Lname']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($customer['username'])   ?></small>
                      </div>
                    </div>
                  </td>
                  <td><?php echo ($customer['Email']); ?></td>
                  <td>0</td>
                  <td>$0</td>
                  <td><span class="customer-activity"> low</span></td>
                  <td>
                    <span class="status-badge <?= strtolower($customer['status']) ?>">
                      <?= htmlspecialchars($customer['status']) ?>
                    </span>
                  </td>
                  <td><?php echo ($customer['registerDate']); ?></td>
                  <td>
                    <div class="btn-group">
                      <?php if ($customer['status'] == 'Accepted'): ?>
                        <button class="btn btn-sm btn-outline-danger ban-btn"
                          onclick="banFun(this)"

                          data-id="<?= $customer['userID'] ?>"
                          data-name="<?= htmlspecialchars($customer['Fname'] . ' ' . $customer['Lname']) ?>">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      <?php endif; ?>

                      <?php if ($customer['status'] == 'Rejected'): ?>
                        <button class="btn btn-sm btn-outline-success unban-btn"
                          onclick="unbanFun(this)"

                          data-id="<?= $customer['userID'] ?>"
                          data-name="<?= htmlspecialchars($customer['Fname'] . ' ' . $customer['Lname']) ?>">
                          <i class="fa-solid fa-lock-open"></i>
                        </button>
                      <?php endif; ?>

                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="8" class="text-center">No customers found.</td>
              </tr>

            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Admin Footer -->
    <footer class="admin-footer">
      <p>&copy; 2025 ArtShelf Admin Dashboard. All rights reserved.</p>
    </footer>
  </main>
  <!-- unban artist modal -->
  <div class="modal fade" id="unbanArtistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="unbanArtistModalTitle">Unban An Artist 🔓</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/update-customer-status" method="POST">
          <div class="modal-body">
            <p>Are you sure you want to unban <strong id="unbanArtistName"></strong>?</p>
            <input type="hidden" name="id" id="unbanArtistId">
            <input type="hidden" name="status" value="Accepted">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Unban</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- Ban modal -->
  <div class="modal fade" id="banArtistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="banArtistModalTitle">Ban an Artist 🔨</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/update-customer-status" method="POST">
          <div class="modal-body">
            <p>Are you sure you want to ban <strong id="banArtistName"></strong>?</p>
            <input type="hidden" name="id" id="banArtistId">
            <input type="hidden" name="status" value="Rejected">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Ban</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Popper.js (required for Bootstrap dropdowns) -->
  <script src="/assets/js/popper.min.js"></script>
  <!-- jQuery -->
  <script src="../../assets/js/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../../assets/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

  <!-- Custom JS -->
  <script src="../../assets/js/admin/artists.js"></script>
  <script src="../../assets/js/admin.js"></script>

</body>

</html>