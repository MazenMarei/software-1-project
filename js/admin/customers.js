/**
 * Admin Customers JavaScript
 * Handles customers management functionality for admin
 */

// Global variables
let currentCustomers = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentFilters = {};
let currentCustomerId = null;

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", async () => {
  // Load customers data
  await loadCustomers();

  // Initialize event listeners
  initEventListeners();

  // Update stats
  updateCustomerStats();
});

/**
 * Load customers data
 */
async function loadCustomers(filters = {}) {
  try {
    // Show loading indicator
    document.getElementById("customersTableBody").innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading customers...</p>
                </td>
            </tr>
        `;

    // Get all customers from service
    const customers = await userService.getCustomers();

    // Apply filters
    let filteredCustomers = [...customers];

    if (filters) {
      currentFilters = filters;

      // Filter by status
      if (filters.status && filters.status !== "all") {
        const isActive = filters.status === "active";
        filteredCustomers = filteredCustomers.filter(
          (customer) => customer.isActive === isActive
        );
      }

      // Filter by activity level
      if (filters.activity && filters.activity !== "all") {
        filteredCustomers = filteredCustomers.filter((customer) => {
          const activityLevel = getActivityLevel(customer);
          return activityLevel === filters.activity;
        });
      }

      // Filter by search term
      if (filters.searchTerm) {
        const term = filters.searchTerm.toLowerCase();
        filteredCustomers = filteredCustomers.filter(
          (customer) =>
            customer.name.toLowerCase().includes(term) ||
            customer.email.toLowerCase().includes(term) ||
            customer.id.toLowerCase().includes(term)
        );
      }
    }

    // Store current customers
    currentCustomers = filteredCustomers;

    // Render paginated customers
    renderCustomers(currentPage);

    // Render pagination
    renderPagination();
  } catch (error) {
    console.error("Error loading customers:", error);
    window.notifications.error(
      "Failed to load customers. Please try again later."
    );
  }
}

/**
 * Render customers table
 * @param {number} page - Page number
 */
function renderCustomers(page) {
  // Get table body
  const tableBody = document.getElementById("customersTableBody");

  // Clear table body
  tableBody.innerHTML = "";

  // Get paginated data
  const startIndex = (page - 1) * itemsPerPage;
  const endIndex = Math.min(startIndex + itemsPerPage, currentCustomers.length);
  const paginatedCustomers = currentCustomers.slice(startIndex, endIndex);

  // If no customers, show message
  if (paginatedCustomers.length === 0) {
    tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-users fs-1 text-muted mb-3"></i>
                        <h5>No customers found</h5>
                        <p class="text-muted">Try changing your filters or check back later.</p>
                    </div>
                </td>
            </tr>
        `;
    return;
  }

  // Render Table Rows
  paginatedCustomers.forEach((customer) => {
    const tr = document.createElement("tr");

    // Format status badge
    const statusBadge = getStatusBadge(customer.isActive);

    // Get activity indicator
    const activityLevel = getActivityLevel(customer);
    const activityIndicator = getActivityIndicator(activityLevel);

    // Format date
    const dateJoined = formatDate(customer.createdAt);

    // Calculate spending
    const spending = calculateTotalSpending(customer);

    // Count orders
    const orderCount = customer.orders ? customer.orders.length : 0;

    // Default profile picture
    const profilePic =
      customer.profilePicture ||
      "https://via.placeholder.com/50?text=" + customer.name.charAt(0);

    // Set row HTML
    tr.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <img src="${profilePic}" alt="${
      customer.name
    }" class="customer-avatar me-3">
                    <div>
                        <div class="fw-bold">${customer.name}</div>
                        <small class="text-muted">${customer.id}</small>
                    </div>
                </div>
            </td>
            <td>${customer.email}</td>
            <td>${dateJoined}</td>
            <td>${orderCount}</td>
            <td>$${spending.toFixed(2)}</td>
            <td>${activityIndicator}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-outline-primary btn-sm action-btn view-customer" data-id="${
                      customer.id
                    }">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm action-btn edit-customer" data-id="${
                      customer.id
                    }">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm action-btn delete-customer" data-id="${
                      customer.id
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
 * Update customer stats in the dashboard
 */
function updateCustomerStats() {
  // Count total customers
  const totalCustomers = currentCustomers.length;
  document.getElementById("totalCustomersCount").textContent = totalCustomers;

  // Count active customers
  const activeCustomers = currentCustomers.filter(
    (customer) => customer.isActive === true
  ).length;
  document.getElementById("activeCustomersCount").textContent = activeCustomers;

  // Count inactive customers
  const inactiveCustomers = currentCustomers.filter(
    (customer) => customer.isActive === false
  ).length;
  document.getElementById("inactiveCustomersCount").textContent =
    inactiveCustomers;

  // Count pending orders (would usually come from an order service)
  const pendingOrders = 0; // Mock value
  document.getElementById("pendingOrdersCount").textContent = pendingOrders;
}

/**
 * Render pagination controls
 */
function renderPagination() {
  const paginationContainer = document.getElementById("customerPagination");
  paginationContainer.innerHTML = "";

  // Calculate total pages
  const totalPages = Math.ceil(currentCustomers.length / itemsPerPage);

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
    .querySelectorAll("#customerPagination .page-link")
    .forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const page = parseInt(e.target.closest(".page-link").dataset.page);
        if (page && page !== currentPage) {
          currentPage = page;
          renderCustomers(currentPage);
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
 * @param {boolean} isActive - Customer active status
 * @returns {string} HTML for status badge
 */
function getStatusBadge(isActive) {
  if (isActive === true) {
    return '<span class="status-badge active">Active</span>';
  } else {
    return '<span class="status-badge inactive">Inactive</span>';
  }
}

/**
 * Get activity level based on customer data
 * @param {Object} customer - Customer object
 * @returns {string} Activity level (high, medium, low)
 */
function getActivityLevel(customer) {
  // In a real application, this would be calculated based on
  // recent logins, orders, browsing history, etc.

  // For demo, we'll use some heuristics
  const orderCount = customer.orders ? customer.orders.length : 0;
  const favoritesCount = customer.favorites ? customer.favorites.length : 0;
  const followingCount = customer.following ? customer.following.length : 0;

  const activityScore = orderCount * 3 + favoritesCount + followingCount;

  if (activityScore >= 10) {
    return "high";
  } else if (activityScore >= 5) {
    return "medium";
  } else {
    return "low";
  }
}

/**
 * Get HTML for activity indicator
 * @param {string} activityLevel - Activity level
 * @returns {string} HTML for activity indicator
 */
function getActivityIndicator(activityLevel) {
  return `<span class="customer-activity ${activityLevel}"></span> ${
    activityLevel.charAt(0).toUpperCase() + activityLevel.slice(1)
  }`;
}

/**
 * Calculate total spending for a customer
 * @param {Object} customer - Customer object
 * @returns {number} Total spending
 */
function calculateTotalSpending(customer) {
  // In a real app, this would be calculated from order history
  // For demo, we'll use balance as a proxy or mock value
  return customer.balance || 0;
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
  // View customer buttons
  document.querySelectorAll(".view-customer").forEach((btn) => {
    btn.addEventListener("click", async (e) => {
      const customerId = e.currentTarget.dataset.id;
      openCustomerDetailsModal(customerId);
    });
  });

  // Edit customer buttons
  document.querySelectorAll(".edit-customer").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const customerId = e.currentTarget.dataset.id;
      openEditCustomerModal(customerId);
    });
  });

  // Delete customer buttons
  document.querySelectorAll(".delete-customer").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const customerId = e.currentTarget.dataset.id;
      const customer = currentCustomers.find((c) => c.id === customerId);

      if (
        confirm(
          `Are you sure you want to delete customer "${customer.name}"? This action cannot be undone.`
        )
      ) {
        deleteCustomer(customerId);
      }
    });
  });
}

/**
 * Open customer details modal
 * @param {string} customerId - Customer ID
 */
async function openCustomerDetailsModal(customerId) {
  try {
    // Find customer by ID
    const customer = currentCustomers.find((c) => c.id === customerId);

    if (!customer) {
      window.notifications.error("Customer not found.");
      return;
    }

    // Store current customer ID
    currentCustomerId = customerId;

    // Set modal content
    const modalContent = document.getElementById("customerDetailsContent");

    // Default profile picture
    const profilePic =
      customer.profilePicture ||
      "https://via.placeholder.com/120?text=" + customer.name.charAt(0);

    // Format favorites
    let favoritesHTML = '<span class="text-muted">No favorites added</span>';
    if (customer.favorites && customer.favorites.length > 0) {
      favoritesHTML = `<div class="badge bg-secondary me-1">${customer.favorites.length} artworks</div>`;
    }

    // Format following
    let followingHTML =
      '<span class="text-muted">Not following any artists</span>';
    if (customer.following && customer.following.length > 0) {
      followingHTML = `<div class="badge bg-secondary me-1">${customer.following.length} artists</div>`;
    }

    // Format orders
    let ordersHTML = '<span class="text-muted">No orders yet</span>';
    if (customer.orders && customer.orders.length > 0) {
      ordersHTML = `<div class="badge bg-secondary me-1">${customer.orders.length} orders</div>`;
    }

    // Format address
    let addressHTML = '<span class="text-muted">No address provided</span>';
    if (customer.address) {
      addressHTML = `
                <address class="mb-0">
                    ${customer.address.street || ""}<br>
                    ${customer.address.city || ""}, ${
        customer.address.state || ""
      } ${customer.address.zipCode || ""}<br>
                    ${customer.address.country || ""}
                </address>
            `;
    }

    // Create details HTML
    const detailsHTML = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="${profilePic}" alt="${
      customer.name
    }" class="img-fluid rounded-circle mb-3" style="max-width: 160px;">
                    <h4 class="mb-1">${customer.name}</h4>
                    <p class="text-muted mb-3">${customer.email}</p>
                    <div class="d-flex justify-content-center mb-3">
                        ${getStatusBadge(customer.isActive)}
                    </div>
                    <div class="d-flex justify-content-around text-center mb-3">
                        <div class="px-3">
                            <div class="h5 mb-0">${
                              customer.orders ? customer.orders.length : 0
                            }</div>
                            <div class="small text-muted">Orders</div>
                        </div>
                        <div class="px-3 border-start border-end">
                            <div class="h5 mb-0">${
                              customer.favorites ? customer.favorites.length : 0
                            }</div>
                            <div class="small text-muted">Favorites</div>
                        </div>
                        <div class="px-3">
                            <div class="h5 mb-0">${
                              customer.following ? customer.following.length : 0
                            }</div>
                            <div class="small text-muted">Following</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Bio</h5>
                        <p>${customer.bio || "No biography provided."}</p>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Contact Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Email:</strong> ${
                                  customer.email
                                }</li>
                                <li><strong>Phone:</strong> ${
                                  customer.phone || "Not provided"
                                }</li>
                            </ul>
                            <h6 class="mt-3">Address</h6>
                            ${addressHTML}
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Account Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>ID:</strong> ${customer.id}</li>
                                <li><strong>Joined:</strong> ${formatDate(
                                  customer.createdAt
                                )}</li>
                                <li><strong>Last Updated:</strong> ${formatDate(
                                  customer.updatedAt || customer.createdAt
                                )}</li>
                                <li><strong>Balance:</strong> ${
                                  customer.balance
                                    ? "$" + customer.balance.toFixed(2)
                                    : "$0.00"
                                }</li>
                                <li><strong>Activity:</strong> ${getActivityIndicator(
                                  getActivityLevel(customer)
                                )}</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="border-bottom pb-2">Favorites</h5>
                            ${favoritesHTML}
                        </div>
                        <div class="col-md-4">
                            <h5 class="border-bottom pb-2">Following</h5>
                            ${followingHTML}
                        </div>
                        <div class="col-md-4">
                            <h5 class="border-bottom pb-2">Orders</h5>
                            ${ordersHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Set modal content
    modalContent.innerHTML = detailsHTML;

    // Show/hide deactivate button based on status
    const deactivateBtn = document.getElementById("deactivateCustomerBtn");

    if (customer.isActive) {
      deactivateBtn.textContent = "Deactivate Account";
      deactivateBtn.style.display = "block";
    } else {
      deactivateBtn.textContent = "Activate Account";
      deactivateBtn.style.display = "block";
    }

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(
      document.getElementById("customerDetailsModal")
    );
    modal.show();
  } catch (error) {
    console.error("Error opening customer details:", error);
    window.notifications.error(
      "Failed to load customer details. Please try again."
    );
  }
}

/**
 * Open edit customer modal
 * @param {string} customerId - Customer ID to edit, null for new customer
 */
function openEditCustomerModal(customerId = null) {
  // Set modal title
  const modalTitle = document.getElementById("editModalTitle");
  modalTitle.textContent = customerId ? "Edit Customer" : "Add Customer";

  // Get form fields
  const nameField = document.getElementById("customerName");
  const emailField = document.getElementById("customerEmail");
  const phoneField = document.getElementById("customerPhone");
  const statusField = document.getElementById("customerStatus");
  const bioField = document.getElementById("customerBio");
  const streetField = document.getElementById("customerStreet");
  const cityField = document.getElementById("customerCity");
  const stateField = document.getElementById("customerState");
  const zipField = document.getElementById("customerZip");
  const countryField = document.getElementById("customerCountry");
  const idField = document.getElementById("customerId");

  // Clear form
  document.getElementById("customerForm").reset();

  if (customerId) {
    // Find customer to edit
    const customer = currentCustomers.find((c) => c.id === customerId);

    if (!customer) {
      window.notifications.error("Customer not found.");
      return;
    }

    // Fill form with customer data
    nameField.value = customer.name;
    emailField.value = customer.email;
    phoneField.value = customer.phone || "";
    statusField.value = customer.isActive.toString();
    bioField.value = customer.bio || "";

    // Fill address fields if available
    if (customer.address) {
      streetField.value = customer.address.street || "";
      cityField.value = customer.address.city || "";
      stateField.value = customer.address.state || "";
      zipField.value = customer.address.zipCode || "";
      countryField.value = customer.address.country || "";
    }

    // Store customer ID
    idField.value = customerId;
  }

  // Initialize Bootstrap modal
  const modal = new bootstrap.Modal(
    document.getElementById("editCustomerModal")
  );
  modal.show();
}

/**
 * Save customer (add or update)
 */
async function saveCustomer() {
  try {
    // Get form values
    const nameField = document.getElementById("customerName");
    const emailField = document.getElementById("customerEmail");
    const phoneField = document.getElementById("customerPhone");
    const statusField = document.getElementById("customerStatus");
    const bioField = document.getElementById("customerBio");
    const streetField = document.getElementById("customerStreet");
    const cityField = document.getElementById("customerCity");
    const stateField = document.getElementById("customerState");
    const zipField = document.getElementById("customerZip");
    const countryField = document.getElementById("customerCountry");
    const idField = document.getElementById("customerId");

    // Validate required fields
    if (!nameField.value || !emailField.value) {
      window.notifications.error("Please fill in all required fields.");
      return;
    }

    // Create customer object
    const customerData = {
      name: nameField.value,
      email: emailField.value,
      phone: phoneField.value,
      isActive: statusField.value === "true",
      bio: bioField.value,
      address: {
        street: streetField.value,
        city: cityField.value,
        state: stateField.value,
        zipCode: zipField.value,
        country: countryField.value,
      },
    };

    // Check if updating or adding
    if (idField.value) {
      // Update existing customer
      const customerIndex = currentCustomers.findIndex(
        (c) => c.id === idField.value
      );

      if (customerIndex !== -1) {
        // Merge with existing data
        const updatedCustomer = {
          ...currentCustomers[customerIndex],
          ...customerData,
          updatedAt: new Date().toISOString(),
        };

        // Update in array
        currentCustomers[customerIndex] = updatedCustomer;

        window.notifications.success("Customer updated successfully");
      } else {
        window.notifications.error("Customer not found.");
        return;
      }
    } else {
      // Add new customer
      const newCustomer = {
        ...customerData,
        id: "cust" + Math.floor(Math.random() * 10000),
        role: "customer",
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
        orders: [],
        favorites: [],
        following: [],
        balance: 0,
        currency: "usd",
      };

      // Add to array
      currentCustomers.push(newCustomer);

      window.notifications.success("Customer added successfully");
    }

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("editCustomerModal")
    ).hide();

    // Update stats
    updateCustomerStats();

    // Refresh table
    renderCustomers(currentPage);
    renderPagination();
  } catch (error) {
    console.error("Error saving customer:", error);
    window.notifications.error("Failed to save customer. Please try again.");
  }
}

/**
 * Delete customer
 * @param {string} customerId - Customer ID to delete
 */
async function deleteCustomer(customerId) {
  try {
    // Find customer to delete
    const customerIndex = currentCustomers.findIndex(
      (c) => c.id === customerId
    );

    if (customerIndex === -1) {
      window.notifications.error("Customer not found");
      return;
    }

    // Remove customer from array (in real app, this would call an API)
    const deletedCustomer = currentCustomers.splice(customerIndex, 1)[0];

    // Log action
    console.log(`Customer deleted: ${deletedCustomer.name} (${customerId})`);

    // Show success message
    window.notifications.success("Customer has been deleted");

    // Update stats
    updateCustomerStats();

    // Reload customers
    renderCustomers(currentPage);
    renderPagination();
  } catch (error) {
    console.error("Error deleting customer:", error);
    window.notifications.error("Failed to delete customer. Please try again.");
  }
}

/**
 * Toggle customer active status
 */
async function toggleCustomerStatus() {
  try {
    if (!currentCustomerId) return;

    // Find customer in the current list
    const customerIndex = currentCustomers.findIndex(
      (c) => c.id === currentCustomerId
    );

    if (customerIndex === -1) {
      window.notifications.error("Customer not found");
      return;
    }

    // Toggle active status
    const isActive = !currentCustomers[customerIndex].isActive;
    currentCustomers[customerIndex].isActive = isActive;
    currentCustomers[customerIndex].updatedAt = new Date().toISOString();

    // Show success message
    if (isActive) {
      window.notifications.success("Customer has been activated");
    } else {
      window.notifications.success("Customer has been deactivated");
    }

    // Close modal
    bootstrap.Modal.getInstance(
      document.getElementById("customerDetailsModal")
    ).hide();

    // Update stats
    updateCustomerStats();

    // Refresh table
    renderCustomers(currentPage);
    renderPagination();
  } catch (error) {
    console.error("Error toggling customer status:", error);
    window.notifications.error(
      "Failed to update customer status. Please try again."
    );
  }
}

/**
 * Export customers to CSV
 */
function exportCustomers() {
  try {
    // Create headers
    const headers = [
      "ID",
      "Name",
      "Email",
      "Status",
      "Orders",
      "Spending",
      "Activity",
      "Date Joined",
    ];

    // Create rows
    const rows = currentCustomers.map((customer) => [
      customer.id,
      customer.name,
      customer.email,
      customer.isActive ? "Active" : "Inactive",
      customer.orders ? customer.orders.length : 0,
      "$" + (customer.balance ? customer.balance.toFixed(2) : "0.00"),
      getActivityLevel(customer),
      formatDate(customer.createdAt),
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
      `customers-export-${new Date().toISOString().slice(0, 10)}.csv`
    );
    link.style.display = "none";

    // Append to body and click
    document.body.appendChild(link);
    link.click();

    // Clean up
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    window.notifications.success("Customers exported successfully");
  } catch (error) {
    console.error("Error exporting customers:", error);
    window.notifications.error("Failed to export customers. Please try again.");
  }
}

/**
 * Initialize event listeners
 */
function initEventListeners() {
  // Filter form submit
  document
    .getElementById("customerFilterForm")
    .addEventListener("submit", (e) => {
      e.preventDefault();

      // Get filter values
      const status = document.getElementById("statusFilter").value;
      const activity = document.getElementById("activityFilter").value;
      const searchTerm = document.getElementById("searchFilter").value;

      // Create filters object
      const filters = {
        status,
        activity,
        searchTerm,
      };

      // Reset to first page
      currentPage = 1;

      // Apply filters
      loadCustomers(filters);
    });

  // Reset button
  document
    .getElementById("customerFilterForm")
    .addEventListener("reset", () => {
      // Wait for form to reset
      setTimeout(() => {
        // Reset to first page
        currentPage = 1;

        // Clear filters
        loadCustomers({});
      }, 0);
    });

  // Export button
  document
    .getElementById("exportBtn")
    .addEventListener("click", exportCustomers);

  // Add customer button
  document.getElementById("addCustomerBtn").addEventListener("click", () => {
    openEditCustomerModal();
  });

  // Edit customer button (in details modal)
  document.getElementById("editCustomerBtn").addEventListener("click", () => {
    // Close details modal
    bootstrap.Modal.getInstance(
      document.getElementById("customerDetailsModal")
    ).hide();

    // Open edit modal with current customer
    openEditCustomerModal(currentCustomerId);
  });

  // Deactivate customer button
  document
    .getElementById("deactivateCustomerBtn")
    .addEventListener("click", toggleCustomerStatus);

  // Save customer button
  document
    .getElementById("saveCustomerBtn")
    .addEventListener("click", saveCustomer);
}
