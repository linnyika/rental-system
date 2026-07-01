/**
 * SIDEBAR.JS - Sidebar Management
 * Handles toggle, active states, submenus, and responsive behavior
 */

import { Notifications } from './notifications.js';

// ========================================
// SIDEBAR CONFIGURATION
// ========================================
const SIDEBAR_CONFIG = {
    storageKey: 'sidebarState',
    breakpoint: 992,
    animationSpeed: 300,
    defaultCollapsed: false
};

// ========================================
// SIDEBAR CLASS
// ========================================
class SidebarManager {
    constructor(config = {}) {
        this.config = { ...SIDEBAR_CONFIG, ...config };
        this.sidebar = document.querySelector('.sidebar');
        this.toggleBtn = document.querySelector('[data-toggle="sidebar"]');
        this.offcanvasElement = document.getElementById('sidebarOffcanvas');
        this.isMobile = window.innerWidth < this.config.breakpoint;
        this.isCollapsed = this.loadState();
        this.init();
    }

    init() {
        if (!this.sidebar) return;

        // Set initial state
        this.applyState();

        // Initialize event listeners
        this.initEventListeners();

        // Initialize submenus
        this.initSubmenus();

        // Initialize active links
        this.highlightActiveLink();

        // Handle offcanvas for mobile
        this.initOffcanvas();

        // Handle resize
        this.initResizeHandler();
    }

    // ========================================
    // STATE MANAGEMENT
    // ========================================

    loadState() {
        if (this.isMobile) return false;

        try {
            const saved = localStorage.getItem(this.config.storageKey);
            return saved !== null ? JSON.parse(saved) : this.config.defaultCollapsed;
        } catch {
            return this.config.defaultCollapsed;
        }
    }

    saveState() {
        try {
            localStorage.setItem(this.config.storageKey, JSON.stringify(this.isCollapsed));
        } catch {
        }
    }

    applyState() {
        if (this.isMobile) {
            this.sidebar.classList.remove('collapsed');
            return;
        }

        if (this.isCollapsed) {
            this.sidebar.classList.add('collapsed');
        } else {
            this.sidebar.classList.remove('collapsed');
        }
    }

    // ========================================
    // TOGGLE METHODS
    // ========================================

    toggle() {
        if (this.isMobile) return;

        this.isCollapsed = !this.isCollapsed;
        this.applyState();
        this.saveState();

        // Trigger resize event for charts
        window.dispatchEvent(new Event('resize'));

        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('sidebarToggle', {
            detail: { collapsed: this.isCollapsed }
        }));
    }

    expand() {
        if (this.isMobile) return;
        this.isCollapsed = false;
        this.applyState();
        this.saveState();
        window.dispatchEvent(new Event('resize'));
    }

    collapse() {
        if (this.isMobile) return;
        this.isCollapsed = true;
        this.applyState();
        this.saveState();
        window.dispatchEvent(new Event('resize'));
    }

    getState() {
        return this.isCollapsed;
    }

    // ========================================
    // EVENT LISTENERS
    // ========================================

    initEventListeners() {
        // Toggle button click
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl + B or Alt + S to toggle sidebar
            if ((e.ctrlKey && e.key === 'b') || (e.altKey && e.key === 's')) {
                e.preventDefault();
                this.toggle();
            }
        });

        // Click outside to close on mobile
        document.addEventListener('click', (e) => {
            if (this.isMobile && this.sidebar && !this.sidebar.contains(e.target)) {
                const isToggle = this.toggleBtn && this.toggleBtn.contains(e.target);
                if (!isToggle) {
                    this.closeMobile();
                }
            }
        });
    }

    // ========================================
    // SUBMENUS
    // ========================================

    initSubmenus() {
        document.querySelectorAll('.sidebar .has-submenu > .nav-link').forEach((toggle) => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = toggle.closest('.has-submenu');
                const submenu = parent.querySelector('.submenu, .collapse');

                if (submenu) {
                    // Close other open submenus
                    if (!this.isMobile) {
                        document.querySelectorAll('.sidebar .has-submenu .submenu.show, .sidebar .has-submenu .collapse.show')
                            .forEach((openSubmenu) => {
                                if (openSubmenu !== submenu) {
                                    openSubmenu.classList.remove('show');
                                    const openToggle = openSubmenu.closest('.has-submenu')?.querySelector('.nav-link');
                                    if (openToggle) {
                                        openToggle.setAttribute('aria-expanded', 'false');
                                    }
                                }
                            });
                    }

                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', !isExpanded);

                    // Use Bootstrap collapse if available
                    if (submenu.classList.contains('collapse')) {
                        const bsCollapse = bootstrap.Collapse.getInstance(submenu) || new bootstrap.Collapse(submenu, {
                            toggle: !isExpanded
                        });
                        bsCollapse.toggle();
                    } else {
                        submenu.classList.toggle('show');
                    }

                    // Save expanded state
                    this.saveSubmenuState(parent, !isExpanded);
                }
            });
        });

        // Restore submenu states
        this.restoreSubmenuStates();
    }

    saveSubmenuState(parent, expanded) {
        const id = parent.id || 'submenu-' + Math.random().toString(36).substr(2, 9);
        if (!parent.id) parent.id = id;

        try {
            const states = JSON.parse(localStorage.getItem('sidebarSubmenus') || '{}');
            states[id] = expanded;
            localStorage.setItem('sidebarSubmenus', JSON.stringify(states));
        } catch {
        }
    }

    restoreSubmenuStates() {
        try {
            const states = JSON.parse(localStorage.getItem('sidebarSubmenus') || '{}');

            document.querySelectorAll('.sidebar .has-submenu').forEach((parent) => {
                const id = parent.id;
                if (id && states[id]) {
                    const submenu = parent.querySelector('.submenu, .collapse');
                    const toggle = parent.querySelector('.nav-link');
                    if (submenu && toggle) {
                        submenu.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                }
            });
        } catch {
        }
    }

    // ========================================
    // ACTIVE LINK HIGHLIGHTING
    // ========================================

    highlightActiveLink() {
        const currentPath = window.location.pathname;
        const currentHash = window.location.hash;

        document.querySelectorAll('.sidebar .nav-link').forEach((link) => {
            const href = link.getAttribute('href');
            const isActive = this.isLinkActive(href, currentPath, currentHash);

            if (isActive) {
                link.classList.add('active');

                // Expand parent submenu
                const parent = link.closest('.has-submenu');
                if (parent) {
                    const submenu = parent.querySelector('.submenu, .collapse');
                    if (submenu) {
                        submenu.classList.add('show');
                        const toggle = parent.querySelector('.nav-link');
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            } else {
                link.classList.remove('active');
            }
        });
    }

    isLinkActive(href, currentPath, currentHash) {
        if (!href) return false;

        // Exact match
        if (href === currentPath) return true;
        if (href === currentPath + currentHash) return true;

        // Home page
        if (href === '/' && currentPath === '/') return true;

        // Partial match for nested routes
        if (href !== '/' && currentPath.startsWith(href)) return true;

        return false;
    }

    // ========================================
    // OFFCANVAS (Mobile)
    // ========================================

    initOffcanvas() {
        if (!this.offcanvasElement) return;

        const offcanvas = new bootstrap.Offcanvas(this.offcanvasElement, {
            backdrop: true,
            keyboard: true,
            scroll: false
        });

        // Close offcanvas when clicking a link
        this.offcanvasElement.querySelectorAll('.nav-link').forEach((link) => {
            link.addEventListener('click', () => {
                offcanvas.hide();
            });
        });

        // Handle show/hide events
        this.offcanvasElement.addEventListener('show.bs.offcanvas', () => {
            document.body.classList.add('sidebar-open');
        });

        this.offcanvasElement.addEventListener('hidden.bs.offcanvas', () => {
            document.body.classList.remove('sidebar-open');
        });
    }

    closeMobile() {
        if (this.offcanvasElement) {
            const offcanvas = bootstrap.Offcanvas.getInstance(this.offcanvasElement);
            if (offcanvas) {
                offcanvas.hide();
            }
        }
    }

    // ========================================
    // RESIZE HANDLER
    // ========================================

    initResizeHandler() {
        let resizeTimeout;

        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                const wasMobile = this.isMobile;
                this.isMobile = window.innerWidth < this.config.breakpoint;

                if (wasMobile !== this.isMobile) {
                    // Breakpoint changed
                    if (this.isMobile) {
                        this.sidebar.classList.remove('collapsed');
                    } else {
                        // Restore desktop state
                        this.isCollapsed = this.loadState();
                        this.applyState();
                    }
                }
            }, 250);
        });

        // Also handle resize end event from app.js
        window.addEventListener('resizeEnd', () => {
            // Update chart containers if needed
            if (window.SystemUI && window.SystemUI.charts) {
                window.SystemUI.charts.resize();
            }
        });
    }
}

// ========================================
// INITIALIZE SIDEBAR
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = new SidebarManager();
    window.sidebar = sidebar;
    window.SystemUI = window.SystemUI || {};
    window.SystemUI.sidebar = sidebar;
});

// ========================================
// EXPORT SIDEBAR
// ========================================
export default SidebarManager;
