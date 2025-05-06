/**
 * Authentication Module for ArtShelf
 * Handles user login, registration, and authentication state
 */
class Authentication {
    constructor() {
        this.currentUser = null;
        this.initTabs();
        this.initFormSubmission();
        this.initPasswordToggle();
        this.checkAuthState();
    }

    /**
     * Initialize the authentication tabs
     */
    initTabs() {
        const authTabs = document.querySelectorAll('.auth-tab');
        const authForms = document.querySelectorAll('.auth-form');

        authTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and forms
                authTabs.forEach(t => t.classList.remove('active'));
                authForms.forEach(f => f.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Show the corresponding form
                const formId = `${tab.dataset.tab}-form`;
                document.getElementById(formId).classList.add('active');
            });
        });
    }

    /**
     * Initialize password visibility toggle
     */
    initPasswordToggle() {
        const passwordToggles = document.querySelectorAll('.password-toggle');
        
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const passwordInput = toggle.parentElement.querySelector('input');
                const icon = toggle.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    }

    /**
     * Initialize form submission handlers
     */
    initFormSubmission() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleLogin();
            });
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleRegistration();
            });
        }
    }

    /**
     * Handle user login
     */
    handleLogin() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        const rememberMe = document.getElementById('rememberMe').checked;

        // Simulate API call for login
        this.loginUser(email, password, rememberMe)
            .then(response => {
                if (response.success) {
                    this.setCurrentUser(response.user);
                    this.redirectToDashboard(response.user.role);
                } else {
                    this.showError(response.message);
                }
            })
            .catch(error => {
                this.showError('An error occurred during login. Please try again.');
                console.error('Login error:', error);
            });
    }

    /**
     * Handle user registration
     */
    handleRegistration() {
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const accountType = document.getElementById('accountType').value;
        const termsAgreed = document.getElementById('termsAgreement').checked;

        if (!termsAgreed) {
            this.showError('You must agree to the Terms of Service and Privacy Policy');
            return;
        }

        // Simulate API call for registration
        this.registerUser(name, email, password, accountType)
            .then(response => {
                if (response.success) {
                    if (accountType === 'artist') {
                        this.showSuccess('Your artist account has been created and is pending approval.');
                        // Switch to login tab after registration
                        document.querySelector('.auth-tab[data-tab="login"]').click();
                    } else {
                        this.setCurrentUser(response.user);
                        this.redirectToDashboard(response.user.role);
                    }
                } else {
                    this.showError(response.message);
                }
            })
            .catch(error => {
                this.showError('An error occurred during registration. Please try again.');
                console.error('Registration error:', error);
            });
    }

    /**
     * Simulate API call for user login
     * In a real app, this would be an actual API call
     */
    async loginUser(email, password, rememberMe) {
        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Mock login validation
        if (email === 'admin@artshelf.com' && password === 'admin123') {
            return {
                success: true,
                user: { id: 1, name: 'Admin User', email, role: 'admin' }
            };
        } else if (email === 'artist@artshelf.com' && password === 'artist123') {
            return {
                success: true,
                user: { id: 2, name: 'Artist User', email, role: 'artist' }
            };
        } else if (email === 'customer@artshelf.com' && password === 'customer123') {
            return {
                success: true,
                user: { id: 3, name: 'Customer User', email, role: 'customer' }
            };
        } else {
            return {
                success: false,
                message: 'Invalid email or password'
            };
        }
    }

    /**
     * Simulate API call for user registration
     * In a real app, this would be an actual API call
     */
    async registerUser(name, email, password, accountType) {
        // Simulate network delay
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Mock registration validation
        if (email === 'admin@artshelf.com' || email === 'artist@artshelf.com' || email === 'customer@artshelf.com') {
            return {
                success: false,
                message: 'This email is already registered'
            };
        }

        const role = accountType === 'artist' ? 'artist_pending' : 'customer';
        return {
            success: true,
            user: { id: Math.floor(Math.random() * 1000), name, email, role }
        };
    }

    /**
     * Set the current authenticated user
     */
    setCurrentUser(user) {
        this.currentUser = user;
        // Store user in local storage or session
        localStorage.setItem('currentUser', JSON.stringify(user));
    }

    /**
     * Check if user is already authenticated
     */
    checkAuthState() {
        const storedUser = localStorage.getItem('currentUser');
        if (storedUser) {
            this.currentUser = JSON.parse(storedUser);
            // If user is already logged in, redirect to appropriate dashboard
            this.redirectToDashboard(this.currentUser.role);
        }
    }

    /**
     * Redirect to appropriate dashboard based on user role
     */
    redirectToDashboard(role) {
        switch (role) {
            case 'admin':
                window.location.href = 'pages/admin/dashboard.html';
                break;
            case 'artist':
                window.location.href = 'pages/artist/dashboard.html';
                break;
            case 'customer':
                window.location.href = 'pages/customer/dashboard.html';
                break;
            case 'artist_pending':
                this.showSuccess('Your artist account is pending approval. You will be notified once approved.');
                break;
            default:
                console.error('Unknown role:', role);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        // In a real app, use a toast or alert component
        alert(message);
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        // In a real app, use a toast or alert component
        alert(message);
    }

    /**
     * Log out the current user
     */
    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        window.location.href = '/index.html';
    }
}

// Initialize authentication when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const auth = new Authentication();
    // Make auth globally accessible
    window.auth = auth;
});