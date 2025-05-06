/**
 * ArtShelf Admin - Orders Management
 * This file handles all the functionality for the admin orders page
 */

// Class to handle orders management
class OrdersManager {
  constructor() {
    this.orders = [];
    this.customers = [];
    this.artworks = [];
    this.currentPage = 1;
    this.ordersPerPage = 10;
    this.totalOrders = 0;
    this.filteredOrders = [];
    this.initEventListeners();
    this.loadData();
  }

  /**
   * Initialize all event listeners for the orders page
   */
  initEventListeners() {
    // Filter form submission
    document
      .getElementById("orderFilterForm")
      .addEventListener("submit", (e) => {
        e.preventDefault();
        this.applyFilters();
      });

    // Reset filters
    document.getElementById("orderFilterForm").addEventListener("reset", () => {
      setTimeout(() => this.applyFilters(), 0);
    });

    // Date range filter behavior
    document
      .getElementById("dateRangeFilter")
      .addEventListener("change", (e) => {
        const customDateRangeContainer =
          document.querySelector(".custom-date-range");
        if (e.target.value === "custom") {
          customDateRangeContainer.style.display = "flex";
        } else {
          customDateRangeContainer.style.display = "none";
        }
      });

    // Export orders button
    document
      .getElementById("exportOrdersBtn")
      .addEventListener("click", () => this.exportOrders());

    // Create order button
    document
      .getElementById("createOrderBtn")
      .addEventListener("click", () => this.showEditOrderModal());

    // Save order button
    document
      .getElementById("saveOrderBtn")
      .addEventListener("click", () => this.saveOrder());

    // Same as billing checkbox
    document.getElementById("sameAsBilling").addEventListener("change", (e) => {
      const shippingFields = document.getElementById("shippingFields");
      if (e.target.checked) {
        shippingFields.style.display = "none";
        this.copyBillingToShipping();
      } else {
        shippingFields.style.display = "block";
      }
    });

    // Add item button
    document
      .getElementById("addItemBtn")
      .addEventListener("click", () => this.showAddItemModal());

    // Search artwork button
    document
      .getElementById("searchArtworkBtn")
      .addEventListener("click", () => this.searchArtworks());

    // Update order status button in details modal
    document
      .getElementById("updateOrderBtn")
      .addEventListener("click", () => this.showUpdateStatusModal());

    // Save status button
    document
      .getElementById("saveStatusBtn")
      .addEventListener("click", () => this.updateOrderStatus());

    // Order status change
    document.getElementById("orderStatus").addEventListener("change", (e) => {
      const shippingFields = document.querySelectorAll(".shipping-fields");
      if (e.target.value === "shipped") {
        shippingFields.forEach((field) => (field.style.display = "block"));
      } else {
        shippingFields.forEach((field) => (field.style.display = "none"));
      }
    });

    // Calculate order total when prices change
    document
      .querySelectorAll("#taxAmount, #shippingAmount, #discountAmount")
      .forEach((el) => {
        el.addEventListener("input", () => this.calculateOrderTotal());
      });
  }

  /**
   * Load all required data for the orders page
   */
  loadData() {
    // In a real application, these would be API calls
    this.loadOrders();
    this.loadCustomers();
    this.loadArtworks();
  }

  /**
   * Load orders data (simulated)
   */
  loadOrders() {
    // Simulate API call to get orders
    setTimeout(() => {
      this.orders = this.generateMockOrders(50);
      this.filteredOrders = [...this.orders];
      this.totalOrders = this.orders.length;
      this.updateOrderStats();
      this.renderOrdersTable();
      this.renderPagination();
    }, 500);
  }

  /**
   * Load customers data (simulated)
   */
  loadCustomers() {
    // Simulate API call to get customers
    setTimeout(() => {
      this.customers = this.generateMockCustomers(20);
      this.populateCustomerDropdowns();
    }, 500);
  }

  /**
   * Load artworks data (simulated)
   */
  loadArtworks() {
    // Simulate API call to get artworks
    setTimeout(() => {
      this.artworks = this.generateMockArtworks(30);
    }, 500);
  }

  /**
   * Populate customer dropdown in the filter and order form
   */
  populateCustomerDropdowns() {
    const customerFilter = document.getElementById("customerFilter");
    const customerSelect = document.getElementById("customerSelect");

    // Clear existing options except the first one
    while (customerFilter.options.length > 1) {
      customerFilter.remove(1);
    }

    while (customerSelect.options.length > 1) {
      customerSelect.remove(1);
    }

    // Add customers to dropdowns
    this.customers.forEach((customer) => {
      const filterOption = document.createElement("option");
      filterOption.value = customer.id;
      filterOption.textContent = `${customer.firstName} ${customer.lastName}`;
      customerFilter.appendChild(filterOption);

      const selectOption = filterOption.cloneNode(true);
      customerSelect.appendChild(selectOption);
    });
  }

  /**
   * Apply filters to the orders list
   */
  applyFilters() {
    const statusFilter = document.getElementById("statusFilter").value;
    const dateRangeFilter = document.getElementById("dateRangeFilter").value;
    const searchFilter = document
      .getElementById("searchFilter")
      .value.toLowerCase();
    const startDateFilter = document.getElementById("startDateFilter").value;
    const endDateFilter = document.getElementById("endDateFilter").value;

    this.filteredOrders = this.orders.filter((order) => {
      // Status filter
      if (statusFilter !== "all" && order.status !== statusFilter) {
        return false;
      }

      // Search filter
      if (searchFilter && !this.orderMatchesSearch(order, searchFilter)) {
        return false;
      }

      // Date range filter
      if (
        !this.orderMatchesDateRange(
          order,
          dateRangeFilter,
          startDateFilter,
          endDateFilter
        )
      ) {
        return false;
      }

      return true;
    });

    this.currentPage = 1;
    this.renderOrdersTable();
    this.renderPagination();
  }

  /**
   * Check if order matches the search criteria
   */
  orderMatchesSearch(order, searchTerm) {
    return (
      order.id.toLowerCase().includes(searchTerm) ||
      order.customer.firstName.toLowerCase().includes(searchTerm) ||
      order.customer.lastName.toLowerCase().includes(searchTerm) ||
      order.customer.email.toLowerCase().includes(searchTerm) ||
      order.items.some((item) => item.title.toLowerCase().includes(searchTerm))
    );
  }

  /**
   * Check if order matches the date range filter
   */
  orderMatchesDateRange(order, dateRangeFilter, startDate, endDate) {
    const orderDate = new Date(order.date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    const last7Days = new Date(today);
    last7Days.setDate(last7Days.getDate() - 7);

    const last30Days = new Date(today);
    last30Days.setDate(last30Days.getDate() - 30);

    const thisMonthStart = new Date(today.getFullYear(), today.getMonth(), 1);

    const lastMonthStart = new Date(
      today.getFullYear(),
      today.getMonth() - 1,
      1
    );
    const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);

    switch (dateRangeFilter) {
      case "all":
        return true;
      case "today":
        return orderDate >= today;
      case "yesterday":
        return orderDate >= yesterday && orderDate < today;
      case "last7days":
        return orderDate >= last7Days;
      case "last30days":
        return orderDate >= last30Days;
      case "thisMonth":
        return orderDate >= thisMonthStart;
      case "lastMonth":
        return orderDate >= lastMonthStart && orderDate <= lastMonthEnd;
      case "custom":
        const start = startDate ? new Date(startDate) : null;
        const end = endDate ? new Date(endDate) : null;

        if (start && end) {
          // Set end date to end of day
          end.setHours(23, 59, 59, 999);
          return orderDate >= start && orderDate <= end;
        } else if (start) {
          return orderDate >= start;
        } else if (end) {
          end.setHours(23, 59, 59, 999);
          return orderDate <= end;
        }
        return true;
      default:
        return true;
    }
  }

  /**
   * Update order statistics display
   */
  updateOrderStats() {
    const totalOrdersCount = document.getElementById("totalOrdersCount");
    const pendingOrdersCount = document.getElementById("pendingOrdersCount");
    const completedOrdersCount = document.getElementById(
      "completedOrdersCount"
    );
    const totalRevenue = document.getElementById("totalRevenue");

    // Calculate stats
    const pendingCount = this.orders.filter(
      (order) => order.status === "pending" || order.status === "processing"
    ).length;

    const completedCount = this.orders.filter(
      (order) => order.status === "delivered" || order.status === "completed"
    ).length;

    const revenue = this.orders
      .filter(
        (order) =>
          (order.status === "delivered" || order.status === "completed") &&
          new Date(order.date) >=
            new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
      )
      .reduce((sum, order) => sum + order.total, 0);

    // Update DOM
    totalOrdersCount.textContent = this.orders.length;
    pendingOrdersCount.textContent = pendingCount;
    completedOrdersCount.textContent = completedCount;
    totalRevenue.textContent = `$${revenue.toFixed(2)}`;
  }

  /**
   * Render the orders table with current filtered data
   */
  renderOrdersTable() {
    const tableBody = document.getElementById("ordersTableBody");
    tableBody.innerHTML = "";

    const startIndex = (this.currentPage - 1) * this.ordersPerPage;
    const endIndex = startIndex + this.ordersPerPage;
    const ordersToShow = this.filteredOrders.slice(startIndex, endIndex);

    if (ordersToShow.length === 0) {
      const noDataRow = document.createElement("tr");
      noDataRow.innerHTML = `<td colspan="8" class="text-center">No orders found</td>`;
      tableBody.appendChild(noDataRow);
      return;
    }

    ordersToShow.forEach((order) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td><strong>${order.id}</strong></td>
                <td>${this.formatDate(order.date)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${
                          order.customer.avatar
                        }" alt="Customer" class="rounded-circle me-2" width="30" height="30">
                        <div>
                            <div>${order.customer.firstName} ${
        order.customer.lastName
      }</div>
                            <small class="text-muted">${
                              order.customer.email
                            }</small>
                        </div>
                    </div>
                </td>
                <td>${order.items.length} item${
        order.items.length !== 1 ? "s" : ""
      }</td>
                <td>$${order.total.toFixed(2)}</td>
                <td>${this.formatPaymentMethod(order.paymentMethod)}</td>
                <td><span class="status-badge ${
                  order.status
                }">${this.formatStatus(order.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-outline-primary btn-sm action-btn view-order" data-id="${
                          order.id
                        }">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm action-btn edit-order" data-id="${
                          order.id
                        }">
                            <i class="fas fa-edit"></i>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-secondary btn-sm action-btn dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item update-status" href="#" data-id="${
                                  order.id
                                }">Update Status</a></li>
                                <li><a class="dropdown-item print-invoice" href="#" data-id="${
                                  order.id
                                }">Print Invoice</a></li>
                                <li><a class="dropdown-item send-email" href="#" data-id="${
                                  order.id
                                }">Email Customer</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger delete-order" href="#" data-id="${
                                  order.id
                                }">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            `;
      tableBody.appendChild(row);
    });

    // Add event listeners to buttons
    document.querySelectorAll(".view-order").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showOrderDetails(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll(".edit-order").forEach((btn) => {
      btn.addEventListener("click", (e) =>
        this.showEditOrderModal(e.target.closest("button").dataset.id)
      );
    });

    document.querySelectorAll(".update-status").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        this.showUpdateStatusModal(e.target.dataset.id);
      });
    });

    document.querySelectorAll(".delete-order").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        this.deleteOrder(e.target.dataset.id);
      });
    });
  }

  /**
   * Render pagination controls
   */
  renderPagination() {
    const paginationContainer = document.getElementById("orderPagination");
    paginationContainer.innerHTML = "";

    const totalPages = Math.ceil(
      this.filteredOrders.length / this.ordersPerPage
    );

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
        this.renderOrdersTable();
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
        this.renderOrdersTable();
        this.renderPagination();
      }
    });

    nextItem.addEventListener("click", (e) => {
      e.preventDefault();
      if (this.currentPage < totalPages) {
        this.currentPage++;
        this.renderOrdersTable();
        this.renderPagination();
      }
    });
  }

  /**
   * Show order details in modal
   */
  showOrderDetails(orderId) {
    const order = this.orders.find((o) => o.id === orderId);
    if (!order) return;

    const modal = new bootstrap.Modal(
      document.getElementById("orderDetailsModal")
    );
    const modalContent = document.getElementById("orderDetailsContent");

    // Format details for display
    const orderDate = this.formatDate(order.date);
    const statusBadge = `<span class="status-badge ${
      order.status
    }">${this.formatStatus(order.status)}</span>`;

    let itemsHtml = "";
    order.items.forEach((item) => {
      itemsHtml += `
                <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                    <img src="${item.image}" alt="${
        item.title
      }" class="me-3 rounded" width="70" height="70" style="object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${item.title}</h6>
                        <p class="mb-0 text-muted small">Artist: ${
                          item.artist
                        }</p>
                        <p class="mb-0 text-muted small">Medium: ${
                          item.medium
                        }</p>
                    </div>
                    <div class="text-end">
                        <p class="mb-0 fw-bold">$${item.price.toFixed(2)}</p>
                    </div>
                </div>
            `;
    });

    // Timeline events
    let timelineHtml = "";
    if (order.timeline && order.timeline.length > 0) {
      order.timeline.forEach((event) => {
        timelineHtml += `
                    <div class="mb-3">
                        <div class="d-flex">
                            <div class="timeline-marker ${event.status}"></div>
                            <div>
                                <span class="timeline-date">${this.formatDate(
                                  event.date
                                )}</span>
                                <p class="mb-0">${event.description}</p>
                            </div>
                        </div>
                    </div>
                `;
      });
    }

    modalContent.innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">Order #${order.id}</h4>
                            <p class="text-muted mb-0">Placed on ${orderDate}</p>
                        </div>
                        <div>
                            ${statusBadge}
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3">Order Items</h5>
                        ${itemsHtml}
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">Order Timeline</h5>
                        <div class="activity-timeline">
                            ${
                              timelineHtml ||
                              "<p>No timeline events available</p>"
                            }
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$${order.subtotal.toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>$${order.shipping.toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>$${order.tax.toFixed(2)}</span>
                            </div>
                            ${
                              order.discount
                                ? `
                            <div class="d-flex justify-content-between mb-2">
                                <span>Discount:</span>
                                <span>-$${order.discount.toFixed(2)}</span>
                            </div>
                            `
                                : ""
                            }
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>$${order.total.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Customer Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="${
                                  order.customer.avatar
                                }" alt="Customer" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-0">${
                                      order.customer.firstName
                                    } ${order.customer.lastName}</h6>
                                    <p class="mb-0 text-muted">${
                                      order.customer.email
                                    }</p>
                                </div>
                            </div>
                            <div class="mb-2">
                                <strong>Phone:</strong> ${order.customer.phone}
                            </div>
                            <div>
                                <a href="customers.html?id=${
                                  order.customer.id
                                }" class="btn btn-sm btn-outline-primary">View Customer Profile</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <address>
                                ${order.shipping.name}<br>
                                ${order.shipping.address}<br>
                                ${order.shipping.city}, ${
      order.shipping.state
    } ${order.shipping.zip}<br>
                                ${order.shipping.country}
                            </address>
                            <div class="mb-2">
                                <strong>Method:</strong> ${
                                  order.shipping.method
                                }
                            </div>
                            ${
                              order.shipping.tracking
                                ? `
                            <div class="mb-2">
                                <strong>Tracking:</strong> ${order.shipping.tracking}
                            </div>
                            `
                                : ""
                            }
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>Method:</strong> ${this.formatPaymentMethod(
                                  order.paymentMethod
                                )}
                            </div>
                            <div class="mb-2">
                                <strong>Status:</strong> ${order.paymentStatus}
                            </div>
                            ${
                              order.transactionId
                                ? `
                            <div class="mb-2">
                                <strong>Transaction ID:</strong><br>
                                <small class="text-muted">${order.transactionId}</small>
                            </div>
                            `
                                : ""
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;

    modal.show();
  }

  /**
   * Show modal to create or edit an order
   */
  showEditOrderModal(orderId = null) {
    const modal = new bootstrap.Modal(
      document.getElementById("editOrderModal")
    );
    const modalTitle = document.getElementById("editModalTitle");
    const orderForm = document.getElementById("orderForm");
    const orderId_input = document.getElementById("orderId");
    const customerSelect = document.getElementById("customerSelect");
    const orderDate = document.getElementById("orderDate");

    // Reset form
    orderForm.reset();

    // Clear order items
    const orderItemsBody = document.getElementById("orderItemsBody");
    orderItemsBody.innerHTML = `
            <tr id="emptyItemRow">
                <td colspan="5" class="text-center">No items added yet</td>
            </tr>
        `;

    if (orderId) {
      // Edit existing order
      const order = this.orders.find((o) => o.id === orderId);
      if (!order) return;

      modalTitle.textContent = "Edit Order";
      orderId_input.value = order.id;

      // Set customer
      customerSelect.value = order.customer.id;

      // Set date
      const orderDateTime = new Date(order.date);
      orderDate.value = this.formatDateTimeForInput(orderDateTime);

      // Add order items
      if (order.items.length > 0) {
        document.getElementById("emptyItemRow").remove();
        order.items.forEach((item) => this.addItemToOrderForm(item));
      }

      // Set billing info
      document.getElementById("billingName").value = order.billing.name;
      document.getElementById("billingEmail").value = order.billing.email;
      document.getElementById("billingPhone").value = order.billing.phone;
      document.getElementById("billingAddress").value = order.billing.address;
      document.getElementById("billingCity").value = order.billing.city;
      document.getElementById("billingState").value = order.billing.state;
      document.getElementById("billingPostal").value = order.billing.zip;
      document.getElementById("billingCountry").value = order.billing.country;

      // Set shipping info
      document.getElementById("shippingName").value = order.shipping.name;
      document.getElementById("shippingEmail").value = order.shipping.email;
      document.getElementById("shippingPhone").value = order.shipping.phone;
      document.getElementById("shippingAddress").value = order.shipping.address;
      document.getElementById("shippingCity").value = order.shipping.city;
      document.getElementById("shippingState").value = order.shipping.state;
      document.getElementById("shippingPostal").value = order.shipping.zip;
      document.getElementById("shippingCountry").value = order.shipping.country;

      // Set payment info
      document.getElementById("paymentMethod").value = order.paymentMethod;
      document.getElementById("paymentStatus").value = order.paymentStatus;
      document.getElementById("transactionId").value =
        order.transactionId || "";

      // Set order totals
      document.getElementById("taxAmount").value = order.tax.toFixed(2);
      document.getElementById("shippingAmount").value =
        order.shipping.cost.toFixed(2);
      document.getElementById("discountAmount").value = (
        order.discount || 0
      ).toFixed(2);

      // Set notes
      document.getElementById("orderNotes").value = order.notes || "";

      // Calculate total
      this.calculateOrderTotal();
    } else {
      // New order
      modalTitle.textContent = "Create Order";
      orderId_input.value = "";

      // Set current date/time
      const now = new Date();
      orderDate.value = this.formatDateTimeForInput(now);
    }

    modal.show();
  }

  /**
   * Add an item to the order form
   */
  addItemToOrderForm(item) {
    const orderItemsBody = document.getElementById("orderItemsBody");
    const emptyRow = document.getElementById("emptyItemRow");

    if (emptyRow) {
      emptyRow.remove();
    }

    const newRow = document.createElement("tr");
    newRow.dataset.id = item.id;
    newRow.dataset.price = item.price;

    newRow.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <img src="${item.image}" alt="${
      item.title
    }" class="me-2 rounded" width="40" height="40" style="object-fit: cover;">
                    <div>
                        <div>${item.title}</div>
                        <small class="text-muted">${item.artist}</small>
                    </div>
                </div>
            </td>
            <td>$${item.price.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm item-quantity" min="1" value="1" style="width: 70px;">
            </td>
            <td class="item-total">$${item.price.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

    orderItemsBody.appendChild(newRow);

    // Add event listeners
    newRow.querySelector(".item-quantity").addEventListener("change", (e) => {
      const quantity = parseInt(e.target.value) || 1;
      const price = parseFloat(newRow.dataset.price);
      const total = price * quantity;
      newRow.querySelector(".item-total").textContent = `$${total.toFixed(2)}`;
      this.calculateOrderTotal();
    });

    newRow.querySelector(".remove-item").addEventListener("click", () => {
      newRow.remove();
      this.calculateOrderTotal();

      // If no items, add empty row
      if (orderItemsBody.children.length === 0) {
        orderItemsBody.innerHTML = `
                    <tr id="emptyItemRow">
                        <td colspan="5" class="text-center">No items added yet</td>
                    </tr>
                `;
      }
    });

    this.calculateOrderTotal();
  }

  /**
   * Show modal to add an item to the order
   */
  showAddItemModal() {
    const modal = new bootstrap.Modal(document.getElementById("addItemModal"));
    const resultsContainer = document.getElementById("artworkSearchResults");

    // Clear previous results
    resultsContainer.innerHTML =
      '<p class="text-center">Search for artworks to add to the order</p>';

    // Clear search input
    document.getElementById("searchArtwork").value = "";

    modal.show();
  }

  /**
   * Search artworks based on input
   */
  searchArtworks() {
    const searchTerm = document
      .getElementById("searchArtwork")
      .value.toLowerCase();
    const resultsContainer = document.getElementById("artworkSearchResults");

    if (!searchTerm) {
      resultsContainer.innerHTML =
        '<p class="text-center">Please enter a search term</p>';
      return;
    }

    const filteredArtworks = this.artworks.filter(
      (artwork) =>
        artwork.title.toLowerCase().includes(searchTerm) ||
        artwork.artist.toLowerCase().includes(searchTerm) ||
        artwork.id.toLowerCase().includes(searchTerm)
    );

    if (filteredArtworks.length === 0) {
      resultsContainer.innerHTML =
        '<p class="text-center">No artworks found</p>';
      return;
    }

    let resultsHtml = '<div class="row">';

    filteredArtworks.forEach((artwork) => {
      resultsHtml += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <img src="${artwork.image}" class="card-img-top" alt="${
        artwork.title
      }" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">${artwork.title}</h6>
                            <p class="card-text">
                                <small class="text-muted">${
                                  artwork.artist
                                }</small><br>
                                <strong>$${artwork.price.toFixed(2)}</strong>
                            </p>
                            <button type="button" class="btn btn-primary btn-sm select-artwork" data-id="${
                              artwork.id
                            }">Select</button>
                        </div>
                    </div>
                </div>
            `;
    });

    resultsHtml += "</div>";
    resultsContainer.innerHTML = resultsHtml;

    // Add event listeners to select buttons
    document.querySelectorAll(".select-artwork").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const artworkId = e.target.dataset.id;
        const artwork = this.artworks.find((a) => a.id === artworkId);

        if (artwork) {
          this.addItemToOrderForm(artwork);
          bootstrap.Modal.getInstance(
            document.getElementById("addItemModal")
          ).hide();
        }
      });
    });
  }

  /**
   * Calculate the order total based on items and other amounts
   */
  calculateOrderTotal() {
    const orderItems = document.querySelectorAll(
      "#orderItemsBody tr:not(#emptyItemRow)"
    );
    let subtotal = 0;

    orderItems.forEach((row) => {
      const priceEl = row.querySelector(".item-total");
      if (priceEl) {
        const price = parseFloat(priceEl.textContent.replace("$", "")) || 0;
        subtotal += price;
      }
    });

    const tax = parseFloat(document.getElementById("taxAmount").value) || 0;
    const shipping =
      parseFloat(document.getElementById("shippingAmount").value) || 0;
    const discount =
      parseFloat(document.getElementById("discountAmount").value) || 0;

    const total = subtotal + tax + shipping - discount;

    document.getElementById(
      "subtotalAmount"
    ).textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById("totalAmount").textContent = `$${total.toFixed(2)}`;
  }

  /**
   * Copy billing information to shipping fields
   */
  copyBillingToShipping() {
    document.getElementById("shippingName").value =
      document.getElementById("billingName").value;
    document.getElementById("shippingEmail").value =
      document.getElementById("billingEmail").value;
    document.getElementById("shippingPhone").value =
      document.getElementById("billingPhone").value;
    document.getElementById("shippingAddress").value =
      document.getElementById("billingAddress").value;
    document.getElementById("shippingCity").value =
      document.getElementById("billingCity").value;
    document.getElementById("shippingState").value =
      document.getElementById("billingState").value;
    document.getElementById("shippingPostal").value =
      document.getElementById("billingPostal").value;
    document.getElementById("shippingCountry").value =
      document.getElementById("billingCountry").value;
  }

  /**
   * Save the order (create or update)
   */
  saveOrder() {
    const orderId = document.getElementById("orderId").value;
    const customerSelect = document.getElementById("customerSelect");

    if (!customerSelect.value) {
      alert("Please select a customer");
      return;
    }

    const orderItems = document.querySelectorAll(
      "#orderItemsBody tr:not(#emptyItemRow)"
    );
    if (orderItems.length === 0) {
      alert("Please add at least one item to the order");
      return;
    }

    // Get order data from form
    const orderData = {
      id: orderId || `ORD${Math.floor(Math.random() * 90000) + 10000}`,
      date: document.getElementById("orderDate").value,
      customer: this.customers.find((c) => c.id === customerSelect.value),
      items: [],
      billing: {
        name: document.getElementById("billingName").value,
        email: document.getElementById("billingEmail").value,
        phone: document.getElementById("billingPhone").value,
        address: document.getElementById("billingAddress").value,
        city: document.getElementById("billingCity").value,
        state: document.getElementById("billingState").value,
        zip: document.getElementById("billingPostal").value,
        country: document.getElementById("billingCountry").value,
      },
      shipping: {
        name: document.getElementById("shippingName").value,
        email: document.getElementById("shippingEmail").value,
        phone: document.getElementById("shippingPhone").value,
        address: document.getElementById("shippingAddress").value,
        city: document.getElementById("shippingCity").value,
        state: document.getElementById("shippingState").value,
        zip: document.getElementById("shippingPostal").value,
        country: document.getElementById("shippingCountry").value,
        method: "Standard Shipping",
        cost: parseFloat(document.getElementById("shippingAmount").value) || 0,
      },
      paymentMethod: document.getElementById("paymentMethod").value,
      paymentStatus: document.getElementById("paymentStatus").value,
      transactionId: document.getElementById("transactionId").value,
      subtotal:
        parseFloat(
          document.getElementById("subtotalAmount").textContent.replace("$", "")
        ) || 0,
      tax: parseFloat(document.getElementById("taxAmount").value) || 0,
      discount:
        parseFloat(document.getElementById("discountAmount").value) || 0,
      notes: document.getElementById("orderNotes").value,
      status: "pending",
      timeline: [
        {
          date: new Date(),
          status: "pending",
          description: "Order created",
        },
      ],
    };

    // Get items
    orderItems.forEach((row) => {
      const itemId = row.dataset.id;
      const artwork = this.artworks.find((a) => a.id === itemId);
      const quantity = parseInt(row.querySelector(".item-quantity").value) || 1;

      if (artwork) {
        orderData.items.push({
          ...artwork,
          quantity: quantity,
        });
      }
    });

    // Calculate total
    orderData.total =
      orderData.subtotal +
      orderData.tax +
      orderData.shipping.cost -
      orderData.discount;

    // Check if creating new or updating existing
    if (orderId) {
      // Update existing order
      const orderIndex = this.orders.findIndex((o) => o.id === orderId);
      if (orderIndex >= 0) {
        this.orders[orderIndex] = orderData;
      }
    } else {
      // Add new order
      this.orders.unshift(orderData);
    }

    // Refresh the display
    this.filteredOrders = [...this.orders];
    this.updateOrderStats();
    this.renderOrdersTable();
    this.renderPagination();

    // Close the modal
    bootstrap.Modal.getInstance(
      document.getElementById("editOrderModal")
    ).hide();

    // Show success message
    alert(`Order ${orderId ? "updated" : "created"} successfully`);
  }

  /**
   * Show modal to update order status
   */
  showUpdateStatusModal(orderId) {
    const order = this.orders.find((o) => o.id === orderId);
    if (!order) return;

    const modal = new bootstrap.Modal(
      document.getElementById("updateOrderStatusModal")
    );
    document.getElementById("updateOrderId").value = orderId;
    document.getElementById("orderStatus").value = order.status;

    // Show/hide shipping fields based on status
    const shippingFields = document.querySelectorAll(".shipping-fields");
    if (order.status === "shipped") {
      shippingFields.forEach((field) => (field.style.display = "block"));
      document.getElementById("trackingNumber").value =
        order.shipping?.tracking || "";
      document.getElementById("shippingCarrier").value =
        order.shipping?.carrier || "";
    } else {
      shippingFields.forEach((field) => (field.style.display = "none"));
    }

    modal.show();
  }

  /**
   * Update the order status
   */
  updateOrderStatus() {
    const orderId = document.getElementById("updateOrderId").value;
    const order = this.orders.find((o) => o.id === orderId);
    if (!order) return;

    const newStatus = document.getElementById("orderStatus").value;
    const notes = document.getElementById("statusNotes").value;
    const trackingNumber = document.getElementById("trackingNumber").value;
    const shippingCarrier = document.getElementById("shippingCarrier").value;
    const notifyCustomer = document.getElementById("notifyCustomer").checked;

    // Update order status
    order.status = newStatus;

    // Update shipping info if provided
    if (newStatus === "shipped" && trackingNumber) {
      order.shipping.tracking = trackingNumber;
      order.shipping.carrier = shippingCarrier;
    }

    // Add timeline event
    order.timeline.unshift({
      date: new Date(),
      status: newStatus,
      description:
        notes ||
        `Order ${this.formatStatus(newStatus)}${
          notifyCustomer ? " and customer notified" : ""
        }`,
    });

    // Refresh the display
    this.renderOrdersTable();

    // Close the modal
    bootstrap.Modal.getInstance(
      document.getElementById("updateOrderStatusModal")
    ).hide();

    // Show success message
    alert(`Order status updated to ${this.formatStatus(newStatus)}`);
  }

  /**
   * Delete an order
   */
  deleteOrder(orderId) {
    if (
      !confirm(
        "Are you sure you want to delete this order? This action cannot be undone."
      )
    ) {
      return;
    }

    const orderIndex = this.orders.findIndex((o) => o.id === orderId);
    if (orderIndex >= 0) {
      this.orders.splice(orderIndex, 1);
      this.filteredOrders = this.filteredOrders.filter((o) => o.id !== orderId);

      this.updateOrderStats();
      this.renderOrdersTable();
      this.renderPagination();

      alert("Order deleted successfully");
    }
  }

  /**
   * Export orders data
   */
  exportOrders() {
    // In a real application, this would export to CSV/Excel
    alert("Orders data would be exported in a real application");
  }

  /**
   * Format date for display
   */
  formatDate(dateString) {
    const options = {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("en-US", options);
  }

  /**
   * Format date for datetime-local input
   */
  formatDateTimeForInput(date) {
    return date.toISOString().slice(0, 16);
  }

  /**
   * Format payment method for display
   */
  formatPaymentMethod(method) {
    switch (method) {
      case "credit_card":
        return "Credit Card";
      case "bank_transfer":
        return "Bank Transfer";
      case "paypal":
        return "PayPal";
      case "stripe":
        return "Stripe";
      case "site_balance":
        return "Site Balance";
      case "gift_card":
        return "Gift Card";
      default:
        return method;
    }
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
  generateMockOrders(count) {
    const orders = [];
    const customers = this.generateMockCustomers(20);
    const statuses = [
      "pending",
      "processing",
      "shipped",
      "delivered",
      "cancelled",
      "refunded",
    ];
    const paymentMethods = [
      "credit_card",
      "paypal",
      "bank_transfer",
      "site_balance",
      "gift_card",
    ];
    const paymentStatuses = ["pending", "paid", "failed", "refunded"];

    for (let i = 0; i < count; i++) {
      const orderId = `ORD${Math.floor(Math.random() * 90000) + 10000}`;
      const customer = customers[Math.floor(Math.random() * customers.length)];
      const status = statuses[Math.floor(Math.random() * statuses.length)];
      const paymentMethod =
        paymentMethods[Math.floor(Math.random() * paymentMethods.length)];
      const paymentStatus =
        paymentStatuses[Math.floor(Math.random() * paymentStatuses.length)];

      // Generate order date (within the last 90 days)
      const orderDate = new Date();
      orderDate.setDate(orderDate.getDate() - Math.floor(Math.random() * 90));

      // Generate items (1-5 random artworks)
      const itemCount = Math.floor(Math.random() * 5) + 1;
      const items = [];
      let subtotal = 0;

      for (let j = 0; j < itemCount; j++) {
        const artwork = this.generateMockArtworks(1)[0];
        items.push(artwork);
        subtotal += artwork.price;
      }

      // Generate shipping and tax
      const shipping = {
        cost: Math.floor(Math.random() * 5) * 10 + 15,
        method: "Standard Shipping",
        name: customer.firstName + " " + customer.lastName,
        address: `${Math.floor(Math.random() * 1000) + 100} ${
          ["Main", "Oak", "Maple", "Pine", "Cedar"][
            Math.floor(Math.random() * 5)
          ]
        } St`,
        city: ["New York", "Los Angeles", "Chicago", "Houston", "Phoenix"][
          Math.floor(Math.random() * 5)
        ],
        state: ["NY", "CA", "IL", "TX", "AZ"][Math.floor(Math.random() * 5)],
        zip: `${Math.floor(Math.random() * 90000) + 10000}`,
        country: "United States",
        tracking:
          status === "shipped" || status === "delivered"
            ? `TRK${Math.floor(Math.random() * 9000000) + 1000000}`
            : null,
        carrier: ["UPS", "FedEx", "USPS", "DHL"][Math.floor(Math.random() * 4)],
      };

      const tax = Math.round(subtotal * 0.08 * 100) / 100;
      const total = subtotal + shipping.cost + tax;

      // Generate timeline
      const timeline = [
        {
          date: orderDate,
          status: "pending",
          description: "Order placed",
        },
      ];

      if (status !== "pending") {
        const processingDate = new Date(orderDate);
        processingDate.setHours(
          processingDate.getHours() + Math.floor(Math.random() * 24) + 1
        );

        timeline.unshift({
          date: processingDate,
          status: "processing",
          description: "Order processing started",
        });

        if (status === "shipped" || status === "delivered") {
          const shippedDate = new Date(processingDate);
          shippedDate.setHours(
            shippedDate.getHours() + Math.floor(Math.random() * 48) + 24
          );

          timeline.unshift({
            date: shippedDate,
            status: "shipped",
            description: `Order shipped via ${shipping.carrier} (Tracking: ${shipping.tracking})`,
          });

          if (status === "delivered") {
            const deliveredDate = new Date(shippedDate);
            deliveredDate.setHours(
              deliveredDate.getHours() + Math.floor(Math.random() * 72) + 24
            );

            timeline.unshift({
              date: deliveredDate,
              status: "delivered",
              description: "Order delivered",
            });
          }
        }

        if (status === "cancelled") {
          const cancelledDate = new Date(orderDate);
          cancelledDate.setHours(
            cancelledDate.getHours() + Math.floor(Math.random() * 24) + 1
          );

          timeline.unshift({
            date: cancelledDate,
            status: "cancelled",
            description: "Order cancelled by customer",
          });
        }

        if (status === "refunded") {
          const refundDate = new Date(orderDate);
          refundDate.setHours(
            refundDate.getHours() + Math.floor(Math.random() * 72) + 24
          );

          timeline.unshift({
            date: refundDate,
            status: "refunded",
            description: "Order refunded",
          });
        }
      }

      orders.push({
        id: orderId,
        date: orderDate,
        customer: customer,
        items: items,
        subtotal: subtotal,
        shipping: shipping,
        tax: tax,
        total: total,
        status: status,
        paymentMethod: paymentMethod,
        paymentStatus: paymentStatus,
        transactionId:
          paymentStatus === "paid"
            ? `txn_${Math.random().toString(36).substring(2, 15)}`
            : null,
        timeline: timeline,
        billing: {
          name: customer.firstName + " " + customer.lastName,
          email: customer.email,
          phone: customer.phone,
          address: shipping.address,
          city: shipping.city,
          state: shipping.state,
          zip: shipping.zip,
          country: shipping.country,
        },
      });
    }

    // Sort by date (newest first)
    return orders.sort((a, b) => new Date(b.date) - new Date(a.date));
  }

  /**
   * Generate mock customers for testing
   */
  generateMockCustomers(count) {
    const firstNames = [
      "John",
      "Jane",
      "Michael",
      "Emma",
      "David",
      "Olivia",
      "James",
      "Sophia",
      "Robert",
      "Ava",
    ];
    const lastNames = [
      "Smith",
      "Johnson",
      "Williams",
      "Brown",
      "Jones",
      "Miller",
      "Davis",
      "Garcia",
      "Rodriguez",
      "Wilson",
    ];
    const domains = [
      "gmail.com",
      "yahoo.com",
      "hotmail.com",
      "outlook.com",
      "icloud.com",
    ];
    const customers = [];

    for (let i = 0; i < count; i++) {
      const firstName =
        firstNames[Math.floor(Math.random() * firstNames.length)];
      const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
      const email = `${firstName.toLowerCase()}.${lastName.toLowerCase()}@${
        domains[Math.floor(Math.random() * domains.length)]
      }`;

      customers.push({
        id: `CUST${Math.floor(Math.random() * 90000) + 10000}`,
        firstName: firstName,
        lastName: lastName,
        email: email,
        phone: `(${Math.floor(Math.random() * 900) + 100}) ${
          Math.floor(Math.random() * 900) + 100
        }-${Math.floor(Math.random() * 9000) + 1000}`,
        avatar: `https://randomuser.me/api/portraits/${
          Math.random() > 0.5 ? "men" : "women"
        }/${Math.floor(Math.random() * 99) + 1}.jpg`,
      });
    }

    return customers;
  }

  /**
   * Generate mock artworks for testing
   */
  generateMockArtworks(count) {
    const titles = [
      "Abstract Composition",
      "Mountain Landscape",
      "Sunset Over Water",
      "Portrait Study",
      "Still Life with Fruits",
      "Urban Scene",
      "Geometric Abstraction",
      "Coastal View",
      "Forest Path",
      "Figurative Expression",
      "Architectural Detail",
      "Garden Study",
    ];

    const artists = [
      "Emily Chen",
      "Marcus Williams",
      "Sophia Rodriguez",
      "David Kim",
      "Olivia Taylor",
      "James Johnson",
      "Elena Petrov",
      "Michael Lee",
      "Isabella Garcia",
      "Lucas Brown",
      "Zoe Martin",
      "Noah Wilson",
    ];

    const mediums = [
      "Oil on Canvas",
      "Acrylic on Panel",
      "Watercolor on Paper",
      "Mixed Media",
      "Digital Print",
      "Charcoal Drawing",
      "Sculpture",
      "Photography",
      "Collage",
    ];

    const artworks = [];

    for (let i = 0; i < count; i++) {
      const title = titles[Math.floor(Math.random() * titles.length)];
      const artist = artists[Math.floor(Math.random() * artists.length)];
      const medium = mediums[Math.floor(Math.random() * mediums.length)];
      const price = Math.floor(Math.random() * 10) * 500 + 500;

      artworks.push({
        id: `ART${Math.floor(Math.random() * 90000) + 10000}`,
        title: `${title} #${Math.floor(Math.random() * 100) + 1}`,
        artist: artist,
        medium: medium,
        price: price,
        image: `https://source.unsplash.com/500x500/?artwork,${
          title.toLowerCase().split(" ")[0]
        },${i}`,
      });
    }

    return artworks;
  }
}

// Initialize the orders manager when DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  window.ordersManager = new OrdersManager();
});
