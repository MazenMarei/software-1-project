/**
 * View in Room JavaScript
 * Handles the "View in Room" feature functionality
 */

// Global variables
let currentArtwork = null;
let currentSize = 60; // Default size percentage

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    // Get artwork ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const artworkId = urlParams.get('id');
    
    if (!artworkId) {
        window.notifications.error('No artwork specified');
        window.location.href = 'dashboard.html';
        return;
    }
    
    // Load artwork data
    await loadArtworkData(artworkId);
    
    // Initialize draggable functionality
    initDraggable();
    
    // Initialize room selection
    initRoomSelection();
    
    // Initialize size controls
    initSizeControls();
    
    // Initialize room upload
    initRoomUpload();
    
    // Initialize action buttons
    initActionButtons();
    
    // Initialize cart and favorites previews
    await loadCartPreview();
    await loadFavoritesPreview();
});

/**
 * Load artwork data for preview
 * @param {string} artworkId - Artwork ID
 */
async function loadArtworkData(artworkId) {
    try {
        // Get artwork data
        const artwork = await artworkService.getArtworkById(artworkId);
        
        if (!artwork) {
            window.notifications.error('Artwork not found');
            window.location.href = 'dashboard.html';
            return;
        }
        
        // Set current artwork
        currentArtwork = artwork;
        
        // Set artwork preview image
        const artworkPreview = document.getElementById('artworkPreview');
        if (artworkPreview) {
            artworkPreview.src = artwork.mainImage;
            artworkPreview.alt = artwork.title;
        }
        
        // Update artwork details
        updateArtworkDetails(artwork);
        
        // Update Add to Cart button
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                addToCart(artwork.id);
            });
        }
    } catch (error) {
        console.error('Error loading artwork data:', error);
        window.notifications.error('Failed to load artwork data. Please try again.');
    }
}

/**
 * Update artwork details section
 * @param {Object} artwork - Artwork object
 */
function updateArtworkDetails(artwork) {
    const detailsContainer = document.getElementById('artworkDetails');
    if (!detailsContainer) return;
    
    detailsContainer.innerHTML = `
        <h2 class="artwork-title">${artwork.title}</h2>
        <p class="artwork-artist">${artwork.artistName}</p>
        
        <div class="artwork-info">
            <div class="artwork-info-item">
                <span class="info-label">Category:</span>
                <span class="info-value">${artwork.category}</span>
            </div>
            <div class="artwork-info-item">
                <span class="info-label">Style:</span>
                <span class="info-value">${artwork.style}</span>
            </div>
            <div class="artwork-info-item">
                <span class="info-label">Medium:</span>
                <span class="info-value">${artwork.medium}</span>
            </div>
            <div class="artwork-info-item">
                <span class="info-label">Year:</span>
                <span class="info-value">${artwork.year}</span>
            </div>
            <div class="artwork-info-item">
                <span class="info-label">Dimensions:</span>
                <span class="info-value">${artwork.getFormattedDimensions()}</span>
            </div>
        </div>
        
        <p class="artwork-price">${artwork.getFormattedPrice()}</p>
        
        <div class="d-grid gap-2">
            <a href="artwork.html?id=${artwork.id}" class="btn btn-outline-primary">
                <i class="fas fa-info-circle me-2"></i> View Details
            </a>
            <a href="artist.html?id=${artwork.artistId}" class="btn btn-outline-secondary">
                <i class="fas fa-user-alt me-2"></i> About the Artist
            </a>
        </div>
    `;
}

/**
 * Initialize draggable functionality for artwork
 */
function initDraggable() {
    const artworkContainer = document.getElementById('artworkContainer');
    if (!artworkContainer) return;
    
    // Make artwork draggable
    $(artworkContainer).draggable({
        containment: 'parent',
        cursor: 'move',
        start: function() {
            $(this).css('z-index', 100);
        },
        stop: function() {
            $(this).css('z-index', 10);
        }
    });
}

/**
 * Initialize room selection
 */
function initRoomSelection() {
    const roomOptions = document.querySelectorAll('.room-option');
    const roomImage = document.getElementById('roomImage');
    
    if (!roomOptions.length || !roomImage) return;
    
    roomOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Remove active class from all options
            roomOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to clicked option
            option.classList.add('active');
            
            // Update room image
            const roomSrc = option.dataset.room;
            roomImage.src = roomSrc;
        });
    });
}

/**
 * Initialize size controls
 */
function initSizeControls() {
    const sizeSlider = document.getElementById('sizeSlider');
    const sizeValue = document.getElementById('sizeValue');
    const sizeDecrease = document.getElementById('sizeDecrease');
    const sizeIncrease = document.getElementById('sizeIncrease');
    const artworkPreview = document.getElementById('artworkPreview');
    
    if (!sizeSlider || !sizeValue || !sizeDecrease || !sizeIncrease || !artworkPreview) return;
    
    // Update size value display
    sizeValue.textContent = `${sizeSlider.value}%`;
    
    // Apply initial size
    applySize(parseInt(sizeSlider.value));
    
    // Add event listeners
    sizeSlider.addEventListener('input', () => {
        const newSize = parseInt(sizeSlider.value);
        sizeValue.textContent = `${newSize}%`;
        applySize(newSize);
    });
    
    sizeDecrease.addEventListener('click', () => {
        const newSize = Math.max(parseInt(sizeSlider.value) - 5, parseInt(sizeSlider.min));
        sizeSlider.value = newSize;
        sizeValue.textContent = `${newSize}%`;
        applySize(newSize);
    });
    
    sizeIncrease.addEventListener('click', () => {
        const newSize = Math.min(parseInt(sizeSlider.value) + 5, parseInt(sizeSlider.max));
        sizeSlider.value = newSize;
        sizeValue.textContent = `${newSize}%`;
        applySize(newSize);
    });
}

/**
 * Apply size to artwork preview
 * @param {number} size - Size percentage
 */
function applySize(size) {
    const artworkPreview = document.getElementById('artworkPreview');
    if (!artworkPreview) return;
    
    // Calculate size based on room container
    const roomContainer = document.querySelector('.room-image-container');
    if (!roomContainer) return;
    
    const roomHeight = roomContainer.clientHeight;
    const maxHeight = roomHeight * (size / 100);
    
    // Apply size
    artworkPreview.style.maxHeight = `${maxHeight}px`;
    
    // Store current size
    currentSize = size;
}

/**
 * Initialize room upload functionality
 */
function initRoomUpload() {
    const uploadInput = document.getElementById('uploadRoomInput');
    const roomImage = document.getElementById('roomImage');
    
    if (!uploadInput || !roomImage) return;
    
    uploadInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        
        if (file) {
            // Check if file is an image
            if (!file.type.startsWith('image/')) {
                window.notifications.error('Please select an image file');
                return;
            }
            
            // Check file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                window.notifications.error('File is too large. Please select an image under 5MB');
                return;
            }
            
            // Read file and set as room image
            const reader = new FileReader();
            reader.onload = (event) => {
                roomImage.src = event.target.result;
                
                // Deselect room options
                const roomOptions = document.querySelectorAll('.room-option');
                roomOptions.forEach(opt => opt.classList.remove('active'));
                
                window.notifications.success('Room photo uploaded successfully');
            };
            reader.readAsDataURL(file);
        }
    });
}

/**
 * Initialize action buttons
 */
function initActionButtons() {
    const shareButton = document.getElementById('shareButton');
    const downloadButton = document.getElementById('downloadButton');
    
    if (shareButton) {
        shareButton.addEventListener('click', () => {
            showShareModal();
        });
    }
    
    if (downloadButton) {
        downloadButton.addEventListener('click', () => {
            downloadRoomView();
        });
    }
}

/**
 * Show share modal
 */
function showShareModal() {
    // Get current URL
    const shareLink = document.getElementById('shareLink');
    const currentUrl = window.location.href;
    
    if (shareLink) {
        shareLink.value = currentUrl;
    }
    
    // Initialize copy button
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', () => {
            shareLink.select();
            document.execCommand('copy');
            window.notifications.success('Link copied to clipboard');
        });
    }
    
    // Initialize social share buttons
    const socialButtons = document.querySelectorAll('.social-share-btn');
    socialButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const platform = btn.dataset.platform;
            shareToSocialMedia(platform, currentUrl);
        });
    });
    
    // Show modal
    const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
    shareModal.show();
}

/**
 * Share to social media
 * @param {string} platform - Social media platform
 * @param {string} url - URL to share
 */
function shareToSocialMedia(platform, url) {
    let shareUrl = '';
    const title = `Check out this artwork on ArtShelf`;
    
    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'pinterest':
            if (currentArtwork) {
                shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&media=${encodeURIComponent(currentArtwork.mainImage)}&description=${encodeURIComponent(title)}`;
            }
            break;
        case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(`I thought you might like this artwork: ${url}`)}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank');
    }
}

/**
 * Download room view as image
 */
function downloadRoomView() {
    const roomPreview = document.querySelector('.room-preview');
    if (!roomPreview) return;
    
    window.notifications.info('Preparing your image for download...');
    
    html2canvas(roomPreview, {
        allowTaint: true,
        useCORS: true,
        scale: 2
    }).then(canvas => {
        // Create download link
        const link = document.createElement('a');
        link.download = `artshelf-room-preview-${Date.now()}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        window.notifications.success('Image downloaded successfully');
    }).catch(error => {
        console.error('Error generating image:', error);
        window.notifications.error('Failed to generate image. Please try again.');
    });
}

/**
 * Add artwork to cart
 * @param {string} artworkId - Artwork ID
 */
async function addToCart(artworkId) {
    try {
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) {
            window.notifications.warning('Please log in to add items to cart');
            return;
        }
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user) return;
        
        // Add to cart
        if (user.addToCart(artworkId)) {
            window.notifications.success('Added to cart');
            
            // Update cart preview
            await loadCartPreview();
        } else {
            window.notifications.info('This item is already in your cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        window.notifications.error('Failed to add to cart. Please try again.');
    }
}

/**
 * Load cart preview dropdown
 */
async function loadCartPreview() {
    try {
        // Get cart preview container
        const container = document.querySelector('.cart-preview');
        if (!container) return;
        
        // Clear container
        container.innerHTML = '';
        
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) return;
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user || !user.cart || user.cart.length === 0) {
            container.innerHTML = `
                <div class="p-3 text-center">
                    <p class="text-muted">Your cart is empty</p>
                </div>
            `;
            
            // Update cart count
            const cartBadge = document.querySelector('.btn-icon .badge');
            if (cartBadge) {
                cartBadge.textContent = '0';
            }
            
            return;
        }
        
        // Load artwork data
        await artworkService.loadArtworks();
        
        // Get artwork data for cart items
        const cartItems = [];
        for (const item of user.cart) {
            const artwork = await artworkService.getArtworkById(item.artworkId);
            if (artwork) {
                cartItems.push(artwork);
            }
        }
        
        // Update cart count
        const cartBadge = document.querySelector('.btn-icon .badge');
        if (cartBadge) {
            cartBadge.textContent = cartItems.length.toString();
        }
        
        // Show only first 3 items in preview
        const previewItems = cartItems.slice(0, 3);
        
        // Create HTML for each cart item
        previewItems.forEach(item => {
            const cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item';
            cartItemElement.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.getThumbnail()}" alt="${item.title}">
                </div>
                <div class="cart-item-details">
                    <h6 class="cart-item-title">${item.title}</h6>
                    <p class="cart-item-artist">${item.artistName}</p>
                    <p class="cart-item-price">${item.getFormattedPrice()}</p>
                </div>
                <button class="cart-item-remove" data-id="${item.id}">
                    <i class="far fa-trash-alt"></i>
                </button>
            `;
            container.appendChild(cartItemElement);
            
            // Add event listener to remove button
            const removeBtn = cartItemElement.querySelector('.cart-item-remove');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    removeFromCart(item.id);
                });
            }
        });
        
        // Add "more items" message if there are more than 3 items
        if (cartItems.length > 3) {
            const moreElement = document.createElement('div');
            moreElement.className = 'p-2 text-center';
            moreElement.innerHTML = `
                <small class="text-muted">+${cartItems.length - 3} more items</small>
            `;
            container.appendChild(moreElement);
        }
    } catch (error) {
        console.error('Error loading cart preview:', error);
    }
}

/**
 * Load favorites preview dropdown
 */
async function loadFavoritesPreview() {
    try {
        // Get favorites preview container
        const container = document.querySelector('.favorites-preview');
        if (!container) return;
        
        // Clear container
        container.innerHTML = '';
        
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) return;
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user || !user.favorites || user.favorites.length === 0) {
            container.innerHTML = `
                <div class="p-3 text-center">
                    <p class="text-muted">No favorites yet</p>
                </div>
            `;
            
            // Update favorites count
            const favBadge = document.querySelector('.btn-icon .badge');
            if (favBadge) {
                favBadge.textContent = '0';
            }
            
            return;
        }
        
        // Load artwork data
        await artworkService.loadArtworks();
        
        // Get artwork data for favorite items
        const favoriteItems = [];
        for (const artworkId of user.favorites) {
            const artwork = await artworkService.getArtworkById(artworkId);
            if (artwork) {
                favoriteItems.push(artwork);
            }
        }
        
        // Update favorites count
        const favBadge = document.querySelector('.btn-icon .badge');
        if (favBadge) {
            favBadge.textContent = favoriteItems.length.toString();
        }
        
        // Show only first 3 items in preview
        const previewItems = favoriteItems.slice(0, 3);
        
        // Create HTML for each favorite item
        previewItems.forEach(item => {
            const favItemElement = document.createElement('div');
            favItemElement.className = 'cart-item';
            favItemElement.innerHTML = `
                <div class="cart-item-image">
                    <img src="${item.getThumbnail()}" alt="${item.title}">
                </div>
                <div class="cart-item-details">
                    <h6 class="cart-item-title">${item.title}</h6>
                    <p class="cart-item-artist">${item.artistName}</p>
                    <p class="cart-item-price">${item.getFormattedPrice()}</p>
                </div>
                <button class="cart-item-remove" data-id="${item.id}">
                    <i class="far fa-heart-broken"></i>
                </button>
            `;
            container.appendChild(favItemElement);
            
            // Add event listener to remove button
            const removeBtn = favItemElement.querySelector('.cart-item-remove');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    removeFromFavorites(item.id);
                });
            }
        });
        
        // Add "more items" message if there are more than 3 items
        if (favoriteItems.length > 3) {
            const moreElement = document.createElement('div');
            moreElement.className = 'p-2 text-center';
            moreElement.innerHTML = `
                <small class="text-muted">+${favoriteItems.length - 3} more favorites</small>
            `;
            container.appendChild(moreElement);
        }
    } catch (error) {
        console.error('Error loading favorites preview:', error);
    }
}

/**
 * Remove artwork from cart
 * @param {string} artworkId - Artwork ID
 */
async function removeFromCart(artworkId) {
    try {
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) return;
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user) return;
        
        // Remove from cart
        if (user.removeFromCart(artworkId)) {
            window.notifications.success('Removed from cart');
            
            // Update cart preview
            await loadCartPreview();
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        window.notifications.error('Failed to remove from cart. Please try again.');
    }
}

/**
 * Remove artwork from favorites
 * @param {string} artworkId - Artwork ID
 */
async function removeFromFavorites(artworkId) {
    try {
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) return;
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user) return;
        
        // Remove from favorites
        if (user.removeFromFavorites(artworkId)) {
            window.notifications.success('Removed from favorites');
            
            // Update favorites preview
            await loadFavoritesPreview();
        }
    } catch (error) {
        console.error('Error removing favorite:', error);
        window.notifications.error('Failed to remove from favorites. Please try again.');
    }
}