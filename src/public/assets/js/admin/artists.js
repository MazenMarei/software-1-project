/**
 * Admin Artists Management functionality
 */
$(document).ready(function () {
  // Initialize DataTable for artists
  let columnIndex =
    document.querySelectorAll(".search-table tr th")[4].innerText === "Status"
      ? 4
      : 5;

  const artistsTable = $(".search-table").DataTable({
    responsive: true,
    order: [[columnIndex + 1, "desc"]], // Sort by join date by default
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search By Name...",
      lengthMenu: "Show _MENU_  per page",
      info: "Showing _START_ to _END_ of _TOTAL_ items",
      infoEmpty: "No data found",
      infoFiltered: "(filtered from _MAX_ total items)",
    },
    columnDefs: [
      { orderable: false, targets: [0, 6] }, // Disable sorting for image and actions columns
    ],
  });

  // Status filter
  $("#statusFilter").on("change", function () {
    const status = $(this).val();

    artistsTable.column(columnIndex).search(status).draw();
  });

  $("#categoryFilter").on("change", function () {
    const category = $(this).val();
    artistsTable.column(3).search(category).draw();
  });

  // Approval button handler

  // Rejection button handler
  window.rejectionFun = function (element) {
    const id = $(element).data("id");
    const name = $(element).data("name");

    $("#rejectionItemId").val(id);
    $("#rejectionItemName").text(name);

    const rejectionModal = new bootstrap.Modal(
      document.getElementById("rejectionModal")
    );
    rejectionModal.show();
  };

  window.approvalFun = function (element) {
    const id = $(element).data("id");
    const name = $(element).data("name");

    $("#approvalItemId").val(id);
    $("#approvalItemName").text(name);

    const approvalModal = new bootstrap.Modal(
      document.getElementById("approvalModal")
    );
    approvalModal.show();
  };

  window.banFun = function (element) {
    console.log("Ban button clicked", element);

    const id = $(element).data("id");
    const name = $(element).data("name");

    $("#banArtistId").val(id);
    $("#banArtistName").text(name);

    const banModal = new bootstrap.Modal(
      document.getElementById("banArtistModal")
    );
    banModal.show();
  };

  window.unbanFun = function (element) {
    console.log("Unban button clicked", element);

    const id = $(element).data("id");
    const name = $(element).data("name");

    $("#unbanArtistId").val(id);
    $("#unbanArtistName").text(name);

    const unbanModal = new bootstrap.Modal(
      document.getElementById("unbanArtistModal")
    );
    unbanModal.show();
  };

  window.showDetails = function (element) {
    const id = $(element).data("id");
    const name = $(element).data("name");

    $("#showDetailsArtistId").val(id);
    $("#showDetailsArtistName").text(name);

    const showDetailsModal = new bootstrap.Modal(
      document.getElementById("showDetailsModal")
    );
    showDetailsModal.show();
  };

  // When any modal is hidden
  $(".modal").on("hidden.bs.modal", function () {
    // Make sure the backdrop is removed
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
    $("body").css("padding-right", "");
  });

  // Additional fix for the Cancel button
  $(".modal .btn-secondary").on("click", function () {
    // Ensure modal is completely hidden
    $(this).closest(".modal").modal("hide");
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open");
    $("body").css("padding-right", "");
  });
  // Configure toastr options
  // toastr.options = {
  //   closeButton: true,
  //   progressBar: true,
  //   positionClass: "toast-top-right",
  //   timeOut: 3000,
  // };
});
