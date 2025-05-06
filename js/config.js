/**
 * Configuration file for ArtShelf
 * Contains global settings and configuration
 */

const AppConfig = {
    // API configuration
    api: {
        baseUrl: '/api',
        timeout: 30000, // 30 seconds
        headers: {
            'Content-Type': 'application/json'
        }
    },
    
    // Stripe configuration
    stripe: {
        publishableKey: 'pk_test_TYooMQauvdEDq54NiTphI7jx', // Replace with your Stripe publishable key
        currency: 'usd',
        supportedCurrencies: ['usd', 'eur', 'gbp', 'cad', 'aud']
    },
    
    // Feature flags
    features: {
        viewInRoom: true,
        artAdvisor: true,
        eGiftCards: true,
        weeklyCollections: true,
        referralSystem: true
    },
    
    // Image settings
    images: {
        artworkThumbnail: {
            width: 300,
            height: 300,
            quality: 80
        },
        artworkLarge: {
            width: 1200,
            height: 1200,
            quality: 90
        },
        profilePicture: {
            width: 200,
            height: 200,
            quality: 80
        }
    },
    
    // Pagination defaults
    pagination: {
        artworksPerPage: 12,
        artistsPerPage: 10,
        collectionsPerPage: 6
    },
    
    // Art categories
    artCategories: [
        'Painting', 
        'Photography', 
        'Sculpture', 
        'Drawing', 
        'Digital Art', 
        'Mixed Media', 
        'Collage', 
        'Prints'
    ],
    
    // Art styles
    artStyles: [
        'Abstract', 
        'Contemporary', 
        'Figurative', 
        'Minimalism', 
        'Expressionism', 
        'Impressionism', 
        'Pop Art', 
        'Realism', 
        'Surrealism'
    ],
    
    // Gift card values
    giftCardValues: [50, 100, 200, 350],
    
    // Max discount percentage for admin offers
    maxDiscountPercentage: 15
};

// Freeze the config to prevent modifications
Object.freeze(AppConfig);