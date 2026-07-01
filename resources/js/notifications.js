/**
 * NOTIFICATIONS.JS - Notification System
 * Toast notifications with multiple types, positions, and custom options
 */

// ========================================
// NOTIFICATION CONFIGURATION
// ========================================
const NOTIFICATION_CONFIG = {
    container: 'notification-container',
    position: 'top-right',
    duration: 5000,
    maxNotifications: 5,
    animationDuration: 300,
    icons: {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    },
    colors: {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    },
    titles: {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Information'
    }
};

// ========================================
// NOTIFICATION CLASS
// ========================================
class NotificationManager {
    constructor(config = {}) {
        this.config = { ...NOTIFICATION_CONFIG, ...config };
        this.container = null;
        this.queue = [];
        this.isProcessing = false;
        this.init();
    }

    init() {
        this.container = this.createContainer();
        this.injectStyles();
    }

    // ========================================
    // CONTAINER MANAGEMENT
    // ========================================

    createContainer() {
        let container = document.getElementById(this.config.container);

        if (!container) {
            container = document.createElement('div');
            container.id = this.config.container;
            container.className = 'notification-container';

            // Position styles
            const positions = this.getPositionStyles(this.config.position);
            Object.assign(container.style, {
                position: 'fixed',
                zIndex: 9999,
                display: 'flex',
                flexDirection: 'column',
                gap: '10px',
                maxWidth: '400px',
                width: '100%',
                pointerEvents: 'none',
                ...positions
            });

            document.body.appendChild(container);
        }

        return container;
    }

    getPositionStyles(position) {
        const positions = {
            'top-right': { top: '20px', right: '20px' },
            'top-left': { top: '20px', left: '20px' },
            'top-center': { top: '20px', left: '50%', transform: 'translateX(-50%)' },
            'bottom-right': { bottom: '20px', right: '20px' },
            'bottom-left': { bottom: '20px', left: '20px' },
            'bottom-center': { bottom: '20px', left: '50%', transform: 'translateX(-50%)' }
        };
        return positions[position] || positions['top-right'];
    }

    // ========================================
    // STYLES
    // ========================================

    injectStyles() {
        if (document.getElementById('notification-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                pointer-events: auto;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                padding: 1rem 1.25rem;
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                animation: notificationSlideIn 0.3s ease forwards;
                border-left: 4px solid transparent;
                min-width: 300px;
                max-width: 100%;
                position: relative;
                transition: all 0.3s ease;
            }

            .notification:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            }

            .notification .icon {
                font-size: 1.25rem;
                flex-shrink: 0;
                margin-top: 0.1rem;
            }

            .notification .content {
                flex: 1;
                min-width: 0;
            }

            .notification .title {
                font-weight: 600;
                margin-bottom: 0.15rem;
                font-size: 0.95rem;
            }

            .notification .message {
                font-size: 0.9rem;
                color: #6b7280;
                word-wrap: break-word;
                line-height: 1.4;
            }

            .notification .close-btn {
                background: none;
                border: none;
                font-size: 1rem;
                color: #adb5bd;
                cursor: pointer;
                padding: 0.25rem;
                flex-shrink: 0;
                transition: color 0.2s ease;
                margin-top: -0.2rem;
            }

            .notification .close-btn:hover {
                color: #6b7280;
            }

            .notification .progress-bar {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: currentColor;
                border-radius: 0 0 0 3px;
                animation: notificationProgress linear forwards;
                opacity: 0.3;
            }

            .notification.success .progress-bar { color: #28a745; }
            .notification.error .progress-bar { color: #dc3545; }
            .notification.warning .progress-bar { color: #ffc107; }
            .notification.info .progress-bar { color: #17a2b8; }

            @keyframes notificationSlideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes notificationSlideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                    max-height: 200px;
                    margin-bottom: 10px;
                    padding: 1rem 1.25rem;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                    max-height: 0;
                    margin-bottom: 0;
                    padding: 0 1.25rem;
                }
            }

            @keyframes notificationProgress {
                from { width: 100%; }
                to { width: 0%; }
            }

            .notification-exiting {
                animation: notificationSlideOut 0.3s ease forwards !important;
            }

            @media (max-width: 576px) {
                .notification {
                    min-width: unset;
                    width: 100%;
                    border-radius: 0;
                    padding: 0.75rem 1rem;
                }
                .notification-container {
                    max-width: 100%;
                    left: 0 !important;
                    right: 0 !important;
                    top: 0 !important;
                    bottom: auto !important;
                    transform: none !important;
                    padding: 0 10px;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    // ========================================
    // CREATE NOTIFICATION
    // ========================================

    show(message, type = 'info', options = {}) {
        const config = {
            type: type,
            title: options.title || this.config.titles[type] || type,
            duration: options.duration || this.config.duration,
            icon: options.icon || this.config.icons[type],
            color: options.color || this.config.colors[type],
            position: options.position || this.config.position,
            onClose: options.onClose || null,
            onClick: options.onClick || null,
            showProgress: options.showProgress !== false
        };

        // Create notification element
        const notification = this.createNotificationElement(message, config);

        // Add to queue
        this.queue.push({ notification, config });

        // Process queue
        this.processQueue();

        return notification;
    }

    createNotificationElement(message, config) {
        const el = document.createElement('div');
        el.className = `notification ${config.type}`;
        el.style.borderLeftColor = config.color;
        el.style.maxWidth = '400px';
        el.style.width = '100%';

        el.innerHTML = `
            <i class="${config.icon} icon" style="color: ${config.color}"></i>
            <div class="content">
                <div class="title">${config.title}</div>
                <div class="message">${message}</div>
            </div>
            <button type="button" class="close-btn" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            ${config.showProgress ? `<div class="progress-bar"></div>` : ''}
        `;

        // Close button
        const closeBtn = el.querySelector('.close-btn');
        closeBtn.addEventListener('click', () => {
            this.dismiss(el);
        });

        // Click handler
        if (config.onClick) {
            el.style.cursor = 'pointer';
            el.addEventListener('click', (e) => {
                if (!e.target.closest('.close-btn')) {
                    config.onClick(el);
                }
            });
        }

        // Auto-dismiss
        if (config.duration > 0) {
            setTimeout(() => {
                if (el.parentNode) {
                    this.dismiss(el);
                }
            }, config.duration);
        }

        return el;
    }

    // ========================================
    // QUEUE MANAGEMENT
    // ========================================

    processQueue() {
        if (this.isProcessing || this.queue.length === 0) return;

        // Check max notifications
        const currentCount = this.container.querySelectorAll('.notification:not(.notification-exiting)').length;
        if (currentCount >= this.config.maxNotifications) {
            // Remove oldest notification
            const oldest = this.container.querySelector('.notification:not(.notification-exiting)');
            if (oldest) {
                this.dismiss(oldest);
            }
            setTimeout(() => this.processQueue(), 300);
            return;
        }

        this.isProcessing = true;
        const item = this.queue.shift();

        // Update container position if changed
        const position = item.config.position || this.config.position;
        const positions = this.getPositionStyles(position);
        Object.assign(this.container.style, positions);

        // Add notification to container
        this.container.appendChild(item.notification);

        // Trigger animation
        requestAnimationFrame(() => {
            item.notification.style.animation = 'notificationSlideIn 0.3s ease forwards';
        });

        this.isProcessing = false;

        // Process next after animation
        setTimeout(() => {
            this.processQueue();
        }, this.config.animationDuration + 100);
    }

    // ========================================
    // DISMISS NOTIFICATION
    // ========================================

    dismiss(notification) {
        if (notification.classList.contains('notification-exiting')) return;

        notification.classList.add('notification-exiting');

        // Get config for onClose callback
        const config = this.queue.find(item => item.notification === notification)?.config;
        if (config?.onClose) {
            config.onClose(notification);
        }

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
                this.processQueue();
            }
        }, this.config.animationDuration);
    }

    // ========================================
    // BULK OPERATIONS
    // ========================================

    dismissAll() {
        const notifications = this.container.querySelectorAll('.notification:not(.notification-exiting)');
        notifications.forEach(notification => {
            this.dismiss(notification);
        });
        this.queue = [];
    }

    // ========================================
    // NOTIFICATION TYPES
    // ========================================

    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }

    // ========================================
    // CUSTOM NOTIFICATION
    // ========================================

    custom(message, options = {}) {
        const type = options.type || 'info';
        return this.show(message, type, options);
    }

    // ========================================
    // PROMISE-BASED NOTIFICATIONS
    // ========================================

    async loading(message, promise, options = {}) {
        const loadingId = this.info(message, {
            ...options,
            duration: 0, // Don't auto-dismiss
            title: options.title || 'Loading...',
            icon: 'fas fa-spinner fa-spin'
        });

        try {
            const result = await promise;
            this.dismiss(loadingId);
            this.success(options.successMessage || 'Completed successfully!', options);
            return result;
        } catch (error) {
            this.dismiss(loadingId);
            this.error(error.message || 'An error occurred', options);
            throw error;
        }
    }
}

let managerInstance = null;

export const Notifications = {
    success(message, options = {}) {
        managerInstance?.success(message, options);
    },
    error(message, options = {}) {
        managerInstance?.error(message, options);
    },
    warning(message, options = {}) {
        managerInstance?.warning(message, options);
    },
    info(message, options = {}) {
        managerInstance?.info(message, options);
    },
    dismissAll() {
        managerInstance?.dismissAll();
    }
};

// ========================================
// INITIALIZE NOTIFICATION MANAGER
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const notifications = new NotificationManager();
    managerInstance = notifications;
    window.notifications = notifications;
    window.SystemUI = window.SystemUI || {};
    window.SystemUI.Notifications = notifications;
});

// ========================================
// EXPORT NOTIFICATION MANAGER
// ========================================
export default NotificationManager;
