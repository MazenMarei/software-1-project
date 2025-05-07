/**
 * Admin Artists Management functionality
 */
$(document).ready(function () {
  // Initialize DataTable for artists
  const artistsTable = $("#artistsTable").DataTable({
    responsive: true,
    order: [[6, "desc"]], // Sort by join date by default
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search artists...",
      lengthMenu: "Show _MENU_ artists per page",
      info: "Showing _START_ to _END_ of _TOTAL_ artists",
      infoEmpty: "No artists found",
      infoFiltered: "(filtered from _MAX_ total artists)",
    },
    columnDefs: [
      { orderable: false, targets: [0, 7] }, // Disable sorting for image and actions columns
    ],
  });

  // Status filter
  $("#statusFilter").on("change", function () {
    const status = $(this).val();
    artistsTable.column(5).search(status).draw();
  });

  // View Artist Details
  $(document).on("click", ".view-artist-btn", function () {
    const artistId = $(this).data("userID");
    loadArtistDetails(artistId);
  });

  function loadArtistDetails(artistId) {
    $.ajax({
      url: "/admin/getArtistDetails",
      type: "GET",
      data: { artistID: artistId },
      beforeSend: function () {
        $("#artistDetailsContent").html(
          '<div class="text-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
        );
        $("#artistDetailsModal").modal("show");
      },
      success: function (response) {
        try {
          const data =
            typeof response === "string" ? JSON.parse( ) : response;

          if (data.success) {
            const artist = data.artist;
            let statusClass = "";

            switch (artist.status) {
              case "Approved":
                statusClass = "text-success";
                break;
              case "Pending":
                statusClass = "text-warning";
                break;
              case "Rejected":
                statusClass = "text-danger";
                break;
              default:
                statusClass = "text-secondary";
            }

            // Format specialties
            let specialtiesHtml = "";
            if (artist.specialties && artist.specialties.length > 0) {
              specialtiesHtml = artist.specialties
                .map(
                  (specialty) =>
                    `<span class="badge bg-secondary me-1">${specialty.trim()}</span>`
                )
                .join("");
            } else {
              specialtiesHtml = "<em>No specialties listed</em>";
            }

            // Format recent artworks
            let artworksHtml = "";
            if (artist.recentArtworks && artist.recentArtworks.length > 0) {
              artworksHtml = '<div class="row">';
              artist.recentArtworks.forEach((artwork) => {
                artworksHtml += `
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="${
                                          artwork.imagePath
                                            ? "../../uploads/" +
                                              artwork.imagePath
                                            : "../../assets/images/placeholder.jpg"
                                        }" 
                                            class="card-img-top artwork-thumbnail" alt="${
                                              artwork.title
                                            }">
                                        <div class="card-body">
                                            <h5 class="card-title">${
                                              artwork.title
                                            }</h5>
                                            <p class="card-text text-muted">${
                                              artwork.price
                                                ? "$" + artwork.price
                                                : "Price not set"
                                            }</p>
                                        </div>
                                    </div>
                                </div>`;
              });
              artworksHtml += "</div>";
            } else {
              artworksHtml = "<p><em>No artworks available</em></p>";
            }

            // Build the HTML content
            const detailsHtml = `
                        <div class="artist-details">
                            <div class="row mb-4">
                                <div class="col-lg-3 text-center">
                                    <img src="${
                                      artist.profilePic
                                        ? "../../assets/uploads/profiles/" +
                                          artist.profilePic
                                        : "../../assets/uploads/profiles/default.jpg"
                                    }" 
                                        class="img-fluid rounded-circle artist-profile-img mb-3" alt="${
                                          artist.fullName
                                        }">
                                    <h4 class="artist-name">${
                                      artist.fullName
                                    }</h4>
                                    <p class="status ${statusClass}">${
              artist.status
            }</p>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="fw-bold">Email:</label>
                                                <p>${artist.email}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold">Phone:</label>
                                                <p>${
                                                  artist.phone || "Not provided"
                                                }</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold">Address:</label>
                                                <p>${
                                                  artist.address ||
                                                  "Not provided"
                                                }</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="fw-bold">Joined:</label>
                                                <p>${artist.joinDate}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold">Total Artworks:</label>
                                                <p>${artist.totalArtworks}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold">Balance:</label>
                                                <p>$${parseFloat(
                                                  artist.Balance || 0
                                                ).toFixed(2)}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Bio:</label>
                                        <p>${
                                          artist.Bio || "No bio provided"
                                        }</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Specialties:</label>
                                        <div class="mt-1">${specialtiesHtml}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="recent-artworks mt-4">
                                <h5 class="mb-3">Recent Artworks</h5>
                                ${artworksHtml}
                            </div>
                        </div>`;

            $("#artistDetailsContent").html(detailsHtml);
          } else {
            $("#artistDetailsContent").html(
              `<div class="alert alert-danger">${
                data.message || "Failed to load artist details."
              }</div>`
            );
          }
        } catch (error) {
          console.error("Error parsing response:", error);
          $("#artistDetailsContent").html(
            '<div class="alert alert-danger">Error loading artist details. Please try again.</div>'
          );
        }
      },
      error: function (xhr) {
        $("#artistDetailsContent").html(
          '<div class="alert alert-danger">Failed to load artist details. Please try again.</div>'
        );
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
