/**
 * Artwork Class
 * Represents an artwork in the system
 */
class Artwork {
    /**
     * Create a new Artwork
     * @param {Object} data - Artwork data
     */
    constructor(data) {
        this.id = data.id || Utilities.generateId();
        this.title = data.title || '';
        this.artistId = data.artistId || '';
        this.artistName = data.artistName || '';
        this.description = data.description || '';
        this.category = data.category || '';
        this.style = data.style || '';
        this.year = data.year || new Date().getFullYear();
        this.medium = data.medium || '';
        this.dimensions = data.dimensions || { width: 0, height: 0, depth: 0, unit: 'cm' };
        this.price = data.price || 0;
        this.currency = data.currency || 'usd';
        this.images = data.images || [];
        this.mainImage = data.mainImage || (this.images.length > 0 ? this.images[0] : '');
        this.tags = data.tags || [];
        this.status = data.status || 'pending'; // pending, approved, rejected, sold
        this.createdAt = data.createdAt || new Date().toISOString();
        this.updatedAt = data.updatedAt || new Date().toISOString();
        this.isFramed = data.isFramed || false;
        this.frameDetails = data.frameDetails || '';
        this.isLimited = data.isLimited || false;
        this.editionNumber = data.isLimited ? (data.editionNumber || '') : '';
        this.totalEditions = data.isLimited ? (data.totalEditions || '') : '';
        this.views = data.views || 0;
        this.favorites = data.favorites || 0;
        this.collectionIds = data.collectionIds || [];
    }
    
    /**
     * Get formatted price with currency symbol
     * @returns {string} Formatted price
     */
    getFormattedPrice() {
        return Utilities.formatPrice(this.price, this.currency);
    }
    
    /**
     * Get artwork dimensions as formatted string
     * @returns {string} Formatted dimensions
     */
    getFormattedDimensions() {
        const { width, height, depth, unit } = this.dimensions;
        let result = `${width} × ${height} ${unit}`;
        if (depth > 0) {
            result += ` × ${depth} ${unit}`;
        }
        return result;
    }
    
    /**
     * Check if artwork is available for purchase
     * @returns {boolean} Is available
     */
    isAvailable() {
        return this.status === 'approved' && this.status !== 'sold';
    }
    
    /**
     * Format creation date
     * @returns {string} Formatted date
     */
    getFormattedDate() {
        return Utilities.formatDate(this.createdAt);
    }
    
    /**
     * Get artwork thumbnail for preview
     * @returns {string} Thumbnail URL
     */
    getThumbnail() {
        return this.mainImage || (this.images.length > 0 ? this.images[0] : '');
    }
    
    /**
     * Get short description for previews
     * @param {number} length - Maximum length
     * @returns {string} Truncated description
     */
    getShortDescription(length = 100) {
        return Utilities.truncateText(this.description, length);
    }
    
    /**
     * Generate HTML for artwork card
     * @returns {string} HTML string
     */
    toCardHTML() {
        return `
            <div class="artwork-card" data-id="${this.id}">
                <div class="artwork-image">
                    <img src="${this.getThumbnail()}" alt="${this.title}" loading="lazy">
                    <div class="artwork-actions">
                        <button class="btn-favorite" data-id="${this.id}">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="btn-quickview" data-id="${this.id}">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="artwork-info">
                    <h3 class="artwork-title">${this.title}</h3>
                    <p class="artwork-artist">${this.artistName}</p>
                    <p class="artwork-details">${this.category}, ${this.year}</p>
                    <p class="artwork-dimensions">${this.getFormattedDimensions()}</p>
                    <p class="artwork-price">${this.getFormattedPrice()}</p>
                </div>
            </div>
        `;
    }
    
    /**
     * Generate HTML for artwork details
     * @returns {string} HTML string
     */
    toDetailHTML() {
        const tagsHTML = this.tags.map(tag => `<span class="tag">${tag}</span>`).join('');
        
        let editionInfo = '';
        if (this.isLimited) {
            editionInfo = `
                <div class="artwork-edition">
                    <h4>Limited Edition</h4>
                    <p>Edition ${this.editionNumber} of ${this.totalEditions}</p>
                </div>
            `;
        }
        
        return `
            <div class="artwork-detail" data-id="${this.id}">
                <div class="artwork-gallery">
                    <div class="artwork-main-image">
                        <img src="${this.mainImage}" alt="${this.title}">
                    </div>
                    <div class="artwork-thumbnails">
                        ${this.images.map(img => `
                            <div class="artwork-thumbnail">
                                <img src="${img}" alt="${this.title}" loading="lazy">
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="artwork-content">
                    <h2 class="artwork-title">${this.title}</h2>
                    <p class="artwork-artist">${this.artistName}</p>
                    <p class="artwork-price">${this.getFormattedPrice()}</p>
                    
                    <div class="artwork-actions">
                        <button class="btn btn-primary btn-add-to-cart" data-id="${this.id}">
                            Add to Cart
                        </button>
                        <button class="btn btn-outline-primary btn-view-in-room" data-id="${this.id}">
                            View in Room
                        </button>
                        <button class="btn btn-favorite" data-id="${this.id}">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="artwork-metadata">
                        <div class="metadata-item">
                            <h4>Details</h4>
                            <ul>
                                <li><strong>Category:</strong> ${this.category}</li>
                                <li><strong>Style:</strong> ${this.style}</li>
                                <li><strong>Year:</strong> ${this.year}</li>
                                <li><strong>Medium:</strong> ${this.medium}</li>
                                <li><strong>Dimensions:</strong> ${this.getFormattedDimensions()}</li>
                                ${this.isFramed ? `<li><strong>Framing:</strong> ${this.frameDetails}</li>` : ''}
                            </ul>
                        </div>
                        
                        ${editionInfo}
                        
                        <div class="artwork-description">
                            <h4>About this artwork</h4>
                            <p>${this.description}</p>
                        </div>
                        
                        <div class="artwork-tags">
                            ${tagsHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Convert to JSON for API requests
     * @returns {Object} JSON object
     */
    toJSON() {
        return {
            id: this.id,
            title: this.title,
            artistId: this.artistId,
            artistName: this.artistName,
            description: this.description,
            category: this.category,
            style: this.style,
            year: this.year,
            medium: this.medium,
            dimensions: this.dimensions,
            price: this.price,
            currency: this.currency,
            images: this.images,
            mainImage: this.mainImage,
            tags: this.tags,
            status: this.status,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            isFramed: this.isFramed,
            frameDetails: this.frameDetails,
            isLimited: this.isLimited,
            editionNumber: this.editionNumber,
            totalEditions: this.totalEditions,
            views: this.views,
            favorites: this.favorites,
            collectionIds: this.collectionIds
        };
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Artwork;
} else {
    window.Artwork = Artwork;
}