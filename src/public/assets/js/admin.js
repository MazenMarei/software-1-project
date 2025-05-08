
// Toggle sidebar on mobile
$(".toggle-sidebar").on("click", function () {
  $(".admin-sidebar").toggleClass("show");
});

// Close sidebar when clicking outside on mobile
$(document).on("click", function (e) {
  if ($(window).width() < 992) {
    if (!$(e.target).closest(".admin-sidebar, .toggle-sidebar").length) {
      $(".admin-sidebar").removeClass("show");
    }
  }
});

$("#avatarUpload").on("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#profileAvatar").attr("src", e.target.result);
    };
    reader.readAsDataURL(file);
  }
});
