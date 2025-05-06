/**
 * Artwork Service
 * Handles artwork data operations
 */
class ArtworkService {
  constructor() {
    this.artworks = [];
    this.collections = [];
    this.loaded = false;
  }

  /**
   * Load artwork data
   * @returns {Promise<Array>} Artwork data
   */
  async loadArtworks() {
    if (this.loaded) return this.artworks;

    try {
      // In a real app, this would be an API call
      // For demo purposes, we'll load mock data
      const mockData = await this.fetchMockArtworks();
      this.artworks = mockData.map((data) => new Artwork(data));
      this.loaded = true;
      return this.artworks;
    } catch (error) {
      console.error("Failed to load artworks:", error);
      return [];
    }
  }

  /**
   * Get artwork by ID
   * @param {string} id - Artwork ID
   * @returns {Promise<Object|null>} Artwork object
   */
  async getArtworkById(id) {
    if (!this.loaded) await this.loadArtworks();
    return this.artworks.find((artwork) => artwork.id === id) || null;
  }

  /**
   * Search artworks by criteria
   * @param {Object} criteria - Search criteria
   * @returns {Promise<Array>} Filtered artworks
   */
  async searchArtworks(criteria = {}) {
    if (!this.loaded) await this.loadArtworks();

    let results = [...this.artworks];

    // Filter by status (default to approved)
    const status = criteria.status || "approved";
    results = results.filter((artwork) => artwork.status === status);

    // Filter by search term
    if (criteria.term) {
      const term = criteria.term.toLowerCase();
      results = results.filter(
        (artwork) =>
          artwork.title.toLowerCase().includes(term) ||
          artwork.artistName.toLowerCase().includes(term) ||
          artwork.description.toLowerCase().includes(term) ||
          artwork.tags.some((tag) => tag.toLowerCase().includes(term))
      );
    }

    // Filter by category
    if (criteria.category) {
      results = results.filter(
        (artwork) => artwork.category === criteria.category
      );
    }

    // Filter by style
    if (criteria.style) {
      results = results.filter((artwork) => artwork.style === criteria.style);
    }

    // Filter by price range
    if (criteria.minPrice !== undefined) {
      results = results.filter((artwork) => artwork.price >= criteria.minPrice);
    }
    if (criteria.maxPrice !== undefined) {
      results = results.filter((artwork) => artwork.price <= criteria.maxPrice);
    }

    // Filter by artist
    if (criteria.artistId) {
      results = results.filter(
        (artwork) => artwork.artistId === criteria.artistId
      );
    }

    // Filter by collection
    if (criteria.collectionId) {
      results = results.filter((artwork) =>
        artwork.collectionIds.includes(criteria.collectionId)
      );
    }

    // Sort results
    if (criteria.sortBy) {
      switch (criteria.sortBy) {
        case "priceAsc":
          results.sort((a, b) => a.price - b.price);
          break;
        case "priceDesc":
          results.sort((a, b) => b.price - a.price);
          break;
        case "newest":
          results.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
          break;
        case "popular":
          results.sort((a, b) => b.views - a.views);
          break;
        case "featured":
          results.sort((a, b) => b.favorites - a.favorites);
          break;
      }
    }

    return results;
  }

  /**
   * Fetch mock artwork data
   * @returns {Promise<Array>} Mock artwork data
   */
  async fetchMockArtworks() {
    // Simulate network delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock artwork data
    return [
      {
        id: "art1",
        title: "Abstract Harmony",
        artistId: "artist1",
        artistName: "Emma Reynolds",
        description:
          "A vibrant exploration of color and form, this abstract piece invites viewers to find their own meaning within its dynamic composition.",
        category: "Painting",
        style: "Abstract",
        year: 2023,
        medium: "Acrylic on canvas",
        dimensions: { width: 80, height: 100, depth: 3, unit: "cm" },
        price: 1200,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/1568607/pexels-photo-1568607.jpeg",
          "https://images.pexels.com/photos/1568607/pexels-photo-1568607.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/1568607/pexels-photo-1568607.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/1568607/pexels-photo-1568607.jpeg",
        tags: ["abstract", "colorful", "contemporary", "vibrant"],
        status: "approved",
        isFramed: true,
        frameDetails: "Minimal white wooden frame",
        views: 245,
        favorites: 42,
        collectionIds: ["collection1"],
      },
      {
        id: "art2",
        title: "Coastal Serenity",
        artistId: "artist2",
        artistName: "Michael Chen",
        description:
          "Inspired by the peaceful coastlines of the Pacific Northwest, this painting captures the tranquil essence of ocean meeting land.",
        category: "Painting",
        style: "Impressionism",
        year: 2022,
        medium: "Oil on canvas",
        dimensions: { width: 60, height: 90, depth: 2, unit: "cm" },
        price: 1800,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/2119706/pexels-photo-2119706.jpeg",
          "https://images.pexels.com/photos/2119706/pexels-photo-2119706.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/2119706/pexels-photo-2119706.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/2119706/pexels-photo-2119706.jpeg",
        tags: ["landscape", "ocean", "impressionism", "peaceful"],
        status: "approved",
        isFramed: false,
        views: 188,
        favorites: 31,
        collectionIds: ["collection2"],
      },
      {
        id: "art3",
        title: "Urban Reflection",
        artistId: "artist3",
        artistName: "Sophia Williams",
        description:
          "A photographic exploration of city architecture, capturing the interplay of light and glass in modern urban environments.",
        category: "Photography",
        style: "Contemporary",
        year: 2023,
        medium: "Digital photography on metallic paper",
        dimensions: { width: 50, height: 70, depth: 0, unit: "cm" },
        price: 850,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/2563256/pexels-photo-2563256.jpeg",
          "https://images.pexels.com/photos/2563256/pexels-photo-2563256.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/2563256/pexels-photo-2563256.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/2563256/pexels-photo-2563256.jpeg",
        tags: ["photography", "urban", "architecture", "reflection"],
        status: "approved",
        isFramed: true,
        frameDetails: "Black aluminum frame with museum glass",
        views: 310,
        favorites: 58,
        collectionIds: ["collection3"],
      },
      {
        id: "art4",
        title: "Bronze Elegance",
        artistId: "artist4",
        artistName: "Marcus Johnson",
        description:
          "This bronze sculpture explores the grace of human form through simplified, flowing lines and dynamic movement.",
        category: "Sculpture",
        style: "Figurative",
        year: 2021,
        medium: "Cast bronze",
        dimensions: { width: 30, height: 45, depth: 20, unit: "cm" },
        price: 3200,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/134402/pexels-photo-134402.jpeg",
          "https://images.pexels.com/photos/134402/pexels-photo-134402.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/134402/pexels-photo-134402.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/134402/pexels-photo-134402.jpeg",
        tags: ["sculpture", "bronze", "figurative", "movement"],
        status: "approved",
        isFramed: false,
        views: 142,
        favorites: 26,
        collectionIds: ["collection4"],
      },
      {
        id: "art5",
        title: "Memory Fragments",
        artistId: "artist5",
        artistName: "Olivia Rodriguez",
        description:
          "A mixed media collage that weaves together personal photographs, found objects, and painted elements to explore the nature of memory.",
        category: "Mixed Media",
        style: "Contemporary",
        year: 2023,
        medium: "Mixed media on wood panel",
        dimensions: { width: 45, height: 60, depth: 4, unit: "cm" },
        price: 1450,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/1061778/pexels-photo-1061778.jpeg",
          "https://images.pexels.com/photos/1061778/pexels-photo-1061778.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/1061778/pexels-photo-1061778.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/1061778/pexels-photo-1061778.jpeg",
        tags: ["mixed media", "collage", "memory", "personal"],
        status: "approved",
        isFramed: false,
        views: 174,
        favorites: 37,
        collectionIds: ["collection5"],
      },
      {
        id: "art6",
        title: "Digital Dreams",
        artistId: "artist6",
        artistName: "Aiden Park",
        description:
          "A digital artwork exploring the intersection of technology and imagination, creating surreal landscapes that exist only in digital space.",
        category: "Digital Art",
        style: "Surrealism",
        year: 2023,
        medium: "Digital art, giclee print on archival paper",
        dimensions: { width: 40, height: 60, depth: 0, unit: "cm" },
        price: 780,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/3222686/pexels-photo-3222686.jpeg",
          "https://images.pexels.com/photos/3222686/pexels-photo-3222686.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/3222686/pexels-photo-3222686.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/3222686/pexels-photo-3222686.jpeg",
        tags: ["digital", "surreal", "technology", "landscape"],
        status: "approved",
        isFramed: true,
        frameDetails: "Floating frame with UV-protective glass",
        isLimited: true,
        editionNumber: "3",
        totalEditions: "25",
        views: 425,
        favorites: 83,
        collectionIds: ["collection6"],
      },
      // Adding new pending artworks
      {
        id: "art7",
        title: "Ethereal Whispers",
        artistId: "artist2",
        artistName: "Michael Chen",
        description:
          "An exploration of light and shadow through delicate brushstrokes, creating an atmosphere of mystery and contemplation.",
        category: "Painting",
        style: "Abstract",
        year: 2025,
        medium: "Oil and gold leaf on canvas",
        dimensions: { width: 70, height: 100, depth: 3, unit: "cm" },
        price: 2400,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/1616403/pexels-photo-1616403.jpeg",
          "https://images.pexels.com/photos/1616403/pexels-photo-1616403.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/1616403/pexels-photo-1616403.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/1616403/pexels-photo-1616403.jpeg",
        tags: ["abstract", "gold leaf", "ethereal", "shadows", "modern"],
        status: "pending",
        isFramed: true,
        frameDetails: "Floating gold-trimmed frame",
        views: 47,
        favorites: 12,
        collectionIds: [],
      },
      {
        id: "art8",
        title: "Urban Fragments",
        artistId: "artist3",
        artistName: "Sophia Williams",
        description:
          "A photographic series capturing the beauty in decaying urban landscapes, focusing on textures and patterns often overlooked.",
        category: "Photography",
        style: "Urban",
        year: 2025,
        medium: "Digital photography on aluminum",
        dimensions: { width: 60, height: 40, depth: 0, unit: "cm" },
        price: 950,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/2096700/pexels-photo-2096700.jpeg",
          "https://images.pexels.com/photos/2096700/pexels-photo-2096700.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/2096700/pexels-photo-2096700.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/2096700/pexels-photo-2096700.jpeg",
        tags: ["urban", "decay", "photography", "texture", "black and white"],
        status: "pending",
        isFramed: false,
        views: 28,
        favorites: 8,
        collectionIds: [],
      },
      {
        id: "art9",
        title: "Organic Formations",
        artistId: "artist5",
        artistName: "Olivia Rodriguez",
        description:
          "A ceramic sculpture inspired by natural forms and biological structures, exploring the intersection of art and science.",
        category: "Sculpture",
        style: "Organic",
        year: 2025,
        medium: "Glazed ceramic",
        dimensions: { width: 35, height: 40, depth: 35, unit: "cm" },
        price: 1850,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/2123337/pexels-photo-2123337.jpeg",
          "https://images.pexels.com/photos/2123337/pexels-photo-2123337.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/2123337/pexels-photo-2123337.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/2123337/pexels-photo-2123337.jpeg",
        tags: ["sculpture", "ceramic", "organic", "biomorphic", "handcrafted"],
        status: "pending",
        isFramed: false,
        views: 15,
        favorites: 3,
        collectionIds: [],
      },
      {
        id: "art10",
        title: "Digital Dystopia",
        artistId: "artist6",
        artistName: "Aiden Park",
        description:
          "A series of digital illustrations exploring a speculative future where technology has overtaken natural environments.",
        category: "Digital Art",
        style: "Sci-Fi",
        year: 2025,
        medium: "Digital art, archival inkjet print",
        dimensions: { width: 50, height: 70, depth: 0, unit: "cm" },
        price: 890,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/1493226/pexels-photo-1493226.jpeg",
          "https://images.pexels.com/photos/1493226/pexels-photo-1493226.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/1493226/pexels-photo-1493226.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/1493226/pexels-photo-1493226.jpeg",
        tags: ["digital", "dystopian", "sci-fi", "futuristic", "technology"],
        status: "pending",
        isFramed: true,
        frameDetails: "Minimalist black aluminum frame",
        isLimited: true,
        editionNumber: "2",
        totalEditions: "15",
        views: 37,
        favorites: 14,
        collectionIds: [],
      },
      {
        id: "art11",
        title: "Woven Memories",
        artistId: "artist1",
        artistName: "Emma Reynolds",
        description:
          "A textile art piece incorporating traditional weaving techniques with contemporary materials and concepts of memory.",
        category: "Textile Art",
        style: "Contemporary",
        year: 2025,
        medium: "Mixed fibers, found objects",
        dimensions: { width: 90, height: 120, depth: 5, unit: "cm" },
        price: 2100,
        currency: "usd",
        images: [
          "https://images.pexels.com/photos/6044266/pexels-photo-6044266.jpeg",
          "https://images.pexels.com/photos/6044266/pexels-photo-6044266.jpeg?auto=compress&cs=tinysrgb&w=400",
          "https://images.pexels.com/photos/6044266/pexels-photo-6044266.jpeg?auto=compress&cs=tinysrgb&w=200",
        ],
        mainImage:
          "https://images.pexels.com/photos/6044266/pexels-photo-6044266.jpeg",
        tags: ["textile", "weaving", "mixed media", "memory", "handcrafted"],
        status: "pending",
        isFramed: false,
        views: 22,
        favorites: 5,
        collectionIds: [],
      },
    ];
  }
}

// Create a singleton instance
const artworkService = new ArtworkService();

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = artworkService;
} else {
  window.artworkService = artworkService;
}
