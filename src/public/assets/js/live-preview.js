$(() => {
  const KonvaContainer = $("#konvaContainer");
  const tableElement = $("#SelectedArtworksTable");
  const SelectionRoom = $(".room-select");

  const imagesLayers = {};
  let dataTable = tableElement.DataTable({
    responsive: true,
    order: [[1, "asc"]],
    searching: true,
    columnDefs: [
      {
        orderable: false,
        targets: [0, 2],
      }, // Disable sorting for image and actions columns
    ],
    language: {
      lengthMenu: "per page _MENU_",
      info: " _START_ to _END_ of _TOTAL_ items",
      infoEmpty: "No data found",
      infoFiltered: "(filtered from _MAX_ total items)",
    },
  });

  const bgImage = new Image();
  bgImage.src =
    "https://www.housedigest.com/img/gallery/the-affordable-way-nate-berkus-fills-empty-wall-space/l-intro-1688412309.jpg";

  const stage = new Konva.Stage({
    container: "konvaContainer",
    width: KonvaContainer.width(),
    height: calcHeight(),
    "max-width": "100%",
  });
  const layer = new Konva.Layer();

  var konvaBackImg = new Konva.Image({
    width: KonvaContainer.width(),
    height: calcHeight(),
    Image: bgImage,
    draggable: false,
  });
  layer.add(konvaBackImg);
  stage.add(layer);

  function calcHeight() {
    const width = KonvaContainer.width();
    const height = (width * 600) / 1000;
    return height;
  }

  window.ArtPreview = function (element) {
    const id = $(element).data("id").toString();
    const image = $(element).data("image");
    const list = $("#selectedArtworks").val().split(",");
    const row = $(element).closest("tr");
    const height = parseFloat($(element).data("height")) * 2.5;
    const width = parseFloat($(element).data("width")) *2.5;
    if (list.includes(id)) {
      list.splice(list.indexOf(id), 1);
      $(element).find("i").removeClass("fa-trash").addClass("fa-plus");
      $(element)
        .removeClass("btn-outline-danger")
        .addClass("btn-outline-success");

      if (imagesLayers[id]) {
        imagesLayers[id].destroy();
        delete imagesLayers[id];
      }
    } else {
      list.push(id);
      $(element).find("i").removeClass("fa-plus").addClass("fa-trash");
      $(element)
        .removeClass("btn-outline-success")
        .addClass("btn-outline-danger");
      if (stage) {
        let img = new Image();
        img.crossOrigin = "Anonymous"; // Enable CORS
        img.src = image;

        // Make sure to only add the image after it's loaded
        img.onload = function () {
          var ArtworkImg = new Konva.Image({
            image: img, // Correct property name is 'image' (lowercase)
            y: 0,
            x: 0,
            width: width,
            height: height,
            stroke: "black",
            draggable: true,
            offsetX: 0,
            offsetY: 0,
          });

          if (layer) {
            imagesLayers[id] = new Konva.Layer();
            imagesLayers[id].add(ArtworkImg);
            stage.add(imagesLayers[id]);
            imagesLayers[id].draw();
          }
        };
      }
    }
    $("#selectedArtworks").val(list.join(","));

    dataTable.row(row).invalidate().draw(false);
  };

  SelectionRoom.on("click", function () {
    $(".room-select").removeClass("border-3");
    $(this).addClass("border-3");
    bgImage.src = $(this).attr("src");
    layer.draw();
  });

  $("#roomImage").on("change", function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        bgImage.src = e.target.result;
        layer.draw();
      };
      reader.readAsDataURL(file);
    }
  });

  $("#downloadButton").on("click", function () {
    const dataURL = stage.toDataURL({
      pixelRatio: 3,
    });
    const link = document.createElement("a");
    link.href = dataURL;
    link.download = `PreviewArtwork_${Date.now()}.jpeg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
});
