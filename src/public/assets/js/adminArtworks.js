"use strict";
// Cache DOM elements
const elements = {
  views: {
    tableBtn: document.getElementById("tableViewBtn"),
    gridBtn: document.getElementById("gridViewBtn"),
    table: document.getElementById("tableView"),
    grid: document.getElementById("gridView"),
  },
  modals: {
    approval: document.getElementById("approvalModal"),
    rejection: document.getElementById("rejectionModal"),
    details: document.getElementById("artworkDetailsModal"),
  },
  forms: {
    filter: document.getElementById("artworkFilterForm"),
    approval: document.getElementById("approvalForm"),
    rejection: document.getElementById("rejectionForm"),
  },
  buttons: {
    confirmApprove: document.getElementById("confirmApproveBtn"),
    confirmReject: document.getElementById("confirmRejectBtn"),
    export: document.getElementById("exportBtn"),
    viewArtwork: document.querySelectorAll(".view-artwork-btn"),
    approveArtwork: document.getElementById("approveArtworkBtn"),
    rejectArtwork: document.getElementById("rejectArtworkBtn"),
  },
  inputs: {
    approvalArtworkId: document.getElementById("approvalArtworkId"),
    rejectionArtworkId: document.getElementById("rejectionArtworkId"),
    rejectionReason: document.getElementById("rejectionReason"),
  },
  content: {
    detailsContent: document.getElementById("artworkDetailsContent"),
  },
};

// Initialize view toggle
function initViewToggle() {
  if (!elements.views.tableBtn || !elements.views.gridBtn) return;

  elements.views.tableBtn.addEventListener("click", () => {
    elements.views.tableBtn.classList.add("active");
    elements.views.gridBtn.classList.remove("active");
    elements.views.table.classList.remove("d-none");
    elements.views.grid.classList.add("d-none");
  });

  elements.views.gridBtn.addEventListener("click", () => {
    elements.views.gridBtn.classList.add("active");
    elements.views.tableBtn.classList.remove("active");
    elements.views.grid.classList.remove("d-none");
    elements.views.table.classList.add("d-none");
  });
}

// // Initialize modals
// function initModals() {
//   // Approval modal
//   if (elements.modals.approval) {
//     elements.modals.approval.addEventListener("show.bs.modal", (event) => {
//       const button = event.relatedTarget;
//       if (button && button.hasAttribute("data-id")) {
//         const artworkId = button.getAttribute("data-id");
//         elements.inputs.approvalArtworkId.value = artworkId;
//       }
//     });
//   }

//   // Rejection modal
//   if (elements.modals.rejection) {
//     elements.modals.rejection.addEventListener("show.bs.modal", (event) => {
//       const button = event.relatedTarget;
//       if (button && button.hasAttribute("data-id")) {
//         const artworkId = button.getAttribute("data-id");
//         elements.inputs.rejectionArtworkId.value = artworkId;
//       }
//     });
//   }
// }

// Initialize form submissions
function initFormSubmissions() {
  // Handle approval form submission
  if (elements.buttons.confirmApprove) {
    elements.buttons.confirmApprove.addEventListener("click", () => {
      elements.forms.approval.submit();
    });
  }

  // Handle rejection form submission with validation
  if (elements.buttons.confirmReject && elements.inputs.rejectionReason) {
    elements.buttons.confirmReject.addEventListener("click", () => {
      const reasonField = elements.inputs.rejectionReason;
      if (reasonField.value.trim() === "") {
        reasonField.classList.add("is-invalid");
        return;
      }
      reasonField.classList.remove("is-invalid");
      elements.forms.rejection.submit();
    });
  }
}

// Initialize filter functionality
function initFilterFunctionality() {
  if (!elements.forms.filter) return;

  elements.forms.filter.addEventListener("submit", (e) => {
    e.preventDefault();
    applyFilters();
  });

  elements.forms.filter.addEventListener("reset", () => {
    setTimeout(() => {
      applyFilters();
    }, 10);
  });
}

// Apply filters via AJAX
function applyFilters() {
  const formData = new FormData(elements.forms.filter);
  const params = new URLSearchParams(formData);

  fetch(`/admin/artworks?${params.toString()}`, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) throw new Error("Filter request failed");
      return response.text();
    })
    .then((html) => {
      // Redirect to filtered page
      window.location.href = `/admin/artworks?${params.toString()}`;
    })
    .catch((error) => {
      // Silent error in production, log to monitoring service in real app
      // Display user-friendly message
      const alertElement = document.createElement("div");
      alertElement.className =
        "alert alert-danger alert-dismissible fade show mt-3";
      alertElement.innerHTML = `
          Error applying filters. Please try again.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
      elements.forms.filter.after(alertElement);
    });
}

// Initialize export functionality
function initExportFunctionality() {
  if (!elements.buttons.export) return;

  elements.buttons.export.addEventListener("click", () => {
    // Get current filter parameters
    const formData = elements.forms.filter
      ? new FormData(elements.forms.filter)
      : new FormData();
    const params = new URLSearchParams(formData);

    // Redirect to export endpoint
    window.location.href = `/admin/export-artworks?${params.toString()}`;
  });
}

// Initialize artwork details functionality
function initArtworkDetails() {
  if (!elements.buttons.viewArtwork.length) return;

  elements.buttons.viewArtwork.forEach((btn) => {
    btn.addEventListener("click", function () {
      const artworkId = this.getAttribute("data-id");
      if (!artworkId) return;

      loadArtworkDetails(artworkId);
    });
  });
}

// Load artwork details via AJAX
function loadArtworkDetails(artworkId) {
  // Show the modal with loading state
  const detailsModal = new bootstrap.Modal(elements.modals.details);
  detailsModal.show();

  // Clear previous content and show loader
  if (elements.content.detailsContent) {
    elements.content.detailsContent.innerHTML = `
          <div class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        `;
  }

  // Fetch artwork details
  fetch(`/admin/get-artwork-details/${artworkId}`, {
    method: "GET",
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      Accept: "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) throw new Error("Failed to load artwork details");
      return response.json();
    })
    .then((data) => {
      updateArtworkDetailsModal(data, artworkId, detailsModal);
    })
    .catch((error) => {
      // Display error in modal
      if (elements.content.detailsContent) {
        elements.content.detailsContent.innerHTML = `
            <div class="alert alert-danger">
              Unable to load artwork details. Please try again.
            </div>
          `;
      }
    });
}

// Update artwork details modal with fetched data
function updateArtworkDetailsModal(artwork, artworkId, detailsModal) {
  if (!elements.content.detailsContent) return;

  // Format data for display (this would use actual data from the API response)
  const statusClass = artwork.status ? artwork.status.toLowerCase() : "pending";

  // Update modal content
  elements.content.detailsContent.innerHTML = `
        <div class="text-center mb-4">
          <img src="/uploads/artworks/${
            artwork.imageUrl || "placeholder.jpg"
          }" alt="${artwork.title || "Artwork"}" 
               class="img-fluid rounded" style="max-height: 300px;">
        </div>
        <div class="row">
          <div class="col-md-8">
            <h4>${artwork.title || `Artwork #${artworkId}`}</h4>
            <p class="text-muted">By ${
              artwork.artistName || "Unknown Artist"
            }</p>
            <p>${artwork.description || "No description available."}</p>
            <div class="row mt-3">
              <div class="col-6 col-md-4">
                <strong>Category:</strong> ${
                  artwork.category || "Uncategorized"
                }
              </div>
              <div class="col-6 col-md-4">
                <strong>Price:</strong> $${
                  artwork.price
                    ? Number(artwork.price).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                      })
                    : "0.00"
                }
              </div>
              <div class="col-6 col-md-4">
                <strong>Date Added:</strong> ${
                  artwork.createDate
                    ? new Date(artwork.createDate).toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                      })
                    : "Unknown"
                }
              </div>
              <div class="col-6 col-md-4">
                <strong>Medium:</strong> ${artwork.medium || "Not specified"}
              </div>
              <div class="col-6 col-md-4">
                <strong>Dimensions:</strong> ${
                  artwork.dimensions || "Not specified"
                }
              </div>
              <div class="col-6 col-md-4">
                <strong>Status:</strong> <span class="status-badge ${statusClass}">${
    artwork.status || "Pending"
  }</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">Artist Details</h5>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="/uploads/profiles/${
                    artwork.artistProfilePic || "default.jpg"
                  }" alt="Artist" class="rounded-circle me-3" width="50" height="50">
                  <div>
                    <h6 class="mb-0">${
                      artwork.artistName || "Unknown Artist"
                    }</h6>
                    <small class="text-muted">${
                      artwork.artistEmail || ""
                    }</small>
                  </div>
                </div>
                <div class="mb-2">
                  <strong>Total Artworks:</strong> ${
                    artwork.artistTotalArtworks || "0"
                  }
                </div>
                <div class="mb-2">
                  <strong>Member Since:</strong> ${
                    artwork.artistJoinDate
                      ? new Date(artwork.artistJoinDate).toLocaleDateString(
                          "en-US",
                          { month: "short", year: "numeric" }
                        )
                      : "Unknown"
                  }
                </div>
                <a href="/admin/artists/${
                  artwork.artistId || "0"
                }" class="btn btn-sm btn-outline-primary w-100">View Artist Profile</a>
              </div>
            </div>
          </div>
        </div>
      `;

  // Update modal buttons
  if (elements.buttons.approveArtwork && elements.buttons.rejectArtwork) {
    // Show/hide approval buttons based on artwork status
    if (artwork.status === "Pending") {
      elements.buttons.approveArtwork.classList.remove("d-none");
      elements.buttons.rejectArtwork.classList.remove("d-none");

      // Set data attributes and event handlers
      elements.buttons.approveArtwork.setAttribute("data-id", artworkId);
      elements.buttons.rejectArtwork.setAttribute("data-id", artworkId);

      elements.buttons.approveArtwork.onclick = function () {
        elements.inputs.approvalArtworkId.value = this.getAttribute("data-id");
        detailsModal.hide();
        new bootstrap.Modal(elements.modals.approval).show();
      };

      elements.buttons.rejectArtwork.onclick = function () {
        elements.inputs.rejectionArtworkId.value = this.getAttribute("data-id");
        detailsModal.hide();
        new bootstrap.Modal(elements.modals.rejection).show();
      };
    } else {
      // Hide buttons for non-pending artworks
      elements.buttons.approveArtwork.classList.add("d-none");
      elements.buttons.rejectArtwork.classList.add("d-none");
    }
  }
}

// Initialize everything when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  // initViewToggle();
  // initModals();
  // initFormSubmissions();
  // initFilterFunctionality();
  // initExportFunctionality();
  initArtworkDetails();
});



