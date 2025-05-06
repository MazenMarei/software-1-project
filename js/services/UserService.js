/**
 * User Service
 * Handles user data operations
 */
class UserService {
  constructor() {
    this.users = [];
    this.loaded = false;
  }

  /**
   * Load user data
   * @returns {Promise<Array>} User data
   */
  async loadUsers() {
    if (this.loaded) return this.users;

    try {
      // In a real app, this would be an API call
      // For demo purposes, we'll load mock data
      const mockData = await this.fetchMockUsers();

      this.users = mockData.map((data) => {
        switch (data.role) {
          case "customer":
            return new Customer(data);
          case "artist":
            return new Artist(data);
          case "admin":
            return new Admin(data);
          default:
            return new User(data);
        }
      });

      this.loaded = true;
      return this.users;
    } catch (error) {
      console.error("Failed to load users:", error);
      return [];
    }
  }

  /**
   * Get user by ID
   * @param {string} id - User ID
   * @returns {Promise<Object|null>} User object
   */
  async getUserById(id) {
    if (!this.loaded) await this.loadUsers();
    return this.users.find((user) => user.id === id) || null;
  }

  /**
   * Get user by email
   * @param {string} email - User email
   * @returns {Promise<Object|null>} User object
   */
  async getUserByEmail(email) {
    if (!this.loaded) await this.loadUsers();
    return this.users.find((user) => user.email === email) || null;
  }

  /**
   * Get artists
   * @param {Object} filters - Filters to apply
   * @returns {Promise<Array>} Artist users
   */
  async getArtists(filters = {}) {
    if (!this.loaded) await this.loadUsers();
    let artists = this.users.filter((user) => user.role === "artist");

    // Apply filters
    if (filters.status) {
      artists = artists.filter((artist) => artist.status === filters.status);
    }

    if (filters.specialties && filters.specialties.length > 0) {
      artists = artists.filter((artist) =>
        filters.specialties.some((specialty) =>
          artist.specialties.includes(specialty)
        )
      );
    }

    // Sort by name by default
    artists.sort((a, b) => a.name.localeCompare(b.name));

    return artists;
  }

  /**
   * Get customers
   * @param {Object} filters - Filters to apply
   * @returns {Promise<Array>} Customer users
   */
  async getCustomers(filters = {}) {
    if (!this.loaded) await this.loadUsers();
    let customers = this.users.filter((user) => user.role === "customer");

    // Apply filters
    if (filters.isActive !== undefined) {
      customers = customers.filter(
        (customer) => customer.isActive === filters.isActive
      );
    }

    // Sort by name by default
    customers.sort((a, b) => a.name.localeCompare(b.name));

    return customers;
  }

  /**
   * Fetch mock user data
   * @returns {Promise<Array>} Mock user data
   */
  async fetchMockUsers() {
    // Simulate network delay
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Mock user data
    return [
      {
        id: "admin1",
        name: "Admin User",
        email: "admin@artshelf.com",
        role: "admin",
        profilePicture:
          "https://images.pexels.com/photos/3779448/pexels-photo-3779448.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "System administrator managing the ArtShelf platform.",
        createdAt: "2022-01-01T00:00:00Z",
        permissions: {
          manageUsers: true,
          manageArtworks: true,
          manageCollections: true,
          manageFairs: true,
          manageWithdrawals: true,
          manageOffers: true,
        },
        actions: [],
      },
      {
        id: "artist1",
        name: "Emma Reynolds",
        email: "artist@artshelf.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/3812743/pexels-photo-3812743.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Contemporary artist specializing in abstract acrylic paintings that explore themes of color and emotion.",
        phone: "+1234567890",
        address: {
          street: "123 Art Studio Ln",
          city: "Portland",
          state: "OR",
          zipCode: "97205",
          country: "USA",
        },
        createdAt: "2022-03-15T00:00:00Z",
        status: "approved",
        artworks: ["art1"],
        collections: ["collection1"],
        followers: ["customer1", "customer2"],
        sales: [],
        balance: 2400,
        currency: "usd",
        specialties: ["Abstract", "Contemporary"],
        education: [
          {
            institution: "Rhode Island School of Design",
            degree: "BFA Painting",
            year: "2019",
          },
        ],
        exhibitions: [
          {
            title: "Color Theory",
            venue: "Portland Modern Art Gallery",
            year: "2021",
          },
          {
            title: "New Perspectives",
            venue: "Seattle Art Space",
            year: "2022",
          },
        ],
      },
      {
        id: "artist2",
        name: "Michael Chen",
        email: "michael@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Landscape painter inspired by the natural beauty of coastal regions and mountains.",
        createdAt: "2022-04-20T00:00:00Z",
        status: "approved",
        artworks: ["art2"],
        collections: ["collection2"],
        followers: ["customer3"],
        specialties: ["Landscape", "Impressionism"],
        education: [
          {
            institution: "California Institute of the Arts",
            degree: "MFA Painting",
            year: "2018",
          },
        ],
      },
      {
        id: "customer1",
        name: "John Smith",
        email: "customer@artshelf.com",
        role: "customer",
        profilePicture:
          "https://images.pexels.com/photos/1681010/pexels-photo-1681010.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Art enthusiast and collector with a passion for contemporary paintings.",
        createdAt: "2022-02-10T00:00:00Z",
        favorites: ["art1", "art3"],
        following: ["artist1", "artist3"],
        orders: [],
        balance: 150,
        currency: "usd",
      },
      {
        id: "customer2",
        name: "Lisa Johnson",
        email: "lisa@example.com",
        role: "customer",
        profilePicture:
          "https://images.pexels.com/photos/1065084/pexels-photo-1065084.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Interior designer who loves incorporating original artwork into client spaces.",
        createdAt: "2022-05-05T00:00:00Z",
        favorites: ["art2", "art5"],
        following: ["artist1", "artist4"],
        orders: [],
      },
      // Adding pending artists for approval/rejection functionality
      {
        id: "artist3",
        name: "Sophia Williams",
        email: "sophia@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/1181686/pexels-photo-1181686.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Urban photographer documenting the evolution of city landscapes and architectural forms through high-contrast black and white compositions.",
        phone: "+1987654321",
        address: {
          street: "456 Gallery Row",
          city: "New York",
          state: "NY",
          zipCode: "10001",
          country: "USA",
        },
        createdAt: "2025-03-15T00:00:00Z",
        status: "pending",
        artworks: ["art3", "art8"],
        collections: [],
        followers: [],
        sales: [],
        balance: 0,
        currency: "usd",
        specialties: ["Photography", "Urban", "Architecture"],
        education: [
          {
            institution: "School of Visual Arts",
            degree: "BFA Photography",
            field: "Fine Art Photography",
            year: "2022",
          },
        ],
        exhibitions: [
          {
            title: "Urban Perspectives",
            venue: "Brooklyn Gallery",
            location: "New York",
            year: "2023",
          },
        ],
      },
      {
        id: "artist4",
        name: "Marcus Johnson",
        email: "marcus@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/1681010/pexels-photo-1681010.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Sculptor focused on creating expressive bronze pieces that capture the essence of human movement and emotion.",
        phone: "+1555777888",
        address: {
          street: "789 Studio Ave",
          city: "Chicago",
          state: "IL",
          zipCode: "60601",
          country: "USA",
        },
        createdAt: "2025-02-22T00:00:00Z",
        status: "pending",
        artworks: ["art4"],
        collections: [],
        followers: [],
        sales: [],
        balance: 0,
        currency: "usd",
        specialties: ["Sculpture", "Bronze", "Figurative"],
        education: [
          {
            institution: "Art Institute of Chicago",
            degree: "MFA",
            field: "Sculpture",
            year: "2020",
          },
          {
            institution: "University of Michigan",
            degree: "BFA",
            field: "Fine Arts",
            year: "2018",
          },
        ],
        exhibitions: [
          {
            title: "Form and Motion",
            venue: "Chicago Contemporary Arts Center",
            location: "Chicago",
            year: "2024",
          },
        ],
      },
      {
        id: "artist5",
        name: "Olivia Rodriguez",
        email: "olivia@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Mixed media artist exploring themes of memory, identity, and cultural heritage through layered compositions that blend traditional and contemporary techniques.",
        phone: "+1222333444",
        address: {
          street: "101 Arts District Blvd",
          city: "Los Angeles",
          state: "CA",
          zipCode: "90012",
          country: "USA",
        },
        createdAt: "2025-01-30T00:00:00Z",
        status: "pending",
        artworks: ["art5", "art9"],
        collections: [],
        followers: [],
        sales: [],
        balance: 0,
        currency: "usd",
        specialties: [
          "Mixed Media",
          "Contemporary",
          "Sculpture",
          "Textile Art",
        ],
        education: [
          {
            institution: "UCLA",
            degree: "MFA",
            field: "Interdisciplinary Studio Art",
            year: "2022",
          },
        ],
        exhibitions: [
          {
            title: "Memory Fragments",
            venue: "LA Contemporary",
            location: "Los Angeles",
            year: "2024",
          },
          {
            title: "Identity in Layers",
            venue: "Museum of Contemporary Art",
            location: "San Diego",
            year: "2023",
          },
        ],
      },
      {
        id: "artist6",
        name: "Aiden Park",
        email: "aiden@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Digital artist and illustrator pushing the boundaries between traditional art forms and new technologies, specializing in immersive digital landscapes and concept art.",
        phone: "+1888999000",
        address: {
          street: "202 Tech Arts Way",
          city: "San Francisco",
          state: "CA",
          zipCode: "94103",
          country: "USA",
        },
        createdAt: "2025-04-05T00:00:00Z",
        status: "pending",
        artworks: ["art6", "art10"],
        collections: [],
        followers: [],
        sales: [],
        balance: 0,
        currency: "usd",
        specialties: ["Digital Art", "Illustration", "Concept Art", "NFTs"],
        education: [
          {
            institution: "Academy of Art University",
            degree: "BFA",
            field: "Illustration and Digital Art",
            year: "2021",
          },
        ],
        exhibitions: [
          {
            title: "Digital Frontiers",
            venue: "SF Gallery of Digital Arts",
            location: "San Francisco",
            year: "2024",
          },
        ],
      },
      {
        id: "artist7",
        name: "Isabella Fernandez",
        email: "isabella@example.com",
        role: "artist",
        profilePicture:
          "https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=200",
        bio: "Textile artist weaving traditional techniques with contemporary designs, exploring themes of cultural heritage and sustainability through handcrafted textiles.",
        phone: "+1444555666",
        address: {
          street: "303 Textile Lane",
          city: "Santa Fe",
          state: "NM",
          zipCode: "87501",
          country: "USA",
        },
        createdAt: "2025-03-28T00:00:00Z",
        status: "pending",
        artworks: ["art11"],
        collections: [],
        followers: [],
        sales: [],
        balance: 0,
        currency: "usd",
        specialties: [
          "Textile Art",
          "Weaving",
          "Fiber Arts",
          "Sustainable Art",
        ],
        education: [
          {
            institution: "Savannah College of Art and Design",
            degree: "MFA",
            field: "Fibers",
            year: "2020",
          },
        ],
        exhibitions: [
          {
            title: "Woven Stories",
            venue: "Santa Fe Textile Museum",
            location: "Santa Fe",
            year: "2024",
          },
        ],
      },
    ];
  }
}

// Create a singleton instance
const userService = new UserService();

// Export for use in other modules
if (typeof module !== "undefined" && module.exports) {
  module.exports = userService;
} else {
  window.userService = userService;
}
