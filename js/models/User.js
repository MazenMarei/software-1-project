/**
 * User Class
 * Base class for all user types in the system
 */
class User {
    /**
     * Create a new User
     * @param {Object} data - User data
     */
    constructor(data) {
        this.id = data.id || Utilities.generateId();
        this.name = data.name || '';
        this.email = data.email || '';
        this.role = data.role || 'customer'; // customer, artist, admin
        this.profilePicture = data.profilePicture || '';
        this.bio = data.bio || '';
        this.phone = data.phone || '';
        this.address = data.address || {
            street: '',
            city: '',
            state: '',
            zipCode: '',
            country: ''
        };
        this.createdAt = data.createdAt || new Date().toISOString();
        this.updatedAt = data.updatedAt || new Date().toISOString();
        this.isActive = data.isActive !== undefined ? data.isActive : true;
        this.preferences = data.preferences || {
            newsletter: true,
            notificationEmail: true,
            notificationSMS: false,
            currency: 'usd',
            language: 'en'
        };
    }
    
    /**
     * Update user profile
     * @param {Object} data - Updated user data
     */
    update(data) {
        if (data.name) this.name = data.name;
        if (data.email) this.email = data.email;
        if (data.profilePicture) this.profilePicture = data.profilePicture;
        if (data.bio) this.bio = data.bio;
        if (data.phone) this.phone = data.phone;
        
        if (data.address) {
            this.address = {
                ...this.address,
                ...data.address
            };
        }
        
        if (data.preferences) {
            this.preferences = {
                ...this.preferences,
                ...data.preferences
            };
        }
        
        this.updatedAt = new Date().toISOString();
    }
    
    /**
     * Get formatted creation date
     * @returns {string} Formatted date
     */
    getFormattedDate() {
        return Utilities.formatDate(this.createdAt);
    }
    
    /**
     * Get formatted address
     * @returns {string} Formatted address
     */
    getFormattedAddress() {
        const { street, city, state, zipCode, country } = this.address;
        let result = '';
        
        if (street) result += street;
        if (city) {
            if (result) result += ', ';
            result += city;
        }
        if (state) {
            if (result) result += ', ';
            result += state;
        }
        if (zipCode) {
            if (state || city) result += ' ';
            result += zipCode;
        }
        if (country) {
            if (result) result += ', ';
            result += country;
        }
        
        return result || 'No address provided';
    }
    
    /**
     * Convert to JSON for API requests
     * @returns {Object} JSON object
     */
    toJSON() {
        return {
            id: this.id,
            name: this.name,
            email: this.email,
            role: this.role,
            profilePicture: this.profilePicture,
            bio: this.bio,
            phone: this.phone,
            address: this.address,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            isActive: this.isActive,
            preferences: this.preferences
        };
    }
}

/**
 * Customer Class
 * Represents a customer user in the system
 */
class Customer extends User {
    /**
     * Create a new Customer
     * @param {Object} data - Customer data
     */
    constructor(data) {
        super(data);
        this.role = 'customer';
        this.favorites = data.favorites || [];
        this.following = data.following || [];
        this.cart = data.cart || [];
        this.orders = data.orders || [];
        this.balance = data.balance || 0;
        this.currency = data.currency || 'usd';
        this.giftCards = data.giftCards || [];
        this.referrals = data.referrals || {
            code: Utilities.generateId().substring(0, 8),
            referred: [],
            earned: 0
        };
    }
    
    /**
     * Add artwork to favorites
     * @param {string} artworkId - Artwork ID
     * @returns {boolean} Success flag
     */
    addToFavorites(artworkId) {
        if (!this.favorites.includes(artworkId)) {
            this.favorites.push(artworkId);
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Remove artwork from favorites
     * @param {string} artworkId - Artwork ID
     * @returns {boolean} Success flag
     */
    removeFromFavorites(artworkId) {
        const index = this.favorites.indexOf(artworkId);
        if (index !== -1) {
            this.favorites.splice(index, 1);
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Follow an artist
     * @param {string} artistId - Artist ID
     * @returns {boolean} Success flag
     */
    followArtist(artistId) {
        if (!this.following.includes(artistId)) {
            this.following.push(artistId);
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Unfollow an artist
     * @param {string} artistId - Artist ID
     * @returns {boolean} Success flag
     */
    unfollowArtist(artistId) {
        const index = this.following.indexOf(artistId);
        if (index !== -1) {
            this.following.splice(index, 1);
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Add item to cart
     * @param {string} artworkId - Artwork ID
     * @returns {boolean} Success flag
     */
    addToCart(artworkId) {
        if (!this.cart.some(item => item.artworkId === artworkId)) {
            this.cart.push({
                artworkId,
                addedAt: new Date().toISOString()
            });
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Remove item from cart
     * @param {string} artworkId - Artwork ID
     * @returns {boolean} Success flag
     */
    removeFromCart(artworkId) {
        const index = this.cart.findIndex(item => item.artworkId === artworkId);
        if (index !== -1) {
            this.cart.splice(index, 1);
            this.updatedAt = new Date().toISOString();
            return true;
        }
        return false;
    }
    
    /**
     * Get cart total
     * @param {Array} artworks - List of artwork objects
     * @returns {number} Cart total
     */
    getCartTotal(artworks) {
        let total = 0;
        this.cart.forEach(item => {
            const artwork = artworks.find(a => a.id === item.artworkId);
            if (artwork) {
                total += artwork.price;
            }
        });
        return total;
    }
    
    /**
     * Add balance
     * @param {number} amount - Amount to add
     * @returns {number} New balance
     */
    addBalance(amount) {
        this.balance += amount;
        this.updatedAt = new Date().toISOString();
        return this.balance;
    }
    
    /**
     * Get formatted balance
     * @returns {string} Formatted balance
     */
    getFormattedBalance() {
        return Utilities.formatPrice(this.balance, this.currency);
    }
    
    /**
     * Convert to JSON for API requests
     * @returns {Object} JSON object
     */
    toJSON() {
        return {
            ...super.toJSON(),
            favorites: this.favorites,
            following: this.following,
            cart: this.cart,
            orders: this.orders,
            balance: this.balance,
            currency: this.currency,
            giftCards: this.giftCards,
            referrals: this.referrals
        };
    }
}

/**
 * Artist Class
 * Represents an artist user in the system
 */
class Artist extends User {
    /**
     * Create a new Artist
     * @param {Object} data - Artist data
     */
    constructor(data) {
        super(data);
        this.role = 'artist';
        this.status = data.status || 'pending'; // pending, approved, rejected
        this.artworks = data.artworks || [];
        this.collections = data.collections || [];
        this.followers = data.followers || [];
        this.sales = data.sales || [];
        this.balance = data.balance || 0;
        this.currency = data.currency || 'usd';
        this.withdrawalRequests = data.withdrawalRequests || [];
        this.fairs = data.fairs || [];
        this.specialties = data.specialties || [];
        this.education = data.education || [];
        this.exhibitions = data.exhibitions || [];
    }
    
    /**
     * Create a new artwork
     * @param {Object} artworkData - Artwork data
     * @returns {Object} New artwork data
     */
    createArtwork(artworkData) {
        const artwork = new Artwork({
            ...artworkData,
            artistId: this.id,
            artistName: this.name,
            status: 'pending'
        });
        
        this.artworks.push(artwork.id);
        this.updatedAt = new Date().toISOString();
        
        return artwork;
    }
    
    /**
     * Create a new collection
     * @param {Object} collectionData - Collection data
     * @returns {Object} New collection data
     */
    createCollection(collectionData) {
        const collection = {
            id: Utilities.generateId(),
            artistId: this.id,
            title: collectionData.title || 'Untitled Collection',
            description: collectionData.description || '',
            artworks: collectionData.artworks || [],
            coverImage: collectionData.coverImage || '',
            isPublic: collectionData.isPublic !== undefined ? collectionData.isPublic : true,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        this.collections.push(collection.id);
        this.updatedAt = new Date().toISOString();
        
        return collection;
    }
    
    /**
     * Request a withdrawal
     * @param {Object} withdrawalData - Withdrawal request data
     * @returns {Object} Withdrawal request data
     */
    requestWithdrawal(withdrawalData) {
        if (this.balance <= 0) {
            throw new Error('Insufficient balance');
        }
        
        if (!withdrawalData.amount || withdrawalData.amount <= 0) {
            throw new Error('Invalid withdrawal amount');
        }
        
        if (withdrawalData.amount > this.balance) {
            throw new Error('Withdrawal amount exceeds available balance');
        }
        
        const withdrawal = {
            id: Utilities.generateId(),
            artistId: this.id,
            amount: withdrawalData.amount,
            currency: this.currency,
            status: 'pending', // pending, approved, rejected, completed
            paymentMethod: withdrawalData.paymentMethod || 'bank_transfer',
            paymentDetails: withdrawalData.paymentDetails || {},
            urgent: withdrawalData.urgent || false,
            notes: withdrawalData.notes || '',
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        this.withdrawalRequests.push(withdrawal);
        this.updatedAt = new Date().toISOString();
        
        return withdrawal;
    }
    
    /**
     * Get formatted balance
     * @returns {string} Formatted balance
     */
    getFormattedBalance() {
        return Utilities.formatPrice(this.balance, this.currency);
    }
    
    /**
     * Register for an art fair
     * @param {Object} fairData - Art fair data
     * @returns {Object} Fair registration data
     */
    registerForFair(fairData) {
        const fair = {
            id: Utilities.generateId(),
            artistId: this.id,
            name: fairData.name || '',
            location: fairData.location || '',
            startDate: fairData.startDate || '',
            endDate: fairData.endDate || '',
            description: fairData.description || '',
            booth: fairData.booth || '',
            status: 'pending', // pending, approved, rejected
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        this.fairs.push(fair);
        this.updatedAt = new Date().toISOString();
        
        return fair;
    }
    
    /**
     * Convert to JSON for API requests
     * @returns {Object} JSON object
     */
    toJSON() {
        return {
            ...super.toJSON(),
            status: this.status,
            artworks: this.artworks,
            collections: this.collections,
            followers: this.followers,
            sales: this.sales,
            balance: this.balance,
            currency: this.currency,
            withdrawalRequests: this.withdrawalRequests,
            fairs: this.fairs,
            specialties: this.specialties,
            education: this.education,
            exhibitions: this.exhibitions
        };
    }
}

/**
 * Admin Class
 * Represents an admin user in the system
 */
class Admin extends User {
    /**
     * Create a new Admin
     * @param {Object} data - Admin data
     */
    constructor(data) {
        super(data);
        this.role = 'admin';
        this.permissions = data.permissions || {
            manageUsers: true,
            manageArtworks: true,
            manageCollections: true,
            manageFairs: true,
            manageWithdrawals: true,
            manageOffers: true
        };
        this.actions = data.actions || [];
    }
    
    /**
     * Approve an artwork
     * @param {string} artworkId - Artwork ID
     * @param {string} notes - Approval notes
     * @returns {Object} Action data
     */
    approveArtwork(artworkId, notes = '') {
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'approve_artwork',
            targetId: artworkId,
            notes,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return action;
    }
    
    /**
     * Reject an artwork
     * @param {string} artworkId - Artwork ID
     * @param {string} reason - Rejection reason
     * @returns {Object} Action data
     */
    rejectArtwork(artworkId, reason = '') {
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'reject_artwork',
            targetId: artworkId,
            notes: reason,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return action;
    }
    
    /**
     * Approve an artist
     * @param {string} artistId - Artist ID
     * @returns {Object} Action data
     */
    approveArtist(artistId) {
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'approve_artist',
            targetId: artistId,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return action;
    }
    
    /**
     * Ban a user
     * @param {string} userId - User ID
     * @param {string} reason - Ban reason
     * @returns {Object} Action data
     */
    banUser(userId, reason = '') {
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'ban_user',
            targetId: userId,
            notes: reason,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return action;
    }
    
    /**
     * Create a weekly collection
     * @param {Object} collectionData - Collection data
     * @returns {Object} Collection data
     */
    createWeeklyCollection(collectionData) {
        const collection = {
            id: Utilities.generateId(),
            adminId: this.id,
            title: collectionData.title || 'Weekly Collection',
            description: collectionData.description || '',
            artworks: collectionData.artworks || [],
            coverImage: collectionData.coverImage || '',
            startDate: collectionData.startDate || new Date().toISOString(),
            endDate: collectionData.endDate || '',
            isActive: collectionData.isActive !== undefined ? collectionData.isActive : true,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'create_weekly_collection',
            targetId: collection.id,
            notes: `Created weekly collection: ${collection.title}`,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return collection;
    }
    
    /**
     * Launch a site-wide offer
     * @param {Object} offerData - Offer data
     * @returns {Object} Offer data
     */
    launchOffer(offerData) {
        if (!offerData.discountPercentage || offerData.discountPercentage <= 0 || offerData.discountPercentage > 15) {
            throw new Error('Discount percentage must be between 1 and 15');
        }
        
        const offer = {
            id: Utilities.generateId(),
            adminId: this.id,
            title: offerData.title || 'Special Offer',
            description: offerData.description || '',
            discountPercentage: offerData.discountPercentage,
            startDate: offerData.startDate || new Date().toISOString(),
            endDate: offerData.endDate || '',
            isActive: offerData.isActive !== undefined ? offerData.isActive : true,
            appliesTo: offerData.appliesTo || 'all', // all, specific_artworks, specific_artists
            targetIds: offerData.targetIds || [],
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        
        const action = {
            id: Utilities.generateId(),
            adminId: this.id,
            type: 'launch_offer',
            targetId: offer.id,
            notes: `Launched offer: ${offer.title} (${offer.discountPercentage}% discount)`,
            createdAt: new Date().toISOString()
        };
        
        this.actions.push(action);
        this.updatedAt = new Date().toISOString();
        
        return offer;
    }
    
    /**
     * Convert to JSON for API requests
     * @returns {Object} JSON object
     */
    toJSON() {
        return {
            ...super.toJSON(),
            permissions: this.permissions,
            actions: this.actions
        };
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { User, Customer, Artist, Admin };
} else {
    window.User = User;
    window.Customer = Customer;
    window.Artist = Artist;
    window.Admin = Admin;
}