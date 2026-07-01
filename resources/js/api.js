/**
 * API.JS - API Service Layer
 * Handles all API communications with the server
 */

import { Notifications } from './notifications.js';

// ========================================
// API CONFIGURATION
// ========================================
const API_CONFIG = {
    baseUrl: window.SystemUI?.api?.baseUrl || '/api',
    timeout: window.SystemUI?.api?.timeout || 30000,
    retryAttempts: window.SystemUI?.api?.retryAttempts || 3,
    retryDelay: 1000
};

// ========================================
// API SERVICE CLASS
// ========================================
class ApiService {
    constructor(config = {}) {
        this.config = { ...API_CONFIG, ...config };
        this.interceptors = {
            request: [],
            response: [],
            error: []
        };
    }

    // Add request interceptor
    addRequestInterceptor(fn) {
        this.interceptors.request.push(fn);
    }

    // Add response interceptor
    addResponseInterceptor(fn) {
        this.interceptors.response.push(fn);
    }

    // Add error interceptor
    addErrorInterceptor(fn) {
        this.interceptors.error.push(fn);
    }

    // ========================================
    // CORE HTTP METHODS
    // ========================================

    async request(endpoint, options = {}) {
        const url = this.buildUrl(endpoint, options.params || null);
        const config = this.buildConfig(options);
        let attempt = 0;

        // Run request interceptors
        this.interceptors.request.forEach(fn => fn(config));

        while (attempt < this.config.retryAttempts) {
            try {
                const response = await this.fetchWithTimeout(url, config);
                const data = await this.handleResponse(response);

                // Run response interceptors
                this.interceptors.response.forEach(fn => fn(data));

                return data;
            } catch (error) {
                attempt++;

                // Run error interceptors
                this.interceptors.error.forEach(fn => fn(error));

                if (attempt >= this.config.retryAttempts || error.status >= 400 && error.status < 500) {
                    throw error;
                }

                // Wait before retrying
                await this.delay(this.config.retryDelay * attempt);
            }
        }
    }

    buildUrl(endpoint, params = null) {
        const baseUrl = this.config.baseUrl.replace(/\/+$/, '');
        const cleanEndpoint = endpoint.replace(/^\/+/, '');
        const url = new URL(`${baseUrl}/${cleanEndpoint}`, window.location.origin);

        if (params && typeof params === 'object') {
            Object.entries(params).forEach(([key, value]) => {
                if (value === null || value === undefined || value === '') {
                    return;
                }
                url.searchParams.append(key, String(value));
            });
        }

        return url.toString();
    }

    buildConfig(options) {
        const isFormData = typeof FormData !== 'undefined' && options.body instanceof FormData;
        const headers = {
            'Accept': 'application/json',
            ...options.headers
        };

        if (!isFormData && !headers['Content-Type']) {
            headers['Content-Type'] = 'application/json';
        }

        // Add CSRF token if available
        const csrf = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrf) {
            headers['X-CSRF-TOKEN'] = headers['X-CSRF-TOKEN'] || csrf;
        }

        return {
            method: options.method || 'GET',
            headers: headers,
            body: options.body
                ? (isFormData ? options.body : JSON.stringify(options.body))
                : undefined,
            credentials: 'include',
            ...options
        };
    }

    async fetchWithTimeout(url, config) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.config.timeout);

        try {
            const response = await fetch(url, {
                ...config,
                signal: controller.signal
            });
            clearTimeout(timeoutId);
            return response;
        } catch (error) {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            throw error;
        }
    }

    async handleResponse(response) {
        const contentType = response.headers.get('content-type');

        if (response.status === 204) {
            return null;
        }

        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();

            if (!response.ok) {
                const error = new Error(data.message || 'An error occurred');
                error.status = response.status;
                error.data = data;
                throw error;
            }

            return data;
        }

        if (!response.ok) {
            const text = await response.text();
            const error = new Error(text || 'An error occurred');
            error.status = response.status;
            throw error;
        }

        return response;
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // ========================================
    // HTTP VERBS
    // ========================================

    get(endpoint, options = {}) {
        return this.request(endpoint, { ...options, method: 'GET' });
    }

    post(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'POST',
            body: data
        });
    }

    put(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'PUT',
            body: data
        });
    }

    patch(endpoint, data = {}, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'PATCH',
            body: data
        });
    }

    delete(endpoint, options = {}) {
        return this.request(endpoint, { ...options, method: 'DELETE' });
    }

    // ========================================
    // FILE UPLOAD
    // ========================================

    upload(endpoint, formData, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'POST',
            headers: {
                ...(options.headers || {})
            },
            body: formData
        });
    }

    // ========================================
    // STREAMING / DOWNLOAD
    // ========================================

    download(endpoint, options = {}) {
        return this.request(endpoint, {
            ...options,
            method: 'GET',
            responseType: 'blob'
        });
    }

    // ========================================
    // API RESOURCE METHODS
    // ========================================

    // Generic CRUD operations
    resources(resource) {
        return {
            getAll: (params = {}) => this.get(`/${resource}`, { params }),
            get: (id) => this.get(`/${resource}/${id}`),
            create: (data) => this.post(`/${resource}`, data),
            update: (id, data) => this.put(`/${resource}/${id}`, data),
            patch: (id, data) => this.patch(`/${resource}/${id}`, data),
            delete: (id) => this.delete(`/${resource}/${id}`),
            upload: (data) => this.upload(`/${resource}/upload`, data)
        };
    }

    // ========================================
    // CUSTOM AUTH METHODS
    // ========================================

    auth = {
        login: (credentials) => {
            return this.post('/login', credentials);
        },
        logout: () => {
            return this.post('/logout');
        },
        register: (data) => {
            return this.post('/register', data);
        },
        user: () => {
            return this.get('/user');
        }
    };

    // ========================================
    // DASHBOARD METHODS
    // ========================================

    dashboard = {
        getStats: (params = {}) => {
            return this.get('/dashboard/stats', { params });
        },
        getRevenue: (params = {}) => {
            return this.get('/dashboard/revenue', { params });
        },
        getOccupancy: (params = {}) => {
            return this.get('/dashboard/occupancy', { params });
        },
        getActivity: (params = {}) => {
            return this.get('/dashboard/activity', { params });
        },
        getNotifications: (params = {}) => {
            return this.get('/notifications', { params });
        },
        markNotificationRead: (id) => {
            return this.put(`/notifications/${id}/read`);
        },
        markAllNotificationsRead: () => {
            return this.put('/notifications/read-all');
        }
    };

    // ========================================
    // REPORTING METHODS
    // ========================================

    reports = {
        generate: (type, params = {}) => {
            return this.post(`/reports/${type}`, params);
        },
        download: (id) => {
            return this.download(`/reports/${id}/download`);
        },
        list: (params = {}) => {
            return this.get('/reports', { params });
        },
        delete: (id) => {
            return this.delete(`/reports/${id}`);
        }
    };

    // ========================================
    // PROPERTY METHODS
    // ========================================

    properties = {
        ...this.resources('properties'),
        search: (query) => {
            return this.get('/properties/search', { params: { q: query } });
        },
        filter: (filters) => {
            return this.post('/properties/filter', filters);
        },
        getTypes: () => {
            return this.get('/properties/types');
        },
        getStatuses: () => {
            return this.get('/properties/statuses');
        },
        assign: (id, data) => {
            return this.post(`/properties/${id}/assign`, data);
        },
        maintenance: (id, data) => {
            return this.post(`/properties/${id}/maintenance`, data);
        }
    };
}

// ========================================
// CREATE AND EXPORT API INSTANCE
// ========================================
const api = new ApiService();

// Add global error interceptor
api.addErrorInterceptor((error) => {
    if (error.status === 401) {
        // Handle unauthorized
        if (!window.location.pathname.includes('/login')) {
            Notifications.error('Your session has expired. Please login again.');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        }
    }

    if (error.status === 403) {
        Notifications.error('You do not have permission to perform this action.');
    }

    if (error.status === 404) {
        Notifications.error('Resource not found.');
    }

    if (error.status >= 500) {
        Notifications.error('Server error. Please try again later.');
    }
});

// Make API globally available
window.api = api;

export default api;
