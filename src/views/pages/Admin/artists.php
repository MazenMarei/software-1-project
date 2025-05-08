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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
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


      <!-- statistic section -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon primary">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <h3> <?php echo count($artists) ?> </h3>
              <p>Total Artist</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-heart"></i>
            </div>
            <div class="stat-content">
              <h3> <?php echo count(array_filter($artists, function ($artist) {
                      return $artist['status'] === 'Accepted';
                    })) ?> </h3>
              <p>Accepted Artist</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon warning">
              <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-content">
              <h3> <?php echo count(array_filter($artists, function ($artist) {
                      return $artist['status'] === 'Pending';
                    })) ?> </h3>
              <p>Pending Artist</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card">
            <div class="stat-icon danger">
              <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-content">
              <h3> <?php echo count(array_filter($artists, function ($artist) {
                      return $artist['status'] === 'Rejected';
                    })) ?> </h3>
              <p>Rejected Artist</p>
            </div>
          </div>
        </div>
      </div>


      <div class="admin-header">
        <h2 class="admin-title">Manage Artists</h2>
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
          <table id="artistsTable" class="table table-hover search-table">
            <thead>
              <tr>
                <th>Artist</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th width="120">Join Date</th>
                <th width="120">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($artists as $artist): ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="<?= !empty($artist['profilePic']) ? '../uploads/profiles/' . $artist['profilePic'] : '../uploads/profiles/default.jpg' ?>"
                        class="small-avatar me-3" alt="<?= htmlspecialchars($artist['Fname']) ?>">
                      <div>
                        <div class="fw-bold"><?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?></div>
                        <small class="text-muted"><?= " " . htmlspecialchars($artist['userID']) . " |  " . htmlspecialchars($artist['username'])   ?></small>
                      </div>
                    </div>
                  </td>

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

                      <?php if ($artist['status'] == 'Accepted'): ?>
                        <button class="btn btn-sm btn-outline-danger ban-btn"
                          onclick="banFun(this)"
                          data-id="<?= $artist['userID'] ?>"
                          data-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      <?php endif; ?>

                      <?php if ($artist['status'] == 'Rejected'): ?>
                        <button class="btn btn-sm btn-outline-success unban-btn"
                          onclick="unbanFun(this)"
                          data-id="<?= $artist['userID'] ?>"
                          data-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
                          <i class="fa-solid fa-lock-open"></i>
                        </button>
                      <?php endif; ?>
                      <?php if ($artist['status'] === 'Pending'): ?>
                        <button class="btn btn-sm btn-outline-success approve-btn"
                          onclick="approvalFun(this)"
                          data-id="<?= $artist['userID'] ?>"
                          data-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
                          <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger reject-btn"
                          data-id="<?= $artist['userID'] ?>"
                          onclick="rejectionFun(this)"
                          data-name="<?= htmlspecialchars($artist['Fname'] . ' ' . $artist['Lname']) ?>">
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

  <!-- unban artist modal -->
  <div class="modal fade" id="unbanArtistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="unbanArtistModalTitle">Unban An Artist 🔓</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/update-artist-status" method="POST">
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
        <form action="/admin/update-artist-status" method="POST">
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

  <!-- Approval Modal -->
  <div class=" modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="approvalModalTitle">Approve Artist</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/update-artist-status" method="POST">
          <div class="modal-body">
            <p>Are you sure you want to approve <strong id="approvalItemName"></strong>?</p>
            <input type="hidden" name="id" id="approvalItemId">
            <input type="hidden" name="status" value="Accepted">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Rejection Modal -->
  <div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectionModalTitle">Reject Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="/admin/update-artist-status" method="POST">
          <div class="modal-body">
            <p>Are you sure you want to reject <strong id="rejectionItemName"></strong>?</p>
            <div class="mb-3">
              <label for="rejectionReason" class="form-label">Reason for Rejection</label>
              <textarea class="form-control" id="rejectionReason" name="reason" rows="3" required></textarea>
              <div class="form-text">This reason will be sent to the submitter.</div>
            </div>
            <input type="hidden" name="id" id="rejectionItemId">
            <input type="hidden" name="status" value="Rejected">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Reject</button>
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