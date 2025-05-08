$("#artwork-image").on("change", function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#preview-img").attr("src", e.target.result);
      $("#image-preview").removeClass("hide");
      $("#remove-image").show();
    };
    reader.readAsDataURL(file);
  }
});

$("#remove-image").on("click", function () {
  $("#artwork-image").val("");
  $("#preview-img").attr("src", "");
  $("#image-preview").addClass("hide");
  $(this).hide();
});
