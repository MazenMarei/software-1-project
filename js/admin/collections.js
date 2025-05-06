/**
 * Admin Collections JavaScript
 * Handles collections management functionality for admin
 */

// Global variables
let currentCollections = [];
let currentPage = 1;
let itemsPerPage = 12;
let currentFilters = {};
let currentCollectionId = null;

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", async () => {
  // Load collections data
  await loadCollections();

  // Initialize event listeners
  initEventListeners();

  // Handle view toggle (grid/list)
  initViewToggle();
});

/**
 * Load collections data
 */
async function loadCollections(filters = {}) {
  try {
    // Show loading indicator for list view
    document.getElementById("collectionsTableBody").innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading collections...</p>
                </td>
            </tr>
        `;

    // Show loading indicator for grid view
    document.getElementById("gridView").innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading collections...</p>
            </div>
        `;

    // Get all collections from mock service
    // In a real app, this would be an API call
    const collections = await getMockCollections();

    // Apply filters
    let filteredCollections = [...collections];

    if (filters) {
      currentFilters = filters;

      // Filter by status
      if (filters.status && filters.status !== "all") {
        filteredCollections = filteredCollections.filter(
          (collection) => collection.status === filters.status
        );
      }

      // Filter by type
      if (filters.type && filters.type !== "all") {
        filteredCollections = filteredCollections.filter(
          (collection) => collection.type === filters.type
        );
      }

      // Filter by search term
      if (filters.searchTerm) {
        const term = filters.searchTerm.toLowerCase();
        filteredCollections = filteredCollections.filter(
          (collection) =>
            collection.title.toLowerCase().includes(term) ||
            collection.description.toLowerCase().includes(term) ||
            collection.id.toLowerCase().includes(term)
        );
      }
    }

    // Store current collections
    currentCollections = filteredCollections;

    // Update stats
    updateCollectionStats(collections);

    // Render paginated collections
    renderCollections(currentPage);

    // Render pagination
    renderPagination();
  } catch (error) {
    console.error("Error loading collections:", error);
    window.notifications.error(
      "Failed to load collections. Please try again later."
    );
  }
}

/**
 * Update collection stats
 * @param {Array} collections - All collections
 */
function updateCollectionStats(collections) {
  // Get stat counters
  const totalCount = document.getElementById("totalCollectionsCount");
  const publishedCount = document.getElementById("publishedCollectionsCount");
  const draftCount = document.getElementById("draftCollectionsCount");
  const featuredCount = document.getElementById("featuredCollectionsCount");

  // Calculate stats
  const total = collections.length;
  const published = collections.filter((c) => c.status === "published").length;
  const drafts = collections.filter((c) => c.status === "draft").length;
  const featured = collections.filter((c) => c.featured).length;

  // Update counters with animation
  animateCounter(totalCount, total);
  animateCounter(publishedCount, published);
  animateCounter(draftCount, drafts);
  animateCounter(featuredCount, featured);
}

/**
 * Animate counter element
 * @param {HTMLElement} element - Counter element
 * @param {number} target - Target value
 */
function animateCounter(element, target) {
  const duration = 1000;
  const start = parseInt(element.innerText) || 0;
  const startTime = performance.now();

  function updateCounter(currentTime) {
    const elapsedTime = currentTime - startTime;
    const progress = Math.min(elapsedTime / duration, 1);
    const value = Math.floor(progress * (target - start) + start);

    element.innerText = value;

    if (progress < 1) {
      requestAnimationFrame(updateCounter);
    } else {
      element.innerText = target;
    }
  }

  requestAnimationFrame(updateCounter);
}

/**
 * Render collections to grid and list view
 * @param {number} page - Page number
 */
function renderCollections(page) {
  // Get containers
  const gridContainer = document.getElementById("gridView");
  const tableBody = document.getElementById("collectionsTableBody");

  // Clear containers
  gridContainer.innerHTML = "";
  tableBody.innerHTML = "";

  // Get paginated data
  const startIndex = (page - 1) * itemsPerPage;
  const endIndex = Math.min(
    startIndex + itemsPerPage,
    currentCollections.length
  );
  const paginatedCollections = currentCollections.slice(startIndex, endIndex);

  // If no collections, show message
  if (paginatedCollections.length === 0) {
    gridContainer.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="empty-state">
                    <i class="fas fa-layer-group fs-1 text-muted mb-3"></i>
                    <h5>No collections found</h5>
                    <p class="text-muted">Try changing your filters or create a new collection.</p>
                </div>
            </div>
        `;

    tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-layer-group fs-1 text-muted mb-3"></i>
                        <h5>No collections found</h5>
                        <p class="text-muted">Try changing your filters or create a new collection.</p>
                    </div>
                </td>
            </tr>
        `;
    return;
  }

  // Render Grid View
  paginatedCollections.forEach((collection) => {
    // Create card element
    const card = document.createElement("div");
    card.className = "collection-card";

    // Format badge
    let badgeHTML = "";
    if (collection.type === "featured") {
      badgeHTML =
        '<span class="collection-badge featured-badge">Featured</span>';
    } else if (collection.type === "seasonal") {
      badgeHTML =
        '<span class="collection-badge seasonal-badge">Seasonal</span>';
    } else if (collection.type === "curated") {
      badgeHTML = '<span class="collection-badge success-badge">Curated</span>';
    } else if (collection.type === "thematic") {
      badgeHTML = '<span class="collection-badge info-badge">Thematic</span>';
    }

    // Format status badge
    const statusBadge =
      collection.status === "published"
        ? '<span class="status-badge active">Published</span>'
        : '<span class="status-badge inactive">Draft</span>';

    // Format date
    const createdDate = formatDate(collection.createdAt);

    // Set card HTML
    card.innerHTML = `
            <div class="collection-image">
                <img src="${collection.coverImage}" alt="${collection.title}">
                ${badgeHTML}
            </div>
            <div class="collection-content">
                <h3 class="collection-title">${collection.title}</h3>
                <div class="collection-meta">
                    <div class="collection-meta-item">
                        <i class="fas fa-calendar"></i> Created: ${createdDate}
                    </div>
                    <div class="collection-meta-item">
                        <i class="fas fa-palette"></i> ${collection.artworks.length} Artworks
                    </div>
                    <div class="collection-meta-item">
                        <i class="fas fa-eye"></i> ${collection.views} Views
                    </div>
                </div>
                <div class="collection-stats">
                    <div class="collection-stat">
                        <div class="collection-stat-value">${collection.artworks.length}</div>
                        <div class="collection-stat-label">Artworks</div>
                    </div>
                    <div class="collection-stat">
                        <div class="collection-stat-value">${collection.artists.length}</div>
                        <div class="collection-stat-label">Artists</div>
                    </div>
                    <div class="collection-stat">
                        <div class="collection-stat-value">${collection.sales}</div>
                        <div class="collection-stat-label">Sales</div>
                    </div>
                </div>
                <div class="collection-actions">
                    <div>
                        ${statusBadge}
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-outline-primary btn-sm action-btn view-collection" data-id="${collection.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm action-btn edit-collection" data-id="${collection.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm action-btn delete-collection" data-id="${collection.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

    // Add card to grid
    gridContainer.appendChild(card);
  });

  // Render List View
  paginatedCollections.forEach((collection) => {
    const tr = document.createElement("tr");

    // Format badge
    let typeBadgeHTML = "";
    switch (collection.type) {
      case "featured":
        typeBadgeHTML = '<span class="badge bg-primary">Featured</span>';
        break;
      case "seasonal":
        typeBadgeHTML = '<span class="badge bg-purple">Seasonal</span>';
        break;
      case "curated":
        typeBadgeHTML = '<span class="badge bg-success">Curated</span>';
        break;
      case "thematic":
        typeBadgeHTML = '<span class="badge bg-info">Thematic</span>';
        break;
      default:
        typeBadgeHTML = '<span class="badge bg-secondary">Regular</span>';
    }

    // Format status badge
    const statusBadge =
      collection.status === "published"
        ? '<span class="status-badge active">Published</span>'
        : '<span class="status-badge inactive">Draft</span>';

    // Format date
    const createdDate = formatDate(collection.createdAt);

    // Create thumbnail HTML
    const thumbnailsHTML = collection.artworks
      .slice(0, 3)
      .map((artwork) => {
        return `<img src="${artwork.thumbnail}" alt="Artwork thumbnail" class="thumbnail">`;
      })
      .join("");

    // Set row HTML
    tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="thumbnail-container me-3">
                        ${thumbnailsHTML}
                    </div>
                    <div>
                        <div class="fw-bold">${collection.title}</div>
                        <small class="text-muted">${collection.id}</small>
                    </div>
                </div>
            </td>
            <td>${typeBadgeHTML}</td>
            <td>${collection.artworks.length}</td>
            <td>${collection.views}</td>
            <td>${createdDate}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-outline-primary btn-sm action-btn view-collection" data-id="${collection.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm action-btn edit-collection" data-id="${collection.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm action-btn delete-collection" data-id="${collection.id}">
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
  const paginationContainer = document.getElementById("collectionPagination");
  paginationContainer.innerHTML = "";

  // Calculate total pages
  const totalPages = Math.ceil(currentCollections.length / itemsPerPage);

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
  document
    .querySelectorAll("#collectionPagination .page-link")
    .forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const page = parseInt(e.target.closest(".page-link").dataset.page);
        if (page && page !== currentPage) {
          currentPage = page;
          renderCollections(currentPage);
          renderPagination();
          // Scroll to top of container
          const container = document.querySelector(".collection-grid");
          container.scrollIntoView({ behavior: "smooth" });
        }
      });
    });
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
  // View collection buttons
  document.querySelectorAll(".view-collection").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const collectionId = e.currentTarget.dataset.id;
      openCollectionDetailsModal(collectionId);
    });
  });

  // Edit collection buttons
  document.querySelectorAll(".edit-collection").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const collectionId = e.currentTarget.dataset.id;
      openEditCollectionModal(collectionId);
    });
  });

  // Delete collection buttons
  document.querySelectorAll(".delete-collection").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const collectionId = e.currentTarget.dataset.id;
      const collection = currentCollections.find((c) => c.id === collectionId);

      if (
        confirm(
          `Are you sure you want to delete collection "${collection.title}"? This action cannot be undone.`
        )
      ) {
        deleteCollection(collectionId);
      }
    });
  });
}

/**
 * Initialize event listeners
 */
function initEventListeners() {
  // Filter form submit
  document
    .getElementById("collectionFilterForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();

      // Get filter values
      const status = document.getElementById("statusFilter").value;
      const type = document.getElementById("typeFilter").value;
      const searchTerm = document.getElementById("searchFilter").value;

      // Create filters object
      const filters = {
        status,
        type,
        searchTerm,
      };

      // Reset to first page
      currentPage = 1;

      // Apply filters
      loadCollections(filters);
    });

  // Reset button
  document
    .getElementById("collectionFilterForm")
    .addEventListener("reset", () => {
      // Wait for form to reset
      setTimeout(() => {
        // Reset to first page
        currentPage = 1;

        // Clear filters
        loadCollections({});
      }, 0);
    });

  // Add collection button
  document.getElementById("addCollectionBtn").addEventListener("click", () => {
    openEditCollectionModal();
  });

  // Save collection button
  document
    .getElementById("saveCollectionBtn")
    .addEventListener("click", saveCollection);

  // Edit collection button in details modal
  document.getElementById("editCollectionBtn").addEventListener("click", () => {
    // Hide details modal
    bootstrap.Modal.getInstance(
      document.getElementById("collectionDetailsModal")
    ).hide();

    // Open edit modal with current collection ID
    if (currentCollectionId) {
      openEditCollectionModal(currentCollectionId);
    }
  });
}

/**
 * Initialize view toggle (grid/list)
 */
function initViewToggle() {
  const gridViewBtn = document.getElementById("gridViewBtn");
  const listViewBtn = document.getElementById("listViewBtn");
  const gridView = document.getElementById("gridView");
  const listView = document.getElementById("listView");

  gridViewBtn.addEventListener("click", () => {
    gridViewBtn.classList.add("active");
    listViewBtn.classList.remove("active");
    gridView.style.display = "grid";
    listView.style.display = "none";
  });

  listViewBtn.addEventListener("click", () => {
    listViewBtn.classList.add("active");
    gridViewBtn.classList.remove("active");
    listView.style.display = "block";
    gridView.style.display = "none";
  });
}

/**
 * Open collection details modal
 * @param {string} collectionId - Collection ID
 */
async function openCollectionDetailsModal(collectionId) {
  try {
    // Find collection by ID
    const collection = currentCollections.find((c) => c.id === collectionId);

    if (!collection) {
      window.notifications.error("Collection not found.");
      return;
    }

    // Store current collection ID
    currentCollectionId = collectionId;

    // Set modal content
    const modalContent = document.getElementById("collectionDetailsContent");

    // Format status badge
    const statusBadge =
      collection.status === "published"
        ? '<span class="status-badge active">Published</span>'
        : '<span class="status-badge inactive">Draft</span>';

    // Format type badge
    let typeBadgeHTML = "";
    switch (collection.type) {
      case "featured":
        typeBadgeHTML = '<span class="badge bg-primary">Featured</span>';
        break;
      case "seasonal":
        typeBadgeHTML = '<span class="badge bg-purple">Seasonal</span>';
        break;
      case "curated":
        typeBadgeHTML = '<span class="badge bg-success">Curated</span>';
        break;
      case "thematic":
        typeBadgeHTML = '<span class="badge bg-info">Thematic</span>';
        break;
      default:
        typeBadgeHTML = '<span class="badge bg-secondary">Regular</span>';
    }

    // Format date
    const createdDate = formatDate(collection.createdAt);
    const updatedDate = formatDate(collection.updatedAt);

    // Create artwork thumbnails HTML
    const artworkItemsHTML = collection.artworks
      .map((artwork) => {
        return `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="${artwork.thumbnail}" class="card-img-top" alt="${artwork.title}">
                        <div class="card-body">
                            <h6 class="card-title">${artwork.title}</h6>
                            <p class="card-text text-muted small">${artwork.artist}</p>
                        </div>
                    </div>
                </div>
            `;
      })
      .join("");

    // Create details HTML
    const detailsHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${collection.coverImage}" alt="${
      collection.title
    }" class="img-fluid rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>${statusBadge} ${typeBadgeHTML}</div>
                        <div>
                            <span class="text-muted"><i class="fas fa-eye me-1"></i> ${
                              collection.views
                            } Views</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2>${collection.title}</h2>
                    <p class="text-muted">${collection.description}</p>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <h6>Collection ID</h6>
                            <p class="text-muted">${collection.id}</p>
                        </div>
                        <div class="col-6">
                            <h6>Created By</h6>
                            <p class="text-muted">${collection.createdBy}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <h6>Created</h6>
                            <p class="text-muted">${createdDate}</p>
                        </div>
                        <div class="col-6">
                            <h6>Last Updated</h6>
                            <p class="text-muted">${updatedDate}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title">${
                              collection.artworks.length
                            }</h3>
                            <p class="card-text text-muted">Artworks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title">${
                              collection.artists.length
                            }</h3>
                            <p class="card-text text-muted">Artists</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title">${collection.sales}</h3>
                            <p class="card-text text-muted">Sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title">$${collection.revenue.toLocaleString()}</h3>
                            <p class="card-text text-muted">Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Collection Artworks</h5>
            <div class="row">
                ${artworkItemsHTML}
            </div>
        `;

    // Set modal content
    modalContent.innerHTML = detailsHTML;

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(
      document.getElementById("collectionDetailsModal")
    );
    modal.show();
  } catch (error) {
    console.error("Error opening collection details:", error);
    window.notifications.error(
      "Failed to load collection details. Please try again."
    );
  }
}

/**
 * Open edit collection modal
 * @param {string} collectionId - Collection ID (optional)
 */
function openEditCollectionModal(collectionId = null) {
  // Reset form
  document.getElementById("collectionForm").reset();

  // Set modal title
  const modalTitle = document.getElementById("editModalTitle");
  modalTitle.textContent = collectionId ? "Edit Collection" : "Add Collection";

  // Get collection if editing
  if (collectionId) {
    const collection = currentCollections.find((c) => c.id === collectionId);

    if (collection) {
      // Populate form fields
      document.getElementById("collectionId").value = collection.id;
      document.getElementById("collectionTitle").value = collection.title;
      document.getElementById("collectionType").value = collection.type;
      document.getElementById("collectionDescription").value =
        collection.description;
      document.getElementById("collectionStatus").value = collection.status;
      document.getElementById("collectionFeatured").checked =
        collection.featured;
      document.getElementById("collectionHomepage").checked =
        collection.showOnHomepage;

      // Store current collection ID
      currentCollectionId = collectionId;
    }
  } else {
    // Clear collection ID for new collection
    document.getElementById("collectionId").value = "";
    currentCollectionId = null;
  }

  // Populate available artworks (would be from an API in a real app)
  // For this demo, just show placeholder items
  const availableArtworks = document.getElementById("availableArtworks");
  availableArtworks.innerHTML = `
        <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="me-2 rounded" alt="Artwork">
            <div>
                <div class="fw-bold">Summer Breeze</div>
                <small class="text-muted">Emma Reynolds</small>
            </div>
            <button class="btn btn-sm btn-outline-primary ms-auto add-artwork-btn" data-id="art1">
                <i class="fas fa-plus"></i>
            </button>
        </li>
        <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="me-2 rounded" alt="Artwork">
            <div>
                <div class="fw-bold">Calm Waters</div>
                <small class="text-muted">Michael Chen</small>
            </div>
            <button class="btn btn-sm btn-outline-primary ms-auto add-artwork-btn" data-id="art2">
                <i class="fas fa-plus"></i>
            </button>
        </li>
        <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="me-2 rounded" alt="Artwork">
            <div>
                <div class="fw-bold">Urban Landscape</div>
                <small class="text-muted">Sophia Williams</small>
            </div>
            <button class="btn btn-sm btn-outline-primary ms-auto add-artwork-btn" data-id="art3">
                <i class="fas fa-plus"></i>
            </button>
        </li>
    `;

  // If editing, populate selected artworks
  const selectedArtworks = document.getElementById("selectedArtworks");
  selectedArtworks.innerHTML = collectionId
    ? `
        <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="me-2 rounded" alt="Artwork">
            <div>
                <div class="fw-bold">Mystic Forest</div>
                <small class="text-muted">Emma Reynolds</small>
            </div>
            <button class="btn btn-sm btn-outline-danger ms-auto remove-artwork-btn" data-id="art4">
                <i class="fas fa-times"></i>
            </button>
        </li>
        <li class="list-group-item d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="me-2 rounded" alt="Artwork">
            <div>
                <div class="fw-bold">Sunset Horizon</div>
                <small class="text-muted">Michael Chen</small>
            </div>
            <button class="btn btn-sm btn-outline-danger ms-auto remove-artwork-btn" data-id="art5">
                <i class="fas fa-times"></i>
            </button>
        </li>
    `
    : "";

  // Initialize Bootstrap modal
  const modal = new bootstrap.Modal(
    document.getElementById("editCollectionModal")
  );
  modal.show();
}

/**
 * Save collection
 */
async function saveCollection() {
  try {
    // Get form values
    const collectionId = document.getElementById("collectionId").value;
    const title = document.getElementById("collectionTitle").value;
    const type = document.getElementById("collectionType").value;
    const description = document.getElementById("collectionDescription").value;
    const status = document.getElementById("collectionStatus").value;
    const featured = document.getElementById("collectionFeatured").checked;
    const showOnHomepage =
      document.getElementById("collectionHomepage").checked;

    // Validate form
    if (!title || !description) {
      window.notifications.error("Please fill in all required fields.");
      return;
    }

    // Create collection object
    const collection = {
      id: collectionId || `coll${Date.now().toString(36)}`,
      title,
      type,
      description,
      status,
      featured,
      showOnHomepage,
      coverImage:
        "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [], // In a real app, this would be populated from the selected artworks
      artists: [], // In a real app, this would be derived from the artworks
      views: 0,
      sales: 0,
      revenue: 0,
      createdBy: "Admin User",
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
    };

    // If editing, update existing collection
    if (collectionId) {
      // Find collection index
      const index = currentCollections.findIndex((c) => c.id === collectionId);

      if (index !== -1) {
        // Preserve existing fields not in the form
        collection.artworks = currentCollections[index].artworks;
        collection.artists = currentCollections[index].artists;
        collection.views = currentCollections[index].views;
        collection.sales = currentCollections[index].sales;
        collection.revenue = currentCollections[index].revenue;
        collection.createdAt = currentCollections[index].createdAt;

        // Update collection
        currentCollections[index] = collection;
      }

      window.notifications.success("Collection updated successfully.");
    } else {
      // Add new collection
      // For demo, add some mock artworks
      collection.artworks = [
        {
          id: "art1",
          title: "Summer Breeze",
          artist: "Emma Reynolds",
          thumbnail:
            "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art2",
          title: "Calm Waters",
          artist: "Michael Chen",
          thumbnail:
            "https://images.pexels.com/photos/1341279/pexels-photo-1341279.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ];

      // Set artists array
      collection.artists = ["Emma Reynolds", "Michael Chen"];

      // Add collection to array
      currentCollections.push(collection);

      window.notifications.success("Collection created successfully.");
    }

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("editCollectionModal")
    ).hide();

    // Render collections
    renderCollections(currentPage);

    // Update stats
    updateCollectionStats(currentCollections);
  } catch (error) {
    console.error("Error saving collection:", error);
    window.notifications.error("Failed to save collection. Please try again.");
  }
}

/**
 * Delete collection
 * @param {string} collectionId - Collection ID
 */
async function deleteCollection(collectionId) {
  try {
    // Find collection index
    const index = currentCollections.findIndex((c) => c.id === collectionId);

    if (index !== -1) {
      // Remove collection
      currentCollections.splice(index, 1);

      window.notifications.success("Collection deleted successfully.");

      // Render collections
      renderCollections(currentPage);

      // Update stats
      updateCollectionStats(currentCollections);

      // Update pagination
      renderPagination();
    }
  } catch (error) {
    console.error("Error deleting collection:", error);
    window.notifications.error(
      "Failed to delete collection. Please try again."
    );
  }
}

/**
 * Get mock collections data
 * @returns {Promise<Array>} Mock collections
 */
async function getMockCollections() {
  // Simulate API delay
  await new Promise((resolve) => setTimeout(resolve, 1000));

  return [
    {
      id: "coll1",
      title: "Abstract Expressionism",
      type: "featured",
      description:
        "A collection of works by master abstract expressionists, featuring bold colors and emotional strokes that define the movement.",
      status: "published",
      featured: true,
      showOnHomepage: true,
      coverImage:
        "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art1",
          title: "Color Fields",
          artist: "Emma Reynolds",
          thumbnail:
            "https://images.pexels.com/photos/1266808/pexels-photo-1266808.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art2",
          title: "Emotional Journey",
          artist: "Michael Chen",
          thumbnail:
            "https://images.pexels.com/photos/1341279/pexels-photo-1341279.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art3",
          title: "Urban Movement",
          artist: "Sophia Williams",
          thumbnail:
            "https://images.pexels.com/photos/2693212/pexels-photo-2693212.png?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Emma Reynolds", "Michael Chen", "Sophia Williams"],
      views: 340,
      sales: 8,
      revenue: 5600,
      createdBy: "Admin User",
      createdAt: "2025-01-15T00:00:00Z",
      updatedAt: "2025-03-12T00:00:00Z",
    },
    {
      id: "coll2",
      title: "Contemporary Landscapes",
      type: "curated",
      description:
        "Modern interpretations of landscapes showcasing the beauty of nature through various artistic styles and techniques.",
      status: "published",
      featured: false,
      showOnHomepage: true,
      coverImage:
        "https://images.pexels.com/photos/346529/pexels-photo-346529.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art4",
          title: "Mountain Vista",
          artist: "Michael Chen",
          thumbnail:
            "https://images.pexels.com/photos/346529/pexels-photo-346529.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art5",
          title: "Coastal Dreams",
          artist: "Emma Reynolds",
          thumbnail:
            "https://images.pexels.com/photos/1287145/pexels-photo-1287145.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Michael Chen", "Emma Reynolds"],
      views: 256,
      sales: 4,
      revenue: 3200,
      createdBy: "Admin User",
      createdAt: "2025-02-03T00:00:00Z",
      updatedAt: "2025-03-01T00:00:00Z",
    },
    {
      id: "coll3",
      title: "Spring Collection 2025",
      type: "seasonal",
      description:
        "A vibrant collection celebrating the colors and energy of spring with fresh, bright artworks from diverse artists.",
      status: "published",
      featured: true,
      showOnHomepage: true,
      coverImage:
        "https://images.pexels.com/photos/1166209/pexels-photo-1166209.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art6",
          title: "Blooming Gardens",
          artist: "Emma Reynolds",
          thumbnail:
            "https://images.pexels.com/photos/1166209/pexels-photo-1166209.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art7",
          title: "Fresh Horizons",
          artist: "Michael Chen",
          thumbnail:
            "https://images.pexels.com/photos/1451040/pexels-photo-1451040.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Emma Reynolds", "Michael Chen"],
      views: 420,
      sales: 12,
      revenue: 8400,
      createdBy: "Admin User",
      createdAt: "2025-03-01T00:00:00Z",
      updatedAt: "2025-03-15T00:00:00Z",
    },
    {
      id: "coll4",
      title: "Urban Photography",
      type: "thematic",
      description:
        "A collection of striking urban photography capturing the essence of city life, architecture, and street culture.",
      status: "published",
      featured: false,
      showOnHomepage: false,
      coverImage:
        "https://images.pexels.com/photos/374870/pexels-photo-374870.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art8",
          title: "City Geometry",
          artist: "Sophia Williams",
          thumbnail:
            "https://images.pexels.com/photos/374870/pexels-photo-374870.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art9",
          title: "Night Streets",
          artist: "Sophia Williams",
          thumbnail:
            "https://images.pexels.com/photos/427679/pexels-photo-427679.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art10",
          title: "Urban Patterns",
          artist: "Sophia Williams",
          thumbnail:
            "https://images.pexels.com/photos/373543/pexels-photo-373543.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Sophia Williams"],
      views: 186,
      sales: 3,
      revenue: 2100,
      createdBy: "Admin User",
      createdAt: "2025-02-15T00:00:00Z",
      updatedAt: "2025-03-10T00:00:00Z",
    },
    {
      id: "coll5",
      title: "Digital Art Explorations",
      type: "featured",
      description:
        "A showcase of cutting-edge digital art pushing boundaries and exploring new creative possibilities through technology.",
      status: "published",
      featured: true,
      showOnHomepage: true,
      coverImage:
        "https://images.pexels.com/photos/1762851/pexels-photo-1762851.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art11",
          title: "Digital Dreams",
          artist: "Aiden Park",
          thumbnail:
            "https://images.pexels.com/photos/1762851/pexels-photo-1762851.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art12",
          title: "Future Worlds",
          artist: "Aiden Park",
          thumbnail:
            "https://images.pexels.com/photos/2832382/pexels-photo-2832382.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Aiden Park"],
      views: 310,
      sales: 6,
      revenue: 4800,
      createdBy: "Admin User",
      createdAt: "2025-01-28T00:00:00Z",
      updatedAt: "2025-03-05T00:00:00Z",
    },
    {
      id: "coll6",
      title: "Summer Art Festival Preview",
      type: "seasonal",
      description:
        "A preview of selected artworks that will be showcased at the upcoming Summer Art Festival 2025.",
      status: "draft",
      featured: false,
      showOnHomepage: false,
      coverImage:
        "https://images.pexels.com/photos/1546901/pexels-photo-1546901.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art13",
          title: "Summer Vibes",
          artist: "Emma Reynolds",
          thumbnail:
            "https://images.pexels.com/photos/1546901/pexels-photo-1546901.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
        {
          id: "art14",
          title: "Beach Abstractions",
          artist: "Michael Chen",
          thumbnail:
            "https://images.pexels.com/photos/1938301/pexels-photo-1938301.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Emma Reynolds", "Michael Chen"],
      views: 45,
      sales: 0,
      revenue: 0,
      createdBy: "Admin User",
      createdAt: "2025-03-20T00:00:00Z",
      updatedAt: "2025-03-25T00:00:00Z",
    },
    {
      id: "coll7",
      title: "Textile Art Showcase",
      type: "curated",
      description:
        "A collection celebrating the artistry of textiles, featuring works that explore patterns, textures, and cultural narratives.",
      status: "draft",
      featured: false,
      showOnHomepage: false,
      coverImage:
        "https://images.pexels.com/photos/4992463/pexels-photo-4992463.jpeg?auto=compress&cs=tinysrgb&w=600",
      artworks: [
        {
          id: "art15",
          title: "Woven Stories",
          artist: "Isabella Fernandez",
          thumbnail:
            "https://images.pexels.com/photos/4992463/pexels-photo-4992463.jpeg?auto=compress&cs=tinysrgb&w=200",
        },
      ],
      artists: ["Isabella Fernandez"],
      views: 12,
      sales: 0,
      revenue: 0,
      createdBy: "Admin User",
      createdAt: "2025-03-22T00:00:00Z",
      updatedAt: "2025-03-22T00:00:00Z",
    },
  ];
}
