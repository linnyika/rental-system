import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap';


import '../css/app.css';
import '../css/dashboard.css';
import '../css/table.css';
import '../css/forms.css';
import '../css/cards.css';
import '../css/responsive.css';
import '../css/auth.css';

import './sidebar.js';
import './modals.js';
import './notifications.js';
import './charts.js';
import './dashboard.js';
import './api.js';

window.bootstrap = bootstrap;

// ========================================
// GLOBAL CONFIGURATION
// ========================================
window.SystemUI = {
    // Application Info
    app: {
        name: 'System Name',
        version: '1.0.0',
        environment: 'production'
    },

    // Color Palette
    colors: {
        primary: '#055236',
        primaryLight: '#7FA48E',
        secondary: '#2D4C39',
        accent: '#80B9B1',
        accentPurple: '#6C27DA',
        success: '#28a745',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    },

    // Toast Defaults
    toast: {
        delay: 5000,
        autohide: true,
        position: 'top-right'
    },

    // Modal Defaults
    modal: {
        backdrop: 'static',
        keyboard: false,
        focus: true
    },

    // API Configuration
    api: {
        baseUrl: '/api',
        timeout: 30000,
        retryAttempts: 3
    },

    // Image Paths
    images: {
        logo: '/images/logo.jpg',
        background: '/images/background-photo.jpg',
        fallback: '/images/fallback.png'
    },

    // Date/Time Formatting
    dateFormat: {
        short: 'MMM D, YYYY',
        long: 'MMMM D, YYYY HH:mm:ss',
        time: 'HH:mm:ss'
    },

    // Currency Formatting
    currency: {
        code: 'KES',
        symbol: 'KSh',
        locale: 'en-KE'
    }
};

// ========================================
// APPLICATION INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap Components
    initBootstrapComponents();

    // Initialize Global Event Listeners
    initGlobalListeners();

    // Handle CSRF Token for AJAX
    setupCSRFProtection();

    // Initialize Auto-hiding Alerts
    initAutoHideAlerts();

    // Handle Form Submissions
    initFormSubmissions();

    // Initialize Image Lazy Loading
    initLazyLoading();

    // Set Application Ready State
    document.body.classList.add('app-ready');
});

// ========================================
// BOOTSTRAP COMPONENTS INITIALIZATION
// ========================================
function initBootstrapComponents() {
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            container: 'body',
            delay: { show: 200, hide: 100 }
        });
    });

    // Popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            container: 'body',
            trigger: 'hover focus'
        });
    });

    // Dropdowns
    const dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownTriggerList.map(function(dropdownTriggerEl) {
        return new bootstrap.Dropdown(dropdownTriggerEl);
    });

    // Tabs
    const tabTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tab"]'));
    tabTriggerList.map(function(tabTriggerEl) {
        return new bootstrap.Tab(tabTriggerEl);
    });

    // Collapse
    const collapseTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="collapse"]'));
    collapseTriggerList.map(function(collapseTriggerEl) {
        return new bootstrap.Collapse(collapseTriggerEl, {
            toggle: false
        });
    });
}

// ========================================
// GLOBAL EVENT LISTENERS
// ========================================
function initGlobalListeners() {
    // Handle AJAX Loading States
    document.addEventListener('ajaxStart', function() {
        document.body.classList.add('ajax-loading');
    });

    document.addEventListener('ajaxComplete', function() {
        document.body.classList.remove('ajax-loading');
    });

    // Handle Network Status
    window.addEventListener('online', function() {
        showNetworkStatus('online');
    });

    window.addEventListener('offline', function() {
        showNetworkStatus('offline');
    });

    // Handle Page Visibility
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            document.body.classList.add('page-hidden');
        } else {
            document.body.classList.remove('page-hidden');
            // Refresh data if needed
            if (window.SystemUI.dashboard) {
                window.SystemUI.dashboard.refresh();
            }
        }
    });

    // Handle Window Resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            window.dispatchEvent(new CustomEvent('resizeEnd'));
        }, 250);
    });
}

// ========================================
// CSRF PROTECTION
// ========================================
function setupCSRFProtection() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) {
        window.csrfToken = token;

        // Add to all AJAX requests
        document.addEventListener('ajaxBeforeSend', function(e) {
            const xhr = e.detail.xhr;
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
        });

        // Add to fetch requests globally
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            options.headers = options.headers || {};
            if (!options.headers['X-CSRF-TOKEN'] && window.csrfToken) {
                options.headers['X-CSRF-TOKEN'] = window.csrfToken;
            }
            return originalFetch.call(this, url, options);
        };
    }
}

// ========================================
// AUTO-HIDE ALERTS
// ========================================
function initAutoHideAlerts() {
    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        const delay = parseInt(alert.getAttribute('data-delay')) || 5000;
        if (delay > 0) {
            setTimeout(function() {
                const bsAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
                bsAlert.close();
            }, delay);
        }
    });
}

// ========================================
// FORM SUBMISSIONS
// ========================================
function initFormSubmissions() {
    document.querySelectorAll('form[data-ajax-submit]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleAjaxFormSubmit(this);
        });
    });
}

async function handleAjaxFormSubmit(form) {
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn?.innerHTML || 'Submit';

    try {
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        }

        const formData = new FormData(form);
        const method = form.method || 'POST';
        const action = form.action || window.location.href;

        const response = await fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken || ''
            }
        });

        const result = await response.json();

        if (result.success) {
            // Show success message
            window.SystemUI.Notifications.success(result.message || 'Form submitted successfully!');

            // Handle redirect if specified
            if (result.redirect) {
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            }

            // Reset form if specified
            if (form.dataset.resetOnSuccess !== 'false') {
                form.reset();
            }
        } else {
            throw new Error(result.message || 'Form submission failed');
        }
    } catch (error) {
        window.SystemUI.Notifications.error(error.message || 'An error occurred');
    } finally {
        // Reset button state
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

// ========================================
// IMAGE LAZY LOADING
// ========================================
function initLazyLoading() {
    // Native lazy loading for images
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
        img.addEventListener('error', function() {
            this.classList.add('error');
            if (this.dataset.fallback) {
                this.src = this.dataset.fallback;
            }
        });
    });

    // Intersection Observer for background images
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    if (el.dataset.bgImage) {
                        el.style.backgroundImage = `url(${el.dataset.bgImage})`;
                        el.classList.add('bg-loaded');
                    }
                    observer.unobserve(el);
                }
            });
        });

        document.querySelectorAll('[data-bg-image]').forEach(el => {
            observer.observe(el);
        });
    }
}

// ========================================
// NETWORK STATUS
// ========================================
function showNetworkStatus(status) {
    const message = status === 'online'
        ? 'You are back online!'
        : 'You are offline. Please check your connection.';
    const type = status === 'online' ? 'success' : 'error';

    if (window.SystemUI.Notifications) {
        window.SystemUI.Notifications[type](message);
    }
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

// Format Currency
window.formatCurrency = function(amount, currency = null) {
    const config = window.SystemUI.currency;
    const code = currency || config.code;

    return new Intl.NumberFormat(config.locale, {
        style: 'currency',
        currency: code,
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(amount);
};

// Format Date
window.formatDate = function(date, format = null) {
    const config = window.SystemUI.dateFormat;
    const formatStr = format || config.short;
    const d = new Date(date);

    if (isNaN(d.getTime())) {
        return 'Invalid Date';
    }

    const map = {
        'MMM': d.toLocaleString('en-US', { month: 'short' }),
        'MMMM': d.toLocaleString('en-US', { month: 'long' }),
        'D': d.getDate(),
        'DD': String(d.getDate()).padStart(2, '0'),
        'YYYY': d.getFullYear(),
        'YY': String(d.getFullYear()).slice(-2),
        'HH': String(d.getHours()).padStart(2, '0'),
        'mm': String(d.getMinutes()).padStart(2, '0'),
        'ss': String(d.getSeconds()).padStart(2, '0')
    };

    let result = formatStr;
    for (const [key, value] of Object.entries(map)) {
        result = result.replace(key, value);
    }
    return result;
};

// Truncate Text
window.truncateText = function(text, length = 50, suffix = '...') {
    if (!text || text.length <= length) return text;
    return text.substring(0, length) + suffix;
};

// Debounce Function
window.debounce = function(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Throttle Function
window.throttle = function(func, limit = 300) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// Generate Random ID
window.generateId = function(prefix = 'id') {
    return `${prefix}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
};

// Copy to Clipboard
window.copyToClipboard = function(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        return navigator.clipboard.writeText(text)
            .then(() => {
                window.SystemUI.Notifications.success('Copied to clipboard!');
                return true;
            })
            .catch(() => {
                return fallbackCopyToClipboard(text);
            });
    }
    return fallbackCopyToClipboard(text);
};

function fallbackCopyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        window.SystemUI.Notifications.success('Copied to clipboard!');
        return true;
    } catch (err) {
        window.SystemUI.Notifications.error('Failed to copy to clipboard');
        return false;
    } finally {
        document.body.removeChild(textarea);
    }
}

// Export main app
export default window.SystemUI;
