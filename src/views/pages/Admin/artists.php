<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Artists | ArtShelf Admin</title>
  <!-- Bootstrap CSS -->
  <link
    href="../../assets/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../assets/css/main.css" />
  <link rel="stylesheet" href="../../assets/css/admin.dashboard.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../assets/css/all.min.css" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet" />
</head>

<body>
  <div class="admin-container">
    <!-- Sidebar -->
    <?php require_once VIEWS . 'components/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
      <!-- Header -->
      <?php require_once VIEWS . 'components/admin_header.php'; ?>

      <?php require_once VIEWS . 'components/error_display.php'; ?>

      <div class="admin-header">
        <h2 class="admin-title">Manage Artists</h2>
        <!-- <div class="admin-header-actions">
          <button id="exportArtistsBtn" class="btn btn-outline-primary">
            <i class="fas fa-file-export"></i> Export Data
          </button>
        </div> -->
      </div>

      <!-- All Artists -->
      <div class="table-container">
        <div class="table-header">
          <h3 class="table-title">All Artists</h3>
          <div class="table-filters">
            <select id="statusFilter" class="form-select form-select-sm">
              <option value="">All Statuses</option>
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="table-responsive">
          <table id="artistsTable" class="table table-hover">
            <thead>
              <tr>
                <th width="60">Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th>Join Date</th>
                <th width="120">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($artists as $artist): ?>
                <tr>
                  <td>
                    <img src="<?= !empty($artist['profilePic']) ? '../uploads/profiles/' . $artist['profilePic'] : '../uploads/profiles/default.jpg' ?>"
                      class="rounded-circle" width="40" height="40" alt="<?= htmlspecialchars($artist['Fname']) ?>">
                  </td>
                  <td><?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?></td>
                  <td><?= htmlspecialchars($artist['Email']) ?></td>
                  <td><?= htmlspecialchars($artist['phone'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($artist['address'] ?? 'N/A') ?></td>
                  <td>
                    <span class="status-badge <?= strtolower($artist['status']) ?>">
                      <?= htmlspecialchars($artist['status']) ?>
                    </span>
                  </td>
                  <td><?= date('M d, Y', strtotime($artist['created_at'] ?? $artist['registerDate'])) ?></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary view-artist-btn" data-artist-id="<?= $artist['userID'] ?>">
                        <i class="fas fa-eye"></i>
                      </button>
                      <?php if ($artist['status'] === 'Pending'): ?>
                        <button class="btn btn-sm btn-outline-success update-status-btn"
                          data-artist-id="<?= $artist['userID'] ?>"
                          data-status="Approved"
                          data-artist-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
                          <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger update-status-btn"
                          data-artist-id="<?= $artist['userID'] ?>"
                          data-status="Rejected"
                          data-artist-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
                          <i class="fas fa-times"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Artist Details Modal -->
  <div class="modal fade" id="artistDetailsModal" tabindex="-1" aria-labelledby="artistDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="artistDetailsModalLabel">Artist Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="artistDetailsContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Update Modal -->
  <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="statusUpdateTitle">Update Artist Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="statusUpdateForm">
          <div class="modal-body">
            <p id="statusUpdateMessage">Are you sure you want to update the status of this artist?</p>
            <input type="hidden" id="statusUpdateArtistID" name="artistID">
            <input type="hidden" id="statusUpdateAction" name="status">

            <div class="mb-3 rejection-reason-group" style="display: none;">
              <label for="rejectionReason" class="form-label">Reason for Rejection:</label>
              <textarea class="form-control" id="rejectionReason" name="reason" rows="3"></textarea>
              <div class="form-text text-danger" id="rejectionReasonError"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" id="confirmStatusBtn">Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- jQuery -->
  <script src="../../assets/js/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="../../assets/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- Custom JS -->
  <script src="../../assets/js/admin/artists.js"></script>
</body>

</html>