/**
 * Admin Artists JavaScript
 * Handles artists management functionality for admin
 */

// Global variables
let currentArtists = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentFilters = {};
let currentArtistId = null;

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", async () => {
  // Load artists data
  await loadArtists();

  // Initialize event listeners
  initEventListeners();

  // Handle view toggle (table/grid)
  initViewToggle();
});

/**
 * Load artists data
 */
async function loadArtists(filters = {}) {
  try {
    // Show loading indicator
    document.getElementById("artistsTableBody").innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading artists...</p>
                </td>
            </tr>
        `;

    // Get all artists from service
    const artists = await userService.getArtists();

    // Apply filters
    let filteredArtists = [...artists];

    if (filters) {
      currentFilters = filters;

      // Filter by status
      if (filters.status && filters.status !== "all") {
        filteredArtists = filteredArtists.filter(
          (artist) => artist.status === filters.status
        );
      }

      // Filter by specialty
      if (filters.specialty && filters.specialty !== "all") {
        filteredArtists = filteredArtists.filter(
          (artist) =>
            artist.specialties && artist.specialties.includes(filters.specialty)
        );
      }

      // Filter by search term
      if (filters.searchTerm) {
        const term = filters.searchTerm.toLowerCase();
        filteredArtists = filteredArtists.filter(
          (artist) =>
            artist.name.toLowerCase().includes(term) ||
            artist.email.toLowerCase().includes(term) ||
            (artist.bio && artist.bio.toLowerCase().includes(term))
        );
      }
    }

    // Store current artists
    currentArtists = filteredArtists;

    // Render paginated artists
    renderArtists(currentPage);

    // Render pagination
    renderPagination();
  } catch (error) {
    console.error("Error loading artists:", error);
    window.notifications.error(
      "Failed to load artists. Please try again later."
    );
  }
}

/**
 * Render artists to table and grid
 * @param {number} page - Page number
 */
function renderArtists(page) {
  // Get table body and grid container
  const tableBody = document.getElementById("artistsTableBody");
  const gridContainer = document.getElementById("artistsGrid");

  // Clear table body and grid
  tableBody.innerHTML = "";
  gridContainer.innerHTML = "";

  // Get paginated data
  const startIndex = (page - 1) * itemsPerPage;
  const endIndex = Math.min(startIndex + itemsPerPage, currentArtists.length);
  const paginatedArtists = currentArtists.slice(startIndex, endIndex);

  // If no artists, show message
  if (paginatedArtists.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-user-artist fs-1 text-muted mb-3"></i>
                        <h5>No artists found</h5>
                        <p class="text-muted">Try changing your filters or check back later.</p>
                    </div>
                </td>
            </tr>
        `;

    gridContainer.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="empty-state">
                    <i class="fas fa-user-artist fs-1 text-muted mb-3"></i>
                    <h5>No artists found</h5>
                    <p class="text-muted">Try changing your filters or check back later.</p>
                </div>
            </div>
        `;
    return;
  }

  // Render Table View
  paginatedArtists.forEach((artist) => {
    const tr = document.createElement("tr");

    // Format status badge
    const statusBadge = getStatusBadge(artist.status);

    // Format date
    const dateJoined = formatDate(artist.createdAt);

    // Format specialties
    const specialties =
      artist.specialties && artist.specialties.length > 0
        ? artist.specialties.join(", ")
        : "Not specified";

    // Default profile picture
    const profilePic =
      artist.profilePicture ||
      "https://via.placeholder.com/60?text=" + artist.name.charAt(0);

    // Set row HTML
    tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <img src="${profilePic}" alt="${
      artist.name
    }" class="artist-avatar me-3">
                    <div>
                        <div class="fw-bold">${artist.name}</div>
                        <small class="text-muted">${artist.id}</small>
                    </div>
                </div>
            </td>
            <td>${artist.email}</td>
            <td>${specialties}</td>
            <td>${artist.artworks ? artist.artworks.length : 0}</td>
            <td>${artist.followers ? artist.followers.length : 0}</td>
            <td>${dateJoined}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-outline-primary btn-sm action-btn view-artist" data-id="${
                      artist.id
                    }">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm action-btn edit-artist" data-id="${
                      artist.id
                    }">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm action-btn delete-artist" data-id="${
                      artist.id
                    }">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

    // Add row to table
    tableBody.appendChild(tr);
  });

  // Render Grid View
  paginatedArtists.forEach((artist) => {
    const col = document.createElement("div");
    col.className = "col";

    // Format status badge
    const statusBadge = getStatusBadge(artist.status);

    // Format specialties
    const specialties =
      artist.specialties && artist.specialties.length > 0
        ? artist.specialties.join(", ")
        : "Not specified";

    // Default profile picture and cover image
    const profilePic =
      artist.profilePicture ||
      "https://via.placeholder.com/80?text=" + artist.name.charAt(0);
    const coverImage =
      "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=1200";

    // Set card HTML
    col.innerHTML = `
            <div class="artist-card">
                <div class="artist-card-header">
                    <img src="${coverImage}" alt="Artist cover">
                    <img src="${profilePic}" alt="${
      artist.name
    }" class="artist-avatar-large">
                </div>
                <div class="artist-card-body">
                    <h3 class="artist-name">${artist.name}</h3>
                    <div class="artist-specialties">${specialties}</div>
                    
                    <div class="artist-stats">
                        <div class="artist-stat">
                            <i class="fas fa-palette"></i> ${
                              artist.artworks ? artist.artworks.length : 0
                            } Artworks
                        </div>
                        <div class="artist-stat">
                            <i class="fas fa-users"></i> ${
                              artist.followers ? artist.followers.length : 0
                            } Followers
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        ${statusBadge}
                    </div>
                    
                    <p class="artist-bio text-muted small">${
                      artist.bio
                        ? truncateText(artist.bio, 120)
                        : "No bio provided."
                    }</p>
                </div>
                <div class="artist-card-footer">
                    <button class="btn btn-sm btn-outline-primary view-artist" data-id="${
                      artist.id
                    }">View Details</button>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary edit-artist" data-id="${
                          artist.id
                        }">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-artist" data-id="${
                          artist.id
                        }">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

    // Add card to grid
    gridContainer.appendChild(col);
  });

  // Add event listeners to action buttons
  addActionButtonListeners();
}

/**
 * Render pagination controls
 */
function renderPagination() {
  const paginationContainer = document.getElementById("artistPagination");
  paginationContainer.innerHTML = "";

  // Calculate total pages
  const totalPages = Math.ceil(currentArtists.length / itemsPerPage);

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
  document.querySelectorAll("#artistPagination .page-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const page = parseInt(e.target.closest(".page-link").dataset.page);
      if (page && page !== currentPage) {
        currentPage = page;
        renderArtists(currentPage);
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
 * @param {string} status - Artist status
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
    default:
      return '<span class="status-badge">' + status + "</span>";
  }
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
 * Truncate text to a specific length
 * @param {string} text - Text to truncate
 * @param {number} length - Maximum length
 * @returns {string} Truncated text
 */
function truncateText(text, length = 100) {
  if (!text) return "";
  if (text.length <= length) return text;
  return text.substring(0, length) + "...";
}

/**
 * Add event listeners to action buttons
 */
function addActionButtonListeners() {
  // View artist buttons
  document.querySelectorAll(".view-artist").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const artistId = e.currentTarget.dataset.id;
      openArtistDetailsModal(artistId);
    });
  });

  // Edit artist buttons
  document.querySelectorAll(".edit-artist").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const artistId = e.currentTarget.dataset.id;
      // Redirect to edit page or open edit modal
      window.location.href = `edit-artist.html?id=${artistId}`;
    });
  });

  // Delete artist buttons
  document.querySelectorAll(".delete-artist").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const artistId = e.currentTarget.dataset.id;
      const artist = currentArtists.find((a) => a.id === artistId);

      if (
        confirm(
          `Are you sure you want to delete artist "${artist.name}"? This action cannot be undone.`
        )
      ) {
        deleteArtist(artistId);
      }
    });
  });
}

/**
 * Open artist details modal
 * @param {string} artistId - Artist ID
 */
async function openArtistDetailsModal(artistId) {
  try {
    // Find artist by ID
    const artist = currentArtists.find((a) => a.id === artistId);

    if (!artist) {
      window.notifications.error("Artist not found.");
      return;
    }

    // Store current artist ID
    currentArtistId = artistId;

    // Set modal content
    const modalContent = document.getElementById("artistDetailsContent");

    // Default profile picture
    const profilePic =
      artist.profilePicture ||
      "https://via.placeholder.com/120?text=" + artist.name.charAt(0);

    // Format specialties
    const specialties =
      artist.specialties && artist.specialties.length > 0
        ? artist.specialties
            .map((s) => `<span class="badge bg-secondary me-1">${s}</span>`)
            .join("")
        : '<span class="text-muted">No specialties specified</span>';

    // Format education
    let educationHTML =
      '<span class="text-muted">No education history provided</span>';
    if (artist.education && artist.education.length > 0) {
      educationHTML = '<ul class="list-unstyled">';
      artist.education.forEach((edu) => {
        educationHTML += `
                    <li class="mb-2">
                        <div class="fw-bold">${edu.institution}</div>
                        <div>${edu.degree}, ${edu.field}</div>
                        <div class="text-muted small">${edu.year}</div>
                    </li>
                `;
      });
      educationHTML += "</ul>";
    }

    // Format exhibitions
    let exhibitionsHTML =
      '<span class="text-muted">No exhibition history provided</span>';
    if (artist.exhibitions && artist.exhibitions.length > 0) {
      exhibitionsHTML = '<ul class="list-unstyled">';
      artist.exhibitions.forEach((exhibition) => {
        exhibitionsHTML += `
                    <li class="mb-2">
                        <div class="fw-bold">${exhibition.title}</div>
                        <div>${exhibition.venue}, ${exhibition.location}</div>
                        <div class="text-muted small">${exhibition.year}</div>
                    </li>
                `;
      });
      exhibitionsHTML += "</ul>";
    }

    // Create details HTML
    const detailsHTML = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="${profilePic}" alt="${
      artist.name
    }" class="img-fluid rounded-circle mb-3" style="max-width: 160px;">
                    <h4 class="mb-1">${artist.name}</h4>
                    <p class="text-muted mb-3">${artist.email}</p>
                    <div class="d-flex justify-content-center mb-3">
                        ${getStatusBadge(artist.status)}
                    </div>
                    <div class="d-flex justify-content-around text-center mb-3">
                        <div class="px-3">
                            <div class="h5 mb-0">${
                              artist.artworks ? artist.artworks.length : 0
                            }</div>
                            <div class="small text-muted">Artworks</div>
                        </div>
                        <div class="px-3 border-start border-end">
                            <div class="h5 mb-0">${
                              artist.followers ? artist.followers.length : 0
                            }</div>
                            <div class="small text-muted">Followers</div>
                        </div>
                        <div class="px-3">
                            <div class="h5 mb-0">${
                              artist.sales ? artist.sales.length : 0
                            }</div>
                            <div class="small text-muted">Sales</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Biography</h5>
                        <p>${artist.bio || "No biography provided."}</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Specialties</h5>
                            <div>${specialties}</div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Account Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>ID:</strong> ${artist.id}</li>
                                <li><strong>Joined:</strong> ${formatDate(
                                  artist.createdAt
                                )}</li>
                                <li><strong>Last Updated:</strong> ${formatDate(
                                  artist.updatedAt
                                )}</li>
                                <li><strong>Balance:</strong> ${
                                  artist.balance
                                    ? "$" + artist.balance.toFixed(2)
                                    : "$0.00"
                                }</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Education</h5>
                            ${educationHTML}
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Exhibitions</h5>
                            ${exhibitionsHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Set modal content
    modalContent.innerHTML = detailsHTML;

    // Show/hide approval buttons based on status
    const approveBtn = document.getElementById("approveArtistBtn");
    const rejectBtn = document.getElementById("rejectArtistBtn");
    const editBtn = document.getElementById("editArtistBtn");

    if (artist.status === "pending") {
      approveBtn.style.display = "block";
      rejectBtn.style.display = "block";
    } else {
      approveBtn.style.display = "none";
      rejectBtn.style.display = "none";
    }

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(
      document.getElementById("artistDetailsModal")
    );
    modal.show();
  } catch (error) {
    console.error("Error opening artist details:", error);
    window.notifications.error(
      "Failed to load artist details. Please try again."
    );
  }
}

/**
 * Initialize event listeners
 */
function initEventListeners() {
  // Filter form submit
  document
    .getElementById("artistFilterForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();

      // Get filter values
      const status = document.getElementById("statusFilter").value;
      const specialty = document.getElementById("specialtyFilter").value;
      const searchTerm = document.getElementById("searchFilter").value;

      // Create filters object
      const filters = {
        status,
        specialty,
        searchTerm,
      };

      // Reset to first page
      currentPage = 1;

      // Apply filters
      loadArtists(filters);
    });

  // Reset button
  document.getElementById("artistFilterForm").addEventListener("reset", () => {
    // Wait for form to reset
    setTimeout(() => {
      // Reset to first page
      currentPage = 1;

      // Clear filters
      loadArtists({});
    }, 0);
  });

  // Export button
  document.getElementById("exportBtn").addEventListener("click", exportArtists);

  // Approve artist button
  document
    .getElementById("approveArtistBtn")
    .addEventListener("click", approveArtist);

  // Reject artist button
  document
    .getElementById("rejectArtistBtn")
    .addEventListener("click", showRejectionModal);

  // Confirm reject button
  document
    .getElementById("confirmRejectBtn")
    .addEventListener("click", rejectArtist);
}

/**
 * Initialize view toggle (table/grid)
 */
function initViewToggle() {
  const tableViewBtn = document.getElementById("tableViewBtn");
  const gridViewBtn = document.getElementById("gridViewBtn");
  const tableView = document.getElementById("tableView");
  const gridView = document.getElementById("gridView");

  tableViewBtn.addEventListener("click", () => {
    tableViewBtn.classList.add("active");
    gridViewBtn.classList.remove("active");

    tableView.classList.remove("hidden");
    gridView.classList.remove("active");
  });

  gridViewBtn.addEventListener("click", () => {
    gridViewBtn.classList.add("active");
    tableViewBtn.classList.remove("active");

    tableView.classList.add("hidden");
    gridView.classList.add("active");
  });
}

/**
 * Export artists to CSV
 */
function exportArtists() {
  try {
    // Create headers
    const headers = [
      "ID",
      "Name",
      "Email",
      "Status",
      "Specialties",
      "Artworks",
      "Followers",
      "Date Joined",
    ];

    // Create rows
    const rows = currentArtists.map((artist) => [
      artist.id,
      artist.name,
      artist.email,
      artist.status,
      artist.specialties ? artist.specialties.join("; ") : "",
      artist.artworks ? artist.artworks.length : 0,
      artist.followers ? artist.followers.length : 0,
      formatDate(artist.createdAt),
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
      `artists-export-${new Date().toISOString().slice(0, 10)}.csv`
    );
    link.style.display = "none";

    // Append to body and click
    document.body.appendChild(link);
    link.click();

    // Clean up
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    window.notifications.success("Artists exported successfully");
  } catch (error) {
    console.error("Error exporting artists:", error);
    window.notifications.error("Failed to export artists. Please try again.");
  }
}

/**
 * Approve artist
 */
async function approveArtist() {
  try {
    if (!currentArtistId) return;

    // Get admin user
    const admin = window.auth.currentUser;

    // Find artist in the current list
    const artistIndex = currentArtists.findIndex(
      (artist) => artist.id === currentArtistId
    );

    if (artistIndex === -1) {
      window.notifications.error("Artist not found");
      return;
    }

    // Update artist status
    currentArtists[artistIndex].status = "approved";
    currentArtists[artistIndex].updatedAt = new Date().toISOString();

    // Log action (in real app, this would update the server)
    console.log(
      `Artist approved by admin: ${admin.id}, artist: ${currentArtistId}`
    );

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("artistDetailsModal")
    ).hide();

    // Show success message
    window.notifications.success("Artist has been approved");

    // Reload artists
    renderArtists(currentPage);
  } catch (error) {
    console.error("Error approving artist:", error);
    window.notifications.error("Failed to approve artist. Please try again.");
  }
}

/**
 * Show rejection modal
 */
function showRejectionModal() {
  // Hide details modal
  bootstrap.Modal.getInstance(
    document.getElementById("artistDetailsModal")
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
 * Reject artist
 */
async function rejectArtist() {
  try {
    if (!currentArtistId) return;

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

    // Find artist in the current list
    const artistIndex = currentArtists.findIndex(
      (artist) => artist.id === currentArtistId
    );

    if (artistIndex === -1) {
      window.notifications.error("Artist not found");
      return;
    }

    // Update artist status
    currentArtists[artistIndex].status = "rejected";
    currentArtists[artistIndex].rejectionReason = rejectionReason;
    currentArtists[artistIndex].updatedAt = new Date().toISOString();

    // Log action (in real app, this would update the server)
    console.log(
      `Artist rejected by admin: ${admin.id}, artist: ${currentArtistId}, reason: ${rejectionReason}`
    );

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("rejectionReasonModal")
    ).hide();

    // Show success message
    window.notifications.success("Artist has been rejected");

    // Reload artists
    renderArtists(currentPage);
  } catch (error) {
    console.error("Error rejecting artist:", error);
    window.notifications.error("Failed to reject artist. Please try again.");
  }
}

/**
 * Delete artist
 * @param {string} artistId - Artist ID to delete
 */
async function deleteArtist(artistId) {
  try {
    // Get admin user
    const admin = window.auth.currentUser;

    // Find artist to delete
    const artistIndex = currentArtists.findIndex(
      (artist) => artist.id === artistId
    );

    if (artistIndex === -1) {
      window.notifications.error("Artist not found");
      return;
    }

    // Remove artist from array (in real app, this would call an API)
    const deletedArtist = currentArtists.splice(artistIndex, 1)[0];

    // Log action
    console.log(
      `Artist deleted by admin: ${admin.id}, artist: ${artistId}, name: ${deletedArtist.name}`
    );

    // Show success message
    window.notifications.success("Artist has been deleted");

    // Reload artists
    renderArtists(currentPage);
    renderPagination();
  } catch (error) {
    console.error("Error deleting artist:", error);
    window.notifications.error("Failed to delete artist. Please try again.");
  }
}
