/**
 * ArtShelf Admin - Withdrawals Management
 *
 * This module handles the functionality for the admin withdrawals page,
 * including listing, filtering, and processing of artist withdrawal requests.
 */

document.addEventListener("DOMContentLoaded", function () {
  // Initialize the withdrawals management
  const WithdrawalsManager = {
    // Properties
    currentPage: 1,
    itemsPerPage: 10,
    totalPages: 1,
    withdrawals: [],
    filteredWithdrawals: [],
    selectedWithdrawals: new Set(),
    currentWithdrawalId: null,
    artists: [],

    // Initialize the module
    init: function () {
      this.loadData();
      this.setupEventListeners();
      this.setupFilterHandlers();
    },

    // Load initial data
    loadData: function () {
      // Show loading state
      this.setLoading(true);

      // Mock data for development - in production, fetch from API
      this.fetchWithdrawals()
        .then(() => {
          this.fetchArtists();
          this.updateStats();
          this.updateTable();
          this.populateArtistFilter();
          this.setLoading(false);
        })
        .catch((error) => {
          console.error("Error loading withdrawal data:", error);
          this.showNotification(
            "Error loading data. Please try again.",
            "error"
          );
          this.setLoading(false);
        });
    },

    // Setup event listeners
    setupEventListeners: function () {
      // Sidebar toggle for mobile
      document
        .querySelector(".toggle-sidebar")
        ?.addEventListener("click", () => {
          document.querySelector(".admin-sidebar").classList.toggle("show");
        });

      // Select all withdrawals
      document
        .getElementById("selectAllWithdrawals")
        .addEventListener("change", (e) => {
          const checkboxes = document.querySelectorAll(
            '#withdrawalsTableBody input[type="checkbox"]'
          );
          checkboxes.forEach((checkbox) => {
            checkbox.checked = e.target.checked;
            const withdrawalId = checkbox.getAttribute("data-id");
            if (e.target.checked) {
              this.selectedWithdrawals.add(withdrawalId);
            } else {
              this.selectedWithdrawals.delete(withdrawalId);
            }
          });
          this.updateBatchButton();
        });

      // Batch process button
      document
        .getElementById("batchProcessBtn")
        .addEventListener("click", () => {
          if (this.selectedWithdrawals.size === 0) {
            this.showNotification(
              "Please select at least one withdrawal to process",
              "warning"
            );
            return;
          }
          this.openBatchProcessModal();
        });

      // Export withdrawals button
      document
        .getElementById("exportWithdrawalsBtn")
        .addEventListener("click", () => {
          this.exportWithdrawals();
        });

      // Confirm batch process
      document
        .getElementById("confirmBatchBtn")
        .addEventListener("click", () => {
          this.processBatch();
        });

      // Update withdrawal status
      document.getElementById("saveStatusBtn").addEventListener("click", () => {
        this.updateWithdrawalStatus();
      });

      // Modal action buttons
      document
        .getElementById("approveWithdrawalBtn")
        .addEventListener("click", () => {
          this.presetStatusUpdate("processing");
        });

      document
        .getElementById("rejectWithdrawalBtn")
        .addEventListener("click", () => {
          this.presetStatusUpdate("rejected");
        });

      document
        .getElementById("markCompletedBtn")
        .addEventListener("click", () => {
          this.presetStatusUpdate("completed");
        });

      // Date range filter change
      document
        .getElementById("dateRangeFilter")
        .addEventListener("change", (e) => {
          const customRangeDiv = document.querySelector(".custom-date-range");
          if (e.target.value === "custom") {
            customRangeDiv.style.display = "flex";
          } else {
            customRangeDiv.style.display = "none";
          }
        });
    },

    // Setup filter form handlers
    setupFilterHandlers: function () {
      const filterForm = document.getElementById("withdrawalFilterForm");

      filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        this.applyFilters();
      });

      filterForm.addEventListener("reset", () => {
        setTimeout(() => {
          this.applyFilters();
          document.querySelector(".custom-date-range").style.display = "none";
        }, 0);
      });
    },

    // Fetch withdrawals data
    fetchWithdrawals: function () {
      // In production, replace with actual API call
      return new Promise((resolve) => {
        setTimeout(() => {
          // Mock data
          this.withdrawals = [
            {
              id: "WD-20250415-001",
              date: "2025-04-15T09:30:00",
              artistId: "ART-001",
              artistName: "Jane Cooper",
              artistEmail: "jane.cooper@example.com",
              artistImage: "https://randomuser.me/api/portraits/women/12.jpg",
              amount: 1250.0,
              currency: "USD",
              paymentMethod: "bank_transfer",
              paymentDetails: {
                bankName: "Chase Bank",
                accountName: "Jane Cooper",
                accountNumber: "********1234",
                routingNumber: "********5678",
                swift: "CHASUS33",
              },
              status: "pending",
              createdAt: "2025-04-15T09:30:00",
              updatedAt: "2025-04-15T09:30:00",
              notes: "",
              history: [
                {
                  date: "2025-04-15T09:30:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
              ],
            },
            {
              id: "WD-20250414-002",
              date: "2025-04-14T14:22:00",
              artistId: "ART-002",
              artistName: "Robert Johnson",
              artistEmail: "robert.johnson@example.com",
              artistImage: "https://randomuser.me/api/portraits/men/32.jpg",
              amount: 875.5,
              currency: "USD",
              paymentMethod: "paypal",
              paymentDetails: {
                email: "robert.johnson@example.com",
              },
              status: "processing",
              createdAt: "2025-04-14T14:22:00",
              updatedAt: "2025-04-15T10:15:00",
              notes: "Processing via PayPal",
              history: [
                {
                  date: "2025-04-14T14:22:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-15T10:15:00",
                  status: "processing",
                  notes: "Processing via PayPal",
                  by: "admin",
                },
              ],
            },
            {
              id: "WD-20250413-003",
              date: "2025-04-13T11:05:00",
              artistId: "ART-003",
              artistName: "Maria Garcia",
              artistEmail: "maria.garcia@example.com",
              artistImage: "https://randomuser.me/api/portraits/women/45.jpg",
              amount: 2380.25,
              currency: "USD",
              paymentMethod: "bank_transfer",
              paymentDetails: {
                bankName: "Bank of America",
                accountName: "Maria Garcia",
                accountNumber: "********7890",
                routingNumber: "********4567",
                swift: "BOFAUS3N",
              },
              status: "completed",
              transactionReference: "TRF-85214796",
              createdAt: "2025-04-13T11:05:00",
              updatedAt: "2025-04-14T16:40:00",
              notes: "Funds transferred successfully",
              history: [
                {
                  date: "2025-04-13T11:05:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-13T15:30:00",
                  status: "processing",
                  notes: "Processing bank transfer",
                  by: "admin",
                },
                {
                  date: "2025-04-14T16:40:00",
                  status: "completed",
                  notes: "Funds transferred successfully",
                  by: "admin",
                  transactionReference: "TRF-85214796",
                },
              ],
            },
            {
              id: "WD-20250412-004",
              date: "2025-04-12T09:18:00",
              artistId: "ART-004",
              artistName: "David Lee",
              artistEmail: "david.lee@example.com",
              artistImage: "https://randomuser.me/api/portraits/men/67.jpg",
              amount: 1500.0,
              currency: "USD",
              paymentMethod: "stripe",
              paymentDetails: {
                accountId: "acct_************1234",
              },
              status: "rejected",
              createdAt: "2025-04-12T09:18:00",
              updatedAt: "2025-04-13T13:45:00",
              notes: "Insufficient account verification documents",
              history: [
                {
                  date: "2025-04-12T09:18:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-13T13:45:00",
                  status: "rejected",
                  notes: "Insufficient account verification documents",
                  by: "admin",
                },
              ],
            },
            {
              id: "WD-20250410-005",
              date: "2025-04-10T16:30:00",
              artistId: "ART-005",
              artistName: "Sophia Chen",
              artistEmail: "sophia.chen@example.com",
              artistImage: "https://randomuser.me/api/portraits/women/79.jpg",
              amount: 950.75,
              currency: "USD",
              paymentMethod: "crypto",
              paymentDetails: {
                currency: "ETH",
                address: "0x89205A3A3b2A69De6Dbf7f01ED13B2108B2c43e7",
              },
              status: "completed",
              transactionReference: "ETH-TX-0x1234abcd",
              createdAt: "2025-04-10T16:30:00",
              updatedAt: "2025-04-11T14:10:00",
              notes: "Crypto transferred successfully",
              history: [
                {
                  date: "2025-04-10T16:30:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-11T10:20:00",
                  status: "processing",
                  notes: "Processing crypto transfer",
                  by: "admin",
                },
                {
                  date: "2025-04-11T14:10:00",
                  status: "completed",
                  notes: "Crypto transferred successfully",
                  by: "admin",
                  transactionReference: "ETH-TX-0x1234abcd",
                },
              ],
            },
            {
              id: "WD-20250409-006",
              date: "2025-04-09T11:45:00",
              artistId: "ART-006",
              artistName: "Michael Brown",
              artistEmail: "michael.brown@example.com",
              artistImage: "https://randomuser.me/api/portraits/men/22.jpg",
              amount: 3200.0,
              currency: "USD",
              paymentMethod: "check",
              paymentDetails: {
                name: "Michael Brown",
                address: "123 Main St, New York, NY 10001",
              },
              status: "processing",
              createdAt: "2025-04-09T11:45:00",
              updatedAt: "2025-04-10T09:30:00",
              notes: "Check being processed",
              history: [
                {
                  date: "2025-04-09T11:45:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-10T09:30:00",
                  status: "processing",
                  notes: "Check being processed",
                  by: "admin",
                },
              ],
            },
            {
              id: "WD-20250408-007",
              date: "2025-04-08T15:20:00",
              artistId: "ART-001",
              artistName: "Jane Cooper",
              artistEmail: "jane.cooper@example.com",
              artistImage: "https://randomuser.me/api/portraits/women/12.jpg",
              amount: 750.5,
              currency: "USD",
              paymentMethod: "bank_transfer",
              paymentDetails: {
                bankName: "Chase Bank",
                accountName: "Jane Cooper",
                accountNumber: "********1234",
                routingNumber: "********5678",
                swift: "CHASUS33",
              },
              status: "completed",
              transactionReference: "TRF-48592367",
              createdAt: "2025-04-08T15:20:00",
              updatedAt: "2025-04-09T17:45:00",
              notes: "Funds transferred successfully",
              history: [
                {
                  date: "2025-04-08T15:20:00",
                  status: "pending",
                  notes: "Withdrawal request submitted",
                  by: "system",
                },
                {
                  date: "2025-04-09T10:15:00",
                  status: "processing",
                  notes: "Processing bank transfer",
                  by: "admin",
                },
                {
                  date: "2025-04-09T17:45:00",
                  status: "completed",
                  notes: "Funds transferred successfully",
                  by: "admin",
                  transactionReference: "TRF-48592367",
                },
              ],
            },
          ];

          this.filteredWithdrawals = [...this.withdrawals];
          this.totalPages = Math.ceil(
            this.filteredWithdrawals.length / this.itemsPerPage
          );
          resolve();
        }, 500);
      });
    },

    // Fetch artists data
    fetchArtists: function () {
      // In production, replace with actual API call
      // Currently extracting unique artists from withdrawals
      const uniqueArtists = [
        ...new Map(
          this.withdrawals.map((w) => [
            w.artistId,
            { id: w.artistId, name: w.artistName, image: w.artistImage },
          ])
        ).values(),
      ];

      this.artists = uniqueArtists;
    },

    // Update the table with filtered withdrawals
    updateTable: function () {
      const tableBody = document.getElementById("withdrawalsTableBody");
      tableBody.innerHTML = "";

      const startIndex = (this.currentPage - 1) * this.itemsPerPage;
      const endIndex = Math.min(
        startIndex + this.itemsPerPage,
        this.filteredWithdrawals.length
      );

      if (this.filteredWithdrawals.length === 0) {
        tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-search me-2"></i>
                            No withdrawal requests match your filters.
                        </td>
                    </tr>
                `;
        return;
      }

      for (let i = startIndex; i < endIndex; i++) {
        const withdrawal = this.filteredWithdrawals[i];
        const row = document.createElement("tr");

        // Format date
        const date = new Date(withdrawal.date);
        const formattedDate = date.toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });

        // Format payment method
        let paymentMethodDisplay;
        switch (withdrawal.paymentMethod) {
          case "bank_transfer":
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fas fa-university"></i> Bank Transfer</span>';
            break;
          case "paypal":
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fab fa-paypal"></i> PayPal</span>';
            break;
          case "stripe":
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fab fa-stripe-s"></i> Stripe</span>';
            break;
          case "crypto":
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fab fa-ethereum"></i> Crypto</span>';
            break;
          case "check":
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fas fa-money-check"></i> Check</span>';
            break;
          default:
            paymentMethodDisplay =
              '<span class="payment-method-badge"><i class="fas fa-money-bill-wave"></i> Other</span>';
        }

        // Format status
        let statusDisplay;
        switch (withdrawal.status) {
          case "pending":
            statusDisplay = '<span class="status-badge pending">Pending</span>';
            break;
          case "processing":
            statusDisplay =
              '<span class="status-badge processing">Processing</span>';
            break;
          case "completed":
            statusDisplay =
              '<span class="status-badge completed">Completed</span>';
            break;
          case "rejected":
            statusDisplay =
              '<span class="status-badge rejected">Rejected</span>';
            break;
          default:
            statusDisplay = '<span class="status-badge">Unknown</span>';
        }

        row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input class="form-check-input withdrawal-checkbox" type="checkbox" 
                                data-id="${withdrawal.id}" 
                                ${
                                  this.selectedWithdrawals.has(withdrawal.id)
                                    ? "checked"
                                    : ""
                                }>
                        </div>
                    </td>
                    <td>${withdrawal.id}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="${withdrawal.artistImage}" alt="${
          withdrawal.artistName
        }" 
                                class="rounded-circle me-2" width="30" height="30">
                            <span>${withdrawal.artistName}</span>
                        </div>
                    </td>
                    <td>$${withdrawal.amount.toFixed(2)}</td>
                    <td>${paymentMethodDisplay}</td>
                    <td>${statusDisplay}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-primary view-withdrawal-btn" 
                                data-id="${withdrawal.id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary update-status-btn" 
                                data-id="${withdrawal.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                `;

        tableBody.appendChild(row);
      }

      // Add event listeners for view and update buttons
      document.querySelectorAll(".view-withdrawal-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          const withdrawalId = e.currentTarget.getAttribute("data-id");
          this.viewWithdrawalDetails(withdrawalId);
        });
      });

      document.querySelectorAll(".update-status-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          const withdrawalId = e.currentTarget.getAttribute("data-id");
          this.openUpdateStatusModal(withdrawalId);
        });
      });

      document.querySelectorAll(".withdrawal-checkbox").forEach((checkbox) => {
        checkbox.addEventListener("change", (e) => {
          const withdrawalId = e.target.getAttribute("data-id");
          if (e.target.checked) {
            this.selectedWithdrawals.add(withdrawalId);
          } else {
            this.selectedWithdrawals.delete(withdrawalId);
          }

          // Update "Select All" checkbox state
          const selectAllCheckbox = document.getElementById(
            "selectAllWithdrawals"
          );
          const checkboxes = document.querySelectorAll(".withdrawal-checkbox");
          const allChecked = [...checkboxes].every((cb) => cb.checked);
          selectAllCheckbox.checked = allChecked;

          this.updateBatchButton();
        });
      });

      this.updatePagination();
    },

    // Update the pagination controls
    updatePagination: function () {
      const paginationEl = document.getElementById("withdrawalPagination");
      paginationEl.innerHTML = "";

      if (this.totalPages <= 1) {
        return;
      }

      // Previous button
      const prevLi = document.createElement("li");
      prevLi.className = `page-item ${
        this.currentPage === 1 ? "disabled" : ""
      }`;
      prevLi.innerHTML = `
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            `;
      prevLi.addEventListener("click", (e) => {
        e.preventDefault();
        if (this.currentPage > 1) {
          this.goToPage(this.currentPage - 1);
        }
      });
      paginationEl.appendChild(prevLi);

      // Page numbers
      const maxPages = 5;
      let startPage = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
      let endPage = Math.min(this.totalPages, startPage + maxPages - 1);

      if (endPage - startPage + 1 < maxPages && startPage > 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
      }

      for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement("li");
        pageLi.className = `page-item ${
          i === this.currentPage ? "active" : ""
        }`;
        pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        pageLi.addEventListener("click", (e) => {
          e.preventDefault();
          this.goToPage(i);
        });
        paginationEl.appendChild(pageLi);
      }

      // Next button
      const nextLi = document.createElement("li");
      nextLi.className = `page-item ${
        this.currentPage === this.totalPages ? "disabled" : ""
      }`;
      nextLi.innerHTML = `
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            `;
      nextLi.addEventListener("click", (e) => {
        e.preventDefault();
        if (this.currentPage < this.totalPages) {
          this.goToPage(this.currentPage + 1);
        }
      });
      paginationEl.appendChild(nextLi);
    },

    // Go to a specific page
    goToPage: function (page) {
      this.currentPage = page;
      this.updateTable();
      window.scrollTo({ top: 0, behavior: "smooth" });
    },

    // Apply filters from the form
    applyFilters: function () {
      const statusFilter = document.getElementById("statusFilter").value;
      const dateRangeFilter = document.getElementById("dateRangeFilter").value;
      const artistFilter = document.getElementById("artistFilter").value;
      const paymentMethodFilter = document.getElementById(
        "paymentMethodFilter"
      ).value;
      const amountMinFilter = document.getElementById("amountMinFilter").value;
      const amountMaxFilter = document.getElementById("amountMaxFilter").value;
      const searchFilter = document
        .getElementById("searchFilter")
        .value.toLowerCase();
      const startDateFilter = document.getElementById("startDateFilter").value;
      const endDateFilter = document.getElementById("endDateFilter").value;

      this.filteredWithdrawals = this.withdrawals.filter((withdrawal) => {
        // Status filter
        if (statusFilter !== "all" && withdrawal.status !== statusFilter) {
          return false;
        }

        // Payment method filter
        if (
          paymentMethodFilter !== "all" &&
          withdrawal.paymentMethod !== paymentMethodFilter
        ) {
          return false;
        }

        // Artist filter
        if (artistFilter !== "all" && withdrawal.artistId !== artistFilter) {
          return false;
        }

        // Amount min filter
        if (
          amountMinFilter &&
          withdrawal.amount < parseFloat(amountMinFilter)
        ) {
          return false;
        }

        // Amount max filter
        if (
          amountMaxFilter &&
          withdrawal.amount > parseFloat(amountMaxFilter)
        ) {
          return false;
        }

        // Search filter
        if (searchFilter) {
          const searchString = `${withdrawal.id} ${withdrawal.artistName} ${
            withdrawal.transactionReference || ""
          }`.toLowerCase();
          if (!searchString.includes(searchFilter)) {
            return false;
          }
        }

        // Date filters
        const withdrawalDate = new Date(withdrawal.date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        const last7Days = new Date(today);
        last7Days.setDate(last7Days.getDate() - 7);
        const last30Days = new Date(today);
        last30Days.setDate(last30Days.getDate() - 30);

        const thisMonthStart = new Date(
          today.getFullYear(),
          today.getMonth(),
          1
        );
        const lastMonthStart = new Date(
          today.getFullYear(),
          today.getMonth() - 1,
          1
        );
        const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);

        switch (dateRangeFilter) {
          case "today":
            if (withdrawalDate < today) return false;
            break;
          case "yesterday":
            if (withdrawalDate < yesterday || withdrawalDate >= today)
              return false;
            break;
          case "last7days":
            if (withdrawalDate < last7Days) return false;
            break;
          case "last30days":
            if (withdrawalDate < last30Days) return false;
            break;
          case "thisMonth":
            if (withdrawalDate < thisMonthStart) return false;
            break;
          case "lastMonth":
            if (
              withdrawalDate < lastMonthStart ||
              withdrawalDate > lastMonthEnd
            )
              return false;
            break;
          case "custom":
            if (startDateFilter) {
              const startDate = new Date(startDateFilter);
              startDate.setHours(0, 0, 0, 0);
              if (withdrawalDate < startDate) return false;
            }
            if (endDateFilter) {
              const endDate = new Date(endDateFilter);
              endDate.setHours(23, 59, 59, 999);
              if (withdrawalDate > endDate) return false;
            }
            break;
          default:
            // All time, no filtering
            break;
        }

        return true;
      });

      this.totalPages = Math.ceil(
        this.filteredWithdrawals.length / this.itemsPerPage
      );
      this.currentPage = 1;
      this.updateTable();
      this.updateStats();
    },

    // Update stats based on filtered withdrawals
    updateStats: function () {
      const totalCount = this.withdrawals.length;
      const pendingCount = this.withdrawals.filter(
        (w) => w.status === "pending"
      ).length;
      const completedCount = this.withdrawals.filter(
        (w) => w.status === "completed"
      ).length;

      // Calculate total amount for the last 30 days
      const last30Days = new Date();
      last30Days.setDate(last30Days.getDate() - 30);
      const totalAmount = this.withdrawals
        .filter(
          (w) => new Date(w.date) >= last30Days && w.status === "completed"
        )
        .reduce((sum, w) => sum + w.amount, 0);

      document.getElementById("totalWithdrawalsCount").textContent = totalCount;
      document.getElementById("pendingWithdrawalsCount").textContent =
        pendingCount;
      document.getElementById("completedWithdrawalsCount").textContent =
        completedCount;
      document.getElementById(
        "totalAmountWithdrawn"
      ).textContent = `$${totalAmount.toFixed(2)}`;
    },

    // Populate artist filter dropdown
    populateArtistFilter: function () {
      const artistFilter = document.getElementById("artistFilter");

      // Clear previous options except "All Artists"
      while (artistFilter.options.length > 1) {
        artistFilter.remove(1);
      }

      // Add artist options
      this.artists.forEach((artist) => {
        const option = document.createElement("option");
        option.value = artist.id;
        option.textContent = artist.name;
        artistFilter.appendChild(option);
      });
    },

    // View withdrawal details
    viewWithdrawalDetails: function (withdrawalId) {
      const withdrawal = this.withdrawals.find((w) => w.id === withdrawalId);
      if (!withdrawal) return;

      this.currentWithdrawalId = withdrawalId;

      const detailsContent = document.getElementById(
        "withdrawalDetailsContent"
      );
      const approveBtn = document.getElementById("approveWithdrawalBtn");
      const rejectBtn = document.getElementById("rejectWithdrawalBtn");
      const completeBtn = document.getElementById("markCompletedBtn");

      // Enable/disable action buttons based on current status
      if (withdrawal.status === "pending") {
        approveBtn.disabled = false;
        rejectBtn.disabled = false;
        completeBtn.disabled = true;
      } else if (withdrawal.status === "processing") {
        approveBtn.disabled = true;
        rejectBtn.disabled = false;
        completeBtn.disabled = false;
      } else {
        approveBtn.disabled = true;
        rejectBtn.disabled = true;
        completeBtn.disabled = true;
      }

      // Format date
      const date = new Date(withdrawal.date);
      const formattedDate = date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });

      // Format payment method
      let paymentMethodDisplay;
      switch (withdrawal.paymentMethod) {
        case "bank_transfer":
          paymentMethodDisplay =
            '<i class="fas fa-university me-2"></i> Bank Transfer';
          break;
        case "paypal":
          paymentMethodDisplay = '<i class="fab fa-paypal me-2"></i> PayPal';
          break;
        case "stripe":
          paymentMethodDisplay = '<i class="fab fa-stripe-s me-2"></i> Stripe';
          break;
        case "crypto":
          paymentMethodDisplay =
            '<i class="fab fa-ethereum me-2"></i> Cryptocurrency';
          break;
        case "check":
          paymentMethodDisplay =
            '<i class="fas fa-money-check me-2"></i> Check';
          break;
        default:
          paymentMethodDisplay =
            '<i class="fas fa-money-bill-wave me-2"></i> Other';
      }

      // Format status
      let statusDisplay;
      switch (withdrawal.status) {
        case "pending":
          statusDisplay = '<span class="status-badge pending">Pending</span>';
          break;
        case "processing":
          statusDisplay =
            '<span class="status-badge processing">Processing</span>';
          break;
        case "completed":
          statusDisplay =
            '<span class="status-badge completed">Completed</span>';
          break;
        case "rejected":
          statusDisplay = '<span class="status-badge rejected">Rejected</span>';
          break;
        default:
          statusDisplay = '<span class="status-badge">Unknown</span>';
      }

      // Generate payment details HTML
      let paymentDetailsHtml = "";
      if (withdrawal.paymentMethod === "bank_transfer") {
        const details = withdrawal.paymentDetails;
        paymentDetailsHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-university me-2"></i>
                                Bank Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="bank-info-list">
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Bank Name:</span>
                                    <span class="bank-info-value">${details.bankName}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Account Name:</span>
                                    <span class="bank-info-value">${details.accountName}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Account Number:</span>
                                    <span class="bank-info-value">${details.accountNumber}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Routing Number:</span>
                                    <span class="bank-info-value">${details.routingNumber}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">SWIFT Code:</span>
                                    <span class="bank-info-value">${details.swift}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
      } else if (withdrawal.paymentMethod === "paypal") {
        paymentDetailsHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fab fa-paypal me-2"></i>
                                PayPal Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="bank-info-list">
                                <li class="bank-info-item">
                                    <span class="bank-info-label">PayPal Email:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.email}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
      } else if (withdrawal.paymentMethod === "stripe") {
        paymentDetailsHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fab fa-stripe-s me-2"></i>
                                Stripe Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="bank-info-list">
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Stripe Account ID:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.accountId}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
      } else if (withdrawal.paymentMethod === "crypto") {
        paymentDetailsHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fab fa-ethereum me-2"></i>
                                Cryptocurrency Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="bank-info-list">
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Currency:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.currency}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Wallet Address:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.address}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
      } else if (withdrawal.paymentMethod === "check") {
        paymentDetailsHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-money-check me-2"></i>
                                Check Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="bank-info-list">
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Recipient Name:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.name}</span>
                                </li>
                                <li class="bank-info-item">
                                    <span class="bank-info-label">Mailing Address:</span>
                                    <span class="bank-info-value">${withdrawal.paymentDetails.address}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                `;
      }

      // Generate status history
      let historyHtml = "";
      if (withdrawal.history && withdrawal.history.length > 0) {
        historyHtml = '<div class="timeline-container mt-3">';

        withdrawal.history.forEach((entry) => {
          const entryDate = new Date(entry.date);
          const formattedEntryDate = entryDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
          });

          historyHtml += `
                        <div class="timeline-item">
                            <div class="timeline-marker ${entry.status}"></div>
                            <div class="timeline-content">
                                <span class="timeline-date">${formattedEntryDate}</span>
                                <div>
                                    <strong>${this.capitalizeFirstLetter(
                                      entry.status
                                    )}</strong>
                                    ${
                                      entry.transactionReference
                                        ? `<span class="ms-2 badge bg-secondary">${entry.transactionReference}</span>`
                                        : ""
                                    }
                                </div>
                                <p class="mb-0 text-muted">${entry.notes}</p>
                            </div>
                        </div>
                    `;
        });

        historyHtml += "</div>";
      }

      // Transaction reference display
      const transactionReferenceHtml = withdrawal.transactionReference
        ? `<div class="mb-3">
                    <p class="mb-1 fw-semibold">Transaction Reference</p>
                    <p class="mb-0">${withdrawal.transactionReference}</p>
                </div>`
        : "";

      detailsContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Withdrawal ID</p>
                            <h5>${withdrawal.id}</h5>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Date Requested</p>
                            <p class="mb-0">${formattedDate}</p>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Current Status</p>
                            <p class="mb-0">${statusDisplay}</p>
                        </div>
                        ${transactionReferenceHtml}
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Amount</p>
                            <h4 class="text-primary">$${withdrawal.amount.toFixed(
                              2
                            )}</h4>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Artist</p>
                            <div class="d-flex align-items-center">
                                <img src="${withdrawal.artistImage}" alt="${
        withdrawal.artistName
      }" 
                                    class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <p class="mb-0 fw-medium">${
                                      withdrawal.artistName
                                    }</p>
                                    <p class="mb-0 small text-muted">${
                                      withdrawal.artistEmail
                                    }</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 fw-semibold">Payment Method</p>
                            <p class="mb-0">${paymentMethodDisplay}</p>
                        </div>
                    </div>
                </div>
                
                ${paymentDetailsHtml}
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Status History
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        ${historyHtml}
                    </div>
                </div>
                
                <div class="mb-0">
                    <p class="mb-1 fw-semibold">Notes</p>
                    <p class="mb-0">${
                      withdrawal.notes || "No notes available"
                    }</p>
                </div>
            `;

      // Show the modal
      new bootstrap.Modal(
        document.getElementById("withdrawalDetailsModal")
      ).show();
    },

    // Open update status modal
    openUpdateStatusModal: function (withdrawalId) {
      const withdrawal = this.withdrawals.find((w) => w.id === withdrawalId);
      if (!withdrawal) return;

      this.currentWithdrawalId = withdrawalId;

      document.getElementById("updateWithdrawalId").value = withdrawalId;
      document.getElementById("withdrawalStatus").value = withdrawal.status;
      document.getElementById("transactionReference").value =
        withdrawal.transactionReference || "";
      document.getElementById("statusNotes").value = "";

      // Update modal title
      document.getElementById(
        "updateWithdrawalTitle"
      ).textContent = `Update Status: ${withdrawalId}`;

      // Show the modal
      new bootstrap.Modal(
        document.getElementById("updateWithdrawalModal")
      ).show();
    },

    // Preset status update modal with a specific status
    presetStatusUpdate: function (status) {
      if (!this.currentWithdrawalId) return;

      // Hide details modal
      const detailsModal = bootstrap.Modal.getInstance(
        document.getElementById("withdrawalDetailsModal")
      );
      if (detailsModal) {
        detailsModal.hide();
      }

      // Set up and show the update status modal
      this.openUpdateStatusModal(this.currentWithdrawalId);
      document.getElementById("withdrawalStatus").value = status;
    },

    // Update withdrawal status
    updateWithdrawalStatus: function () {
      const withdrawalId = document.getElementById("updateWithdrawalId").value;
      const newStatus = document.getElementById("withdrawalStatus").value;
      const transactionReference = document.getElementById(
        "transactionReference"
      ).value;
      const notes = document.getElementById("statusNotes").value;
      const notifyArtist = document.getElementById("notifyArtist").checked;

      if (!withdrawalId || !newStatus) {
        this.showNotification(
          "Please provide all required information",
          "warning"
        );
        return;
      }

      // In a real app, send this to an API
      // For demo purposes, update the local data
      const withdrawalIndex = this.withdrawals.findIndex(
        (w) => w.id === withdrawalId
      );
      if (withdrawalIndex === -1) return;

      const withdrawal = this.withdrawals[withdrawalIndex];

      // Add history entry
      const historyEntry = {
        date: new Date().toISOString(),
        status: newStatus,
        notes: notes || `Status updated to ${newStatus}`,
        by: "admin",
      };

      if (transactionReference) {
        historyEntry.transactionReference = transactionReference;
        this.withdrawals[withdrawalIndex].transactionReference =
          transactionReference;
      }

      if (!withdrawal.history) {
        withdrawal.history = [];
      }

      withdrawal.history.push(historyEntry);
      withdrawal.status = newStatus;
      withdrawal.notes = notes || withdrawal.notes;
      withdrawal.updatedAt = new Date().toISOString();

      // Hide the modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("updateWithdrawalModal")
      );
      if (modal) {
        modal.hide();
      }

      // Refresh the table and stats
      this.applyFilters();
      this.updateStats();

      this.showNotification(
        `Withdrawal status updated to ${newStatus}`,
        "success"
      );

      // In a real app, would notify the artist if requested
      if (notifyArtist) {
        console.log(`Notification would be sent to ${withdrawal.artistEmail}`);
      }
    },

    // Open batch process modal
    openBatchProcessModal: function () {
      if (this.selectedWithdrawals.size === 0) {
        this.showNotification("No withdrawals selected", "warning");
        return;
      }

      const tableBody = document.getElementById("batchWithdrawalsBody");
      tableBody.innerHTML = "";

      let totalAmount = 0;

      // Add selected withdrawals to the table
      this.selectedWithdrawals.forEach((withdrawalId) => {
        const withdrawal = this.withdrawals.find((w) => w.id === withdrawalId);
        if (!withdrawal) return;

        totalAmount += withdrawal.amount;

        const row = document.createElement("tr");

        // Format payment method
        let paymentMethodDisplay;
        switch (withdrawal.paymentMethod) {
          case "bank_transfer":
            paymentMethodDisplay = '<i class="fas fa-university"></i> Bank';
            break;
          case "paypal":
            paymentMethodDisplay = '<i class="fab fa-paypal"></i> PayPal';
            break;
          case "stripe":
            paymentMethodDisplay = '<i class="fab fa-stripe-s"></i> Stripe';
            break;
          case "crypto":
            paymentMethodDisplay = '<i class="fab fa-ethereum"></i> Crypto';
            break;
          case "check":
            paymentMethodDisplay = '<i class="fas fa-money-check"></i> Check';
            break;
          default:
            paymentMethodDisplay =
              '<i class="fas fa-money-bill-wave"></i> Other';
        }

        row.innerHTML = `
                    <td>${withdrawal.id}</td>
                    <td>${withdrawal.artistName}</td>
                    <td>$${withdrawal.amount.toFixed(2)}</td>
                    <td>${paymentMethodDisplay}</td>
                `;

        tableBody.appendChild(row);
      });

      // Update total amount
      document.getElementById(
        "batchTotalAmount"
      ).textContent = `$${totalAmount.toFixed(2)}`;

      // Show the modal
      new bootstrap.Modal(document.getElementById("batchProcessModal")).show();
    },

    // Process batch of withdrawals
    processBatch: function () {
      const action = document.getElementById("batchAction").value;
      const notes = document.getElementById("batchNotes").value;
      const notifyArtists =
        document.getElementById("batchNotifyArtists").checked;

      if (!action) {
        this.showNotification("Please select an action", "warning");
        return;
      }

      // Map action to status
      let newStatus;
      switch (action) {
        case "approve":
          newStatus = "processing";
          break;
        case "process":
          newStatus = "processing";
          break;
        case "complete":
          newStatus = "completed";
          break;
        case "reject":
          newStatus = "rejected";
          break;
        default:
          newStatus = action;
      }

      // In a real app, send this to an API
      // For demo purposes, update the local data
      let updatedCount = 0;
      this.selectedWithdrawals.forEach((withdrawalId) => {
        const withdrawalIndex = this.withdrawals.findIndex(
          (w) => w.id === withdrawalId
        );
        if (withdrawalIndex === -1) return;

        const withdrawal = this.withdrawals[withdrawalIndex];

        // Skip if already in this status
        if (withdrawal.status === newStatus) return;

        // Add history entry
        const historyEntry = {
          date: new Date().toISOString(),
          status: newStatus,
          notes: notes || `Batch updated to ${newStatus}`,
          by: "admin",
        };

        if (!withdrawal.history) {
          withdrawal.history = [];
        }

        withdrawal.history.push(historyEntry);
        withdrawal.status = newStatus;
        if (notes) {
          withdrawal.notes = notes;
        }
        withdrawal.updatedAt = new Date().toISOString();

        updatedCount++;

        // In a real app, would notify the artist if requested
        if (notifyArtists) {
          console.log(
            `Notification would be sent to ${withdrawal.artistEmail}`
          );
        }
      });

      // Hide the modal
      const modal = bootstrap.Modal.getInstance(
        document.getElementById("batchProcessModal")
      );
      if (modal) {
        modal.hide();
      }

      // Clear selection
      this.selectedWithdrawals.clear();
      document.getElementById("selectAllWithdrawals").checked = false;

      // Refresh the table and stats
      this.applyFilters();
      this.updateStats();

      this.showNotification(
        `Updated ${updatedCount} withdrawals to ${newStatus}`,
        "success"
      );
    },

    // Export withdrawals to CSV
    exportWithdrawals: function () {
      const withdrawalsToExport = this.filteredWithdrawals;

      if (withdrawalsToExport.length === 0) {
        this.showNotification("No withdrawals to export", "warning");
        return;
      }

      // Create CSV content
      let csvContent = "data:text/csv;charset=utf-8,";

      // Add header row
      csvContent +=
        "Withdrawal ID,Date,Artist Name,Artist Email,Amount,Currency,Payment Method,Status,Transaction Reference,Notes\n";

      // Add data rows
      withdrawalsToExport.forEach((w) => {
        const row = [
          w.id,
          new Date(w.date).toISOString(),
          w.artistName,
          w.artistEmail,
          w.amount,
          w.currency,
          w.paymentMethod,
          w.status,
          w.transactionReference || "",
          (w.notes || "").replace(/,/g, " ").replace(/\n/g, " "),
        ];

        csvContent += row.join(",") + "\n";
      });

      // Create download link
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute(
        "download",
        `artshelf-withdrawals-${new Date().toISOString().split("T")[0]}.csv`
      );
      document.body.appendChild(link);

      // Download the file
      link.click();

      // Clean up
      document.body.removeChild(link);

      this.showNotification(
        `${withdrawalsToExport.length} withdrawals exported to CSV`,
        "success"
      );
    },

    // Update the batch button status
    updateBatchButton: function () {
      const batchBtn = document.getElementById("batchProcessBtn");

      if (this.selectedWithdrawals.size > 0) {
        batchBtn.classList.remove("btn-outline-primary");
        batchBtn.classList.add("btn-primary");
        batchBtn.innerHTML = `<i class="fas fa-check-double me-1"></i> Process (${this.selectedWithdrawals.size})`;
      } else {
        batchBtn.classList.add("btn-outline-primary");
        batchBtn.classList.remove("btn-primary");
        batchBtn.innerHTML = `<i class="fas fa-check-double me-1"></i> Batch Process`;
      }
    },

    // Show a notification
    showNotification: function (message, type = "info") {
      // In a real app, use a toast or notification system
      console.log(`[${type.toUpperCase()}] ${message}`);

      // Create a Bootstrap toast (in a real app, this would be abstracted)
      const toastContainer = document.querySelector(".toast-container");

      if (!toastContainer) {
        const container = document.createElement("div");
        container.className =
          "toast-container position-fixed bottom-0 end-0 p-3";
        container.style.zIndex = "5";
        document.body.appendChild(container);
      }

      const toastElement = document.createElement("div");
      toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
      toastElement.setAttribute("role", "alert");
      toastElement.setAttribute("aria-live", "assertive");
      toastElement.setAttribute("aria-atomic", "true");

      toastElement.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

      document.querySelector(".toast-container").appendChild(toastElement);

      const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000,
      });

      toast.show();

      // Remove the toast element after it's hidden
      toastElement.addEventListener("hidden.bs.toast", () => {
        toastElement.remove();
      });
    },

    // Set loading state
    setLoading: function (isLoading) {
      // In a real app, show a loading spinner or disable UI
      if (isLoading) {
        console.log("Loading...");
      } else {
        console.log("Loading complete");
      }
    },

    // Helper function to capitalize first letter
    capitalizeFirstLetter: function (string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
  };

  // Initialize the withdrawals manager
  WithdrawalsManager.init();
});
