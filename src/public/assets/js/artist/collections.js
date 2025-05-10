$(document).ready(function () {
  /// Initialize DataTable

  const tableElement = $("#SelectedArtworksTable");
  const specitialCollection = $("#specitialCollection");


  if (tableElement) {
    // Simple initialization with DataTables
    let dataTable = $(tableElement).DataTable({
      responsive: true,
      order: [
        [3 + (specitialCollection ?? 0 ? 1 : 0), "asc"],
        [1, "asc"],
      ], // Sort by collection status and then by artwork name
      dom: "lrtip",
      searching: true,
      columnDefs: [
        {
          orderable: false,
          targets: [0, 4 - (specitialCollection ?? 0 ? 1 : 0)],
        }, // Disable sorting for image and actions columns
      ],
    });

    const ArtworkCategory = $("#ArtworkCategory");
    const collectionStatus = $("#collectionStatus");
    const artworkSearch = $("#artworkSearch");

    ArtworkCategory.on("change", function () {
      const selectedValue = $(this).val();
      dataTable.column(2 + (specitialCollection ?? 0 ? 1 : 0)).search(selectedValue).draw();
    });

    collectionStatus.on("change", function () {
      const selectedValue = $(this).val();
      dataTable.column(3 + (specitialCollection ?? 0 ? 1 : 0)).search(selectedValue).draw();
    });

    artworkSearch.on("input", function () {
      const searchTerm = $(this).val();
      dataTable
        .column(1 + (specitialCollection ?? 0 ? 1 : 0))
        .search(searchTerm)
        .draw();
    });
    window.ArtToggle = function (element) {
      const id = $(element).data("id").toString();
      const list = $("#selectedArtworks").val().split(",");
      const statusBadge = $(element)
        .closest("td")
        .prev("td")
        .find(".status-badge");

      // Find the row that contains this element
      const row = $(element).closest("tr");

      if (list.includes(id)) {
        list.splice(list.indexOf(id), 1);
        $(element).find("i").removeClass("fa-trash").addClass("fa-plus");
        $(element)
          .removeClass("btn-outline-danger")
          .addClass("btn-outline-success");
        statusBadge.removeClass("accepted").addClass("rejected");
        statusBadge.text("Rejected");
      } else {
        list.push(id);
        $(element).find("i").removeClass("fa-plus").addClass("fa-trash");
        $(element)
          .removeClass("btn-outline-success")
          .addClass("btn-outline-danger");
        statusBadge.removeClass("rejected").addClass("accepted");
        statusBadge.text("accepted");
      }
      $("#selectedArtworks").val(list.join(","));

      dataTable.row(row).invalidate().draw(false);

      console.log("Updated selection list:", list);
    };
  }

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
});
