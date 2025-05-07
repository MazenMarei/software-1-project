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
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/assets/css/all.min.css" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap"
    rel="stylesheet" />
  <style>
    /* Status badge styles */
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: var(--radius-sm);
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-badge.pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-badge.approved {
      background-color: #d4edda;
      color: #155724;
    }

    .status-badge.rejected {
      background-color: #f8d7da;
      color: #721c24;
    }

    /* Responsive behavior for sidebar */
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
                <option value="all">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="categoryFilter" class="form-label">Category</label>
              <select class="form-select" id="categoryFilter" name="category">
                <option value="all">All Categories</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="priceFilter" class="form-label">Price Range</label>
              <select class="form-select" id="priceFilter" name="price">
                <option value="all">All Prices</option>
                <option value="0-100">$0 - $100</option>
                <option value="100-500">$100 - $500</option>
                <option value="500-1000">$500 - $1,000</option>
                <option value="1000-5000">$1,000 - $5,000</option>
                <option value="5000+">$5,000+</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="searchFilter" class="form-label">Search</label>
              <input
                type="text"
                class="form-control"
                id="searchFilter"
                name="search"
                placeholder="Search by title, artist, or description" />
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
    </div>

    <!-- Artworks Container -->
    <div class="card shadow-sm mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">All Artworks</h2>
        <div>
          <!-- <button
            type="button"
            class="btn btn-outline-primary me-2"
            id="exportBtn">
            <i class="fas fa-download me-1"></i> Export
          </button> -->
          <div class="btn-group">
            <button
              type="button"
              class="btn btn-outline-secondary active"
              data-view="table"
              id="tableViewBtn">
              <i class="fas fa-list"></i>
            </button>
            <button
              type="button"
              class="btn btn-outline-secondary"
              data-view="grid"
              id="gridViewBtn">
              <i class="fas fa-th"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <!-- Table View -->
        <div id="tableView" class="table-responsive">
          <table class="table table-hover" id="artworksTable">
            <thead>
              <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Artist</th>
                <th>Category</th>
                <th>Price</th>
                <th>Date Added</th>
                <th>Status</th>
                <th>Actions</th>
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
                        src="/uploads/artworks/<?php echo htmlspecialchars($artwork['imageUrl']); ?>"
                        alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                        class="img-thumbnail"
                        style="width: 80px; height: 60px; object-fit: cover;">
                    </td>
                    <td><?php echo htmlspecialchars($artwork['title']); ?></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <img
                          src="/uploads/profiles/default.jpg"
                          alt="Artist"
                          class="rounded-circle me-2"
                          width="30"
                          height="30">
                        <?php echo htmlspecialchars($artwork['Fname'] . ' ' . $artwork['Lname']); ?>
                      </div>
                    </td>
                    <td><?php echo htmlspecialchars($artwork['category']); ?></td>
                    <td>$<?php echo number_format($artwork['price'], 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($artwork['createDate'])); ?></td>
                    <td>
                      <span class="status-badge <?php echo strtolower($artwork['status']); ?>">
                        <?php echo $artwork['status']; ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button
                          type="button"
                          class="btn btn-outline-primary view-artwork-btn"
                          data-id="<?php echo $artwork['artworkID']; ?>">
                          <i class="fas fa-eye"></i>
                        </button>
                        <?php if ($artwork['status'] === 'Pending'): ?>
                          <button
                            type="button"
                            class="btn btn-outline-success approve-artwork-btn"
                            data-id="<?php echo $artwork['artworkID']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#approvalModal">
                            <i class="fas fa-check"></i>
                          </button>
                          <button
                            type="button"
                            class="btn btn-outline-danger reject-artwork-btn"
                            data-id="<?php echo $artwork['artworkID']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectionModal">
                            <i class="fas fa-times"></i>
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

        <!-- Grid View -->
        <div id="gridView" class="d-none">
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="artworksGrid">
            <?php if (empty($artworks)): ?>
              <div class="col-12">
                <div class="alert alert-info">No artworks found</div>
              </div>
            <?php else: ?>
              <?php foreach ($artworks as $artwork): ?>
                <div class="col">
                  <div class="card h-100 shadow-sm">
                    <div class="position-relative" style="padding-top: 75%">
                      <img
                        src="/uploads/artworks/<?php echo htmlspecialchars($artwork['imageUrl']); ?>"
                        alt="<?php echo htmlspecialchars($artwork['title']); ?>"
                        class="position-absolute top-0 start-0 w-100 h-100"
                        style="object-fit: cover">
                    </div>
                    <div class="card-body">
                      <h5 class="card-title"><?php echo htmlspecialchars($artwork['title']); ?></h5>
                      <div class="d-flex align-items-center mb-3">
                        <img
                          src="/uploads/profiles/default.jpg"
                          alt="Artist"
                          class="rounded-circle me-2"
                          width="30"
                          height="30">
                        <span class="text-muted"><?php echo htmlspecialchars($artwork['Fname'] . ' ' . $artwork['Lname']); ?></span>
                      </div>
                      <div class="row mb-3">
                        <div class="col-6 col-xl-4">
                          <small class="text-muted d-block">Category</small>
                          <span class="fw-medium"><?php echo htmlspecialchars($artwork['category']); ?></span>
                        </div>
                        <div class="col-6 col-xl-4">
                          <small class="text-muted d-block">Price</small>
                          <span class="fw-medium">$<?php echo number_format($artwork['price'], 2); ?></span>
                        </div>
                        <div class="col-6 col-xl-4">
                          <small class="text-muted d-block">Status</small>
                          <span class="status-badge <?php echo strtolower($artwork['status']); ?>">
                            <?php echo $artwork['status']; ?>
                          </span>
                        </div>
                      </div>
                      <p class="card-text">
                        <?php echo substr(htmlspecialchars($artwork['description']), 0, 100); ?><?php echo (strlen($artwork['description']) > 100) ? '...' : ''; ?>
                      </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-primary view-artwork-btn"
                        data-id="<?php echo $artwork['artworkID']; ?>">
                        View Details
                      </button>
                      <?php if ($artwork['status'] === 'Pending'): ?>
                        <div>
                          <button
                            type="button"
                            class="btn btn-sm btn-outline-success approve-artwork-btn"
                            data-id="<?php echo $artwork['artworkID']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#approvalModal">
                            Approve
                          </button>
                          <button
                            type="button"
                            class="btn btn-sm btn-outline-danger reject-artwork-btn"
                            data-id="<?php echo $artwork['artworkID']; ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectionModal">
                            Reject
                          </button>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($artworks)): ?>
          <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Artwork pagination">
              <ul class="pagination" id="artworkPagination">
                <!-- Pagination using data from controller -->
                <li class="page-item <?php echo ($pagination['hasPrevPage'] ? '' : 'disabled'); ?>">
                  <a class="page-link" href="?page=<?php echo $pagination['prevPage']; ?>" tabindex="-1">Previous</a>
                </li>

                <?php
                // Display page numbers
                $startPage = max(1, $pagination['currentPage'] - 2);
                $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);

                // Always show first page
                if ($startPage > 1) {
                  echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                  if ($startPage > 2) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                  }
                }

                // Display page numbers
                for ($i = $startPage; $i <= $endPage; $i++) {
                  echo '<li class="page-item ' . ($i == $pagination['currentPage'] ? 'active' : '') . '">';
                  echo '<a class="page-link" href="?page=' . $i . '">' . $i . '</a>';
                  echo '</li>';
                }

                // Always show last page
                if ($endPage < $pagination['totalPages']) {
                  if ($endPage < $pagination['totalPages'] - 1) {
                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                  }
                  echo '<li class="page-item"><a class="page-link" href="?page=' . $pagination['totalPages'] . '">' . $pagination['totalPages'] . '</a></li>';
                }
                ?>

                <li class="page-item <?php echo ($pagination['hasNextPage'] ? '' : 'disabled'); ?>">
                  <a class="page-link" href="?page=<?php echo $pagination['nextPage']; ?>">Next</a>
                </li>
              </ul>
            </nav>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Artwork Details Modal -->
    <div
      class="modal fade"
      id="artworkDetailsModal"
      tabindex="-1"
      aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Artwork Details</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body" id="artworkDetailsContent">
            <!-- Will be populated by JavaScript -->
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
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
            <button
              type="button"
              class="btn btn-success"
              id="approveArtworkBtn">
              Approve
            </button>
            <button type="button" class="btn btn-danger" id="rejectArtworkBtn">
              Reject
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Approval Modal -->
    <div
      class="modal fade"
      id="approvalModal"
      tabindex="-1"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Approve Artwork</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to approve this artwork? It will be visible to all users.</p>
            <form id="approvalForm" action="/admin/update-artwork-status" method="POST">
              <input type="hidden" id="approvalArtworkId" name="artworkId" value="">
              <input type="hidden" name="status" value="Approved">
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-success"
              id="confirmApproveBtn">
              Confirm Approval
            </button>
          </div>
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
              <input type="hidden" id="rejectionArtworkId" name="artworkId" value="">
              <input type="hidden" name="status" value="Rejected">
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-danger"
              id="confirmRejectBtn">
              Confirm Rejection
            </button>
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
  <!-- Admin Dashboard JS -->
  <script src="../../assets/js/admin.js"></script>
  <!-- Custom Scripts -->
  <script src="../../assets/js/adminArtworks.js"></script>
</body>

</html>