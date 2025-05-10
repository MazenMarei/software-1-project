/**
 * Admin Artists Management functionality
 */
$(document).ready(function () {
  // Initialize DataTable for artists
  const SortColumn = document.querySelectorAll(".search-table tr th")[4]; // Default sort column index
  let columnIndex = SortColumn
    ? SortColumn.innerText === "Status"
      ? 4
      : 5
    : 0;

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
      { orderable: false, targets: SortColumn > 0 ? [0, 6] : undefined }, // Disable sorting for image and actions columns
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
    const title = $(element).data("title");
    const image = $(element).data("image");
    const description = $(element).data("description");
    const category = $(element).data("category");
    const status = $(element).data("status");
    const price = $(element).data("price");
    const medium = $(element).data("medium");
    const dimensions = $(element).data("dimensions");
    const date = $(element).data("date");

    $("#modal-artwork-title").text(title);
    $("#modal-artwork-description").text(description);
    $("#modal-artwork-price").text("$ " + price);
    $("#modal-artwork-image").attr("src", "/uploads/" + image);

    $("#modal-artwork-status").text(status);
    $("#modal-artwork-category").text(category);
    $("#modal-artwork-medium").text(medium);
    $("#modal-artwork-dimensions").text(dimensions);
    $("#modal-artwork-date").text(date);
    $("#modal-edit-btn").on("click", function () {
      window.location.href = "/artist/edit-artwork?id=" + id;
    });
    const showArtworkDetailsModal = new bootstrap.Modal(
      document.getElementById("showDetailsModal")
    );
    showArtworkDetailsModal.show();
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
