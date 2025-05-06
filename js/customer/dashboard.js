/**
 * Customer Dashboard JavaScript
 * Handles dashboard page functionality
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    // Load featured artworks
    await loadFeaturedArtworks();
    
    // Load weekly collections
    await loadWeeklyCollections();
    
    // Load featured artists
    await loadFeaturedArtists();
    
    // Load cart and favorites previews
    await loadCartPreview();
    await loadFavoritesPreview();
    
    // Initialize search functionality
    initSearchFunctionality();
});

/**
 * Load featured artworks
 */
async function loadFeaturedArtworks() {
    try {
        // Get artwork container
        const container = document.getElementById('featuredArtworks');
        if (!container) return;
        
        // Clear container
        container.innerHTML = '';
        
        // Get artwork service
        const artworks = await artworkService.searchArtworks({
            status: 'approved',
            sortBy: 'featured'
        });
        
        // Display artworks (limit to 6)
        const featuredArtworks = artworks.slice(0, 6);
        
        if (featuredArtworks.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3 class="empty-state-title">No Artworks Found</h3>
                        <p class="empty-state-message">We're currently updating our featured collection. Please check back soon!</p>
                    </div>
                </div>
            `;
            return;
        }
        
        // Create HTML for each artwork
        featuredArtworks.forEach(artwork => {
            const artworkElement = document.createElement('div');
            artworkElement.className = 'col-md-6 col-lg-4';
            artworkElement.innerHTML = createArtworkCard(artwork);
            container.appendChild(artworkElement);
            
            // Add event listeners
            const favoriteBtn = artworkElement.querySelector('.btn-favorite');
            const quickviewBtn = artworkElement.querySelector('.btn-quickview');
            
            if (favoriteBtn) {
                favoriteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    toggleFavorite(artwork.id);
                });
            }
            
            if (quickviewBtn) {
                quickviewBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    showQuickView(artwork.id);
                });
            }
        });
    } catch (error) {
        console.error('Error loading featured artworks:', error);
        window.notifications.error('Failed to load featured artworks. Please try again later.');
    }
}

/**
 * Load weekly collections
 */
async function loadWeeklyCollections() {
    try {
        // Get collections container
        const container = document.getElementById('weeklyCollections');
        if (!container) return;
        
        // Clear container
        container.innerHTML = '';
        
        // Mock collections data (in a real app, this would come from an API)
        const collections = [
            {
                id: 'collection1',
                title: 'Modern Abstracts',
                description: 'Bold colors and striking forms define this collection of contemporary abstract pieces.',
                image: 'https://images.pexels.com/photos/1568607/pexels-photo-1568607.jpeg',
                artworkCount: 12
            },
            {
                id: 'collection2',
                title: 'Nature Inspired',
                description: 'Artworks that celebrate the beauty and serenity of natural landscapes.',
                image: 'https://images.pexels.com/photos/2119706/pexels-photo-2119706.jpeg',
                artworkCount: 8
            },
            {
                id: 'collection3',
                title: 'Urban Perspectives',
                description: 'Exploring city life through diverse artistic lenses.',
                image: 'https://images.pexels.com/photos/2563256/pexels-photo-2563256.jpeg',
                artworkCount: 10
            },
            {
                id: 'collection4',
                title: 'Sculptural Forms',
                description: 'Three-dimensional works that push the boundaries of form and material.',
                image: 'https://images.pexels.com/photos/134402/pexels-photo-134402.jpeg',
                artworkCount: 6
            }
        ];
        
        // Create HTML for each collection
        collections.forEach(collection => {
            const collectionElement = document.createElement('div');
            collectionElement.className = 'collection-card';
            collectionElement.innerHTML = `
                <div class="collection-image">
                    <img src="${collection.image}" alt="${collection.title}">
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">${collection.title}</h3>
                    <p class="collection-description">${collection.description}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-secondary">${collection.artworkCount} artworks</span>
                        <a href="collections.html?id=${collection.id}" class="btn btn-sm btn-outline-primary">View Collection</a>
                    </div>
                </div>
            `;
            container.appendChild(collectionElement);
        });
    } catch (error) {
        console.error('Error loading weekly collections:', error);
        window.notifications.error('Failed to load weekly collections. Please try again later.');
    }
}

/**
 * Load featured artists
 */
async function loadFeaturedArtists() {
    try {
        // Get artists container
        const container = document.getElementById('featuredArtists');
        if (!container) return;
        
        // Clear container
        container.innerHTML = '';
        
        // Get user service
        const artists = await userService.getArtists({
            status: 'approved'
        });
        
        // Display artists (limit to 4)
        const featuredArtists = artists.slice(0, 4);
        
        if (featuredArtists.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-user-artist"></i>
                        </div>
                        <h3 class="empty-state-title">No Artists Found</h3>
                        <p class="empty-state-message">We're currently updating our featured artists. Please check back soon!</p>
                    </div>
                </div>
            `;
            return;
        }
        
        // Create HTML for each artist
        featuredArtists.forEach(artist => {
            const artistElement = document.createElement('div');
            artistElement.className = 'col-6 col-md-3';
            artistElement.innerHTML = `
                <div class="artist-card">
                    <div class="artist-image">
                        <img src="${artist.profilePicture}" alt="${artist.name}">
                    </div>
                    <h3 class="artist-name">${artist.name}</h3>
                    <p class="artist-specialty">${artist.specialties ? artist.specialties.join(', ') : 'Artist'}</p>
                    <a href="artist.html?id=${artist.id}" class="btn btn-sm btn-outline-primary">View Profile</a>
                </div>
            `;
            container.appendChild(artistElement);
        });
    } catch (error) {
        console.error('Error loading featured artists:', error);
        window.notifications.error('Failed to load featured artists. Please try again later.');
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
 * Initialize search functionality
 */
function initSearchFunctionality() {
    const searchForm = document.querySelector('.search-form');
    if (!searchForm) return;
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const searchInput = this.querySelector('input').value.trim();
        if (searchInput) {
            // Redirect to search page with query parameter
            window.location.href = `search.html?q=${encodeURIComponent(searchInput)}`;
        }
    });
}

/**
 * Toggle favorite status for an artwork
 * @param {string} artworkId - Artwork ID
 */
async function toggleFavorite(artworkId) {
    try {
        // Get current user
        const currentUser = window.auth.currentUser;
        if (!currentUser) {
            window.notifications.warning('Please log in to add favorites');
            return;
        }
        
        // Get user data
        const user = await userService.getUserById(currentUser.id);
        if (!user) return;
        
        // Check if artwork is already in favorites
        const isFavorited = user.favorites.includes(artworkId);
        
        if (isFavorited) {
            // Remove from favorites
            if (user.removeFromFavorites(artworkId)) {
                window.notifications.success('Removed from favorites');
            }
        } else {
            // Add to favorites
            if (user.addToFavorites(artworkId)) {
                window.notifications.success('Added to favorites');
            }
        }
        
        // Update favorites preview
        await loadFavoritesPreview();
        
        // Update favorite button UI
        const favoriteButtons = document.querySelectorAll(`.btn-favorite[data-id="${artworkId}"]`);
        favoriteButtons.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                if (isFavorited) {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                }
            }
        });
    } catch (error) {
        console.error('Error toggling favorite:', error);
        window.notifications.error('Failed to update favorites. Please try again.');
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
            
            // Update favorite button UI
            const favoriteButtons = document.querySelectorAll(`.btn-favorite[data-id="${artworkId}"]`);
            favoriteButtons.forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
            });
        }
    } catch (error) {
        console.error('Error removing favorite:', error);
        window.notifications.error('Failed to remove from favorites. Please try again.');
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
 * Show quick view modal for an artwork
 * @param {string} artworkId - Artwork ID
 */
async function showQuickView(artworkId) {
    try {
        // Get artwork data
        const artwork = await artworkService.getArtworkById(artworkId);
        if (!artwork) return;
        
        // Check if modal already exists
        let modal = document.getElementById('quickViewModal');
        
        // Create modal if it doesn't exist
        if (!modal) {
            const modalElement = document.createElement('div');
            modalElement.className = 'modal fade';
            modalElement.id = 'quickViewModal';
            modalElement.tabIndex = -1;
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Artwork Preview</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Artwork details will be inserted here -->
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalElement);
            modal = modalElement;
        }
        
        // Update modal content
        const modalBody = modal.querySelector('.modal-body');
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${artwork.mainImage}" alt="${artwork.title}" class="img-fluid rounded mb-3">
                </div>
                <div class="col-md-6">
                    <h3>${artwork.title}</h3>
                    <p class="text-accent">${artwork.artistName}</p>
                    <p class="mb-2">${artwork.getShortDescription(200)}</p>
                    <p class="mb-3">
                        <strong>Medium:</strong> ${artwork.medium}<br>
                        <strong>Dimensions:</strong> ${artwork.getFormattedDimensions()}<br>
                        <strong>Year:</strong> ${artwork.year}
                    </p>
                    <h4 class="mb-3">${artwork.getFormattedPrice()}</h4>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary add-to-cart-btn" data-id="${artwork.id}">
                            Add to Cart
                        </button>
                        <a href="artwork.html?id=${artwork.id}" class="btn btn-outline-primary">
                            View Details
                        </a>
                        <button class="btn btn-outline-secondary view-in-room-btn" data-id="${artwork.id}">
                            <i class="fas fa-cubes me-2"></i> View in Room
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Initialize Bootstrap modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        
        // Add event listener to Add to Cart button
        const addToCartBtn = modalBody.querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                addToCart(artwork.id);
            });
        }
        
        // Add event listener to View in Room button
        const viewInRoomBtn = modalBody.querySelector('.view-in-room-btn');
        if (viewInRoomBtn) {
            viewInRoomBtn.addEventListener('click', () => {
                modalInstance.hide();
                window.location.href = `view-in-room.html?id=${artwork.id}`;
            });
        }
    } catch (error) {
        console.error('Error showing quick view:', error);
        window.notifications.error('Failed to load artwork details. Please try again.');
    }
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
 * Create HTML for artwork card
 * @param {Object} artwork - Artwork object
 * @returns {string} HTML string
 */
function createArtworkCard(artwork) {
    return `
        <div class="artwork-card">
            <div class="artwork-image">
                <img src="${artwork.getThumbnail()}" alt="${artwork.title}" loading="lazy">
                <div class="artwork-actions">
                    <button class="btn-favorite" data-id="${artwork.id}">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="btn-quickview" data-id="${artwork.id}">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="artwork-info">
                <h3 class="artwork-title">${artwork.title}</h3>
                <p class="artwork-artist">${artwork.artistName}</p>
                <p class="artwork-details">${artwork.category}, ${artwork.year}</p>
                <p class="artwork-dimensions">${artwork.getFormattedDimensions()}</p>
                <p class="artwork-price">${artwork.getFormattedPrice()}</p>
            </div>
        </div>
    `;
}