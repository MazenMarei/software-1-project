/**
 * ArtShelf Admin - Art Fairs Management
 * This file handles all the functionality for the admin art fairs page
 */

// Class to handle art fairs management
class FairsManager {
  constructor() {
    this.fairs = [];
    this.artists = [];
    this.currentPage = 1;
    this.fairsPerPage = 10;
    this.totalFairs = 0;
    this.filteredFairs = [];
    this.initEventListeners();
    this.loadData();
  }

  /**
   * Initialize all event listeners for the fairs page
   */
  initEventListeners() {
    // Filter form submission
    document
      .getElementById("fairFilterForm")
      .addEventListener("submit", (e) => {
        e.preventDefault();
        this.applyFilters();
      });

    // Reset filters
    document.getElementById("fairFilterForm").addEventListener("reset", () => {
      setTimeout(() => this.applyFilters(), 0);
    });

    // View toggle buttons
    document
      .getElementById("tableViewBtn")
      .addEventListener("click", () => this.toggleView("table"));
    document
      .getElementById("gridViewBtn")
      .addEventListener("click", () => this.toggleView("grid"));

    // Export button
    document
      .getElementById("exportBtn")
      .addEventListener("click", () => this.exportFairs());

    // Add fair button
    document
      .getElementById("addFairBtn")
      .addEventListener("click", () => this.showEditFairModal());

    // Save fair button
    document
      .getElementById("saveFairBtn")
      .addEventListener("click", () => this.saveFair());

    // Edit fair button in details modal
    document.getElementById("editFairBtn").addEventListener("click", () => {
      const fairId = document.querySelector("#fairDetailsContent").dataset
        .fairId;
      this.showEditFairModal(fairId);
      bootstrap.Modal.getInstance(
        document.getElementById("fairDetailsModal")
      ).hide();
    });
  }

  /**
   * Toggle between table and grid view
   */
  toggleView(viewType) {
    const tableView = document.getElementById("tableView");
    const gridView = document.getElementById("gridView");
    const tableViewBtn = document.getElementById("tableViewBtn");
    const gridViewBtn = document.getElementById("gridViewBtn");

    if (viewType === "table") {
      tableView.classList.remove("hidden");
      gridView.classList.remove("active");
      tableViewBtn.classList.add("active");
      gridViewBtn.classList.remove("active");
    } else {
      tableView.classList.add("hidden");
      gridView.classList.add("active");
      tableViewBtn.classList.remove("active");
      gridViewBtn.classList.add("active");
    }
  }

  /**
   * Load all required data for the fairs page
   */
  loadData() {
    // In a real application, these would be API calls
    setTimeout(() => {
      this.fairs = this.generateMockFairs(30);
      this.filteredFairs = [...this.fairs];
      this.totalFairs = this.fairs.length;
      this.updateFairStats();
      this.renderFairsTable();
      this.renderFairsGrid();
      this.renderPagination();
    }, 500);
  }

  /**
   * Apply filters to the fairs list
   */
  applyFilters() {
    const statusFilter = document.getElementById("statusFilter").value;
    const locationFilter = document.getElementById("locationFilter").value;
    const searchFilter = document
      .getElementById("searchFilter")
      .value.toLowerCase();

    this.filteredFairs = this.fairs.filter((fair) => {
      // Status filter
      if (statusFilter !== "all" && fair.status !== statusFilter) {
        return false;
      }

      // Location filter
      if (locationFilter !== "all" && fair.location !== locationFilter) {
        return false;
      }

      // Search filter
      if (searchFilter && !this.fairMatchesSearch(fair, searchFilter)) {
        return false;
      }

      return true;
    });

    this.currentPage = 1;
    this.renderFairsTable();
    this.renderFairsGrid();
    this.renderPagination();
  }

  /**
   * Check if fair matches the search criteria
   */
  fairMatchesSearch(fair, searchTerm) {
    return (
      fair.name.toLowerCase().includes(searchTerm) ||
      fair.description.toLowerCase().includes(searchTerm) ||
      fair.location.toLowerCase().includes(searchTerm) ||
      fair.venue.toLowerCase().includes(searchTerm) ||
      fair.id.toLowerCase().includes(searchTerm)
    );
  }

  /**
   * Update fair statistics display
   */
  updateFairStats() {
    const totalFairsCount = document.getElementById("totalFairsCount");
    const upcomingFairsCount = document.getElementById("upcomingFairsCount");
    const currentFairsCount = document.getElementById("currentFairsCount");
    const registrationsCount = document.getElementById("registrationsCount");

    // Calculate stats
    const upcomingCount = this.fairs.filter(
      (fair) => fair.status === "upcoming"
    ).length;
    const currentCount = this.fairs.filter(
      (fair) => fair.status === "ongoing"
    ).length;
    const registrations = this.fairs.reduce(
      (sum, fair) => sum + fair.registrations.length,
      0
    );

    // Update DOM
    totalFairsCount.textContent = this.fairs.length;
    upcomingFairsCount.textContent = upcomingCount;
    currentFairsCount.textContent = currentCount;
    registrationsCount.textContent = registrations;
  }

  /**
   * Render the fairs table with current filtered data
   */
  renderFairsTable() {
    const tableBody = document.getElementById("fairsTableBody");
    tableBody.innerHTML = "";

    const startIndex = (this.currentPage - 1) * this.fairsPerPage;
    const endIndex = startIndex + this.fairsPerPage;
    const fairsToShow = this.filteredFairs.slice(startIndex, endIndex);

    if (fairsToShow.length === 0) {
      const noDataRow = document.createElement("tr");
      noDataRow.innerHTML = `<td colspan="6" class="text-center">No art fairs found</td>`;
      tableBody.appendChild(noDataRow);
      return;
    }

    fairsToShow.forEach((fair) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${fair.image}" alt="${
        fair.name
      }" class="me-2 rounded" width="40" height="40" style="object-fit: cover;">
                        <div>
                            <div>${fair.name}</div>
                            <small class="text-muted">${fair.venue}</small>
                        </div>
                    </div>
                </td>
                <td>${fair.location}</td>
                <td>${this.formatDateRange(fair.startDate, fair.endDate)}</td>
                <td>
                    ${fair.artistsCount} <span class="registration-pill">${
        fair.registrations.length
      } registered</span>
                </td>
                <td><span class="status-badge ${
                  fair.status
                }">${this.formatStatus(fair.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline-primary btn-sm action-btn view-fair" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm action-btn edit-fair" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm action-btn registrations-btn" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-users"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm action-btn delete-fair" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
      tableBody.appendChild(row);
    });

    // Add event listeners to buttons
    document.querySelectorAll(".view-fair").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showFairDetails(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll(".edit-fair").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showEditFairModal(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll(".registrations-btn").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showRegistrationsModal(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll(".delete-fair").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.deleteFair(e.target.closest("button").dataset.id)
      );
    });
  }

  /**
   * Render the fairs grid with current filtered data
   */
  renderFairsGrid() {
    const gridContainer = document.getElementById("fairsGrid");
    gridContainer.innerHTML = "";

    const startIndex = (this.currentPage - 1) * this.fairsPerPage;
    const endIndex = startIndex + this.fairsPerPage;
    const fairsToShow = this.filteredFairs.slice(startIndex, endIndex);

    if (fairsToShow.length === 0) {
      gridContainer.innerHTML = `<div class="col-12 text-center p-5">No art fairs found</div>`;
      return;
    }

    fairsToShow.forEach((fair) => {
      const card = document.createElement("div");
      card.className = "fair-card";
      card.innerHTML = `
                <div class="fair-card-header">
                    <img src="${fair.image}" alt="${fair.name}">
                </div>
                <div class="fair-card-body">
                    <h3 class="fair-name">${fair.name}</h3>
                    <div class="fair-location">
                        <i class="fas fa-map-marker-alt"></i> ${
                          fair.location
                        }, ${fair.venue}
                    </div>
                    <div class="fair-dates">
                        <div class="fair-date">
                            <i class="fas fa-calendar-day"></i> ${this.formatDate(
                              fair.startDate
                            )} - ${this.formatDate(fair.endDate)}
                        </div>
                    </div>
                    <div class="fair-stats">
                        <div class="fair-stat">
                            <i class="fas fa-users"></i> ${
                              fair.artistsCount
                            } Artists
                        </div>
                        <div class="fair-stat">
                            <i class="fas fa-user-check"></i> ${
                              fair.registrations.length
                            } Registered
                        </div>
                    </div>
                    <p class="text-muted small">${fair.description.substring(
                      0,
                      100
                    )}...</p>
                </div>
                <div class="fair-card-footer">
                    <span class="status-badge ${
                      fair.status
                    }">${this.formatStatus(fair.status)}</span>
                    <div class="action-buttons">
                        <button class="btn btn-outline-primary btn-sm action-btn view-fair" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm action-btn edit-fair" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-info btn-sm action-btn registrations-btn" data-id="${
                          fair.id
                        }">
                            <i class="fas fa-users"></i>
                        </button>
                    </div>
                </div>
            `;
      gridContainer.appendChild(card);
    });

    // Add event listeners to buttons
    document.querySelectorAll("#fairsGrid .view-fair").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showFairDetails(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll("#fairsGrid .edit-fair").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showEditFairModal(e.target.closest("button").dataset.id)
      );
    });

    document
      .querySelectorAll("#fairsGrid .registrations-btn")
      .forEach((btn) => {
        btn.addEventListener("click", (e) =>
          this.showRegistrationsModal(e.target.closest("button").dataset.id)
        );
      });
  }

  /**
   * Render pagination controls
   */
  renderPagination() {
    const paginationContainer = document.getElementById("fairPagination");
    paginationContainer.innerHTML = "";

    const totalPages = Math.ceil(this.filteredFairs.length / this.fairsPerPage);

    if (totalPages <= 1) {
      return;
    }

    // Previous button
    const prevItem = document.createElement("li");
    prevItem.className = `page-item ${
      this.currentPage === 1 ? "disabled" : ""
    }`;
    prevItem.innerHTML = `<a class="page-link" href="#" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
        </a>`;
    paginationContainer.appendChild(prevItem);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      const pageItem = document.createElement("li");
      pageItem.className = `page-item ${
        this.currentPage === i ? "active" : ""
      }`;
      pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      paginationContainer.appendChild(pageItem);

      pageItem.addEventListener("click", (e) => {
        e.preventDefault();
        this.currentPage = i;
        this.renderFairsTable();
        this.renderFairsGrid();
        this.renderPagination();
      });
    }

    // Next button
    const nextItem = document.createElement("li");
    nextItem.className = `page-item ${
      this.currentPage === totalPages ? "disabled" : ""
    }`;
    nextItem.innerHTML = `<a class="page-link" href="#" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
        </a>`;
    paginationContainer.appendChild(nextItem);

    // Add event listeners for prev/next
    prevItem.addEventListener("click", (e) => {
      e.preventDefault();
      if (this.currentPage > 1) {
        this.currentPage--;
        this.renderFairsTable();
        this.renderFairsGrid();
        this.renderPagination();
      }
    });

    nextItem.addEventListener("click", (e) => {
      e.preventDefault();
      if (this.currentPage < totalPages) {
        this.currentPage++;
        this.renderFairsTable();
        this.renderFairsGrid();
        this.renderPagination();
      }
    });
  }

  /**
   * Show fair details in modal
   */
  showFairDetails(fairId) {
    const fair = this.fairs.find((f) => f.id === fairId);
    if (!fair) return;

    const modal = new bootstrap.Modal(
      document.getElementById("fairDetailsModal")
    );
    const modalContent = document.getElementById("fairDetailsContent");

    // Store fair ID in the content div for edit button
    modalContent.dataset.fairId = fairId;

    // Format details for display
    const startDate = this.formatDate(fair.startDate);
    const endDate = this.formatDate(fair.endDate);
    const statusBadge = `<span class="status-badge ${
      fair.status
    }">${this.formatStatus(fair.status)}</span>`;

    modalContent.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <img src="${fair.image}" alt="${
      fair.name
    }" class="img-fluid rounded mb-3" style="max-height: 300px; width: 100%; object-fit: cover;">
                    <h3>${fair.name}</h3>
                    <p class="mb-4">${fair.description}</p>
                    
                    <h5 class="mb-3">Fair Details</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Status:</strong> ${statusBadge}
                            </div>
                            <div class="mb-3">
                                <strong>Date Range:</strong><br>
                                ${startDate} to ${endDate}
                            </div>
                            <div class="mb-3">
                                <strong>Location:</strong><br>
                                ${fair.venue}, ${fair.location}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Artists Capacity:</strong><br>
                                ${fair.artistsCount} artists
                            </div>
                            <div class="mb-3">
                                <strong>Current Registrations:</strong><br>
                                ${fair.registrations.length} artists registered
                            </div>
                            <div class="mb-3">
                                <strong>Registration Deadline:</strong><br>
                                ${this.formatDate(fair.registrationDeadline)}
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Contact & Website</h5>
                    <div class="mb-3">
                        <strong>Website:</strong><br>
                        <a href="${fair.website}" target="_blank">${
      fair.website
    }</a>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Latest Registrations</h5>
                        </div>
                        <div class="card-body">
                            ${
                              fair.registrations.length > 0
                                ? `
                                <ul class="list-group">
                                    ${fair.registrations
                                      .slice(0, 5)
                                      .map(
                                        (reg) => `
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${
                                                  reg.artistName
                                                }</strong><br>
                                                <small class="text-muted">Booth: ${
                                                  reg.booth
                                                }</small>
                                            </div>
                                            <span class="status-badge ${
                                              reg.status
                                            }">${this.formatStatus(
                                          reg.status
                                        )}</span>
                                        </li>
                                    `
                                      )
                                      .join("")}
                                </ul>
                                ${
                                  fair.registrations.length > 5
                                    ? `
                                    <div class="text-center mt-3">
                                        <button class="btn btn-outline-primary btn-sm view-all-registrations" data-id="${fair.id}">
                                            View All ${fair.registrations.length} Registrations
                                        </button>
                                    </div>
                                `
                                    : ""
                                }
                            `
                                : `<p class="text-center">No registrations yet</p>`
                            }
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Features</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Featured on Homepage
                                    <span class="badge ${
                                      fair.featured
                                        ? "bg-success"
                                        : "bg-secondary"
                                    } rounded-pill">
                                        ${fair.featured ? "Yes" : "No"}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Highlighted in Search
                                    <span class="badge ${
                                      fair.highlighted
                                        ? "bg-success"
                                        : "bg-secondary"
                                    } rounded-pill">
                                        ${fair.highlighted ? "Yes" : "No"}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Add event listener to view all registrations button
    const viewAllBtn = modalContent.querySelector(".view-all-registrations");
    if (viewAllBtn) {
      viewAllBtn.addEventListener("click", () => {
        this.showRegistrationsModal(fairId);
      });
    }

    modal.show();
  }

  /**
   * Show modal to create or edit a fair
   */
  showEditFairModal(fairId = null) {
    const modal = new bootstrap.Modal(document.getElementById("editFairModal"));
    const modalTitle = document.getElementById("editModalTitle");
    const fairForm = document.getElementById("fairForm");
    const fairId_input = document.getElementById("fairId");

    // Reset form
    fairForm.reset();

    if (fairId) {
      // Edit existing fair
      const fair = this.fairs.find((f) => f.id === fairId);
      if (!fair) return;

      modalTitle.textContent = "Edit Art Fair";
      fairId_input.value = fair.id;

      document.getElementById("fairName").value = fair.name;
      document.getElementById("fairStatus").value = fair.status;
      document.getElementById("fairStartDate").value = this.formatDateForInput(
        fair.startDate
      );
      document.getElementById("fairEndDate").value = this.formatDateForInput(
        fair.endDate
      );
      document.getElementById("fairLocation").value = fair.location;
      document.getElementById("fairVenue").value = fair.venue;
      document.getElementById("fairDescription").value = fair.description;
      document.getElementById("fairWebsite").value = fair.website;
      document.getElementById("registrationDeadline").value =
        this.formatDateForInput(fair.registrationDeadline);
      document.getElementById("maxArtists").value = fair.artistsCount;
      document.getElementById("fairFeatured").checked = fair.featured;
      document.getElementById("fairHighlight").checked = fair.highlighted;
    } else {
      // New fair
      modalTitle.textContent = "Add Art Fair";
      fairId_input.value = "";

      // Set default dates
      const today = new Date();
      const nextMonth = new Date();
      nextMonth.setMonth(today.getMonth() + 1);

      document.getElementById("fairStartDate").value =
        this.formatDateForInput(nextMonth);
      document.getElementById("fairEndDate").value = this.formatDateForInput(
        new Date(nextMonth.getTime() + 3 * 24 * 60 * 60 * 1000)
      );
      document.getElementById("registrationDeadline").value =
        this.formatDateForInput(today);
    }

    modal.show();
  }

  /**
   * Show registrations modal
   */
  showRegistrationsModal(fairId) {
    const fair = this.fairs.find((f) => f.id === fairId);
    if (!fair) return;

    const modal = new bootstrap.Modal(
      document.getElementById("registrationsModal")
    );
    const tableBody = document.getElementById("registrationsTableBody");

    tableBody.innerHTML = "";

    if (fair.registrations.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="5" class="text-center">No registrations yet</td></tr>`;
      modal.show();
      return;
    }

    fair.registrations.forEach((reg) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${reg.artistAvatar}" alt="${
        reg.artistName
      }" class="rounded-circle me-2" width="30" height="30">
                        <div>
                            <div>${reg.artistName}</div>
                            <small class="text-muted">${reg.artistEmail}</small>
                        </div>
                    </div>
                </td>
                <td>${reg.booth}</td>
                <td>${this.formatDate(reg.registrationDate)}</td>
                <td><span class="status-badge ${
                  reg.status
                }">${this.formatStatus(reg.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline-success btn-sm action-btn approve-reg" data-id="${
                          reg.id
                        }" ${reg.status === "approved" ? "disabled" : ""}>
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm action-btn reject-reg" data-id="${
                          reg.id
                        }" ${reg.status === "rejected" ? "disabled" : ""}>
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-sm action-btn view-artist" data-id="${
                          reg.artistId
                        }">
                            <i class="fas fa-user"></i>
                        </button>
                    </div>
                </td>
            `;
      tableBody.appendChild(row);
    });

    // Add event listeners
    document.querySelectorAll(".approve-reg").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const regId = e.target.closest("button").dataset.id;
        this.updateRegistrationStatus(fairId, regId, "approved");
        btn.disabled = true;
        btn.closest("tr").querySelector(".reject-reg").disabled = false;
        btn.closest("tr").querySelector(".status-badge").textContent =
          "Approved";
        btn.closest("tr").querySelector(".status-badge").className =
          "status-badge approved";
      });
    });

    document.querySelectorAll(".reject-reg").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const regId = e.target.closest("button").dataset.id;
        this.updateRegistrationStatus(fairId, regId, "rejected");
        btn.disabled = true;
        btn.closest("tr").querySelector(".approve-reg").disabled = false;
        btn.closest("tr").querySelector(".status-badge").textContent =
          "Rejected";
        btn.closest("tr").querySelector(".status-badge").className =
          "status-badge rejected";
      });
    });

    document.querySelectorAll(".view-artist").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const artistId = e.target.closest("button").dataset.id;
        // In a real app, this would navigate to artist details page
        alert(`Would navigate to artist profile for ID: ${artistId}`);
      });
    });

    modal.show();
  }

  /**
   * Save the fair (create or update)
   */
  saveFair() {
    const fairForm = document.getElementById("fairForm");

    if (!fairForm.checkValidity()) {
      fairForm.reportValidity();
      return;
    }

    const fairId = document.getElementById("fairId").value;
    const fairData = {
      id: fairId || `FAIR${Math.floor(Math.random() * 90000) + 10000}`,
      name: document.getElementById("fairName").value,
      status: document.getElementById("fairStatus").value,
      startDate: new Date(document.getElementById("fairStartDate").value),
      endDate: new Date(document.getElementById("fairEndDate").value),
      location: document.getElementById("fairLocation").value,
      venue: document.getElementById("fairVenue").value,
      description: document.getElementById("fairDescription").value,
      website: document.getElementById("fairWebsite").value,
      registrationDeadline: new Date(
        document.getElementById("registrationDeadline").value
      ),
      artistsCount: parseInt(document.getElementById("maxArtists").value) || 0,
      featured: document.getElementById("fairFeatured").checked,
      highlighted: document.getElementById("fairHighlight").checked,
      registrations: fairId
        ? this.fairs.find((f) => f.id === fairId)?.registrations || []
        : [],
      image: "https://source.unsplash.com/random/800x600/?art,fair", // Placeholder image
    };

    // Validate dates
    if (fairData.endDate < fairData.startDate) {
      alert("End date cannot be before start date");
      return;
    }

    // Check if creating new or updating existing
    if (fairId) {
      // Update existing fair
      const fairIndex = this.fairs.findIndex((f) => f.id === fairId);
      if (fairIndex >= 0) {
        this.fairs[fairIndex] = fairData;
      }
    } else {
      // Add new fair
      this.fairs.unshift(fairData);
    }

    // Refresh the display
    this.filteredFairs = [...this.fairs];
    this.updateFairStats();
    this.renderFairsTable();
    this.renderFairsGrid();
    this.renderPagination();

    // Close the modal
    bootstrap.Modal.getInstance(
      document.getElementById("editFairModal")
    ).hide();

    // Show success message
    alert(`Art fair ${fairId ? "updated" : "created"} successfully`);
  }

  /**
   * Update registration status
   */
  updateRegistrationStatus(fairId, registrationId, status) {
    const fair = this.fairs.find((f) => f.id === fairId);
    if (!fair) return;

    const registration = fair.registrations.find(
      (r) => r.id === registrationId
    );
    if (!registration) return;

    registration.status = status;
  }

  /**
   * Delete a fair
   */
  deleteFair(fairId) {
    if (
      !confirm(
        "Are you sure you want to delete this art fair? This action cannot be undone."
      )
    ) {
      return;
    }

    const fairIndex = this.fairs.findIndex((f) => f.id === fairId);
    if (fairIndex >= 0) {
      this.fairs.splice(fairIndex, 1);
      this.filteredFairs = this.filteredFairs.filter((f) => f.id !== fairId);

      this.updateFairStats();
      this.renderFairsTable();
      this.renderFairsGrid();
      this.renderPagination();

      alert("Art fair deleted successfully");
    }
  }

  /**
   * Export fairs data
   */
  exportFairs() {
    // In a real application, this would export to CSV/Excel
    alert("Art fairs data would be exported in a real application");
  }

  /**
   * Format date for display
   */
  formatDate(dateString) {
    const options = { year: "numeric", month: "short", day: "numeric" };
    return new Date(dateString).toLocaleDateString("en-US", options);
  }

  /**
   * Format date range for display
   */
  formatDateRange(startDateString, endDateString) {
    const startDate = new Date(startDateString);
    const endDate = new Date(endDateString);

    const startMonth = startDate.toLocaleDateString("en-US", {
      month: "short",
    });
    const endMonth = endDate.toLocaleDateString("en-US", { month: "short" });
    const startDay = startDate.getDate();
    const endDay = endDate.getDate();
    const startYear = startDate.getFullYear();
    const endYear = endDate.getFullYear();

    if (startYear !== endYear) {
      return `${startMonth} ${startDay}, ${startYear} - ${endMonth} ${endDay}, ${endYear}`;
    } else if (startMonth !== endMonth) {
      return `${startMonth} ${startDay} - ${endMonth} ${endDay}, ${endYear}`;
    } else {
      return `${startMonth} ${startDay} - ${endDay}, ${endYear}`;
    }
  }

  /**
   * Format date for input fields
   */
  formatDateForInput(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  }

  /**
   * Format status for display
   */
  formatStatus(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
  }

  /**
   * Generate mock data for testing
   */
  generateMockFairs(count) {
    const fairNames = [
      "International Art Expo",
      "Contemporary Art Fair",
      "Modern Masters Exhibition",
      "Artisan Market",
      "Fine Art Showcase",
      "Emerging Artists Spotlight",
      "Digital Art Convention",
      "Sculpture & Installation Fair",
      "Photography Biennale",
      "Traditional Arts Festival",
      "Mixed Media Exhibition",
      "Art & Technology Summit",
    ];

    const locations = [
      "New York",
      "London",
      "Paris",
      "Tokyo",
      "Miami",
      "Berlin",
      "Hong Kong",
      "Toronto",
      "Sydney",
      "Dubai",
    ];
    const venues = [
      "Grand Exhibition Center",
      "Metropolitan Convention Hall",
      "City Gallery",
      "National Arts Building",
      "Riverside Convention Center",
      "Cultural Palace",
      "Downtown Exhibition Space",
      "Harbor Arts Center",
      "Museum of Modern Art",
    ];

    const statuses = ["upcoming", "ongoing", "completed", "cancelled"];

    const fairs = [];

    for (let i = 0; i < count; i++) {
      const fairId = `FAIR${Math.floor(Math.random() * 90000) + 10000}`;
      const fairName = `${
        fairNames[Math.floor(Math.random() * fairNames.length)]
      } ${new Date().getFullYear()}`;
      const location = locations[Math.floor(Math.random() * locations.length)];
      const venue = venues[Math.floor(Math.random() * venues.length)];

      // Generate dates
      const now = new Date();
      const startOffset = Math.floor(Math.random() * 120) - 60; // -60 to +60 days from now
      const startDate = new Date(now);
      startDate.setDate(startDate.getDate() + startOffset);

      const duration = Math.floor(Math.random() * 7) + 3; // 3-10 days
      const endDate = new Date(startDate);
      endDate.setDate(endDate.getDate() + duration);

      const registrationDeadline = new Date(startDate);
      registrationDeadline.setDate(registrationDeadline.getDate() - 14); // 2 weeks before

      // Determine status based on dates
      let status;
      if (now < startDate) {
        status = "upcoming";
      } else if (now >= startDate && now <= endDate) {
        status = "ongoing";
      } else {
        status = Math.random() < 0.9 ? "completed" : "cancelled";
      }

      // Override with random status occasionally for testing
      if (Math.random() < 0.2) {
        status = statuses[Math.floor(Math.random() * statuses.length)];
      }

      const artistsCount = Math.floor(Math.random() * 100) + 20;
      const registrationsCount = Math.floor(Math.random() * artistsCount);

      // Generate registrations
      const registrations = [];
      for (let j = 0; j < registrationsCount; j++) {
        const firstName = [
          "John",
          "Jane",
          "Michael",
          "Emma",
          "David",
          "Olivia",
          "James",
          "Sophia",
        ][Math.floor(Math.random() * 8)];
        const lastName = [
          "Smith",
          "Johnson",
          "Williams",
          "Brown",
          "Jones",
          "Miller",
          "Davis",
          "Garcia",
        ][Math.floor(Math.random() * 8)];
        const artistName = `${firstName} ${lastName}`;
        const artistEmail = `${firstName.toLowerCase()}.${lastName.toLowerCase()}@example.com`;

        registrations.push({
          id: `REG${Math.floor(Math.random() * 90000) + 10000}`,
          artistId: `ARTIST${Math.floor(Math.random() * 90000) + 10000}`,
          artistName: artistName,
          artistEmail: artistEmail,
          artistAvatar: `https://i.pravatar.cc/150?u=${artistEmail}`,
          booth: `B${Math.floor(Math.random() * 100) + 1}`,
          registrationDate: new Date(
            registrationDeadline.getTime() -
              Math.floor(Math.random() * 14) * 24 * 60 * 60 * 1000
          ),
          status: ["pending", "approved", "rejected"][
            Math.floor(Math.random() * 3)
          ],
        });
      }

      fairs.push({
        id: fairId,
        name: fairName,
        location: location,
        venue: venue,
        startDate: startDate,
        endDate: endDate,
        registrationDeadline: registrationDeadline,
        status: status,
        description: `Experience the finest collection of ${fairName.toLowerCase()} at this exclusive event. Featuring works from leading and emerging artists around the world, this fair promises an immersive journey through contemporary artistic expressions.`,
        website: `https://${fairName
          .toLowerCase()
          .replace(/\s+/g, "-")}.example.com`,
        image: `https://source.unsplash.com/random/800x600/?art,fair,${i}`,
        artistsCount: artistsCount,
        registrations: registrations,
        featured: Math.random() < 0.3,
        highlighted: Math.random() < 0.2,
      });
    }

    // Sort by date (upcoming first, then ongoing, then others)
    return fairs.sort((a, b) => {
      const statusOrder = {
        upcoming: 0,
        ongoing: 1,
        completed: 2,
        cancelled: 3,
      };
      if (statusOrder[a.status] !== statusOrder[b.status]) {
        return statusOrder[a.status] - statusOrder[b.status];
      }
      return new Date(a.startDate) - new Date(b.startDate);
    });
  }
}

// Initialize the fairs manager when DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  window.fairsManager = new FairsManager();
});
