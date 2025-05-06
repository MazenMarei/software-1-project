/**
 * Main JavaScript file for ArtShelf
 * Handles common functionality across the site
 */

/**
 * Utility Class for common functions
 */
class Utilities {
    /**
     * Format price with currency symbol
     * @param {number} price - Price amount
     * @param {string} currency - Currency code
     * @returns {string} Formatted price string
     */
    static formatPrice(price, currency = 'usd') {
        const currencySymbols = {
            usd: '$',
            eur: '€',
            gbp: '£',
            cad: 'CAD $',
            aud: 'AUD $'
        };
        
        const symbol = currencySymbols[currency] || '$';
        return `${symbol}${price.toFixed(2)}`;
    }
    
    /**
     * Format date to locale string
     * @param {string|Date} date - Date to format
     * @returns {string} Formatted date string
     */
    static formatDate(date) {
        const dateObj = typeof date === 'string' ? new Date(date) : date;
        return dateObj.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    /**
     * Truncate text to specified length
     * @param {string} text - Text to truncate
     * @param {number} length - Maximum length
     * @returns {string} Truncated text
     */
    static truncateText(text, length = 100) {
        if (text.length <= length) return text;
        return text.substring(0, length) + '...';
    }
    
    /**
     * Generate a random ID
     * @returns {string} Random ID
     */
    static generateId() {
        return Math.random().toString(36).substring(2, 15) + 
               Math.random().toString(36).substring(2, 15);
    }
    
    /**
     * Debounce function to limit the rate at which a function can fire
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in milliseconds
     * @returns {Function} Debounced function
     */
    static debounce(func, wait = 300) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    
    /**
     * Get URL parameters as an object
     * @returns {Object} URL parameters
     */
    static getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    }
}

/**
 * Notification Manager for displaying system notifications
 */
class NotificationManager {
    constructor() {
        this.container = null;
        this.initContainer();
    }
    
    /**
     * Initialize notification container
     */
    initContainer() {
        // Check if container already exists
        if (document.getElementById('notification-container')) {
            this.container = document.getElementById('notification-container');
            return;
        }
        
        // Create container
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.style.position = 'fixed';
        this.container.style.top = '20px';
        this.container.style.right = '20px';
        this.container.style.zIndex = '9999';
        document.body.appendChild(this.container);
    }
    
    /**
     * Show notification
     * @param {string} message - Notification message
     * @param {string} type - Notification type (success, error, warning, info)
     * @param {number} duration - Duration in milliseconds
     */
    show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-icon">
                <i class="fas ${this.getIconClass(type)}"></i>
            </div>
            <div class="notification-content">
                <p>${message}</p>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Style the notification
        notification.style.backgroundColor = this.getBackgroundColor(type);
        notification.style.color = this.getTextColor(type);
        notification.style.padding = '15px';
        notification.style.borderRadius = '8px';
        notification.style.marginBottom = '10px';
        notification.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.maxWidth = '400px';
        notification.style.minWidth = '300px';
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        notification.style.transition = 'all 0.3s ease';
        
        // Add close button event
        const closeButton = notification.querySelector('.notification-close');
        closeButton.style.background = 'transparent';
        closeButton.style.border = 'none';
        closeButton.style.color = 'inherit';
        closeButton.style.cursor = 'pointer';
        closeButton.style.marginLeft = 'auto';
        closeButton.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Append to container
        this.container.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto hide after duration
        if (duration > 0) {
            setTimeout(() => {
                this.hideNotification(notification);
            }, duration);
        }
    }
    
    /**
     * Hide notification with animation
     * @param {HTMLElement} notification - Notification element
     */
    hideNotification(notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
    
    /**
     * Get background color based on notification type
     * @param {string} type - Notification type
     * @returns {string} Background color
     */
    getBackgroundColor(type) {
        switch (type) {
            case 'success': return '#dff2e8';
            case 'error': return '#f9e1e1';
            case 'warning': return '#fef7e0';
            case 'info':
            default: return '#e1f1fb';
        }
    }
    
    /**
     * Get text color based on notification type
     * @param {string} type - Notification type
     * @returns {string} Text color
     */
    getTextColor(type) {
        switch (type) {
            case 'success': return '#2e7d32';
            case 'error': return '#d32f2f';
            case 'warning': return '#ed6c02';
            case 'info':
            default: return '#0288d1';
        }
    }
    
    /**
     * Get icon class based on notification type
     * @param {string} type - Notification type
     * @returns {string} Icon class
     */
    getIconClass(type) {
        switch (type) {
            case 'success': return 'fa-check-circle';
            case 'error': return 'fa-exclamation-circle';
            case 'warning': return 'fa-exclamation-triangle';
            case 'info':
            default: return 'fa-info-circle';
        }
    }
    
    /**
     * Show success notification
     * @param {string} message - Notification message
     * @param {number} duration - Duration in milliseconds
     */
    success(message, duration = 5000) {
        this.show(message, 'success', duration);
    }
    
    /**
     * Show error notification
     * @param {string} message - Notification message
     * @param {number} duration - Duration in milliseconds
     */
    error(message, duration = 5000) {
        this.show(message, 'error', duration);
    }
    
    /**
     * Show warning notification
     * @param {string} message - Notification message
     * @param {number} duration - Duration in milliseconds
     */
    warning(message, duration = 5000) {
        this.show(message, 'warning', duration);
    }
    
    /**
     * Show info notification
     * @param {string} message - Notification message
     * @param {number} duration - Duration in milliseconds
     */
    info(message, duration = 5000) {
        this.show(message, 'info', duration);
    }
}

/**
 * Initialize common functionality when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialize notification manager
    window.notifications = new NotificationManager();
    
    // Initialize navbar scroll effect if navbar exists
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // Add smooth scrolling for all hash links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});