<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Artworks | ArtShelf Admin</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="/assets/css/main.css" />
  <link rel="stylesheet" href="/assets/css/admin.dashboard.css" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/css/all.min.css" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet" />

</head>

<body>
  <!-- Admin Sidebar -->
  <?php require_once VIEWS . 'components/admin_sidebar.php'; ?>

  <!-- Main Content -->
  <main class="admin-content">
    <!-- Admin Header -->
    <?php
    $pageTitle = 'Manage Artworks';
    require_once VIEWS . 'components/admin_header.php';
    ?>

    <!-- Display error/success messages -->
    <?php require_once VIEWS . 'components/error_display.php'; ?>


    <!-- statistic section -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon primary">
            <i class="fas fa-palette"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count($artworks); ?> </h3>
            <p>Total Artworks</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon success">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count(array_filter($artworks, function ($artwork) {
                    return $artwork->getStatus() === 'sold';
                  })); ?> </h3>
            <p>Sold Artist</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon warning">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count(array_filter($artworks, function ($artwork) {
                    return $artwork->getStatus() === 'Pending';
                  })); ?> </h3>
            <p>Pending Artwork</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon danger">
            <i class="fas fa-user-slash"></i>
          </div>
          <div class="stat-content">
            <h3> <?php echo count(array_filter($artworks, function ($artwork) {
                    return $artwork->getStatus() === 'Rejected';
                  })); ?> </h3>
            <p>Rejected Artwoks</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header">
        <h2 class="h5 mb-0">Filter Artworks</h2>
      </div>
      <div class="card-body">
        <form id="artworkFilterForm">
          <div class="row g-3 mb-3">
            <div class="col-md-3">
              <label for="statusFilter" class="form-label">Status</label>
              <select class="form-select" id="statusFilter" name="status">
                <option value="">All Statuses</option>
                <option value="sold">Sold</option>
                <option value="Pending">Pending</option>
                <option value="Accepted">Accepted</option>
                <option value="Rejected">Rejected</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="categoryFilter" class="form-label">Category</label>
              <select class="form-select" id="categoryFilter" name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- All Artwork -->
    <div class="table-container">
      <div class="table-header">
        <h3 class="table-title">All Artworks</h3>
        <div class="table-filters">
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover search-table" id="artworksTable">
          <thead>
            <tr>
              <th>Image</th>
              <th>Title</th>
              <th>Artist</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Date Added</th>
              <th width="120">Actions</th>
            </tr>
          </thead>
          <tbody id="artworksTableBody">
            <?php if (empty($artworks)): ?>
              <tr>
                <td colspan="8" class="text-center">No artworks found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($artworks as $artwork): ?>
                <tr>
                  <td>
                    <img
                      src="/uploads/artworks/<?php echo htmlspecialchars($artwork->getImages()); ?>"
                      alt="<?php echo htmlspecialchars($artwork->getTitle()); ?>"
                      class="img-thumbnail"
                      style="width: 80px; height: 60px; object-fit: cover;">
                  </td>
                  <td><?php echo htmlspecialchars($artwork->getTitle()); ?></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <img
                        src="/uploads/profiles/<?php echo htmlspecialchars($artwork->getArtist()->getProfilePic()); ?>"
                        alt="Artist"
                        class="rounded-circle me-2"
                        width="30"
                        height="30">
                      <?php echo htmlspecialchars($artwork->getArtist()->getFirstName() . ' ' . $artwork->getArtist()->getLastName()); ?>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($artwork->getCategory()); ?></td>
                  <td>$<?php echo ($artwork->getPrice()); ?></td>
                  <td>
                    <span class="status-badge <?php echo strtolower($artwork->getStatus()); ?>">
                      <?php echo $artwork->getStatus(); ?>
                    </span>
                  </td>
                  <td><?php echo ($artwork->getDateCreated()); ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button
                        type="button"
                        onclick="showDetails(this)"
                        class="btn btn-outline-primary view-artwork-btn"
                        data-id="<?php echo $artwork->getArtworkID(); ?>"
                        data-image="/artworks/<?php echo ($artwork->getImages()); ?>"
                        data-title="<?php echo ($artwork->getTitle()); ?>"
                        data-price="<?php echo ($artwork->getPrice()); ?>"
                        data-description="<?php echo ($artwork->getDescription()); ?>"
                        data-status="<?php echo ($artwork->getStatus()); ?>"
                        data-category="<?php echo ($artwork->getCategory()); ?>"
                        data-medium="<?php echo ($artwork->getMedium()); ?>"
                        data-dimensions="<?php echo join("x", array_values($artwork->getDimensions())); ?>"
                        data-date="<?php echo ($artwork->getDateCreated()); ?>">

                        <i class="fas fa-eye"></i>
                      </button>
                      <?php if ($artwork->getStatus() === 'Pending'): ?>
                        <button
                          type="button"
                          class="btn btn-outline-success approve-btn"
                          data-id="<?php echo $artwork->getArtworkID(); ?>"
                          data-bs-toggle="modal"
                          onclick="approvalFun(this)"
                          data-bs-target="#approvalModal">
                          <i class="fas fa-check"></i>
                        </button>
                        <button
                          type="button"
                          class="btn btn-outline-danger reject-btn"
                          onclick="rejectionFun(this)"
                          data-id="<?php echo $artwork->getArtworkID(); ?>"
                          data-bs-toggle="modal"
                          data-bs-target="#rejectionModal">
                          <i class="fas fa-times"></i>
                        </button>
                      <?php elseif ($artwork->getStatus() === 'Accepted'): ?>
                        <button
                          type="button"
                          class="btn btn-outline-danger reject-btn"
                          onclick="rejectionFun(this)"
                          data-id="<?php echo $artwork->getArtworkID(); ?>"
                          data-bs-toggle="modal"
                          data-bs-target="#rejectionModal">
                          <i class="fa-solid fa-ban"></i>
                        </button>
                      <?php elseif ($artwork->getStatus() === 'Rejected') : ?>
                        <button
                          type="button"
                          class="btn btn-outline-success approve-btn"
                          data-id="<?php echo $artwork->getArtworkID(); ?>"
                          data-bs-toggle="modal"
                          onclick="approvalFun(this)"
                          data-bs-target="#approvalModal">
                          <i class="fa-solid fa-lock-open"></i>
                        </button>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>


    <!-- Artwork Details Modal -->
    <div
      class="modal fade"
      id="showDetailsModal"
      tabindex="-1"
      aria-labelledby="artwork-detail-modal-label"
      aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="artwork-detail-modal-label">
              Artwork Details
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <img
                  src=""
                  alt="Artwork Image"
                  id="modal-artwork-image"
                  class="modal-artwork-image img-fluid" />
              </div>
              <div class="col-md-6">
                <h3 id="modal-artwork-title"></h3>
                <p class="text-accent fs-4" id="modal-artwork-price"></p>
                <p id="modal-artwork-description"></p>
                <div class="mb-3">
                  <strong>Status:</strong>
                  <span id="modal-artwork-status"></span>
                </div>
                <div class="mb-3">
                  <strong>Category:</strong>
                  <span id="modal-artwork-category"></span>
                </div>
                <div class="mb-3">
                  <strong>Medium:</strong>
                  <span id="modal-artwork-medium"></span>
                </div>
                <div class="mb-3">
                  <strong>Dimensions:</strong>
                  <span id="modal-artwork-dimensions"></span>
                </div>
                <div class="mb-3">
                  <strong>Created:</strong>
                  <span id="modal-artwork-date"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Approval Modal -->
    <div class=" modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="approvalModalTitle">Approve Artwork</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="/admin/update-artwork-status" method="POST">
            <div class="modal-body">
              <p>Are you sure you want to approve this artwork? <br>It will be visible to all users.</p>
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
          <form action="/admin/update-artwork-status" method="POST">
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

    <!-- Rejection Modal -->
    <div
      class="modal fade"
      id="rejectionModal"
      tabindex="-1"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Reject Artwork</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="rejectionForm" action="/admin/update-artwork-status" method="POST">
              <div class="mb-3">
                <label for="rejectionReason" class="form-label">Rejection Reason</label>
                <textarea
                  class="form-control"
                  id="rejectionReason"
                  name="reason"
                  rows="4"
                  required></textarea>
                <div class="form-text">
                  This reason will be shared with the artist.
                </div>
              </div>
              <input type="hidden" id="rejectionItemId" name="id" value="">
              <input type="hidden" name="status" value="Rejected">
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Reject</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Footer -->
    <footer class="border-top mt-5 pt-4 text-muted small">
      <p>&copy; <?php echo date('Y'); ?> ArtShelf Admin Dashboard. All rights reserved.</p>
    </footer>
  </main>

  <!-- Bootstrap Bundle with Popper -->
  <script src="../../assets/js/popper.min.js"></script>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="../../assets/js/bootstrap.min.js"></script>
  <!-- jQuery -->
  <script src="../../assets/js/jquery-3.7.1.min.js"></script>
  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <!-- Admin Dashboard JS -->
  <script src="../../assets/js/admin/artists.js"></script>
  <script src="../../assets/js/admin.js"></script>
  <!-- Custom Scripts -->
  <!-- <script src="../../assets/js/adminArtworks.js"></script> -->
</body>

</html>