// ArtShelf - View In Your Room Feature
// Author: GitHub Copilot
// Date: April 29, 2025

class ViewInRoomFeature {
  constructor(canvasId) {
    this.canvas = document.getElementById(canvasId);
    this.ctx = this.canvas.getContext("2d");
    this.roomImage = null;
    this.artworkImage = null;
    this.artworkWidth = 0;
    this.artworkHeight = 0;
    this.artworkPosition = { x: 0, y: 0 };
    this.isDragging = false;
    this.lastMousePosition = { x: 0, y: 0 };
    this.scale = 1;
    this.shadowBlur = 10;
    this.shadowColor = "rgba(0, 0, 0, 0.5)";
    this.artworkBorderWidth = 0; // For framed artwork
    this.artworkBorderColor = "#000";

    this.setupEventListeners();
  }

  // Load the room image from file or camera
  loadRoomImage(imageSource) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => {
        this.roomImage = img;
        // Resize canvas to match image dimensions
        this.canvas.width = img.width;
        this.canvas.height = img.height;
        this.draw();
        resolve(img);
      };
      img.onerror = () => {
        reject(new Error("Failed to load room image"));
      };
      img.src = imageSource;
    });
  }

  // Load artwork image
  loadArtworkImage(imageSource, width, height) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => {
        this.artworkImage = img;
        // Set default artwork size based on a percentage of room size
        this.artworkWidth = width || Math.floor(this.canvas.width * 0.3);
        this.artworkHeight =
          height || Math.floor(this.artworkWidth * (img.height / img.width));

        // Set initial position to center of canvas
        this.artworkPosition = {
          x: (this.canvas.width - this.artworkWidth) / 2,
          y: (this.canvas.height - this.artworkHeight) / 2,
        };

        this.draw();
        resolve(img);
      };
      img.onerror = () => {
        reject(new Error("Failed to load artwork image"));
      };
      img.src = imageSource;
    });
  }

  // Draw the current state to the canvas
  draw() {
    if (!this.roomImage) return;

    // Clear canvas
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

    // Draw room
    this.ctx.drawImage(
      this.roomImage,
      0,
      0,
      this.canvas.width,
      this.canvas.height
    );

    // Draw artwork if available
    if (this.artworkImage) {
      // Draw shadow for the artwork to create 3D effect
      this.ctx.shadowBlur = this.shadowBlur;
      this.ctx.shadowColor = this.shadowColor;
      this.ctx.shadowOffsetX = 5;
      this.ctx.shadowOffsetY = 5;

      // Draw artwork frame/border if specified
      if (this.artworkBorderWidth > 0) {
        this.ctx.fillStyle = this.artworkBorderColor;
        this.ctx.fillRect(
          this.artworkPosition.x - this.artworkBorderWidth,
          this.artworkPosition.y - this.artworkBorderWidth,
          this.artworkWidth + this.artworkBorderWidth * 2,
          this.artworkHeight + this.artworkBorderWidth * 2
        );
      }

      // Reset shadow for the artwork itself
      this.ctx.shadowBlur = 0;
      this.ctx.shadowColor = "transparent";
      this.ctx.shadowOffsetX = 0;
      this.ctx.shadowOffsetY = 0;

      // Draw the artwork
      this.ctx.drawImage(
        this.artworkImage,
        this.artworkPosition.x,
        this.artworkPosition.y,
        this.artworkWidth,
        this.artworkHeight
      );
    }
  }

  // Apply perspective transform to better match the wall
  applyPerspective(points) {
    if (!this.artworkImage || points.length !== 4) return;

    // This is a simplified implementation - in a real app,
    // this would use more complex perspective transformations
    this.ctx.save();
    this.ctx.beginPath();

    // Create a path with the specified points
    this.ctx.moveTo(points[0].x, points[0].y);
    this.ctx.lineTo(points[1].x, points[1].y);
    this.ctx.lineTo(points[2].x, points[2].y);
    this.ctx.lineTo(points[3].x, points[3].y);
    this.ctx.closePath();

    // Clip to this path
    this.ctx.clip();

    // Draw the artwork with transformation
    // In a full implementation, we would use transform() to apply proper perspective
    this.ctx.drawImage(
      this.artworkImage,
      this.artworkPosition.x,
      this.artworkPosition.y,
      this.artworkWidth,
      this.artworkHeight
    );

    this.ctx.restore();
  }

  // Set up various event listeners for interacting with the artwork
  setupEventListeners() {
    this.canvas.addEventListener("mousedown", this.handleMouseDown.bind(this));
    this.canvas.addEventListener("mousemove", this.handleMouseMove.bind(this));
    this.canvas.addEventListener("mouseup", this.handleMouseUp.bind(this));
    this.canvas.addEventListener("mouseleave", this.handleMouseUp.bind(this));

    // For touch devices
    this.canvas.addEventListener("touchstart", (e) => {
      const touch = e.touches[0];
      const mouseEvent = new MouseEvent("mousedown", {
        clientX: touch.clientX,
        clientY: touch.clientY,
      });
      this.canvas.dispatchEvent(mouseEvent);
      e.preventDefault();
    });

    this.canvas.addEventListener("touchmove", (e) => {
      const touch = e.touches[0];
      const mouseEvent = new MouseEvent("mousemove", {
        clientX: touch.clientX,
        clientY: touch.clientY,
      });
      this.canvas.dispatchEvent(mouseEvent);
      e.preventDefault();
    });

    this.canvas.addEventListener("touchend", (e) => {
      const mouseEvent = new MouseEvent("mouseup", {});
      this.canvas.dispatchEvent(mouseEvent);
      e.preventDefault();
    });

    // Zoom with mouse wheel
    this.canvas.addEventListener("wheel", (e) => {
      e.preventDefault();

      // Scale the artwork based on wheel direction
      if (e.deltaY < 0) {
        // Zoom in
        this.scale *= 1.1;
      } else {
        // Zoom out
        this.scale *= 0.9;
      }

      // Apply scale with constraints
      this.scale = Math.max(0.1, Math.min(3, this.scale));

      // Adjust artwork size based on scale
      const newWidth = this.artworkWidth * this.scale;
      const newHeight = this.artworkHeight * this.scale;

      // Adjust position to zoom toward the center of the artwork
      const dw = newWidth - this.artworkWidth;
      const dh = newHeight - this.artworkHeight;

      this.artworkPosition.x -= dw / 2;
      this.artworkPosition.y -= dh / 2;

      this.artworkWidth = newWidth;
      this.artworkHeight = newHeight;

      this.draw();
    });
  }

  // Check if the mouse position is over the artwork
  isMouseOverArtwork(mouseX, mouseY) {
    return (
      mouseX >= this.artworkPosition.x &&
      mouseX <= this.artworkPosition.x + this.artworkWidth &&
      mouseY >= this.artworkPosition.y &&
      mouseY <= this.artworkPosition.y + this.artworkHeight
    );
  }

  // Mouse down event handler
  handleMouseDown(e) {
    const rect = this.canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    if (this.isMouseOverArtwork(mouseX, mouseY)) {
      this.isDragging = true;
      this.lastMousePosition = { x: mouseX, y: mouseY };
    }
  }

  // Mouse move event handler
  handleMouseMove(e) {
    if (!this.isDragging) return;

    const rect = this.canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    // Calculate the distance moved
    const dx = mouseX - this.lastMousePosition.x;
    const dy = mouseY - this.lastMousePosition.y;

    // Update artwork position
    this.artworkPosition.x += dx;
    this.artworkPosition.y += dy;

    // Update last mouse position
    this.lastMousePosition = { x: mouseX, y: mouseY };

    // Redraw
    this.draw();
  }

  // Mouse up event handler
  handleMouseUp() {
    this.isDragging = false;
  }

  // Add a frame to the artwork
  addFrame(borderWidth, borderColor) {
    this.artworkBorderWidth = borderWidth;
    this.artworkBorderColor = borderColor || "#000";
    this.draw();
  }

  // Remove frame
  removeFrame() {
    this.artworkBorderWidth = 0;
    this.draw();
  }

  // Take a screenshot of the current view
  takeScreenshot() {
    return this.canvas.toDataURL("image/png");
  }

  // Save the current view
  saveImage(filename = "artwork-in-room.png") {
    const link = document.createElement("a");
    link.download = filename;
    link.href = this.takeScreenshot();
    link.click();
  }

  // Share the current view
  shareImage() {
    // Check if the Web Share API is available
    if (navigator.share) {
      this.canvas.toBlob((blob) => {
        const file = new File([blob], "artwork-in-room.png", {
          type: "image/png",
        });
        navigator
          .share({
            title: "My Artwork Preview",
            text: "Check out how this artwork looks in my space!",
            files: [file],
          })
          .catch(console.error);
      });
    } else {
      alert(
        "Web Share API is not supported in your browser. Please use the Save option instead."
      );
    }
  }

  // Adjust lighting to match the room (simple implementation)
  adjustLighting(brightness, contrast) {
    // In a real implementation, this would apply more sophisticated
    // image processing to match lighting conditions
    if (!this.artworkImage) return;

    this.ctx.filter = `brightness(${brightness}%) contrast(${contrast}%)`;
    this.draw();
    this.ctx.filter = "none";
  }

  // Reset to default position and scale
  reset() {
    if (!this.artworkImage || !this.roomImage) return;

    this.scale = 1;
    this.artworkWidth = Math.floor(this.canvas.width * 0.3);
    this.artworkHeight = Math.floor(
      this.artworkWidth * (this.artworkImage.height / this.artworkImage.width)
    );

    this.artworkPosition = {
      x: (this.canvas.width - this.artworkWidth) / 2,
      y: (this.canvas.height - this.artworkHeight) / 2,
    };

    this.artworkBorderWidth = 0;
    this.draw();
  }
}

// Expose to global scope
window.ViewInRoomFeature = ViewInRoomFeature;

/**
 * ArtShelf View in Room Feature
 * Allows users to visualize artwork in different room settings
 */

// Global variables to track the current state
let currentArtwork = {
  element: null,
  position: { x: 50, y: 30 }, // Default position (percentage)
  size: 30, // Default size (percentage of container width)
  rotation: 0, // Default rotation (degrees)
};

// Initialize the View in Room feature
function initViewInRoom(
  artworkImageUrl,
  containerSelector = "#viewInRoomPreview"
) {
  const container = document.querySelector(containerSelector);

  if (!container) {
    console.error("View in Room container not found");
    return;
  }

  // Create or get the artwork element
  let artworkElement = container.querySelector(".view-in-room-artwork");

  if (!artworkElement) {
    artworkElement = document.createElement("img");
    artworkElement.className = "view-in-room-artwork";
    artworkElement.draggable = false;
    container.appendChild(artworkElement);
  }

  // Set artwork properties
  artworkElement.src = artworkImageUrl;
  artworkElement.alt = "Artwork visualization";
  artworkElement.style.width = `${currentArtwork.size}%`;
  artworkElement.style.left = `${
    currentArtwork.position.x - currentArtwork.size / 2
  }%`;
  artworkElement.style.top = `${currentArtwork.position.y}%`;
  artworkElement.style.transform = `rotate(${currentArtwork.rotation}deg)`;

  // Store reference to the artwork element
  currentArtwork.element = artworkElement;

  // Initialize drag functionality
  enableDragging(container, artworkElement);

  return {
    setSize: setArtworkSize,
    setPosition: setArtworkPosition,
    setRotation: setArtworkRotation,
    setBackground: setRoomBackground,
    resetPosition: resetArtworkPosition,
  };
}

// Enable dragging functionality for artwork
function enableDragging(container, artworkElement) {
  let isDragging = false;
  let startX, startY;
  let startLeft, startTop;

  // Mouse events for desktop
  container.addEventListener("mousedown", startDrag);
  document.addEventListener("mousemove", dragArtwork);
  document.addEventListener("mouseup", stopDrag);

  // Touch events for mobile
  container.addEventListener("touchstart", startDrag, { passive: false });
  document.addEventListener("touchmove", dragArtwork, { passive: false });
  document.addEventListener("touchend", stopDrag);

  function startDrag(e) {
    // Only allow dragging the artwork itself
    if (e.target !== artworkElement) return;

    e.preventDefault();

    isDragging = true;

    // Get the initial position
    if (e.type === "mousedown") {
      startX = e.clientX;
      startY = e.clientY;
    } else if (e.type === "touchstart") {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    }

    // Get the current position of the artwork
    const rect = artworkElement.getBoundingClientRect();
    startLeft = rect.left;
    startTop = rect.top;

    // Add a class when dragging
    artworkElement.classList.add("dragging");
  }

  function dragArtwork(e) {
    if (!isDragging) return;

    e.preventDefault();

    let deltaX, deltaY;

    if (e.type === "mousemove") {
      deltaX = e.clientX - startX;
      deltaY = e.clientY - startY;
    } else if (e.type === "touchmove") {
      deltaX = e.touches[0].clientX - startX;
      deltaY = e.touches[0].clientY - startY;
    }

    // Calculate new position
    const containerRect = container.getBoundingClientRect();
    const artworkRect = artworkElement.getBoundingClientRect();

    // Calculate new position as percentage of container
    const newLeft =
      ((startLeft + deltaX - containerRect.left) / containerRect.width) * 100;
    const newTop =
      ((startTop + deltaY - containerRect.top) / containerRect.height) * 100;

    // Update position
    artworkElement.style.left = `${newLeft}%`;
    artworkElement.style.top = `${newTop}%`;

    // Update the current position
    currentArtwork.position.x =
      newLeft + (artworkRect.width / containerRect.width) * 50;
    currentArtwork.position.y = newTop;
  }

  function stopDrag() {
    if (!isDragging) return;

    isDragging = false;
    artworkElement.classList.remove("dragging");
  }
}

// Set artwork size
function setArtworkSize(size) {
  if (!currentArtwork.element) return;

  currentArtwork.size = size;

  // Calculate the offset to keep the image centered horizontally
  const offsetX = currentArtwork.size / 2;

  // Update size and adjust position to keep it centered
  currentArtwork.element.style.width = `${size}%`;
  currentArtwork.element.style.left = `${currentArtwork.position.x - offsetX}%`;
}

// Set artwork position
function setArtworkPosition(x, y) {
  if (!currentArtwork.element) return;

  currentArtwork.position.x = x;
  currentArtwork.position.y = y;

  // Calculate the offset to keep the image centered horizontally
  const offsetX = currentArtwork.size / 2;

  currentArtwork.element.style.left = `${x - offsetX}%`;
  currentArtwork.element.style.top = `${y}%`;
}

// Set artwork rotation
function setArtworkRotation(degrees) {
  if (!currentArtwork.element) return;

  currentArtwork.rotation = degrees;
  currentArtwork.element.style.transform = `rotate(${degrees}deg)`;
}

// Set room background
function setRoomBackground(imageUrl, containerSelector = "#viewInRoomPreview") {
  const container = document.querySelector(containerSelector);

  if (!container) {
    console.error("View in Room container not found");
    return;
  }

  container.style.backgroundImage = `url('${imageUrl}')`;
}

// Reset artwork to default position
function resetArtworkPosition() {
  setArtworkPosition(50, 30);
  setArtworkSize(30);
  setArtworkRotation(0);
}

// Helper function to handle different room backgrounds
function loadRoomPresets() {
  return {
    livingRoom: "../assets/images/placeholders/living-room.jpg",
    bedroom: "../assets/images/placeholders/bedroom.jpg",
    diningRoom: "../assets/images/placeholders/dining-room.jpg",
    office: "../assets/images/placeholders/office.jpg",
  };
}

// Handle room background change from select dropdown
function changeRoomBackground() {
  const roomBg = document.getElementById("roomBackground").value;
  setRoomBackground(roomBg);
}

// Handle artwork size change from slider
function changeArtworkSize(size) {
  setArtworkSize(parseFloat(size));
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  // Initialize the feature if we're on the artwork detail page
  const viewInRoomContainer = document.getElementById("viewInRoomPreview");
  const artworkImage = document.getElementById("mainImage");

  if (viewInRoomContainer && artworkImage) {
    initViewInRoom(artworkImage.src);

    // Add event listener for room photo upload
    const uploadRoomPhotoBtn = document.getElementById("uploadRoomPhotoBtn");
    const roomPhotoInput = document.getElementById("roomPhotoInput");

    if (uploadRoomPhotoBtn && roomPhotoInput) {
      uploadRoomPhotoBtn.addEventListener("click", function () {
        roomPhotoInput.click();
      });

      roomPhotoInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (event) {
            setRoomBackground(event.target.result);
          };
          reader.readAsDataURL(file);
        }
      });
    }
  }
});
