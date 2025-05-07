/**
 * Admin Customers Management functionality
 */
$(document).ready(function () {
  // Load customer data and statistics
  loadCustomerStats();
  loadCustomers();

  // Handle filter form submission
  $("#customerFilterForm").on("submit", function (e) {
    e.preventDefault();
    loadCustomers(1);
  });

  // Handle reset button
  $("#customerFilterForm button[type='reset']").on("click", function () {
    // Reset form values
    $("#statusFilter").val("all");
    $("#activityFilter").val("all");
    $("#searchFilter").val("");

    // Reload customers with default filters
    loadCustomers(1);
  });

  // View Customer Details
  $(document).on("click", ".view-customer-btn", function () {
    const customerId = $(this).data("customer-id");
    loadCustomerDetails(customerId);
  });

  // Export customer data
  $("#exportBtn").on("click", function () {
    exportCustomersData();
  });

  // Add customer button click handler
  $("#addCustomerBtn").on("click", function () {
    // Reset form and set title for adding
    $("#customerForm")[0].reset();
    $("#editModalTitle").text("Add Customer");
    $("#customerId").val("");
    $("#customerStatus").val("true");

    // Show the modal
    $("#editCustomerModal").modal("show");
  });

  // Edit customer button in the details modal
  $("#editCustomerBtn").on("click", function () {
    const customerId = $(this).data("customer-id");

    // Hide details modal and show edit modal
    $("#customerDetailsModal").modal("hide");

    // Load customer data into form
    $.ajax({
      url: "/admin/getCustomerDetails",
      type: "GET",
      data: { customerID: customerId },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            const customer = data.customer;

            // Set form values
            $("#editModalTitle").text("Edit Customer");
            $("#customerId").val(customer.userID);
            $("#customerName").val(customer.Fname + " " + customer.Lname);
            $("#customerEmail").val(customer.Email);
            $("#customerPhone").val(customer.Phone || "");
            $("#customerStatus").val(
              customer.Status === "Active" ? "true" : "false"
            );
            $("#customerBio").val(customer.Bio || "");

            // Address fields if available
            if (customer.Address) {
              const address = customer.Address.split(",");
              if (address.length >= 5) {
                $("#customerStreet").val(address[0].trim());
                $("#customerCity").val(address[1].trim());
                $("#customerState").val(address[2].trim());
                $("#customerZip").val(address[3].trim());
                $("#customerCountry").val(address[4].trim());
              }
            }

            // Show edit modal
            $("#editCustomerModal").modal("show");
          } else {
            toastr.error(data.message || "Failed to load customer details.");
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          toastr.error("Error loading customer details. Please try again.");
        }
      },
      error: function (xhr) {
        toastr.error("Failed to load customer details. Please try again.");
        console.error("Ajax error:", xhr);
      },
    });
  });

  // Save customer button click handler
  $("#saveCustomerBtn").on("click", function () {
    // Validate form
    if (!$("#customerForm")[0].checkValidity()) {
      $("#customerForm")[0].reportValidity();
      return;
    }

    // Get form data
    const customerId = $("#customerId").val();
    const fullName = $("#customerName").val();
    const nameParts = fullName.split(" ");
    const firstName = nameParts[0];
    const lastName = nameParts.length > 1 ? nameParts.slice(1).join(" ") : "";

    // Build address string if any address field is filled
    let address = "";
    const street = $("#customerStreet").val().trim();
    const city = $("#customerCity").val().trim();
    const state = $("#customerState").val().trim();
    const zip = $("#customerZip").val().trim();
    const country = $("#customerCountry").val().trim();

    if (street || city || state || zip || country) {
      address = [street, city, state, zip, country].join(", ");
    }

    // Prepare data for AJAX request
    const formData = {
      customerId: customerId,
      firstName: firstName,
      lastName: lastName,
      email: $("#customerEmail").val(),
      phone: $("#customerPhone").val(),
      status: $("#customerStatus").val(),
      bio: $("#customerBio").val(),
      address: address,
    };

    // Determine if this is an add or update operation
    const isUpdate = customerId !== "";
    const url = isUpdate ? "/admin/updateCustomer" : "/admin/addCustomer";

    // Send AJAX request
    $.ajax({
      url: url,
      type: "POST",
      data: formData,
      beforeSend: function () {
        $("#saveCustomerBtn")
          .prop("disabled", true)
          .html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...'
          );
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            toastr.success(data.message);

            // Close modal and reset form
            $("#editCustomerModal").modal("hide");
            $("#customerForm")[0].reset();

            // Reload customers to reflect changes
            loadCustomers();
            loadCustomerStats();
          } else {
            toastr.error(data.message || "Failed to save customer.");
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          toastr.error("An error occurred. Please try again.");
        }

        $("#saveCustomerBtn").prop("disabled", false).text("Save Customer");
      },
      error: function (xhr) {
        toastr.error("Failed to save customer. Please try again.");
        console.error("Ajax error:", xhr);
        $("#saveCustomerBtn").prop("disabled", false).text("Save Customer");
      },
    });
  });

  // Deactivate customer button click handler
  $("#deactivateCustomerBtn").on("click", function () {
    const customerId = $(this).data("customer-id");
    const customerName = $(this).data("customer-name");

    if (
      confirm(`Are you sure you want to deactivate ${customerName}'s account?`)
    ) {
      $.ajax({
        url: "/admin/updateCustomerStatus",
        type: "POST",
        data: {
          customerID: customerId,
          status: "Inactive",
        },
        success: function (response) {
          try {
            const data =
              typeof response === "string" ? JSON.parse(response) : response;

            if (data.success) {
              toastr.success(data.message);

              // Close modal and reload data
              $("#customerDetailsModal").modal("hide");
              loadCustomers();
              loadCustomerStats();
            } else {
              toastr.error(data.message || "Failed to deactivate customer.");
            }
          } catch (error) {
            console.error("Error parsing response:", error);
            toastr.error("An error occurred. Please try again.");
          }
        },
        error: function (xhr) {
          toastr.error("Failed to deactivate customer. Please try again.");
          console.error("Ajax error:", xhr);
        },
      });
    }
  });

  // Function to load customer dashboard statistics
  function loadCustomerStats() {
    $.ajax({
      url: "/admin/getCustomerStats",
      type: "GET",
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            // Update statistics counts
            $("#totalCustomersCount").text(data.stats.totalCustomers || 0);
            $("#activeCustomersCount").text(data.stats.activeCustomers || 0);
            $("#inactiveCustomersCount").text(
              data.stats.inactiveCustomers || 0
            );
            $("#pendingOrdersCount").text(data.stats.pendingOrders || 0);
          } else {
            console.error("Failed to load customer statistics:", data.message);
          }
        } catch (error) {
          console.error("Error parsing response:", error);
        }
      },
      error: function (xhr) {
        console.error("Ajax error:", xhr);
      },
    });
  }

  // Function to load customers list with pagination
  function loadCustomers(page = 1) {
    // Get filter values
    const status = $("#statusFilter").val();
    const activity = $("#activityFilter").val();
    const search = $("#searchFilter").val();

    $.ajax({
      url: "/admin/getCustomers",
      type: "GET",
      data: {
        page: page,
        status: status,
        activity: activity,
        search: search,
      },
      beforeSend: function () {
        $("#customersTableBody").html(
          '<tr><td colspan="8" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>'
        );
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            renderCustomersTable(data.customers);
            renderPagination(data.pagination);
          } else {
            $("#customersTableBody").html(
              `<tr><td colspan="8" class="text-center">${
                data.message || "No customers found."
              }</td></tr>`
            );
            $("#customerPagination").html("");
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          $("#customersTableBody").html(
            '<tr><td colspan="8" class="text-center">Error loading customers. Please try again.</td></tr>'
          );
          $("#customerPagination").html("");
        }
      },
      error: function (xhr) {
        $("#customersTableBody").html(
          '<tr><td colspan="8" class="text-center">Failed to load customers. Please try again.</td></tr>'
        );
        $("#customerPagination").html("");
        console.error("Ajax error:", xhr);
      },
    });
  }

  // Function to render customers table
  function renderCustomersTable(customers) {
    if (!customers || customers.length === 0) {
      $("#customersTableBody").html(
        '<tr><td colspan="8" class="text-center">No customers found.</td></tr>'
      );
      return;
    }

    let html = "";

    customers.forEach((customer) => {
      // Determine activity level icon class
      let activityClass = "low";
      if (customer.activityLevel === "High") {
        activityClass = "high";
      } else if (customer.activityLevel === "Medium") {
        activityClass = "medium";
      }

      // Determine status badge class
      let statusClass = customer.status === "Active" ? "active" : "inactive";

      html += `
        <tr>
          <td>
            <div class="d-flex align-items-center">
              <img src="${
                customer.profilePic
                  ? "../../uploads/profiles/" + customer.profilePic
                  : "../../uploads/profiles/default.jpg"
              }" 
                   alt="${customer.name}" class="customer-avatar me-3">
              <div>
                <h6 class="mb-0">${customer.name}</h6>
                <small class="text-muted">#${customer.userID}</small>
              </div>
            </div>
          </td>
          <td>${customer.email}</td>
          <td>${customer.joinDate}</td>
          <td>${customer.totalOrders}</td>
          <td>$${parseFloat(customer.totalSpending || 0).toFixed(2)}</td>
          <td><span class="customer-activity ${activityClass}"></span> ${
        customer.activityLevel
      }</td>
          <td><span class="status-badge ${statusClass}">${
        customer.status
      }</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn btn-sm btn-outline-primary view-customer-btn" data-customer-id="${
                customer.userID
              }">
                <i class="fas fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-outline-warning toggle-status-btn" data-customer-id="${
                customer.userID
              }" 
                      data-status="${
                        customer.status === "Active" ? "Inactive" : "Active"
                      }">
                <i class="fas fa-${
                  customer.status === "Active" ? "ban" : "check"
                }"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    });

    $("#customersTableBody").html(html);

    // Add event listeners for toggle status buttons
    $(".toggle-status-btn").on("click", function () {
      const customerId = $(this).data("customer-id");
      const newStatus = $(this).data("status");

      updateCustomerStatus(customerId, newStatus);
    });
  }

  // Function to render pagination
  function renderPagination(pagination) {
    if (!pagination) {
      $("#customerPagination").html("");
      return;
    }

    let html = "";

    // Previous button
    html += `
      <li class="page-item ${pagination.currentPage === 1 ? "disabled" : ""}">
        <a class="page-link" href="#" data-page="${
          pagination.currentPage - 1
        }" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
    `;

    // Page numbers
    for (let i = 1; i <= pagination.totalPages; i++) {
      html += `
        <li class="page-item ${i === pagination.currentPage ? "active" : ""}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>
      `;
    }

    // Next button
    html += `
      <li class="page-item ${
        pagination.currentPage === pagination.totalPages ? "disabled" : ""
      }">
        <a class="page-link" href="#" data-page="${
          pagination.currentPage + 1
        }" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    `;

    $("#customerPagination").html(html);

    // Add event listeners for pagination links
    $(".page-link").on("click", function (e) {
      e.preventDefault();
      const page = $(this).data("page");
      loadCustomers(page);
    });
  }

  // Function to load customer details
  function loadCustomerDetails(customerId) {
    $.ajax({
      url: "/admin/getCustomerDetails",
      type: "GET",
      data: { customerID: customerId },
      beforeSend: function () {
        $("#customerDetailsContent").html(
          '<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );
        $("#customerDetailsModal").modal("show");
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            const customer = data.customer;

            // Store customer ID and name for edit and deactivate buttons
            $("#editCustomerBtn").data("customer-id", customer.userID);
            $("#deactivateCustomerBtn").data("customer-id", customer.userID);
            $("#deactivateCustomerBtn").data(
              "customer-name",
              customer.Fname + " " + customer.Lname
            );

            // Determine status class
            let statusClass =
              customer.Status === "Active" ? "text-success" : "text-danger";

            // Build the HTML content
            const detailsHtml = `
              <div class="customer-details">
                <div class="row mb-4">
                  <div class="col-lg-3 text-center">
                    <img src="${
                      customer.ProfilePic
                        ? "../../uploads/profiles/" + customer.ProfilePic
                        : "../../uploads/profiles/default.jpg"
                    }" 
                         class="img-fluid rounded-circle customer-profile-img mb-3" alt="${
                           customer.Fname
                         } ${customer.Lname}">
                    <h4 class="customer-name">${customer.Fname} ${
              customer.Lname
            }</h4>
                    <p class="status ${statusClass}">${customer.Status}</p>
                  </div>
                  <div class="col-lg-9">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="fw-bold">Email:</label>
                          <p>${customer.Email}</p>
                        </div>
                        <div class="mb-3">
                          <label class="fw-bold">Phone:</label>
                          <p>${customer.Phone || "Not provided"}</p>
                        </div>
                        <div class="mb-3">
                          <label class="fw-bold">Address:</label>
                          <p>${customer.Address || "Not provided"}</p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="mb-3">
                          <label class="fw-bold">Joined:</label>
                          <p>${customer.registerDate}</p>
                        </div>
                        <div class="mb-3">
                          <label class="fw-bold">Total Orders:</label>
                          <p>${customer.totalOrders || 0}</p>
                        </div>
                        <div class="mb-3">
                          <label class="fw-bold">Total Spent:</label>
                          <p>$${parseFloat(customer.totalSpent || 0).toFixed(
                            2
                          )}</p>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label class="fw-bold">Bio:</label>
                      <p>${customer.Bio || "No bio provided"}</p>
                    </div>
                  </div>
                </div>
                
                <hr>
                
                <div class="recent-orders mt-4">
                  <h5 class="mb-3">Recent Orders</h5>
                  ${renderRecentOrders(customer.recentOrders)}
                </div>
              </div>`;

            $("#customerDetailsContent").html(detailsHtml);
          } else {
            $("#customerDetailsContent").html(
              `<div class="alert alert-danger">${
                data.message || "Failed to load customer details."
              }</div>`
            );
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          $("#customerDetailsContent").html(
            '<div class="alert alert-danger">Error loading customer details. Please try again.</div>'
          );
        }
      },
      error: function (xhr) {
        $("#customerDetailsContent").html(
          '<div class="alert alert-danger">Failed to load customer details. Please try again.</div>'
        );
        console.error("Ajax error:", xhr);
      },
    });
  }

  // Function to render recent orders in customer details
  function renderRecentOrders(orders) {
    if (!orders || orders.length === 0) {
      return "<p><em>No orders available</em></p>";
    }

    let html = `
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Date</th>
              <th>Items</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
    `;

    orders.forEach((order) => {
      let statusClass = "";
      switch (order.status) {
        case "Completed":
          statusClass = "text-success";
          break;
        case "Processing":
          statusClass = "text-warning";
          break;
        case "Cancelled":
          statusClass = "text-danger";
          break;
        default:
          statusClass = "text-secondary";
      }

      html += `
        <tr>
          <td>#${order.orderID}</td>
          <td>${order.orderDate}</td>
          <td>${order.itemCount}</td>
          <td>$${parseFloat(order.totalPrice).toFixed(2)}</td>
          <td><span class="${statusClass}">${order.status}</span></td>
        </tr>
      `;
    });

    html += `
          </tbody>
        </table>
      </div>
    `;

    return html;
  }

  // Function to update customer status
  function updateCustomerStatus(customerId, status) {
    $.ajax({
      url: "/admin/updateCustomerStatus",
      type: "POST",
      data: {
        customerID: customerId,
        status: status,
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success) {
            toastr.success(data.message);
            loadCustomers();
            loadCustomerStats();
          } else {
            toastr.error(data.message || "Failed to update customer status.");
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          toastr.error("An error occurred. Please try again.");
        }
      },
      error: function (xhr) {
        toastr.error("Failed to update customer status. Please try again.");
        console.error("Ajax error:", xhr);
      },
    });
  }

  // Function to export customers data
  function exportCustomersData() {
    // Get filter values
    const status = $("#statusFilter").val();
    const activity = $("#activityFilter").val();
    const search = $("#searchFilter").val();

    $.ajax({
      url: "/admin/exportCustomers",
      type: "GET",
      data: {
        status: status,
        activity: activity,
        search: search,
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse(response) : response;

          if (data.success && data.customers) {
            // Create CSV content
            let csvContent = "data:text/csv;charset=utf-8,";

            // Add header row
            const headers = [
              "ID",
              "Name",
              "Email",
              "Phone",
              "Address",
              "Joined Date",
              "Orders",
              "Total Spent",
              "Status",
            ];
            csvContent += headers.join(",") + "\r\n";

            // Add data rows
            data.customers.forEach((customer) => {
              const row = [
                customer.userID,
                `"${customer.name}"`,
                `"${customer.email}"`,
                `"${customer.phone || ""}"`,
                `"${customer.address || ""}"`,
                customer.joinDate,
                customer.totalOrders || 0,
                parseFloat(customer.totalSpending || 0).toFixed(2),
                customer.status,
              ];
              csvContent += row.join(",") + "\r\n";
            });

            // Create download link
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute(
              "download",
              "customers_data_" + new Date().toISOString().slice(0, 10) + ".csv"
            );
            document.body.appendChild(link);

            // Trigger download
            link.click();

            toastr.success("Export completed successfully");
          } else {
            toastr.error(data.message || "Failed to export customers data.");
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          toastr.error("An error occurred during export. Please try again.");
        }
      },
      error: function (xhr) {
        toastr.error("Failed to export customers data. Please try again.");
        console.error("Ajax error:", xhr);
      },
    });
  }

  // Configure toastr options
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 3000,
  };
});
