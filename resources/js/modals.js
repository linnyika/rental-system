/**
 * MODALS.JS - Modal Management
 * Handles all modal interactions, dynamic content, and form handling
 */

import { Notifications } from './notifications.js';
import api from './api.js';

// ========================================
// MODAL CONFIGURATION
// ========================================
const MODAL_CONFIG = {
    backdrop: 'static',
    keyboard: false,
    focus: true,
    animation: true
};

// ========================================
// MODAL CLASS
// ========================================
class ModalManager {
    constructor(config = {}) {
        this.config = { ...MODAL_CONFIG, ...config };
        this.modals = new Map();
        this.currentModal = null;
        this.init();
    }

    init() {
        // Initialize all modal triggers
        this.initTriggers();

        // Initialize dynamic modals
        this.initDynamicModals();

        // Initialize modal forms
        this.initModalForms();

        // Add global modal event listeners
        this.initGlobalEvents();
    }

    // ========================================
    // MODAL TRIGGERS
    // ========================================

    initTriggers() {
        document.querySelectorAll('[data-modal]').forEach((trigger) => {
            const modalId = trigger.getAttribute('data-modal');
            const options = {
                backdrop: trigger.getAttribute('data-backdrop') || this.config.backdrop,
                keyboard: trigger.getAttribute('data-keyboard') !== 'false',
                focus: trigger.getAttribute('data-focus') !== 'false'
            };

            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open(modalId, options);
            });
        });
    }

    // ========================================
    // DYNAMIC MODALS
    // ========================================

    initDynamicModals() {
        document.querySelectorAll('[data-dynamic-modal]').forEach((trigger) => {
            trigger.addEventListener('click', async (e) => {
                e.preventDefault();
                const url = trigger.getAttribute('data-dynamic-modal');
                const modalId = trigger.getAttribute('data-modal-target') || 'dynamicModal';
                const method = trigger.getAttribute('data-method') || 'GET';
                const data = trigger.dataset.data ? JSON.parse(trigger.dataset.data) : {};

                await this.loadDynamicModal(modalId, url, method, data);
            });
        });
    }

    async loadDynamicModal(modalId, url, method = 'GET', data = {}) {
        try {
            // Show loading state
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const body = modalElement.querySelector('.modal-body');
                if (body) {
                    body.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading content...</p>
                        </div>
                    `;
                }
            }

            // Fetch content
            const response = await api.request(url, { method, body: data });
            const content = typeof response === 'string' ? response : response.html || response.data || response;

            // Update modal content
            this.setContent(modalId, content);

            // Open modal
            this.open(modalId);

            // Re-initialize forms in dynamic content
            this.initModalForms(modalId);

        } catch (error) {
            Notifications.error('Failed to load modal content: ' + error.message);
        }
    }

    // ========================================
    // MODAL FORMS
    // ========================================

    initModalForms(containerId = null) {
        const container = containerId
            ? document.getElementById(containerId)
            : document;

        container.querySelectorAll('.modal .modal-form').forEach((form) => {
            // Remove existing listeners to prevent duplicates
            form.removeEventListener('submit', this.handleFormSubmit);
            form.addEventListener('submit', this.handleFormSubmit);
        });
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const modal = form.closest('.modal');
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn?.innerHTML || 'Submit';

        try {
            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            }

            const formData = new FormData(form);
            const method = form.getAttribute('data-method') || form.method || 'POST';
            const action = form.getAttribute('data-action') || form.action || window.location.href;
            const isMultipart = form.enctype === 'multipart/form-data';

            let data;
            let headers = {};

            if (isMultipart) {
                data = formData;
                headers['Content-Type'] = 'multipart/form-data';
            } else {
                // Convert FormData to object
                data = {};
                formData.forEach((value, key) => {
                    if (data[key]) {
                        if (!Array.isArray(data[key])) {
                            data[key] = [data[key]];
                        }
                        data[key].push(value);
                    } else {
                        data[key] = value;
                    }
                });
                headers['Content-Type'] = 'application/json';
            }

            // Handle file uploads with progress
            if (isMultipart && form.querySelector('input[type="file"]')) {
                const fileInputs = form.querySelectorAll('input[type="file"]');
                let hasFiles = false;
                fileInputs.forEach(input => {
                    if (input.files && input.files.length > 0) {
                        hasFiles = true;
                    }
                });
                if (hasFiles) {
                    // Show upload progress
                    this.showUploadProgress(modal, 'Uploading files...');
                }
            }

            // Submit via API
            const response = await api.request(action, {
                method: method,
                body: data,
                headers: headers
            });

            // Handle success
            if (response.success !== false) {
                Notifications.success(response.message || 'Form submitted successfully!');

                // Close modal
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }

                // Reset form
                if (form.dataset.resetOnSuccess !== 'false') {
                    form.reset();
                }

                // Dispatch custom event
                document.dispatchEvent(new CustomEvent('formSuccess', {
                    detail: { form, response }
                }));

                // Handle redirect
                if (response.redirect) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                }

                // Refresh dashboard if specified
                if (response.refresh && window.SystemUI.dashboard) {
                    window.SystemUI.dashboard.refresh();
                }
            } else {
                throw new Error(response.message || 'Form submission failed');
            }

        } catch (error) {
            // Handle validation errors
            if (error.data && error.data.errors) {
                this.displayValidationErrors(form, error.data.errors);
            }

            Notifications.error(error.message || 'An error occurred');

            // Dispatch error event
            document.dispatchEvent(new CustomEvent('formError', {
                detail: { form, error }
            }));

        } finally {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    }

    displayValidationErrors(form, errors) {
        // Clear existing errors
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Display new errors
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = Array.isArray(messages) ? messages.join(', ') : messages;
                input.parentNode.appendChild(feedback);
            }
        }
    }

    showUploadProgress(modal, message) {
        const body = modal?.querySelector('.modal-body');
        if (body) {
            const progress = document.createElement('div');
            progress.className = 'upload-progress mt-3';
            progress.innerHTML = `
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">${message}</span>
                    <span class="text-muted small">0%</span>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         style="width: 0%"
                         role="progressbar">
                    </div>
                </div>
            `;
            body.appendChild(progress);
        }
    }

    // ========================================
    // MODAL OPERATIONS
    // ========================================

    open(modalId, options = {}) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            return null;
        }

        // Get or create modal instance
        let modal = this.modals.get(modalId);
        if (!modal) {
            const config = {
                backdrop: options.backdrop || this.config.backdrop,
                keyboard: options.keyboard !== undefined ? options.keyboard : this.config.keyboard,
                focus: options.focus !== undefined ? options.focus : this.config.focus
            };
            modal = new bootstrap.Modal(modalElement, config);
            this.modals.set(modalId, modal);

            // Clean up on hide
            modalElement.addEventListener('hidden.bs.modal', () => {
                this.currentModal = null;
                // Restore focus
                if (this.lastFocusedElement) {
                    this.lastFocusedElement.focus();
                }
            });
        }

        // Save last focused element
        this.lastFocusedElement = document.activeElement;
        this.currentModal = modalId;

        modal.show();
        return modal;
    }

    close(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
                this.modals.delete(modalId);
            }
        }
    }

    closeAll() {
        this.modals.forEach((modal, id) => {
            modal.hide();
            this.modals.delete(id);
        });
        this.currentModal = null;
    }

    getInstance(modalId) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            return bootstrap.Modal.getInstance(modalElement);
        }
        return null;
    }

    // ========================================
    // MODAL CONTENT MANAGEMENT
    // ========================================

    setContent(modalId, content) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const body = modalElement.querySelector('.modal-body');
            if (body) {
                body.innerHTML = content;
            }
        }
    }

    setTitle(modalId, title) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const titleEl = modalElement.querySelector('.modal-title');
            if (titleEl) {
                titleEl.textContent = title;
            }
        }
    }

    setFooter(modalId, footerHtml) {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            const footer = modalElement.querySelector('.modal-footer');
            if (footer) {
                footer.innerHTML = footerHtml;
            }
        }
    }

    // ========================================
    // CONFIRM DIALOG
    // ========================================

    confirm(message, options = {}) {
        return new Promise((resolve) => {
            const modalId = 'confirmModal';
            let modalElement = document.getElementById(modalId);

            if (!modalElement) {
                modalElement = this.createConfirmModal(modalId);
                document.body.appendChild(modalElement);
            }

            // Set message
            const body = modalElement.querySelector('.modal-body');
            if (body) {
                const icon = options.icon || 'fa-exclamation-triangle';
                const iconColor = options.iconColor || 'text-warning';
                body.innerHTML = `
                    <div class="text-center mb-3">
                        <i class="fas ${icon} ${iconColor} fs-1"></i>
                    </div>
                    <p class="text-center mb-0">${message}</p>
                    ${options.detail ? `<p class="text-center text-muted small mt-2">${options.detail}</p>` : ''}
                `;
            }

            // Set title
            const titleEl = modalElement.querySelector('.modal-title');
            if (titleEl) {
                titleEl.textContent = options.title || 'Confirm Action';
            }

            // Set confirm button
            const confirmBtn = modalElement.querySelector('.btn-confirm');
            if (confirmBtn) {
                confirmBtn.textContent = options.confirmText || 'Confirm';
                confirmBtn.className = `btn btn-${options.confirmClass || 'danger'} btn-confirm`;
                confirmBtn.style.display = options.hideConfirm ? 'none' : '';
            }

            // Set cancel button
            const cancelBtn = modalElement.querySelector('.btn-cancel');
            if (cancelBtn) {
                cancelBtn.textContent = options.cancelText || 'Cancel';
                cancelBtn.style.display = options.hideCancel ? 'none' : '';
            }

            // Show modal
            const modal = this.open(modalId);
            let resolved = false;

            // Handle confirm
            confirmBtn.onclick = function() {
                if (resolved) return;
                resolved = true;
                modal.hide();
                resolve(true);
            };

            // Handle cancel
            cancelBtn.onclick = function() {
                if (resolved) return;
                resolved = true;
                modal.hide();
                resolve(false);
            };

            // Handle close button
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (!resolved) {
                    resolved = true;
                    resolve(false);
                }
            }, { once: true });
        });
    }

    createConfirmModal(id) {
        const html = `
            <div class="modal fade" id="${id}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title">Confirm Action</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-2"></div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger btn-confirm">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const div = document.createElement('div');
        div.innerHTML = html;
        return div.firstElementChild;
    }

    // ========================================
    // GLOBAL EVENTS
    // ========================================

    initGlobalEvents() {
        // Handle modal backdrop clicks
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                // Handle click outside modal
                if (this.currentModal) {
                    const modal = this.getInstance(this.currentModal);
                    if (modal && modal._config.backdrop !== 'static') {
                        this.close(this.currentModal);
                    }
                }
            }
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.currentModal) {
                const modal = this.getInstance(this.currentModal);
                if (modal && modal._config.keyboard) {
                    this.close(this.currentModal);
                }
            }
        });

        // Handle modal show events
        document.addEventListener('shown.bs.modal', (e) => {
            // Focus first input
            const firstInput = e.target.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }

            // Lock body scroll
            document.body.style.overflow = 'hidden';
        });

        document.addEventListener('hidden.bs.modal', () => {
            // Restore body scroll
            document.body.style.overflow = '';
        });
    }
}

// ========================================
// INITIALIZE MODAL MANAGER
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const modalManager = new ModalManager();
    window.modalManager = modalManager;
    window.SystemUI = window.SystemUI || {};
    window.SystemUI.modal = modalManager;
});

// ========================================
// EXPORT MODAL MANAGER
// ========================================
export default ModalManager;
