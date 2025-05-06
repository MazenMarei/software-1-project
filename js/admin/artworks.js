/**
 * Admin Artworks JavaScript
 * Handles artworks management functionality for admin
 */

// Global variables
let currentArtworks = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentFilters = {};
let currentArtworkId = null;

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", async () => {
  // Load artworks data
  await loadArtworks();

  // Load artists for filter dropdown
  await loadArtistsForFilter();

  // Initialize event listeners
  initEventListeners();

  // Handle view toggle (table/grid)
  initViewToggle();
});

/**
 * Load artworks data
 */
async function loadArtworks(filters = {}) {
  try {
    // Show loading indicator
    document.getElementById("artworksTableBody").innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading artworks...</p>
                </td>
            </tr>
        `;

    // Get all artworks from service
    await artworkService.loadArtworks();
    let artworks = artworkService.artworks;

    // Apply filters
    if (filters) {
      currentFilters = filters;

      // Filter by status
      if (filters.status && filters.status !== "all") {
        artworks = artworks.filter(
          (artwork) => artwork.status === filters.status
        );
      }

      // Filter by category
      if (filters.category && filters.category !== "all") {
        artworks = artworks.filter(
          (artwork) => artwork.category === filters.category
        );
      }

      // Filter by artist
      if (filters.artistId && filters.artistId !== "all") {
        artworks = artworks.filter(
          (artwork) => artwork.artistId === filters.artistId
        );
      }

      // Filter by price range
      if (filters.minPrice) {
        artworks = artworks.filter(
          (artwork) => artwork.price >= parseFloat(filters.minPrice)
        );
      }

      if (filters.maxPrice) {
        artworks = artworks.filter(
          (artwork) => artwork.price <= parseFloat(filters.maxPrice)
        );
      }

      // Filter by search term
      if (filters.searchTerm) {
        const term = filters.searchTerm.toLowerCase();
        artworks = artworks.filter(
          (artwork) =>
            artwork.title.toLowerCase().includes(term) ||
            artwork.description.toLowerCase().includes(term) ||
            artwork.artistName.toLowerCase().includes(term) ||
            (artwork.tags &&
              artwork.tags.some((tag) => tag.toLowerCase().includes(term)))
        );
      }
    }

    // Store current artworks
    currentArtworks = artworks;

    // Render paginated artworks
    renderArtworks(currentPage);

    // Render pagination
    renderPagination();
  } catch (error) {
    console.error("Error loading artworks:", error);
    window.notifications.error(
      "Failed to load artworks. Please try again later."
    );
  }
}

/**
 * Render artworks to table
 * @param {number} page - Page number
 */
function renderArtworks(page) {
  // Get table body
  const tableBody = document.getElementById("artworksTableBody");

  // Clear table body
  tableBody.innerHTML = "";

  // Get paginated data
  const startIndex = (page - 1) * itemsPerPage;
  const endIndex = Math.min(startIndex + itemsPerPage, currentArtworks.length);
  const paginatedArtworks = currentArtworks.slice(startIndex, endIndex);

  // If no artworks, show message
  if (paginatedArtworks.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-palette fs-1 text-muted mb-3"></i>
                        <h5>No artworks found</h5>
                        <p class="text-muted">Try changing your filters or check back later.</p>
                    </div>
                </td>
            </tr>
        `;
    return;
  }

  // Create HTML for each artwork
  paginatedArtworks.forEach((artwork) => {
    const tr = document.createElement("tr");

    // Format status badge
    const statusBadge = getStatusBadge(artwork.status);

    // Format date
    const dateAdded = formatDate(artwork.createdAt);

    // Format price
    const formattedPrice = formatPrice(artwork.price, artwork.currency);

    // Set row HTML
    tr.innerHTML = `
            <td>
                <img src="${artwork.getThumbnail()}" alt="${
      artwork.title
    }" class="artwork-thumbnail">
            </td>
            <td>${artwork.title}</td>
            <td>${artwork.artistName}</td>
            <td>${artwork.category}</td>
            <td>${formattedPrice}</td>
            <td>${dateAdded}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-outline-primary btn-sm action-btn view-artwork" data-id="${
                      artwork.id
                    }">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm action-btn edit-artwork" data-id="${
                      artwork.id
                    }">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm action-btn delete-artwork" data-id="${
                      artwork.id
                    }">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

    // Add row to table
    tableBody.appendChild(tr);
  });

  // Add event listeners to action buttons
  addActionButtonListeners();
}

/**
 * Render pagination controls
 */
function renderPagination() {
  const paginationContainer = document.getElementById("artworkPagination");
  paginationContainer.innerHTML = "";

  // Calculate total pages
  const totalPages = Math.ceil(currentArtworks.length / itemsPerPage);

  // No need for pagination if only one page
  if (totalPages <= 1) {
    return;
  }

  // Previous button
  const prevLi = document.createElement("li");
  prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`;
  prevLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Previous" data-page="${
          currentPage - 1
        }">
            <span aria-hidden="true">&laquo;</span>
        </a>
    `;
  paginationContainer.appendChild(prevLi);

  // Page number buttons
  for (let i = 1; i <= totalPages; i++) {
    const pageLi = document.createElement("li");
    pageLi.className = `page-item ${currentPage === i ? "active" : ""}`;
    pageLi.innerHTML = `
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        `;
    paginationContainer.appendChild(pageLi);
  }

  // Next button
  const nextLi = document.createElement("li");
  nextLi.className = `page-item ${
    currentPage === totalPages ? "disabled" : ""
  }`;
  nextLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Next" data-page="${
          currentPage + 1
        }">
            <span aria-hidden="true">&raquo;</span>
        </a>
    `;
  paginationContainer.appendChild(nextLi);

  // Add click event listeners to pagination links
  document.querySelectorAll("#artworkPagination .page-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const page = parseInt(e.target.closest(".page-link").dataset.page);
      if (page && page !== currentPage) {
        currentPage = page;
        renderArtworks(currentPage);
        renderPagination();
        // Scroll to top of table
        document
          .querySelector(".table-container")
          .scrollIntoView({ behavior: "smooth" });
      }
    });
  });
}

/**
 * Get status badge HTML based on status
 * @param {string} status - Artwork status
 * @returns {string} HTML for status badge
 */
function getStatusBadge(status) {
  switch (status) {
    case "pending":
      return '<span class="status-badge pending">Pending</span>';
    case "approved":
      return '<span class="status-badge approved">Approved</span>';
    case "rejected":
      return '<span class="status-badge rejected">Rejected</span>';
    case "sold":
      return '<span class="status-badge sold">Sold</span>';
    default:
      return '<span class="status-badge">' + status + "</span>";
  }
}

/**
 * Format price with currency symbol
 * @param {number} price - Price amount
 * @param {string} currency - Currency code
 * @returns {string} Formatted price
 */
function formatPrice(price, currency = "usd") {
  const formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: currency.toUpperCase(),
    minimumFractionDigits: 2,
  });

  return formatter.format(price);
}

/**
 * Format date
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

/**
 * Add event listeners to action buttons
 */
function addActionButtonListeners() {
  // View artwork buttons
  document.querySelectorAll(".view-artwork").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const artworkId = e.currentTarget.dataset.id;
      openArtworkDetailsModal(artworkId);
    });
  });

  // Edit artwork buttons
  document.querySelectorAll(".edit-artwork").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const artworkId = e.currentTarget.dataset.id;
      // Redirect to edit page or open edit modal
      window.location.href = `edit-artwork.html?id=${artworkId}`;
    });
  });

  // Delete artwork buttons
  document.querySelectorAll(".delete-artwork").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const artworkId = e.currentTarget.dataset.id;
      const artwork = currentArtworks.find((a) => a.id === artworkId);

      if (
        confirm(
          `Are you sure you want to delete "${artwork.title}"? This action cannot be undone.`
        )
      ) {
        deleteArtwork(artworkId);
      }
    });
  });
}

/**
 * Open artwork details modal
 * @param {string} artworkId - Artwork ID
 */
async function openArtworkDetailsModal(artworkId) {
  try {
    // Get artwork
    const artwork = await artworkService.getArtworkById(artworkId);

    if (!artwork) {
      window.notifications.error("Artwork not found.");
      return;
    }

    // Store current artwork ID
    currentArtworkId = artworkId;

    // Set modal content
    const modalContent = document.getElementById("artworkDetailsContent");

    // Create details HTML
    let detailsHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="artwork-image-main mb-3">
                        <img src="${artwork.mainImage}" alt="${artwork.title}" class="img-fluid rounded">
                    </div>
                    <div class="artwork-image-thumbnails d-flex flex-wrap gap-2">
                    `;

    // Add thumbnails
    artwork.images.forEach((image) => {
      detailsHTML += `
                <div class="artwork-image-thumbnail">
                    <img src="${image}" alt="" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                </div>
            `;
    });

    detailsHTML += `
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="mb-2">${artwork.title}</h3>
                    <p class="text-muted mb-3">by ${artwork.artistName}</p>
                    
                    <div class="artwork-meta mb-4">
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Category:</div>
                            <div class="col-7">${artwork.category}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Style:</div>
                            <div class="col-7">${artwork.style}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Year:</div>
                            <div class="col-7">${artwork.year}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Medium:</div>
                            <div class="col-7">${artwork.medium}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Dimensions:</div>
                            <div class="col-7">${artwork.getFormattedDimensions()}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Price:</div>
                            <div class="col-7">${formatPrice(
                              artwork.price,
                              artwork.currency
                            )}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Status:</div>
                            <div class="col-7">${getStatusBadge(
                              artwork.status
                            )}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 fw-bold">Date Added:</div>
                            <div class="col-7">${formatDate(
                              artwork.createdAt
                            )}</div>
                        </div>
                    </div>
                    
                    <div class="artwork-description mb-4">
                        <h5 class="mb-2">Description</h5>
                        <p>${artwork.description}</p>
                    </div>
                    
                    <div class="artwork-tags mb-4">
                        <h5 class="mb-2">Tags</h5>
                        <div class="d-flex flex-wrap gap-1">
                        `;

    // Add tags
    if (artwork.tags && artwork.tags.length > 0) {
      artwork.tags.forEach((tag) => {
        detailsHTML += `<span class="badge bg-secondary">${tag}</span>`;
      });
    } else {
      detailsHTML += `<span class="text-muted">No tags</span>`;
    }

    detailsHTML += `
                        </div>
                    </div>
                    
                    <div class="artwork-stats">
                        <h5 class="mb-2">Statistics</h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fas fa-eye me-2"></i> ${
                                      artwork.views || 0
                                    } Views
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <i class="fas fa-heart me-2"></i> ${
                                      artwork.favorites || 0
                                    } Favorites
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Set modal content
    modalContent.innerHTML = detailsHTML;

    // Show/hide approval buttons based on status
    const approveBtn = document.getElementById("approveArtworkBtn");
    const rejectBtn = document.getElementById("rejectArtworkBtn");
    const editBtn = document.getElementById("editArtworkBtn");

    if (artwork.status === "pending") {
      approveBtn.style.display = "block";
      rejectBtn.style.display = "block";
    } else {
      approveBtn.style.display = "none";
      rejectBtn.style.display = "none";
    }

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(
      document.getElementById("artworkDetailsModal")
    );
    modal.show();
  } catch (error) {
    console.error("Error opening artwork details:", error);
    window.notifications.error(
      "Failed to load artwork details. Please try again."
    );
  }
}

/**
 * Load artists for filter dropdown
 */
async function loadArtistsForFilter() {
  try {
    const select = document.getElementById("artistFilter");

    // Get all artists
    const artists = await userService.getArtists();

    // Add options for each artist
    artists.forEach((artist) => {
      const option = document.createElement("option");
      option.value = artist.id;
      option.textContent = artist.name;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Error loading artists for filter:", error);
  }
}

/**
 * Initialize event listeners
 */
function initEventListeners() {
  // Filter form submit
  document
    .getElementById("artworkFilterForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();

      // Get filter values
      const status = document.getElementById("statusFilter").value;
      const category = document.getElementById("categoryFilter").value;
      const artistId = document.getElementById("artistFilter").value;
      const minPrice = document.getElementById("priceMinFilter").value;
      const maxPrice = document.getElementById("priceMaxFilter").value;
      const searchTerm = document.getElementById("searchFilter").value;

      // Create filters object
      const filters = {
        status,
        category,
        artistId,
        minPrice,
        maxPrice,
        searchTerm,
      };

      // Reset to first page
      currentPage = 1;

      // Apply filters
      loadArtworks(filters);
    });

  // Reset button
  document.getElementById("artworkFilterForm").addEventListener("reset", () => {
    // Wait for form to reset
    setTimeout(() => {
      // Reset to first page
      currentPage = 1;

      // Clear filters
      loadArtworks({});
    }, 0);
  });

  // Export button
  document
    .getElementById("exportBtn")
    .addEventListener("click", exportArtworks);

  // Approve artwork button
  document
    .getElementById("approveArtworkBtn")
    .addEventListener("click", approveArtwork);

  // Reject artwork button
  document
    .getElementById("rejectArtworkBtn")
    .addEventListener("click", showRejectionModal);

  // Confirm reject button
  document
    .getElementById("confirmRejectBtn")
    .addEventListener("click", rejectArtwork);
}

/**
 * Initialize view toggle (table/grid)
 */
function initViewToggle() {
  const viewButtons = document.querySelectorAll("[data-view]");
  viewButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      const view = btn.dataset.view;

      // Remove active class from all buttons
      viewButtons.forEach((b) => b.classList.remove("active"));

      // Add active class to clicked button
      btn.classList.add("active");

      // Handle view change
      if (view === "grid") {
        // TODO: Implement grid view
        window.notifications.info("Grid view is not implemented yet.");
      }
    });
  });
}

/**
 * Export artworks to CSV
 */
function exportArtworks() {
  try {
    // Create headers
    const headers = [
      "ID",
      "Title",
      "Artist",
      "Category",
      "Style",
      "Year",
      "Medium",
      "Price",
      "Status",
      "Date Added",
    ];

    // Create rows
    const rows = currentArtworks.map((artwork) => [
      artwork.id,
      artwork.title,
      artwork.artistName,
      artwork.category,
      artwork.style,
      artwork.year,
      artwork.medium,
      artwork.price,
      artwork.status,
      formatDate(artwork.createdAt),
    ]);

    // Create CSV content
    let csvContent = headers.join(",") + "\n";
    rows.forEach((row) => {
      // Escape fields with commas
      const escapedRow = row.map((field) => {
        const str = String(field);
        return str.includes(",") ? `"${str}"` : str;
      });
      csvContent += escapedRow.join(",") + "\n";
    });

    // Create download link
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute(
      "download",
      `artworks-export-${new Date().toISOString().slice(0, 10)}.csv`
    );
    link.style.display = "none";

    // Append to body and click
    document.body.appendChild(link);
    link.click();

    // Clean up
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    window.notifications.success("Artworks exported successfully");
  } catch (error) {
    console.error("Error exporting artworks:", error);
    window.notifications.error("Failed to export artworks. Please try again.");
  }
}

/**
 * Approve artwork
 */
async function approveArtwork() {
  try {
    if (!currentArtworkId) return;

    // Get admin user
    const admin = window.auth.currentUser;

    // Update artwork status
    const artwork = await artworkService.getArtworkById(currentArtworkId);
    if (!artwork) return;

    // Update status
    artwork.status = "approved";
    artwork.updatedAt = new Date().toISOString();

    // Log action (in real app, this would update the server)
    console.log(
      `Artwork approved by admin: ${admin.id}, artwork: ${currentArtworkId}`
    );

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("artworkDetailsModal")
    ).hide();

    // Show success message
    window.notifications.success("Artwork has been approved");

    // Reload artworks
    loadArtworks(currentFilters);
  } catch (error) {
    console.error("Error approving artwork:", error);
    window.notifications.error("Failed to approve artwork. Please try again.");
  }
}

/**
 * Show rejection modal
 */
function showRejectionModal() {
  // Hide details modal
  bootstrap.Modal.getInstance(
    document.getElementById("artworkDetailsModal")
  ).hide();

  // Clear previous reason
  document.getElementById("rejectionReason").value = "";

  // Show rejection modal
  const rejectionModal = new bootstrap.Modal(
    document.getElementById("rejectionReasonModal")
  );
  rejectionModal.show();
}

/**
 * Reject artwork
 */
async function rejectArtwork() {
  try {
    if (!currentArtworkId) return;

    // Get rejection reason
    const rejectionReason = document
      .getElementById("rejectionReason")
      .value.trim();

    if (!rejectionReason) {
      window.notifications.error("Please provide a rejection reason");
      return;
    }

    // Get admin user
    const admin = window.auth.currentUser;

    // Update artwork status
    const artwork = await artworkService.getArtworkById(currentArtworkId);
    if (!artwork) return;

    // Update status
    artwork.status = "rejected";
    artwork.rejectionReason = rejectionReason;
    artwork.updatedAt = new Date().toISOString();

    // Log action (in real app, this would update the server)
    console.log(
      `Artwork rejected by admin: ${admin.id}, artwork: ${currentArtworkId}, reason: ${rejectionReason}`
    );

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("rejectionReasonModal")
    ).hide();

    // Show success message
    window.notifications.success("Artwork has been rejected");

    // Reload artworks
    loadArtworks(currentFilters);
  } catch (error) {
    console.error("Error rejecting artwork:", error);
    window.notifications.error("Failed to reject artwork. Please try again.");
  }
}

/**
 * Delete artwork
 * @param {string} artworkId - Artwork ID to delete
 */
async function deleteArtwork(artworkId) {
  try {
    // Get admin user
    const admin = window.auth.currentUser;

    // Find artwork to delete
    const artworkIndex = artworkService.artworks.findIndex(
      (artwork) => artwork.id === artworkId
    );

    if (artworkIndex === -1) {
      window.notifications.error("Artwork not found");
      return;
    }

    // Remove artwork from array (in real app, this would call an API)
    const deletedArtwork = artworkService.artworks.splice(artworkIndex, 1)[0];

    // Log action
    console.log(
      `Artwork deleted by admin: ${admin.id}, artwork: ${artworkId}, title: ${deletedArtwork.title}`
    );

    // Show success message
    window.notifications.success("Artwork has been deleted");

    // Reload artworks
    loadArtworks(currentFilters);
  } catch (error) {
    console.error("Error deleting artwork:", error);
    window.notifications.error("Failed to delete artwork. Please try again.");
  }
}
