/**
 * Admin Reports Module
 * Handles the reports page functionality including:
 * - Generating different types of reports
 * - Date range filtering
 * - Chart visualization
 * - Reports data table
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the reports module
  const reportsModule = {
    // Current state
    state: {
      reportType: "sales",
      dateRange: "30days",
      startDate: null,
      endDate: null,
      currentPage: 1,
      itemsPerPage: 10,
      totalItems: 0,
      chartPeriod: "daily",
      searchQuery: "",
    },

    // DOM Elements
    elements: {
      reportType: document.getElementById("report-type"),
      dateRange: document.getElementById("date-range"),
      startDate: document.getElementById("start-date"),
      endDate: document.getElementById("end-date"),
      generateBtn: document.getElementById("generate-report"),
      exportBtn: document.getElementById("export-report"),
      searchInput: document.querySelector(".table-header input[type='text']"),
      searchBtn: document.querySelector(".table-header button"),

      // Stats elements
      totalRevenue: document.getElementById("total-revenue"),
      totalOrders: document.getElementById("total-orders"),
      artworksSold: document.getElementById("artworks-sold"),
      newCustomers: document.getElementById("new-customers"),

      // Chart canvases
      revenueChart: document.getElementById("revenue-chart"),
      distributionChart: document.getElementById("distribution-chart"),

      // Chart period buttons
      chartPeriodBtns: document.querySelectorAll(".chart-filters button"),

      // Table elements
      tableBody: document.getElementById("report-table-body"),
      pagination: document.querySelector(".pagination"),
    },

    // Chart instances
    charts: {
      revenueChart: null,
      distributionChart: null,
    },

    /**
     * Initialize reports module
     */
    init() {
      this.setupDateFilters();
      this.setupEventListeners();
      // Initialize device capability detection for better responsiveness
      this.detectDeviceCapabilities();
      // Enhance chart accessibility
      this.enhanceChartAccessibility();

      // Load initial report data
      this.loadReport("revenue");
    },

    /**
     * Set default dates based on selected range
     */
    setupDateDefaults() {
      const today = new Date();
      const endDate = new Date(today);
      let startDate;

      switch (this.state.dateRange) {
        case "7days":
          startDate = new Date(today);
          startDate.setDate(today.getDate() - 7);
          break;
        case "30days":
          startDate = new Date(today);
          startDate.setDate(today.getDate() - 30);
          break;
        case "90days":
          startDate = new Date(today);
          startDate.setDate(today.getDate() - 90);
          break;
        case "year":
          startDate = new Date(today);
          startDate.setFullYear(today.getFullYear() - 1);
          break;
        default:
          startDate = new Date(today);
          startDate.setDate(today.getDate() - 30);
      }

      this.state.startDate = startDate;
      this.state.endDate = endDate;

      // Format and set input values
      this.elements.startDate.value = this.formatDateForInput(startDate);
      this.elements.endDate.value = this.formatDateForInput(endDate);

      // Toggle date input fields based on custom selection
      const isCustomRange = this.state.dateRange === "custom";
      this.elements.startDate.parentElement.style.display = isCustomRange
        ? "block"
        : "none";
      this.elements.endDate.parentElement.style.display = isCustomRange
        ? "block"
        : "none";
    },

    /**
     * Format date for input field (YYYY-MM-DD)
     */
    formatDateForInput(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    /**
     * Setup event listeners
     */
    setupEventListeners() {
      // Date range pickers
      const startDatePicker = document.getElementById("start-date");
      const endDatePicker = document.getElementById("end-date");

      startDatePicker.addEventListener("change", () => {
        this.filters.startDate = startDatePicker.value;
        this.generateReport();
      });

      endDatePicker.addEventListener("change", () => {
        this.filters.endDate = endDatePicker.value;
        this.generateReport();
      });

      // Report type selector
      const reportTypeSelector = document.getElementById("report-type");
      reportTypeSelector.addEventListener("change", () => {
        this.filters.reportType = reportTypeSelector.value;
        this.generateReport();
      });

      // Export buttons
      document
        .getElementById("export-pdf")
        .addEventListener("click", () => this.exportReport("pdf"));
      document
        .getElementById("export-csv")
        .addEventListener("click", () => this.exportReport("csv"));

      // Add resize event listener for responsive charts
      window.addEventListener("resize", this.handleWindowResize.bind(this));

      // Listen for orientation change events on mobile devices
      window.addEventListener("orientationchange", () => {
        // Force immediate redraw on orientation change
        this.forceChartReflow();
        // Then do a full resize handler after the orientation change completes
        setTimeout(() => this.handleWindowResize(), 100);
      });

      // Add visibility change listener to handle tab switching
      document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "visible") {
          // When tab becomes visible again, check if charts need re-rendering
          setTimeout(() => {
            // Force chart redraw when returning to the tab
            this.forceChartReflow();
          }, 100);
        }
      });

      // Custom refresh button
      const refreshButton = document.getElementById("refresh-report");
      if (refreshButton) {
        refreshButton.addEventListener("click", () => {
          refreshButton.classList.add("rotating");
          // Force data refresh
          this.generateReport(true);
          // Remove rotation after animation completes
          setTimeout(() => refreshButton.classList.remove("rotating"), 750);
        });
      }
    },

    /**
     * Setup touch interactions for better mobile experience
     */
    setupTouchInteractions() {
      // Add touch event handlers to chart containers
      const chartContainers = document.querySelectorAll(".chart-card");

      chartContainers.forEach((container) => {
        let startX, startY;
        let initialPinchDistance = 0;
        let initialScale = 1;
        let isDragging = false;
        let lastTouchEnd = 0;

        // Touch start handler
        container.addEventListener(
          "touchstart",
          (e) => {
            // Prevent double-tap zoom on iOS
            const now = new Date().getTime();
            if (now - lastTouchEnd <= 300) {
              e.preventDefault();
            }

            if (e.touches.length === 1) {
              // Single touch - track start position for potential drag
              startX = e.touches[0].clientX;
              startY = e.touches[0].clientY;
              isDragging = false;
            } else if (e.touches.length === 2) {
              // Pinch gesture - calculate initial distance
              initialPinchDistance = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
              );

              // Find associated chart
              const canvasId = container.querySelector("canvas").id;
              if (canvasId === "revenue-chart" && this.charts.revenueChart) {
                initialScale =
                  this.charts.revenueChart.options.scales.x.ticks.maxTicksLimit;
              }
            }
          },
          { passive: false }
        );

        // Touch move handler
        container.addEventListener(
          "touchmove",
          (e) => {
            if (e.touches.length === 2) {
              // Handle pinch zoom for charts
              const currentDistance = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
              );

              // Only apply to revenue chart (line chart)
              const canvasId = container.querySelector("canvas").id;
              if (canvasId === "revenue-chart" && this.charts.revenueChart) {
                // Calculate new scale based on pinch
                const pinchRatio = currentDistance / initialPinchDistance;

                // Adjust tick limit based on pinch (zoom in = more ticks, zoom out = fewer ticks)
                let newTickLimit;
                if (pinchRatio > 1.1) {
                  // Zooming in
                  newTickLimit = Math.min(initialScale + 2, 15);
                } else if (pinchRatio < 0.9) {
                  // Zooming out
                  newTickLimit = Math.max(initialScale - 2, 4);
                }

                // Only update if changed
                if (
                  newTickLimit &&
                  newTickLimit !==
                    this.charts.revenueChart.options.scales.x.ticks
                      .maxTicksLimit
                ) {
                  this.charts.revenueChart.options.scales.x.ticks.maxTicksLimit =
                    newTickLimit;
                  this.charts.revenueChart.update("none"); // Update without animation for better performance
                }
              }
            } else if (e.touches.length === 1 && window.innerWidth < 768) {
              // Handle drag on mobile devices for panning the chart
              const moveX = e.touches[0].clientX - startX;
              const moveY = e.touches[0].clientY - startY;

              // Only start dragging if moved more than 10px in any direction
              if (
                !isDragging &&
                (Math.abs(moveX) > 10 || Math.abs(moveY) > 10)
              ) {
                isDragging = true;
              }

              if (isDragging) {
                const canvasId = container.querySelector("canvas").id;
                if (canvasId === "revenue-chart" && this.charts.revenueChart) {
                  // We could implement custom panning here, but for now just add visual feedback
                  container.classList.add("chart-dragging");
                }
              }
            }
          },
          { passive: true }
        );

        // Touch end handler
        container.addEventListener(
          "touchend",
          (e) => {
            if (isDragging) {
              container.classList.remove("chart-dragging");
              // Force a gentle refresh of the chart after drag
              const canvasId = container.querySelector("canvas").id;
              if (canvasId === "revenue-chart" && this.charts.revenueChart) {
                // Use a short animation for smooth transition after pan
                this.charts.revenueChart.update("none");
              }
            }

            // Store timestamp for double-tap prevention
            lastTouchEnd = new Date().getTime();

            // Remove any highlights
            setTimeout(() => {
              container.classList.remove("chart-dragging");
            }, 150);
          },
          { passive: true }
        );

        // Double-tap handler for charts to zoom in
        container.addEventListener("dblclick", (e) => {
          const canvasId = container.querySelector("canvas").id;
          if (canvasId === "revenue-chart" && this.charts.revenueChart) {
            // Toggle between detailed view and normal view
            const currentTickLimit =
              this.charts.revenueChart.options.scales.x.ticks.maxTicksLimit;
            this.charts.revenueChart.options.scales.x.ticks.maxTicksLimit =
              currentTickLimit === 15 ? 7 : 15;

            // Use animation for the zoom effect
            this.charts.revenueChart.update({
              duration: 300,
              easing: "easeOutQuad",
            });
          }
        });
      });

      // Add swipe handler for changing chart periods on mobile
      const chartHeaders = document.querySelectorAll(".chart-header");
      chartHeaders.forEach((header) => {
        let touchStartX = 0;

        header.addEventListener(
          "touchstart",
          (e) => {
            touchStartX = e.touches[0].clientX;
          },
          { passive: true }
        );

        header.addEventListener(
          "touchend",
          (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            const diff = touchEndX - touchStartX;

            // Detect horizontal swipe (>50px)
            if (Math.abs(diff) > 50) {
              const chartPeriodBtns = this.elements.chartPeriodBtns;
              const activeBtn = Array.from(chartPeriodBtns).findIndex((btn) =>
                btn.classList.contains("active")
              );

              if (activeBtn !== -1) {
                // Left swipe: next period, Right swipe: previous period
                let newIndex =
                  diff < 0
                    ? Math.min(activeBtn + 1, chartPeriodBtns.length - 1)
                    : Math.max(activeBtn - 1, 0);

                if (newIndex !== activeBtn) {
                  chartPeriodBtns[newIndex].click();
                }
              }
            }
          },
          { passive: true }
        );
      });
    },

    /**
     * Handle window resize event to redraw charts
     */
    handleWindowResize() {
      // Debounce the resize event to avoid performance issues
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }

      // Quick immediate response for better UX
      this.quickRenderAdjustment();

      this.resizeTimeout = setTimeout(
        () => {
          // Check if device orientation changed
          const newOrientation =
            window.innerWidth > window.innerHeight ? "landscape" : "portrait";
          const orientationChanged = this.currentOrientation !== newOrientation;
          this.currentOrientation = newOrientation;

          // Force re-layout for orientation changes
          if (orientationChanged) {
            this.forceChartReflow();
          }

          // Re-render charts with new dimensions
          if (this.charts.revenueChart) {
            // Use Chart.js resize method first for native resizing
            this.charts.revenueChart.resize();
            // Then apply our custom responsiveness adjustments
            this.updateChartResponsiveness(this.charts.revenueChart, true);
          }

          if (this.charts.distributionChart) {
            // Use Chart.js resize method first for native resizing
            this.charts.distributionChart.resize();
            // Then apply our custom responsiveness adjustments
            this.updateChartResponsiveness(
              this.charts.distributionChart,
              false
            );
          }

          // Adjust card layout based on screen size
          this.adjustCardLayout();

          // Update the charts visibility based on container visibility
          this.optimizeHiddenCharts();
        },
        orientationChanged ? 100 : 250
      ); // Faster response for orientation changes
    },

    /**
     * Make quick visual adjustments before full resize computation
     */
    quickRenderAdjustment() {
      // For immediate visual feedback during resize
      const windowWidth = window.innerWidth;

      // Add temp class to indicate resizing state
      document.querySelectorAll(".chart-card").forEach((card) => {
        card.classList.add("resizing");

        // Quick size adjustment for immediate feedback
        if (windowWidth < 576) {
          card.style.height = "300px";
        } else if (windowWidth < 768) {
          card.style.height = "350px";
        }
      });

      // Remove the class after a short delay
      setTimeout(() => {
        document.querySelectorAll(".chart-card").forEach((card) => {
          card.classList.remove("resizing");
        });
      }, 100);
    },

    /**
     * Force charts to reflow after major layout changes
     */
    forceChartReflow() {
      const chartContainers = document.querySelectorAll(".chart-card");

      // Briefly adjust container sizes to force reflow
      chartContainers.forEach((container) => {
        const canvas = container.querySelector("canvas");
        if (canvas) {
          // Store original display style
          const originalDisplay = canvas.style.display;

          // Force reflow by toggling display
          canvas.style.display = "none";

          // Use requestAnimationFrame to ensure browser processes the change
          requestAnimationFrame(() => {
            canvas.style.display = originalDisplay;

            // Find and update the associated chart
            const chartId = canvas.id;
            if (chartId === "revenue-chart" && this.charts.revenueChart) {
              this.charts.revenueChart.resize();
            } else if (
              chartId === "distribution-chart" &&
              this.charts.distributionChart
            ) {
              this.charts.distributionChart.resize();
            }
          });
        }
      });
    },

    /**
     * Optimize rendering of charts that are not visible
     */
    optimizeHiddenCharts() {
      // Check if charts are visible in the viewport
      const charts = document.querySelectorAll(".chart-card canvas");

      charts.forEach((canvas) => {
        const rect = canvas.getBoundingClientRect();
        const isVisible =
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <=
            (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <=
            (window.innerWidth || document.documentElement.clientWidth);

        const chartId = canvas.id;
        let chart = null;

        if (chartId === "revenue-chart") {
          chart = this.charts.revenueChart;
        } else if (chartId === "distribution-chart") {
          chart = this.charts.distributionChart;
        }

        if (chart) {
          if (!isVisible) {
            // If chart is not visible, optimize rendering
            if (!chart._suspended) {
              chart._suspended = true;
              chart.options._oldAnimations = chart.options.animation;
              chart.options.animation = false;
            }
          } else if (chart._suspended) {
            // If chart becomes visible again, restore animations
            chart._suspended = false;
            chart.options.animation = chart.options._oldAnimations;
            // Force update to ensure chart is rendered correctly
            chart.update();
          }
        }
      });
    },

    /**
     * Adjust card layout based on screen size
     */
    adjustCardLayout() {
      const chartCards = document.querySelectorAll(".chart-card");
      const windowWidth = window.innerWidth;
      const isPortrait = window.innerHeight > window.innerWidth;

      chartCards.forEach((card) => {
        // Adjust card height based on screen size and orientation
        if (windowWidth < 576) {
          // Extra small devices
          card.style.height = isPortrait ? "300px" : "250px";
        } else if (windowWidth < 768) {
          // Small devices
          card.style.height = isPortrait ? "350px" : "300px";
        } else if (windowWidth < 992) {
          // Medium devices
          card.style.height = "400px";
        } else {
          // Large devices
          card.style.height = "450px";
        }

        // Add a 'compact' class for small screens
        if (windowWidth < 768) {
          card.classList.add("compact");
        } else {
          card.classList.remove("compact");
        }
      });

      // Adjust stats cards layout
      const statCards = document.querySelectorAll(".stat-card");
      statCards.forEach((card) => {
        if (windowWidth < 576) {
          card.classList.add("compact-stat");
        } else {
          card.classList.remove("compact-stat");
        }
      });

      // More efficient table layout for small screens
      const tableContainer = document.querySelector(".report-table-container");
      if (tableContainer) {
        if (windowWidth < 768) {
          tableContainer.classList.add("compact-table");
        } else {
          tableContainer.classList.remove("compact-table");
        }
      }

      // Store current orientation for future reference
      this.currentOrientation = isPortrait ? "portrait" : "landscape";
    },

    /**
     * Update chart responsiveness based on current window size
     * @param {Chart} chart - The chart to update
     * @param {boolean} isRevenueChart - Whether this is the revenue chart (line) or distribution chart (doughnut)
     */
    updateChartResponsiveness(chart, isRevenueChart) {
      const windowWidth = window.innerWidth;
      const isPortrait = window.innerHeight > window.innerWidth;
      const devicePixelRatio = window.devicePixelRatio || 1;

      // Optimize rendering for high pixel density displays
      if (chart.canvas) {
        chart.canvas.style.width = "100%";
        chart.canvas.style.height = "100%";

        // For high-DPI displays, adjust rendering quality based on performance needs
        if (devicePixelRatio > 1.5) {
          // Check if device is low-powered
          const isLowPowered =
            navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;

          if (isLowPowered) {
            // For low-power devices with high-DPI screens, reduce rendering quality slightly
            chart.canvas.height = chart.canvas.offsetHeight * 1.5;
            chart.canvas.width = chart.canvas.offsetWidth * 1.5;
          } else {
            // For powerful devices, match the device pixel ratio
            chart.canvas.height = chart.canvas.offsetHeight * devicePixelRatio;
            chart.canvas.width = chart.canvas.offsetWidth * devicePixelRatio;
          }
        }
      }

      if (isRevenueChart) {
        // Adjust x-axis tick settings based on window width and orientation
        if (windowWidth < 576 || (windowWidth < 768 && isPortrait)) {
          // Extra small devices or portrait small devices
          chart.options.scales.x.ticks.maxTicksLimit = 5;
          chart.options.scales.x.ticks.maxRotation = 90;
          chart.options.scales.x.ticks.minRotation = 45;
          chart.options.scales.x.ticks.font.size = 10;
          chart.options.scales.y.ticks.font.size = 10;
          chart.options.layout.padding = {
            left: 5,
            right: 10,
            top: 15,
            bottom: 10,
          };
          // Adjust point sizes for small screens
          chart.data.datasets[0].pointRadius = 2;
          chart.data.datasets[0].pointHoverRadius = 3;
          chart.data.datasets[0].borderWidth = 2;

          // Use decreased animation duration for better performance
          chart.options.animation.duration = 300;
        } else if (windowWidth < 768) {
          // Small devices in landscape
          chart.options.scales.x.ticks.maxTicksLimit = 7;
          chart.options.scales.x.ticks.maxRotation = 45;
          chart.options.scales.x.ticks.minRotation = 30;
          chart.options.scales.x.ticks.font.size = 11;
          chart.options.scales.y.ticks.font.size = 11;
          chart.options.layout.padding = {
            left: 10,
            right: 15,
            top: 20,
            bottom: 15,
          };
          // Adjust point sizes for medium screens
          chart.data.datasets[0].pointRadius = 3;
          chart.data.datasets[0].pointHoverRadius = 4;
          chart.data.datasets[0].borderWidth = 2;

          // Use moderate animation duration
          chart.options.animation.duration = 500;
        } else {
          // Medium and larger devices
          chart.options.scales.x.ticks.maxTicksLimit = 10;
          chart.options.scales.x.ticks.maxRotation = 30;
          chart.options.scales.x.ticks.minRotation = 0;
          chart.options.scales.x.ticks.font.size = 12;
          chart.options.scales.y.ticks.font.size = 12;
          chart.options.layout.padding = {
            left: 10,
            right: 25,
            top: 25,
            bottom: 20,
          };
          // Adjust point sizes for larger screens
          chart.data.datasets[0].pointRadius = 3;
          chart.data.datasets[0].pointHoverRadius = 5;
          chart.data.datasets[0].borderWidth = 3;

          // Full animation duration for larger screens
          chart.options.animation.duration = 750;
        }

        // Adjust y-axis tick settings
        if (windowWidth < 768) {
          chart.options.scales.y.ticks.maxTicksLimit = 5;
        } else {
          chart.options.scales.y.ticks.maxTicksLimit = 8;
        }

        // Optimize tooltip rendering for performance
        chart.options.plugins.tooltip.enabled = true;
        chart.options.plugins.tooltip.animation.duration =
          windowWidth < 768 ? 100 : 200;
        chart.options.plugins.tooltip.position = "nearest";
      } else {
        // Adjust doughnut chart settings
        if (windowWidth < 576 || (windowWidth < 768 && isPortrait)) {
          chart.options.plugins.legend.position = "bottom";
          chart.options.plugins.legend.labels.font.size = 10;
          chart.options.plugins.legend.labels.boxWidth = 10;
          chart.options.plugins.legend.labels.padding = 15;
          chart.options.cutout = "65%";
          chart.options.layout.padding = {
            left: 5,
            right: 5,
            top: 15,
            bottom: 10,
          };

          // Use simplified animation for performance
          chart.options.animation.duration = 300;
        } else if (windowWidth < 768) {
          chart.options.plugins.legend.position = "bottom";
          chart.options.plugins.legend.labels.font.size = 11;
          chart.options.plugins.legend.labels.boxWidth = 12;
          chart.options.plugins.legend.labels.padding = 18;
          chart.options.cutout = "68%";
          chart.options.layout.padding = {
            left: 10,
            right: 10,
            top: 20,
            bottom: 15,
          };

          // Moderate animation duration
          chart.options.animation.duration = 500;
        } else if (windowWidth < 992) {
          // Medium-sized screens
          chart.options.plugins.legend.position = "right";
          chart.options.plugins.legend.labels.font.size = 11;
          chart.options.plugins.legend.labels.boxWidth = 14;
          chart.options.plugins.legend.labels.padding = 18;
          chart.options.cutout = "68%";
          chart.options.layout.padding = {
            left: 10,
            right: 10,
            top: 20,
            bottom: 15,
          };

          // Full animation for medium screens
          chart.options.animation.duration = 650;
        } else {
          // Large screens
          chart.options.plugins.legend.position = "right";
          chart.options.plugins.legend.labels.font.size = 12;
          chart.options.plugins.legend.labels.boxWidth = 15;
          chart.options.plugins.legend.labels.padding = 20;
          chart.options.cutout = "70%";
          chart.options.layout.padding = {
            left: 10,
            right: 10,
            top: 25,
            bottom: 20,
          };

          // Full animation duration for larger screens
          chart.options.animation.duration = 750;
        }
      }

      // Apply the changes
      chart.update();
    },

    /**
     * Generate report based on current filters
     */
    async generateReport() {
      try {
        // Show loading state
        this.setLoadingState(true);

        // Fetch data based on report type
        const reportData = await this.fetchReportData();

        // Update UI with the new data
        this.updateSummaryStats(reportData.summary);
        this.updateCharts(reportData);
        this.updateReportTable(reportData.details);

        // Store total items for pagination
        this.state.totalItems = reportData.totalItems;

        // Hide loading state
        this.setLoadingState(false);
      } catch (error) {
        console.error("Error generating report:", error);
        alert("Failed to generate report. Please try again.");
        this.setLoadingState(false);
      }
    },

    /**
     * Set loading state for the page
     */
    setLoadingState(isLoading) {
      if (isLoading) {
        this.elements.generateBtn.innerHTML =
          '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
        this.elements.generateBtn.disabled = true;
      } else {
        this.elements.generateBtn.innerHTML =
          '<i class="fas fa-sync-alt me-2"></i>Generate Report';
        this.elements.generateBtn.disabled = false;
      }
    },

    /**
     * Fetch report data from API based on current filters
     */
    async fetchReportData() {
      // In a real implementation, this would call your backend API
      // For now, we'll return mock data based on report type

      // Build query parameters
      const params = new URLSearchParams({
        reportType: this.state.reportType,
        startDate: this.formatDateForInput(this.state.startDate),
        endDate: this.formatDateForInput(this.state.endDate),
        page: this.state.currentPage,
        limit: this.state.itemsPerPage,
        search: this.state.searchQuery,
        chartPeriod: this.state.chartPeriod,
      });

      // Would be replaced with actual API call
      // const response = await fetch(`${window.config.apiUrl}/reports?${params}`);
      // return await response.json();

      // For now, simulate API delay and return mock data
      await new Promise((resolve) => setTimeout(resolve, 700));
      return this.getMockReportData();
    },

    /**
     * Generate mock data for testing
     */
    getMockReportData() {
      // Different mock data based on report type
      switch (this.state.reportType) {
        case "sales":
          return this.getMockSalesData();
        case "inventory":
          return this.getMockInventoryData();
        case "customer":
          return this.getMockCustomerData();
        case "artist":
          return this.getMockArtistData();
        default:
          return this.getMockSalesData();
      }
    },

    /**
     * Mock sales report data
     */
    getMockSalesData() {
      let chartLabels;
      let chartData;

      // Generate different data based on chart period
      switch (this.state.chartPeriod) {
        case "daily":
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 2000, 7000);
          break;
        case "weekly":
          chartLabels = [
            "Week 1",
            "Week 2",
            "Week 3",
            "Week 4",
            "Week 5",
            "Week 6",
            "Week 7",
            "Week 8",
          ];
          chartData = this.generateRandomData(chartLabels.length, 12000, 25000);
          break;
        case "monthly":
          chartLabels = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ];
          chartData = this.generateRandomData(chartLabels.length, 40000, 80000);
          break;
        default:
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 2000, 7000);
      }

      // Filter data based on search query if provided
      let detailsData = Array.from({ length: 20 }, (_, i) => ({
        id: `ORD${String(1000 + i).padStart(4, "0")}`,
        date: this.getRandomDate(),
        customer: `Customer ${i + 1}`,
        artwork: `Artwork Title ${i + 1}`,
        artist: `Artist ${(i % 10) + 1}`,
        amount: Math.floor(Math.random() * 5000) + 500,
        status: ["Completed", "Processing", "Shipped"][
          Math.floor(Math.random() * 3)
        ],
      }));

      if (this.state.searchQuery) {
        const query = this.state.searchQuery.toLowerCase();
        detailsData = detailsData.filter(
          (item) =>
            item.id.toLowerCase().includes(query) ||
            item.customer.toLowerCase().includes(query) ||
            item.artwork.toLowerCase().includes(query) ||
            item.artist.toLowerCase().includes(query) ||
            item.status.toLowerCase().includes(query)
        );
      }

      return {
        summary: {
          totalRevenue: 32458,
          totalOrders: 124,
          artworksSold: 85,
          newCustomers: 38,
        },
        chartData: {
          revenue: {
            labels: chartLabels,
            data: chartData,
          },
          distribution: {
            labels: [
              "Paintings",
              "Sculptures",
              "Digital Art",
              "Photography",
              "Other",
            ],
            data: [45, 20, 15, 12, 8],
          },
        },
        details: detailsData,
        totalItems: detailsData.length > 0 ? 124 : 0, // Simulating total items in the database
      };
    },

    /**
     * Generate daily date labels for charts
     */
    generateDailyLabels() {
      const labels = [];
      const today = new Date();

      for (let i = 14; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);

        // Format as "Jun 12" or similar
        const formatter = new Intl.DateTimeFormat("en", {
          month: "short",
          day: "numeric",
        });
        labels.push(formatter.format(date));
      }

      return labels;
    },

    /**
     * Generate random data for charts
     */
    generateRandomData(length, min, max) {
      return Array.from(
        { length },
        () => Math.floor(Math.random() * (max - min + 1)) + min
      );
    },

    /**
     * Mock inventory report data
     */
    getMockInventoryData() {
      let chartLabels;
      let chartData;

      // Generate different data based on chart period
      switch (this.state.chartPeriod) {
        case "daily":
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 1500, 6000);
          break;
        case "weekly":
          chartLabels = [
            "Week 1",
            "Week 2",
            "Week 3",
            "Week 4",
            "Week 5",
            "Week 6",
            "Week 7",
            "Week 8",
          ];
          chartData = this.generateRandomData(chartLabels.length, 10000, 20000);
          break;
        case "monthly":
          chartLabels = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ];
          chartData = this.generateRandomData(chartLabels.length, 35000, 70000);
          break;
        default:
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 1500, 6000);
      }

      // Filter data based on search query if provided
      let detailsData = Array.from({ length: 20 }, (_, i) => ({
        id: `INV${String(2000 + i).padStart(4, "0")}`,
        date: this.getRandomDate(),
        customer: `Gallery ${(i % 8) + 1}`,
        artwork: `Inventory Item ${i + 1}`,
        artist: `Artist ${(i % 12) + 1}`,
        amount: Math.floor(Math.random() * 300) + 10,
        status: ["In Stock", "Low Stock", "Out of Stock"][
          Math.floor(Math.random() * 3)
        ],
      }));

      if (this.state.searchQuery) {
        const query = this.state.searchQuery.toLowerCase();
        detailsData = detailsData.filter(
          (item) =>
            item.id.toLowerCase().includes(query) ||
            item.customer.toLowerCase().includes(query) ||
            item.artwork.toLowerCase().includes(query) ||
            item.artist.toLowerCase().includes(query) ||
            item.status.toLowerCase().includes(query)
        );
      }

      return {
        summary: {
          totalRevenue: 28750,
          totalOrders: 95,
          artworksSold: 72,
          newCustomers: 26,
        },
        chartData: {
          revenue: {
            labels: chartLabels,
            data: chartData,
          },
          distribution: {
            labels: ["In Stock", "Low Stock", "Out of Stock", "On Order"],
            data: [65, 15, 10, 10],
          },
        },
        details: detailsData,
        totalItems: detailsData.length > 0 ? 95 : 0,
      };
    },

    /**
     * Get random date within the past year
     */
    getRandomDate() {
      const today = new Date();
      const pastDate = new Date(today);
      pastDate.setDate(today.getDate() - Math.floor(Math.random() * 365));
      return pastDate.toISOString().split("T")[0];
    },

    /**
     * Mock customer report data
     */
    getMockCustomerData() {
      let chartLabels;
      let chartData;

      // Generate different data based on chart period
      switch (this.state.chartPeriod) {
        case "daily":
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 1800, 6500);
          break;
        case "weekly":
          chartLabels = [
            "Week 1",
            "Week 2",
            "Week 3",
            "Week 4",
            "Week 5",
            "Week 6",
            "Week 7",
            "Week 8",
          ];
          chartData = this.generateRandomData(chartLabels.length, 11000, 22000);
          break;
        case "monthly":
          chartLabels = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ];
          chartData = this.generateRandomData(chartLabels.length, 38000, 75000);
          break;
        default:
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 1800, 6500);
      }

      // Filter data based on search query if provided
      let detailsData = Array.from({ length: 20 }, (_, i) => ({
        id: `CUST${String(3000 + i).padStart(4, "0")}`,
        date: this.getRandomDate(),
        customer: `Customer Name ${i + 1}`,
        artwork: `N/A`,
        artist: `N/A`,
        amount: Math.floor(Math.random() * 3000) + 200,
        status: ["Active", "Inactive", "New"][Math.floor(Math.random() * 3)],
      }));

      if (this.state.searchQuery) {
        const query = this.state.searchQuery.toLowerCase();
        detailsData = detailsData.filter(
          (item) =>
            item.id.toLowerCase().includes(query) ||
            item.customer.toLowerCase().includes(query) ||
            item.status.toLowerCase().includes(query)
        );
      }

      return {
        summary: {
          totalRevenue: 29850,
          totalOrders: 112,
          artworksSold: 68,
          newCustomers: 42,
        },
        chartData: {
          revenue: {
            labels: chartLabels,
            data: chartData,
          },
          distribution: {
            labels: ["New Customers", "Returning Customers"],
            data: [35, 65],
          },
        },
        details: detailsData,
        totalItems: detailsData.length > 0 ? 112 : 0,
      };
    },

    /**
     * Mock artist report data
     */
    getMockArtistData() {
      let chartLabels;
      let chartData;

      // Generate different data based on chart period
      switch (this.state.chartPeriod) {
        case "daily":
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 2000, 7500);
          break;
        case "weekly":
          chartLabels = [
            "Week 1",
            "Week 2",
            "Week 3",
            "Week 4",
            "Week 5",
            "Week 6",
            "Week 7",
            "Week 8",
          ];
          chartData = this.generateRandomData(chartLabels.length, 13000, 24000);
          break;
        case "monthly":
          chartLabels = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ];
          chartData = this.generateRandomData(chartLabels.length, 42000, 80000);
          break;
        default:
          chartLabels = this.generateDailyLabels();
          chartData = this.generateRandomData(chartLabels.length, 2000, 7500);
      }

      // Filter data based on search query if provided
      let detailsData = Array.from({ length: 20 }, (_, i) => ({
        id: `ART${String(4000 + i).padStart(4, "0")}`,
        date: this.getRandomDate(),
        customer: `N/A`,
        artwork: `Artwork ${i + 1}`,
        artist: `Artist Name ${(i % 10) + 1}`,
        amount: Math.floor(Math.random() * 4000) + 300,
        status: ["Active", "Featured", "Exclusive"][
          Math.floor(Math.random() * 3)
        ],
      }));

      if (this.state.searchQuery) {
        const query = this.state.searchQuery.toLowerCase();
        detailsData = detailsData.filter(
          (item) =>
            item.id.toLowerCase().includes(query) ||
            item.artwork.toLowerCase().includes(query) ||
            item.artist.toLowerCase().includes(query) ||
            item.status.toLowerCase().includes(query)
        );
      }

      return {
        summary: {
          totalRevenue: 34250,
          totalOrders: 108,
          artworksSold: 92,
          newCustomers: 31,
        },
        chartData: {
          revenue: {
            labels: chartLabels,
            data: chartData,
          },
          distribution: {
            labels: ["Top Artists", "Mid-tier Artists", "New Artists"],
            data: [55, 30, 15],
          },
        },
        details: detailsData,
        totalItems: detailsData.length > 0 ? 108 : 0,
      };
    },

    /**
     * Update the summary statistics cards
     */
    updateSummaryStats(summaryData) {
      this.elements.totalRevenue.textContent = `$${summaryData.totalRevenue.toLocaleString()}`;
      this.elements.totalOrders.textContent =
        summaryData.totalOrders.toLocaleString();
      this.elements.artworksSold.textContent =
        summaryData.artworksSold.toLocaleString();
      this.elements.newCustomers.textContent =
        summaryData.newCustomers.toLocaleString();
    },

    /**
     * Update all charts with new data
     */
    updateCharts(reportData) {
      this.updateRevenueChart(reportData.chartData.revenue);
      this.updateDistributionChart(reportData.chartData.distribution);
    },

    /**
     * Update the revenue chart with new data
     */
    updateRevenueChart(chartData) {
      if (this.charts.revenueChart) {
        this.charts.revenueChart.destroy();
      }

      const canvas = this.elements.revenueChart;
      const containerWidth = canvas.parentElement.clientWidth;

      // Create a new chart with improved responsive options
      this.charts.revenueChart = new Chart(canvas, {
        type: "line",
        data: {
          labels: chartData ? chartData.labels : [],
          datasets: [
            {
              label: "Revenue",
              backgroundColor: "rgba(78, 115, 223, 0.05)",
              borderColor: "rgba(78, 115, 223, 1)",
              pointRadius: containerWidth < 576 ? 2 : 3,
              pointBackgroundColor: "rgba(78, 115, 223, 1)",
              pointBorderColor: "rgba(78, 115, 223, 1)",
              pointHoverRadius: containerWidth < 576 ? 3 : 5,
              pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
              pointHoverBorderColor: "rgba(78, 115, 223, 1)",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: chartData ? chartData.data : [],
              lineTension: 0.3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: window.innerWidth < 768 ? 500 : 1000, // Faster animations on mobile
          },
          onResize: (chart, size) => {
            // Implement better resize handling with RAF for smoother transitions
            if (this.chartResizeRAF) {
              cancelAnimationFrame(this.chartResizeRAF);
            }

            this.chartResizeRAF = requestAnimationFrame(() => {
              this.updateChartResponsiveness(chart, true);
            });
          },
          layout: {
            padding: {
              left: containerWidth < 576 ? 5 : 10,
              right: containerWidth < 576 ? 10 : 25,
              top: containerWidth < 576 ? 15 : 25,
              bottom: containerWidth < 576 ? 10 : 20,
            },
          },
          scales: {
            x: {
              ticks: {
                maxTicksLimit:
                  containerWidth < 768 ? 5 : containerWidth < 992 ? 7 : 10,
                font: {
                  size: containerWidth < 576 ? 10 : 12,
                },
                padding: containerWidth < 576 ? 5 : 10,
              },
              grid: {
                drawBorder: false,
                borderDash: [2],
                borderDashOffset: [2],
                color: "rgba(0, 0, 0, 0.1)",
                zeroLineColor: "rgba(0, 0, 0, 0.1)",
              },
            },
            y: {
              beginAtZero: true,
              ticks: {
                maxTicksLimit: containerWidth < 576 ? 5 : 8,
                padding: 10,
                font: {
                  size: containerWidth < 576 ? 10 : 12,
                },
                callback: function (value) {
                  return "$" + value.toLocaleString();
                },
              },
              grid: {
                color: "rgba(0, 0, 0, 0.1)",
                drawBorder: false,
                borderDash: [2],
                borderDashOffset: [2],
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
            tooltip: {
              backgroundColor: "rgb(255, 255, 255)",
              bodyColor: "#858796",
              titleMarginBottom: 10,
              titleColor: "#6e707e",
              titleFont: {
                size: containerWidth < 576 ? 12 : 14,
              },
              bodyFont: {
                size: containerWidth < 576 ? 11 : 14,
              },
              borderColor: "#dddfeb",
              borderWidth: 1,
              xPadding: containerWidth < 576 ? 10 : 15,
              yPadding: containerWidth < 576 ? 10 : 15,
              displayColors: false,
              caretPadding: 10,
              callbacks: {
                label: function (context) {
                  const value = context.parsed.y;
                  return "Revenue: $" + value.toLocaleString();
                },
              },
            },
          },
        },
      });

      // Apply responsiveness settings based on current window size
      this.updateChartResponsiveness(this.charts.revenueChart, true);
    },

    /**
     * Update the distribution chart
     */
    updateDistributionChart(chartData) {
      if (this.charts.distributionChart) {
        this.charts.distributionChart.destroy();
      }

      const canvas = this.elements.distributionChart;
      const containerWidth = canvas.parentElement.clientWidth;

      // Create new chart with improved responsive options
      this.charts.distributionChart = new Chart(canvas, {
        type: "doughnut",
        data: {
          labels: chartData.labels,
          datasets: [
            {
              data: chartData.data,
              backgroundColor: [
                "#4e73df",
                "#1cc88a",
                "#36b9cc",
                "#f6c23e",
                "#e74a3b",
                "#858796",
              ],
              hoverBackgroundColor: [
                "#2e59d9",
                "#17a673",
                "#2c9faf",
                "#dda20a",
                "#be2617",
                "#60616f",
              ],
              hoverBorderColor: "rgba(234, 236, 244, 1)",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          onResize: (chart, size) => {
            // Trigger a proper resize when container dimensions change
            setTimeout(() => {
              this.updateChartResponsiveness(chart, false);
            }, 0);
          },
          cutout: containerWidth < 576 ? "65%" : "70%",
          plugins: {
            legend: {
              display: true,
              position: containerWidth < 768 ? "bottom" : "right",
              labels: {
                font: {
                  size: containerWidth < 576 ? 10 : 12,
                },
                padding: containerWidth < 576 ? 15 : 20,
                boxWidth: containerWidth < 576 ? 10 : 15,
                usePointStyle: true,
                pointStyle: "circle",
              },
            },
            tooltip: {
              backgroundColor: "rgb(255, 255, 255)",
              bodyColor: "#858796",
              borderColor: "#dddfeb",
              borderWidth: 1,
              caretPadding: 10,
              displayColors: false,
              callbacks: {
                label: function (context) {
                  const value = context.parsed;
                  const total = context.dataset.data.reduce(
                    (acc, val) => acc + val,
                    0
                  );
                  const percentage = Math.round((value / total) * 100);
                  return `${
                    context.label
                  }: ${percentage}% ($${value.toLocaleString()})`;
                },
              },
            },
          },
          elements: {
            arc: {
              borderWidth: 1,
            },
          },
          layout: {
            padding: {
              left: containerWidth < 576 ? 5 : 10,
              right: containerWidth < 576 ? 5 : 10,
              top: containerWidth < 576 ? 15 : 25,
              bottom: containerWidth < 576 ? 10 : 20,
            },
          },
        },
      });

      // Apply responsiveness settings based on current window size
      this.updateChartResponsiveness(this.charts.distributionChart, false);
    },

    /**
     * Lighten or darken a color
     */
    lightenDarkenColor(color, amount) {
      let usePound = false;

      if (color[0] === "#") {
        color = color.slice(1);
        usePound = true;
      }

      const num = parseInt(color, 16);

      let r = (num >> 16) + amount;
      r = Math.max(Math.min(255, r), 0).toString(16).padStart(2, "0");

      let g = ((num >> 8) & 0x00ff) + amount;
      g = Math.max(Math.min(255, g), 0).toString(16).padStart(2, "0");

      let b = (num & 0x0000ff) + amount;
      b = Math.max(Math.min(255, b), 0).toString(16).padStart(2, "0");

      return (usePound ? "#" : "") + r + g + b;
    },

    /**
     * Update the report details table
     */
    updateReportTable(detailsData) {
      // Clear table
      this.elements.tableBody.innerHTML = "";

      // Add data rows or show empty state
      if (detailsData.length === 0) {
        const emptyRow = document.createElement("tr");
        emptyRow.innerHTML = `
          <td colspan="7" class="text-center py-4">
            <div class="empty-state">
              <i class="fas fa-search fa-3x mb-3"></i>
              <p>No results found for "${this.state.searchQuery}"</p>
              <button class="btn btn-outline-primary btn-sm mt-2" id="clear-search">
                Clear search
              </button>
            </div>
          </td>
        `;

        this.elements.tableBody.appendChild(emptyRow);

        // Add event listener to the clear search button
        document
          .getElementById("clear-search")
          .addEventListener("click", () => {
            this.state.searchQuery = "";
            this.elements.searchInput.value = "";
            this.generateReport();
          });
      } else {
        detailsData.forEach((item) => {
          const row = document.createElement("tr");

          row.innerHTML = `
            <td>${item.date}</td>
            <td>${item.id}</td>
            <td>${item.customer}</td>
            <td>${item.artwork}</td>
            <td>${item.artist}</td>
            <td>$${item.amount.toLocaleString()}</td>
            <td>
              <span class="status-badge status-${item.status
                .toLowerCase()
                .replace(" ", "-")}">
                ${item.status}
              </span>
            </td>
          `;

          this.elements.tableBody.appendChild(row);
        });
      }

      // Update pagination
      this.updatePagination(this.state.totalItems);
    },

    /**
     * Update pagination controls
     */
    updatePagination(totalItems) {
      const totalPages = Math.ceil(totalItems / this.state.itemsPerPage);
      const paginationElement = this.elements.pagination;

      // Clear pagination
      paginationElement.innerHTML = "";

      // Don't show pagination if no items or only one page
      if (totalItems === 0 || totalPages <= 1) {
        return;
      }

      // Previous button
      const prevLi = document.createElement("li");
      prevLi.className = `page-item ${
        this.state.currentPage === 1 ? "disabled" : ""
      }`;
      prevLi.innerHTML = `<a class="page-link" href="#" tabindex="-1" aria-disabled="${
        this.state.currentPage === 1
      }">Previous</a>`;
      prevLi.addEventListener("click", (e) => {
        e.preventDefault();
        if (this.state.currentPage > 1) {
          this.state.currentPage--;
          this.generateReport();
        }
      });
      paginationElement.appendChild(prevLi);

      // Page numbers
      const startPage = Math.max(1, this.state.currentPage - 2);
      const endPage = Math.min(totalPages, startPage + 4);

      for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement("li");
        pageLi.className = `page-item ${
          i === this.state.currentPage ? "active" : ""
        }`;
        pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        pageLi.addEventListener("click", (e) => {
          e.preventDefault();
          this.state.currentPage = i;
          this.generateReport();
        });
        paginationElement.appendChild(pageLi);
      }

      // Next button
      const nextLi = document.createElement("li");
      nextLi.className = `page-item ${
        this.state.currentPage === totalPages ? "disabled" : ""
      }`;
      nextLi.innerHTML = `<a class="page-link" href="#" aria-disabled="${
        this.state.currentPage === totalPages
      }">Next</a>`;
      nextLi.addEventListener("click", (e) => {
        e.preventDefault();
        if (this.state.currentPage < totalPages) {
          this.state.currentPage++;
          this.generateReport();
        }
      });
      paginationElement.appendChild(nextLi);
    },

    /**
     * Export report as CSV
     */
    exportReport() {
      // Get current date for filename
      const now = new Date();
      const dateStr = this.formatDateForInput(now).replace(/-/g, "");

      // Create filename based on report type
      const filename = `${this.state.reportType}_report_${dateStr}.csv`;

      // Show loading state
      this.elements.exportBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
      this.elements.exportBtn.disabled = true;

      // Simulate API call delay
      setTimeout(() => {
        try {
          // Get table headers
          const headers = Array.from(
            document.querySelector(".admin-table thead tr").children
          ).map((th) => th.innerText.trim());

          // Get table data
          const rows = Array.from(
            this.elements.tableBody.querySelectorAll("tr")
          ).map((row) => {
            return Array.from(row.querySelectorAll("td")).map((cell) => {
              // Remove currency symbols and clean text
              return cell.innerText.replace("$", "").replace(/,/g, "").trim();
            });
          });

          // Create CSV content
          let csvContent = headers.join(",") + "\n";
          rows.forEach((row) => {
            csvContent += row.join(",") + "\n";
          });

          // Create download link
          const blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;",
          });
          const url = URL.createObjectURL(blob);
          const link = document.createElement("a");
          link.setAttribute("href", url);
          link.setAttribute("download", filename);
          link.style.visibility = "hidden";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);

          // Show success message
          alert(`Report exported successfully as ${filename}`);
        } catch (error) {
          console.error("Error exporting report:", error);
          alert("Failed to export report. Please try again.");
        } finally {
          // Reset button state
          this.elements.exportBtn.innerHTML =
            '<i class="fas fa-download me-2"></i>Export';
          this.elements.exportBtn.disabled = false;
        }
      }, 1000);
    },

    mounted() {
      // Initialize data and fetch reports
      this.fetchReportData();

      // Add window resize listener for chart responsiveness
      window.addEventListener("resize", this.handleWindowResize);

      // Initial layout adjustment
      this.$nextTick(() => {
        this.adjustCardLayout();
      });
    },

    beforeDestroy() {
      // Clean up resize listener when component is destroyed
      window.removeEventListener("resize", this.handleWindowResize);

      // Clear any pending resize timeouts
      if (this.resizeTimeout) {
        clearTimeout(this.resizeTimeout);
      }
    },

    /**
     * Update the sales chart with new data
     */
    updateSalesChart(chartData) {
      if (this.charts.salesChart) {
        this.charts.salesChart.destroy();
      }

      const canvas = this.elements.salesChart;
      const containerWidth = canvas.parentElement.clientWidth;

      // Create a new chart with improved responsive options
      this.charts.salesChart = new Chart(canvas, {
        type: "bar",
        data: {
          labels: chartData ? chartData.labels : [],
          datasets: [
            {
              label: "Sales",
              backgroundColor: "rgba(28, 200, 138, 0.8)",
              borderColor: "rgba(28, 200, 138, 1)",
              borderWidth: containerWidth < 576 ? 1 : 2,
              hoverBackgroundColor: "rgba(28, 200, 138, 1)",
              hoverBorderColor: "rgba(28, 200, 138, 1)",
              data: chartData ? chartData.data : [],
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: window.innerWidth < 768 ? 500 : 1000, // Faster animations on mobile
          },
          onResize: (chart, size) => {
            // Implement better resize handling with RAF for smoother transitions
            if (this.chartResizeRAF) {
              cancelAnimationFrame(this.chartResizeRAF);
            }

            this.chartResizeRAF = requestAnimationFrame(() => {
              this.updateChartResponsiveness(chart, true);
            });
          },
          // ... rest of options remain the same
        },
      });

      // Apply responsiveness settings based on current window size
      this.updateChartResponsiveness(this.charts.salesChart, true);
    },

    /**
     * Detect device capabilities to optimize chart rendering
     */
    detectDeviceCapabilities() {
      // Check if this is a mobile device
      const isMobile =
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
          navigator.userAgent
        );

      // Try to detect hardware capabilities
      const isLowPowered =
        navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;

      // Check network connection quality if available
      const isSlowConnection =
        navigator.connection &&
        (navigator.connection.saveData ||
          ["slow-2g", "2g", "3g"].includes(navigator.connection.effectiveType));

      // Store detected capabilities
      this.deviceCapabilities = {
        isMobile,
        isLowPowered,
        isSlowConnection,
        devicePixelRatio: window.devicePixelRatio || 1,
        prefersReducedMotion: window.matchMedia(
          "(prefers-reduced-motion: reduce)"
        ).matches,
      };

      // Apply performance optimizations based on capabilities
      this.applyPerformanceOptimizations();

      // Listen for reduced motion preference changes
      window
        .matchMedia("(prefers-reduced-motion: reduce)")
        .addEventListener("change", (e) => {
          this.deviceCapabilities.prefersReducedMotion = e.matches;
          this.applyPerformanceOptimizations();
        });
    },

    /**
     * Apply performance optimizations based on device capabilities
     */
    applyPerformanceOptimizations() {
      const { isMobile, isLowPowered, isSlowConnection, prefersReducedMotion } =
        this.deviceCapabilities;

      // Base animation duration based on device capabilities
      let animationDuration = 750; // Default

      if (prefersReducedMotion) {
        animationDuration = 0; // Disable animations for users who prefer reduced motion
      } else if (isLowPowered || isSlowConnection) {
        animationDuration = 300; // Shorter animations for low-power devices
      } else if (isMobile) {
        animationDuration = 500; // Moderate animations for mobile devices
      }

      // Apply to existing charts
      if (this.charts.revenueChart) {
        this.charts.revenueChart.options.animation.duration = animationDuration;

        // Reduce data points for performance on low-power devices
        if (isLowPowered && this.charts.revenueChart.data.labels.length > 15) {
          this.simplifyChartData(this.charts.revenueChart);
        }

        this.charts.revenueChart.update("none");
      }

      if (this.charts.distributionChart) {
        this.charts.distributionChart.options.animation.duration =
          animationDuration;
        this.charts.distributionChart.update("none");
      }

      // Optimize rendering frequency on scroll for low-power devices
      if (isLowPowered || isSlowConnection) {
        this.optimizeScrollHandling();
      }
    },

    /**
     * Simplify chart data for better performance on low-power devices
     * @param {Chart} chart - The chart to simplify
     */
    simplifyChartData(chart) {
      if (
        !chart ||
        !chart.data ||
        !chart.data.labels ||
        chart.data.labels.length <= 15
      ) {
        return; // No need to simplify
      }

      // For longer datasets, sample fewer points on low-power devices
      const originalLabels = [...chart.data.labels];
      const originalData = [...chart.data.datasets[0].data];

      // Determine the sampling rate based on the data length
      const length = originalLabels.length;
      let samplingRate = 1;

      if (length > 60) {
        samplingRate = 6;
      } else if (length > 30) {
        samplingRate = 3;
      } else if (length > 15) {
        samplingRate = 2;
      }

      // Sample the data at the determined rate
      const newLabels = [];
      const newData = [];

      for (let i = 0; i < length; i += samplingRate) {
        newLabels.push(originalLabels[i]);
        newData.push(originalData[i]);
      }

      // Ensure we include the last data point for continuity
      if (
        samplingRate > 1 &&
        newLabels[newLabels.length - 1] !== originalLabels[length - 1]
      ) {
        newLabels.push(originalLabels[length - 1]);
        newData.push(originalData[length - 1]);
      }

      // Update the chart with simplified data
      chart.data.labels = newLabels;
      chart.data.datasets[0].data = newData;

      // Store original data for restoration if needed
      chart._originalData = {
        labels: originalLabels,
        data: originalData,
      };

      // Add indicator that data is simplified
      if (!chart._dataSimplified) {
        chart._dataSimplified = true;

        // Add visual indicator to chart
        const chartCard = chart.canvas.closest(".chart-card");
        if (chartCard) {
          const indicator = document.createElement("div");
          indicator.className = "simplified-data-indicator";
          indicator.innerHTML =
            '<small><i class="fas fa-info-circle"></i> Optimized view</small>';
          indicator.style.position = "absolute";
          indicator.style.top = "10px";
          indicator.style.right = "10px";
          indicator.style.padding = "2px 5px";
          indicator.style.background = "rgba(0, 0, 0, 0.05)";
          indicator.style.borderRadius = "3px";
          indicator.style.fontSize = "10px";
          indicator.style.cursor = "pointer";

          // Toggle between simplified and full data
          indicator.addEventListener("click", () => {
            if (chart._showingFullData) {
              // Switch back to simplified
              chart.data.labels = newLabels;
              chart.data.datasets[0].data = newData;
              chart._showingFullData = false;
              indicator.innerHTML =
                '<small><i class="fas fa-info-circle"></i> Optimized view</small>';
            } else {
              // Switch to full data
              chart.data.labels = chart._originalData.labels;
              chart.data.datasets[0].data = chart._originalData.data;
              chart._showingFullData = true;
              indicator.innerHTML =
                '<small><i class="fas fa-info-circle"></i> Full data view</small>';
            }
            chart.update();
          });

          chartCard.appendChild(indicator);
        }
      }
    },

    /**
     * Optimize scroll handling for better performance
     */
    optimizeScrollHandling() {
      // If we already set up optimized scrolling, don't do it again
      if (this._optimizedScrolling) return;

      // Track current scroll timeout
      let scrollTimeout;
      // Store charts visibility state
      let chartsVisible = true;

      // Use passive scroll listener for better performance
      window.addEventListener(
        "scroll",
        () => {
          // If charts are currently visible, temporarily hide them during rapid scrolling
          if (chartsVisible && !scrollTimeout) {
            // Hide chart canvases during scroll for better performance
            document
              .querySelectorAll(".chart-card canvas")
              .forEach((canvas) => {
                canvas.style.opacity = "0.1";
              });
            chartsVisible = false;
          }

          // Clear existing timeout
          if (scrollTimeout) {
            clearTimeout(scrollTimeout);
          }

          // Set new timeout to restore charts after scrolling stops
          scrollTimeout = setTimeout(() => {
            // Restore chart visibility
            document
              .querySelectorAll(".chart-card canvas")
              .forEach((canvas) => {
                canvas.style.opacity = "1";
              });
            chartsVisible = true;
            scrollTimeout = null;

            // Check if charts need to be redrawn
            this.checkVisibleCharts();
          }, 150);
        },
        { passive: true }
      );

      this._optimizedScrolling = true;
    },

    /**
     * Check which charts are visible and update them accordingly
     */
    checkVisibleCharts() {
      document.querySelectorAll(".chart-card canvas").forEach((canvas) => {
        const rect = canvas.getBoundingClientRect();
        const isVisible =
          rect.top <=
            (window.innerHeight || document.documentElement.clientHeight) &&
          rect.bottom >= 0;

        if (isVisible) {
          const chartId = canvas.id;
          if (chartId === "revenue-chart" && this.charts.revenueChart) {
            this.charts.revenueChart.update("none");
          } else if (
            chartId === "distribution-chart" &&
            this.charts.distributionChart
          ) {
            this.charts.distributionChart.update("none");
          }
        }
      });
    },

    /**
     * Initialize and enhance chart accessibility
     */
    enhanceChartAccessibility() {
      // Add keyboard navigation to charts
      document.querySelectorAll(".chart-card").forEach((card) => {
        const canvas = card.querySelector("canvas");
        if (!canvas) return;

        // Make canvas focusable
        canvas.setAttribute("tabindex", "0");
        canvas.setAttribute("role", "img");

        // Add ARIA labels
        if (canvas.id === "revenue-chart") {
          canvas.setAttribute(
            "aria-label",
            "Revenue trend chart. Use arrow keys to navigate data points."
          );
        } else if (canvas.id === "distribution-chart") {
          canvas.setAttribute(
            "aria-label",
            "Distribution chart showing sales by category. Use arrow keys to navigate segments."
          );
        }

        // Add keyboard navigation
        canvas.addEventListener("keydown", (e) => {
          // Find the relevant chart
          let chart = null;
          if (canvas.id === "revenue-chart") {
            chart = this.charts.revenueChart;
          } else if (canvas.id === "distribution-chart") {
            chart = this.charts.distributionChart;
          }

          if (!chart) return;

          // Handle keyboard navigation
          switch (e.key) {
            case "ArrowRight":
              this.highlightNextChartElement(chart, true);
              e.preventDefault();
              break;
            case "ArrowLeft":
              this.highlightNextChartElement(chart, false);
              e.preventDefault();
              break;
            case "Enter":
            case " ":
              // Toggle data point details
              if (chart._highlightedIndex !== undefined) {
                this.announceChartDataPoint(chart, chart._highlightedIndex);
              }
              e.preventDefault();
              break;
          }
        });
      });
    },

    /**
     * Highlight the next element in a chart for keyboard navigation
     * @param {Chart} chart - The chart object
     * @param {boolean} next - Whether to move to the next (true) or previous (false) element
     */
    highlightNextChartElement(chart, next) {
      if (
        !chart ||
        !chart.data ||
        !chart.data.datasets ||
        !chart.data.datasets.length
      ) {
        return;
      }

      // Initialize highlighted index if not set
      if (chart._highlightedIndex === undefined) {
        chart._highlightedIndex = next ? 0 : chart.data.labels.length - 1;
      } else {
        // Move to next/previous index
        chart._highlightedIndex = next
          ? (chart._highlightedIndex + 1) % chart.data.labels.length
          : (chart._highlightedIndex - 1 + chart.data.labels.length) %
            chart.data.labels.length;
      }

      // Visually highlight the element
      const meta = chart.getDatasetMeta(0);
      const element = meta.data[chart._highlightedIndex];

      // Create highlight effect
      if (chart._currentHighlight) {
        chart._currentHighlight.destroy();
      }

      // Store original style
      const originalBorderColor = chart.data.datasets[0].borderColor;
      const originalBackgroundColor = chart.data.datasets[0].backgroundColor;

      // Apply highlight
      if (chart.config.type === "line") {
        // For line chart, highlight the specific point
        chart._currentHighlight = {
          destroy: () => {
            chart.data.datasets[0].pointBackgroundColor =
              originalBackgroundColor;
            chart.data.datasets[0].pointBorderColor = originalBorderColor;
            chart.update();
          },
        };

        // Set point colors
        const pointColors = Array(chart.data.labels.length).fill(
          originalBackgroundColor
        );
        pointColors[chart._highlightedIndex] = "#ff6b6b";
        chart.data.datasets[0].pointBackgroundColor = pointColors;

        const pointBorderColors = Array(chart.data.labels.length).fill(
          originalBorderColor
        );
        pointBorderColors[chart._highlightedIndex] = "#ff6b6b";
        chart.data.datasets[0].pointBorderColor = pointBorderColors;

        // Increase size of highlighted point
        const pointRadius = Array(chart.data.labels.length).fill(
          chart.data.datasets[0].pointRadius || 3
        );
        pointRadius[chart._highlightedIndex] =
          (chart.data.datasets[0].pointRadius || 3) * 1.5;
        chart.data.datasets[0].pointRadius = pointRadius;
      } else if (
        chart.config.type === "doughnut" ||
        chart.config.type === "pie"
      ) {
        // For doughnut/pie chart, highlight the specific segment
        chart._currentHighlight = {
          destroy: () => {
            chart.update();
          },
        };

        // Trigger hover state on the segment
        const segment = meta.data[chart._highlightedIndex];
        chart.setActiveElements([
          {
            datasetIndex: 0,
            index: chart._highlightedIndex,
          },
        ]);
      }

      chart.update();

      // Announce highlighted element for screen readers
      this.announceChartDataPoint(chart, chart._highlightedIndex);
    },

    /**
     * Announce chart data point for screen readers
     * @param {Chart} chart - The chart object
     * @param {number} index - The index of the data point
     */
    announceChartDataPoint(chart, index) {
      if (!chart || index === undefined || !chart.data || !chart.data.labels)
        return;

      const label = chart.data.labels[index];
      const dataset = chart.data.datasets[0];
      const value = dataset.data[index];

      let announcement = "";

      if (chart.config.type === "line") {
        announcement = `${label}: $${this.formatCurrency(value)}`;
      } else if (
        chart.config.type === "doughnut" ||
        chart.config.type === "pie"
      ) {
        const total = dataset.data.reduce((sum, val) => sum + val, 0);
        const percentage = ((value / total) * 100).toFixed(1);
        announcement = `${label}: ${percentage}% (${this.formatCurrency(
          value
        )})`;
      }

      // Create or update screen reader announcement element
      let announcer = document.getElementById("chart-announcer");
      if (!announcer) {
        announcer = document.createElement("div");
        announcer.id = "chart-announcer";
        announcer.setAttribute("aria-live", "polite");
        announcer.setAttribute("aria-atomic", "true");
        announcer.className = "sr-only";
        document.body.appendChild(announcer);
      }

      announcer.textContent = announcement;
    },

    /**
     * Format currency values
     * @param {number} value - The value to format
     * @returns {string} Formatted currency string
     */
    formatCurrency(value) {
      return value.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    /**
     * Detect device capabilities for better performance tuning
     */
    detectDeviceCapabilities() {
      // Detect if battery API is available to optimize for low power
      if ("getBattery" in navigator) {
        navigator.getBattery().then((battery) => {
          this.deviceInfo = {
            ...(this.deviceInfo || {}),
            batteryLevel: battery.level,
            batteryCharging: battery.charging,
          };

          // Listen for battery changes to adapt chart performance
          battery.addEventListener("levelchange", () => {
            this.deviceInfo.batteryLevel = battery.level;
            this.deviceInfo.batteryCharging = battery.charging;

            // If battery is low and not charging, optimize charts
            if (battery.level < 0.2 && !battery.charging) {
              this.applyLowPowerOptimizations();
            } else if (this.lowPowerMode) {
              // Restore normal operation
              this.lowPowerMode = false;
              this.restoreChartSettings();
            }
          });

          // Initial check for low battery
          if (battery.level < 0.2 && !battery.charging) {
            this.applyLowPowerOptimizations();
          }
        });
      }

      // Check device performance via device memory and hardware concurrency
      this.deviceInfo = {
        ...(this.deviceInfo || {}),
        memory: navigator.deviceMemory || 4, // Default to 4GB if not available
        cores: navigator.hardwareConcurrency || 4, // Default to 4 cores
        isLowPowered:
          (navigator.deviceMemory && navigator.deviceMemory < 4) ||
          (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4),
        connection: navigator.connection
          ? navigator.connection.effectiveType
          : "4g",
        isHighDpi: window.devicePixelRatio > 1.5,
        hasTouchScreen:
          "ontouchstart" in window || navigator.maxTouchPoints > 0,
      };

      // Set performance mode based on device capabilities
      this.performanceMode = this.deviceInfo.isLowPowered ? "low" : "high";

      // Apply performance optimizations
      this.applyPerformanceSettings();

      // Check for print media queries to optimize chart for printing
      this.setupPrintMediaListener();

      // Listen for network status changes
      if (navigator.connection) {
        navigator.connection.addEventListener("change", () => {
          this.deviceInfo.connection = navigator.connection.effectiveType;
          this.applyPerformanceSettings();
        });
      }
    },

    /**
     * Apply performance settings based on device capabilities
     */
    applyPerformanceSettings() {
      // Store performance settings to be applied to charts
      this.chartPerformanceSettings = {
        animation: {
          duration: this.getOptimalAnimationDuration(),
          easing: this.getOptimalEasing(),
        },
        responsiveAnimationDuration: this.getOptimalAnimationDuration() / 2,
        hover: {
          animationDuration: this.getOptimalAnimationDuration() / 3,
        },
        elements: {
          line: {
            tension: this.deviceInfo.isLowPowered ? 0 : 0.4,
            borderWidth: this.deviceInfo.isLowPowered ? 2 : 3,
          },
          point: {
            radius: this.deviceInfo.isLowPowered ? 2 : 3,
            hoverRadius: this.deviceInfo.isLowPowered ? 3 : 5,
          },
          arc: {
            borderWidth: this.deviceInfo.isLowPowered ? 1 : 2,
          },
        },
        // For doughnut/pie charts
        cutoutPercentage: this.deviceInfo.isLowPowered ? 75 : 70,
      };

      // Apply to existing charts
      if (this.charts.revenueChart) {
        Object.assign(
          this.charts.revenueChart.options.animation,
          this.chartPerformanceSettings.animation
        );
        this.charts.revenueChart.update("none");
      }

      if (this.charts.distributionChart) {
        Object.assign(
          this.charts.distributionChart.options.animation,
          this.chartPerformanceSettings.animation
        );
        this.charts.distributionChart.update("none");
      }
    },

    /**
     * Get optimal animation duration based on device capabilities
     */
    getOptimalAnimationDuration() {
      // Base animation duration
      let duration = 1000;

      // Adjust based on device capabilities
      if (this.deviceInfo.isLowPowered) {
        duration = 300;
      } else if (this.deviceInfo.cores < 6) {
        duration = 600;
      }

      // Further reduce duration on slow networks
      if (
        this.deviceInfo.connection === "slow-2g" ||
        this.deviceInfo.connection === "2g"
      ) {
        duration = Math.floor(duration / 2);
      } else if (this.deviceInfo.connection === "3g") {
        duration = Math.floor(duration * 0.7);
      }

      // Battery level adjustment (if available)
      if (
        this.deviceInfo.batteryLevel !== undefined &&
        this.deviceInfo.batteryLevel < 0.3 &&
        !this.deviceInfo.batteryCharging
      ) {
        duration = Math.floor(duration * 0.5);
      }

      return duration;
    },

    /**
     * Get optimal easing function based on device capabilities
     */
    getOptimalEasing() {
      return this.deviceInfo.isLowPowered ? "linear" : "easeOutQuad";
    },

    /**
     * Apply low-power optimizations for charts
     */
    applyLowPowerOptimizations() {
      this.lowPowerMode = true;

      // Apply to all charts
      Object.values(this.charts).forEach((chart) => {
        if (!chart) return;

        // Store original settings for later restoration
        chart._originalSettings = {
          animation: { ...chart.options.animation },
          hover: { ...chart.options.hover },
          responsiveAnimationDuration:
            chart.options.responsiveAnimationDuration,
          events: [...chart.options.events],
        };

        // Disable animations
        chart.options.animation.duration = 0;
        chart.options.hover.animationDuration = 0;
        chart.options.responsiveAnimationDuration = 0;

        // Reduce event handling to essential only
        chart.options.events = ["click", "mouseleave", "touchend"];

        // Update chart without animation
        chart.update("none");
      });

      // Reduce chart canvas quality
      document.querySelectorAll(".chart-card canvas").forEach((canvas) => {
        canvas.style.imageRendering = "optimizeSpeed";
      });
    },

    /**
     * Restore normal chart settings after low power mode
     */
    restoreChartSettings() {
      // Restore original settings for all charts
      Object.values(this.charts).forEach((chart) => {
        if (!chart || !chart._originalSettings) return;

        // Restore stored settings
        chart.options.animation = chart._originalSettings.animation;
        chart.options.hover = chart._originalSettings.hover;
        chart.options.responsiveAnimationDuration =
          chart._originalSettings.responsiveAnimationDuration;
        chart.options.events = chart._originalSettings.events;

        // Update chart without animation
        chart.update("none");

        // Clean up stored settings
        delete chart._originalSettings;
      });

      // Restore canvas quality
      document.querySelectorAll(".chart-card canvas").forEach((canvas) => {
        canvas.style.imageRendering = "auto";
      });
    },

    /**
     * Enhance chart accessibility
     */
    enhanceChartAccessibility() {
      // Add event listeners for keyboard navigation
      document.querySelectorAll(".chart-card").forEach((card) => {
        // Make chart container focusable
        card.setAttribute("tabindex", "0");

        // Add appropriate ARIA roles and properties
        card.setAttribute("role", "figure");
        const chartTitle =
          card.querySelector(".chart-header h3")?.textContent || "Chart";
        card.setAttribute("aria-label", `${chartTitle} - Interactive chart`);

        // Add tooltip for screen readers
        const tooltip = document.createElement("div");
        tooltip.className = "sr-only chart-tooltip";
        tooltip.setAttribute("aria-live", "polite");
        card.appendChild(tooltip);

        // Add keyboard handlers for chart exploration
        card.addEventListener("keydown", (e) => {
          const canvas = card.querySelector("canvas");
          if (!canvas) return;

          const chartId = canvas.id;
          let chart = null;

          if (chartId === "revenue-chart") {
            chart = this.charts.revenueChart;
          } else if (chartId === "distribution-chart") {
            chart = this.charts.distributionChart;
          }

          if (!chart) return;

          // Handle arrow keys for chart exploration
          if (e.key.startsWith("Arrow")) {
            e.preventDefault();

            // Update accessibility text based on chart type and key pressed
            if (chartId === "revenue-chart") {
              this.exploreLineChartWithKeyboard(chart, e.key, tooltip);
            } else if (chartId === "distribution-chart") {
              this.exploreDoughnutChartWithKeyboard(chart, e.key, tooltip);
            }
          }
        });
      });
    },

    /**
     * Enable keyboard exploration of line charts
     */
    exploreLineChartWithKeyboard(chart, key, tooltipElement) {
      // Current focused point index
      if (chart._keyboardFocusIndex === undefined) {
        chart._keyboardFocusIndex = 0;
      }

      // Handle left/right navigation through data points
      if (key === "ArrowRight") {
        chart._keyboardFocusIndex = Math.min(
          chart._keyboardFocusIndex + 1,
          chart.data.labels.length - 1
        );
      } else if (key === "ArrowLeft") {
        chart._keyboardFocusIndex = Math.max(chart._keyboardFocusIndex - 1, 0);
      }

      // Get data for focused point
      const index = chart._keyboardFocusIndex;
      const label = chart.data.labels[index];
      const value = chart.data.datasets[0].data[index];

      // Update tooltip text
      tooltipElement.textContent = `${label}: ${value}`;

      // Highlight the point visually
      chart.setActiveElements([
        {
          datasetIndex: 0,
          index: index,
        },
      ]);
      chart.update();
    },

    /**
     * Enable keyboard exploration of doughnut charts
     */
    exploreDoughnutChartWithKeyboard(chart, key, tooltipElement) {
      // Current focused segment index
      if (chart._keyboardFocusIndex === undefined) {
        chart._keyboardFocusIndex = 0;
      }

      // Handle left/right/up/down navigation through segments
      if (key === "ArrowRight" || key === "ArrowDown") {
        chart._keyboardFocusIndex =
          (chart._keyboardFocusIndex + 1) % chart.data.labels.length;
      } else if (key === "ArrowLeft" || key === "ArrowUp") {
        chart._keyboardFocusIndex =
          (chart._keyboardFocusIndex - 1 + chart.data.labels.length) %
          chart.data.labels.length;
      }

      // Get data for focused segment
      const index = chart._keyboardFocusIndex;
      const label = chart.data.labels[index];
      const value = chart.data.datasets[0].data[index];

      // Update tooltip text
      tooltipElement.textContent = `${label}: ${value}`;

      // Highlight the segment visually
      chart.setActiveElements([
        {
          datasetIndex: 0,
          index: index,
        },
      ]);
      chart.update();
    },

    /**
     * Setup media query listener for print mode
     */
    setupPrintMediaListener() {
      // Create a media query that targets print mode
      const mediaQueryList = window.matchMedia("print");

      // Handle print media changes
      const handlePrintChange = (mql) => {
        if (mql.matches) {
          // Entering print mode
          this.optimizeChartsForPrinting();
        } else {
          // Exiting print mode
          this.restoreChartsFromPrinting();
        }
      };

      // Add listener
      mediaQueryList.addEventListener("change", handlePrintChange);

      // Also add beforeprint/afterprint listeners as fallback
      window.addEventListener("beforeprint", () =>
        this.optimizeChartsForPrinting()
      );
      window.addEventListener("afterprint", () =>
        this.restoreChartsFromPrinting()
      );
    },

    /**
     * Optimize charts for printing
     */
    optimizeChartsForPrinting() {
      // Store original settings
      this.printOriginalSettings = {};

      // Modify all charts for better print rendering
      Object.entries(this.charts).forEach(([chartName, chart]) => {
        if (!chart) return;

        // Store original settings
        this.printOriginalSettings[chartName] = {
          legends: { ...chart.options.plugins.legend },
          tooltips: { ...chart.options.plugins.tooltip },
          animation: { ...chart.options.animation },
          scales: chart.options.scales
            ? JSON.parse(JSON.stringify(chart.options.scales))
            : null,
          backgroundColor: [...chart.data.datasets[0].backgroundColor],
        };

        // Ensure legends are visible for printing
        chart.options.plugins.legend.display = true;

        // Disable tooltips
        chart.options.plugins.tooltip.enabled = false;

        // Disable animations
        chart.options.animation.duration = 0;

        // Increase font sizes for better legibility
        if (chart.options.scales) {
          Object.values(chart.options.scales).forEach((scale) => {
            if (scale.ticks) {
              scale.ticks.font = scale.ticks.font || {};
              scale.ticks.font.size = 12;
            }
          });
        }

        // Use print-friendly colors
        const dataset = chart.data.datasets[0];
        if (Array.isArray(dataset.backgroundColor)) {
          // For pie/doughnut charts
          dataset.backgroundColor = dataset.backgroundColor.map(
            (color, index) => {
              // Convert to a print-friendly pattern or darker shade
              return index % 2 === 0 ? color : this.darkenColor(color, 0.2);
            }
          );
        } else {
          // For line/bar charts
          dataset.backgroundColor = this.adjustColorForPrinting(
            dataset.backgroundColor
          );
          if (dataset.borderColor) {
            dataset.borderColor = this.adjustColorForPrinting(
              dataset.borderColor
            );
          }
        }

        // Update chart without animation
        chart.update("none");
      });

      // Add a print-specific stylesheet
      const style = document.createElement("style");
      style.id = "chart-print-styles";
      style.textContent = `
        @media print {
          .chart-card {
            page-break-inside: avoid;
            break-inside: avoid;
            height: auto !important;
            min-height: 300px;
          }
          .chart-header, .chart-filters {
            display: block !important;
          }
          .chart-header h3 {
            font-size: 16px !important;
            margin-bottom: 10px !important;
          }
          canvas {
            max-height: 400px !important;
          }
        }
      `;
      document.head.appendChild(style);
    },

    /**
     * Restore charts after printing
     */
    restoreChartsFromPrinting() {
      // If no stored settings, do nothing
      if (!this.printOriginalSettings) return;

      // Remove print stylesheet
      const style = document.getElementById("chart-print-styles");
      if (style) style.remove();

      // Restore each chart to original settings
      Object.entries(this.charts).forEach(([chartName, chart]) => {
        if (!chart || !this.printOriginalSettings[chartName]) return;

        const original = this.printOriginalSettings[chartName];

        // Restore legends
        chart.options.plugins.legend = original.legends;

        // Restore tooltips
        chart.options.plugins.tooltip = original.tooltips;

        // Restore animation
        chart.options.animation = original.animation;

        // Restore scales if they exist
        if (original.scales && chart.options.scales) {
          chart.options.scales = original.scales;
        }

        // Restore colors
        chart.data.datasets[0].backgroundColor = original.backgroundColor;

        // Update chart without animation
        chart.update("none");
      });

      // Clean up stored settings
      this.printOriginalSettings = null;
    },

    /**
     * Darken a color for print-friendly rendering
     */
    darkenColor(color, amount) {
      // Simple color darkening for print visibility
      if (color.startsWith("#")) {
        return color; // Keep hex colors as is
      } else if (color.startsWith("rgb")) {
        // Parse RGB values
        const rgb = color.match(/\d+/g).map(Number);
        // Darken each component
        const darkerRgb = rgb.map((c) =>
          Math.max(0, Math.floor(c * (1 - amount)))
        );
        return `rgb(${darkerRgb.join(",")})`;
      }
      return color;
    },

    /**
     * Adjust color for print-friendly rendering
     */
    adjustColorForPrinting(color) {
      // Ensure high contrast for printing
      if (!color) return "#000000";

      if (color.startsWith("rgba")) {
        // Make fully opaque for printing
        return color.replace(/rgba\((\d+,\s*\d+,\s*\d+),[^)]+\)/, "rgb($1)");
      }

      return color;
    },
  };

  // Initialize the module
  reportsModule.init();
});
