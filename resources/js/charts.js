/**
 * CHART.JS - Chart Management
 * Line and Bar Charts Only
 * Yeti Bootstrap Theme with Custom Colors
 * Colors: #055236, #7FA48E, #2D4C39, #80B9B1, #6C27DA
 */

import { Chart, registerables } from 'chart.js';
import annotationPlugin from 'chartjs-plugin-annotation';

// Register all Chart.js components
Chart.register(...registerables);
Chart.register(annotationPlugin);

// ========================================
// DEFAULT CHART CONFIGURATION
// ========================================
Chart.defaults.font.family = "'Segoe UI', system-ui, -apple-system, sans-serif";
Chart.defaults.color = '#6b7280';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.pointStyle = 'circle';

// ========================================
// COLOR PALETTE (Theme Colors)
// ========================================
export const ChartColors = {
    primary: '#055236',
    primaryLight: '#7FA48E',
    secondary: '#2D4C39',
    accent: '#80B9B1',
    accentPurple: '#6C27DA',
    success: '#28a745',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8',
    purple: '#8e44ad',
    orange: '#f39c12',
    teal: '#1abc9c',
    pink: '#e83e8c',
    indigo: '#6610f2',
    gray: '#6c757d',
    grayLight: '#e9ecef'
};

// ========================================
// CHART PALETTES
// ========================================
export const ChartPalettes = {
    default: ['#055236', '#7FA48E', '#2D4C39', '#80B9B1', '#6C27DA'],
    pastel: ['#a8d8ea', '#aa96da', '#fcbad3', '#ffffd2', '#d4edda'],
    monochrome: ['#055236', '#1a6b4a', '#2d8c5e', '#40ad72', '#53ce86'],
    vibrant: ['#055236', '#6C27DA', '#dc3545', '#ffc107', '#17a2b8'],
    gradient: ['#055236', '#2D4C39', '#7FA48E', '#80B9B1']
};

// ========================================
// CHART BUILDER CLASS
// ========================================
export class ChartBuilder {
    constructor(ctx, config = {}) {
        this.ctx = ctx;
        this.config = config;
        this.chart = null;
        this.isDark = false;

        // Detect theme
        if (window.themeManager) {
            this.isDark = window.themeManager.isDark();
        }
    }

    // ========================================
    // CREATE LINE CHART
    // ========================================
    createLineChart(data, options = {}) {
        const defaultOptions = this.getDefaultLineOptions();
        const mergedOptions = this.deepMerge(defaultOptions, options);

        // Apply theme colors
        this.applyThemeColors(mergedOptions);

        this.chart = new Chart(this.ctx, {
            type: 'line',
            data: data,
            options: mergedOptions
        });

        return this.chart;
    }

    // ========================================
    // CREATE BAR CHART
    // ========================================
    createBarChart(data, options = {}) {
        const defaultOptions = this.getDefaultBarOptions();
        const mergedOptions = this.deepMerge(defaultOptions, options);

        // Apply theme colors
        this.applyThemeColors(mergedOptions);

        this.chart = new Chart(this.ctx, {
            type: 'bar',
            data: data,
            options: mergedOptions
        });

        return this.chart;
    }

    // ========================================
    // CREATE HORIZONTAL BAR CHART
    // ========================================
    createHorizontalBarChart(data, options = {}) {
        const defaultOptions = this.getDefaultBarOptions();
        defaultOptions.indexAxis = 'y';

        const mergedOptions = this.deepMerge(defaultOptions, options);
        this.applyThemeColors(mergedOptions);

        this.chart = new Chart(this.ctx, {
            type: 'bar',
            data: data,
            options: mergedOptions
        });

        return this.chart;
    }

    // ========================================
    // CREATE STACKED BAR CHART
    // ========================================
    createStackedBarChart(data, options = {}) {
        const defaultOptions = this.getDefaultBarOptions();
        defaultOptions.scales = {
            ...defaultOptions.scales,
            x: {
                ...defaultOptions.scales.x,
                stacked: true
            },
            y: {
                ...defaultOptions.scales.y,
                stacked: true
            }
        };

        const mergedOptions = this.deepMerge(defaultOptions, options);
        this.applyThemeColors(mergedOptions);

        this.chart = new Chart(this.ctx, {
            type: 'bar',
            data: data,
            options: mergedOptions
        });

        return this.chart;
    }

    // ========================================
    // CREATE MULTI-AXIS CHART (Line + Bar)
    // ========================================
    createMixedChart(data, options = {}) {
        const defaultOptions = this.getDefaultLineOptions();
        defaultOptions.plugins = {
            ...defaultOptions.plugins,
            legend: {
                ...defaultOptions.plugins.legend,
                position: 'top'
            }
        };

        const mergedOptions = this.deepMerge(defaultOptions, options);
        this.applyThemeColors(mergedOptions);

        this.chart = new Chart(this.ctx, {
            type: 'bar',
            data: data,
            options: mergedOptions
        });

        return this.chart;
    }

    // ========================================
    // DEFAULT OPTIONS
    // ========================================

    getDefaultLineOptions() {
        const isDark = this.isDark;
        const textColor = isDark ? '#e0e0e0' : '#6b7280';
        const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
        const borderColor = isDark ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)';

        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(26,31,46,0.9)' : 'rgba(255,255,255,0.9)',
                    titleColor: isDark ? '#e0e0e0' : '#17202a',
                    bodyColor: isDark ? '#9ca3af' : '#6b7280',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                // Check if it's currency
                                if (context.dataset.currency) {
                                    label += 'KES ' + context.parsed.y.toLocaleString();
                                } else {
                                    label += context.parsed.y.toLocaleString();
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor,
                        drawBorder: true,
                        borderColor: borderColor
                    },
                    ticks: {
                        color: textColor
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor,
                        drawBorder: true,
                        borderColor: borderColor
                    },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            if (this.chart && this.chart.data && this.chart.data.datasets) {
                                const hasCurrency = this.chart.data.datasets.some(d => d.currency);
                                if (hasCurrency) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                            return value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        };
    }

    getDefaultBarOptions() {
        const isDark = this.isDark;
        const textColor = isDark ? '#e0e0e0' : '#6b7280';
        const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';
        const borderColor = isDark ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.1)';

        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(26,31,46,0.9)' : 'rgba(255,255,255,0.9)',
                    titleColor: isDark ? '#e0e0e0' : '#17202a',
                    bodyColor: isDark ? '#9ca3af' : '#6b7280',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true
                }
            },
            scales: {
                x: {
                    grid: {
                        color: gridColor,
                        drawBorder: true,
                        borderColor: borderColor
                    },
                    ticks: {
                        color: textColor
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor,
                        drawBorder: true,
                        borderColor: borderColor
                    },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            if (this.chart && this.chart.data && this.chart.data.datasets) {
                                const hasCurrency = this.chart.data.datasets.some(d => d.currency);
                                if (hasCurrency) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                            return value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        };
    }

    // ========================================
    // THEME APPLICATION
    // ========================================

    applyThemeColors(options) {
        const isDark = this.isDark;
        if (isDark) {
            // Apply dark theme to options
            if (options.scales) {
                if (options.scales.x) {
                    options.scales.x.grid.color = 'rgba(255,255,255,0.1)';
                    options.scales.x.ticks.color = '#e0e0e0';
                }
                if (options.scales.y) {
                    options.scales.y.grid.color = 'rgba(255,255,255,0.1)';
                    options.scales.y.ticks.color = '#e0e0e0';
                }
            }
            if (options.plugins && options.plugins.legend) {
                options.plugins.legend.labels.color = '#e0e0e0';
            }
        }
        return options;
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    deepMerge(target, source) {
        const result = { ...target };
        for (const key in source) {
            if (source[key] instanceof Object && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        return result;
    }

    updateData(data) {
        if (this.chart) {
            this.chart.data = data;
            this.chart.update();
        }
    }

    updateOptions(options) {
        if (this.chart) {
            this.chart.options = this.deepMerge(this.chart.options, options);
            this.chart.update();
        }
    }

    destroy() {
        if (this.chart) {
            this.chart.destroy();
            this.chart = null;
        }
    }

    resize() {
        if (this.chart) {
            this.chart.resize();
        }
    }

    getInstance() {
        return this.chart;
    }

    // ========================================
    // EXPORT / DOWNLOAD METHODS
    // ========================================

    toBase64() {
        if (this.chart) {
            return this.chart.toBase64Image();
        }
        return null;
    }

    downloadImage(filename = 'chart.png') {
        const link = document.createElement('a');
        link.download = filename;
        link.href = this.toBase64();
        link.click();
    }

    print() {
        if (this.chart) {
            const canvas = this.chart.canvas;
            const win = window.open('', '_blank');
            win.document.write('<img src="' + canvas.toDataURL() + '" onload="window.print(); window.close();" />');
            win.document.close();
        }
    }
}

// ========================================
// GRADIENT HELPERS
// ========================================

export function createGradient(ctx, color1, color2, direction = 'vertical') {
    const chartArea = ctx.chartArea || { top: 0, bottom: 300, left: 0, right: 400 };

    let gradient;
    if (direction === 'vertical') {
        gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    } else {
        gradient = ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0);
    }

    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

export function createAreaGradient(ctx, color, opacity = 0.3) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, color);
    gradient.addColorStop(1, color + '00');
    return gradient;
}

// ========================================
// DATA HELPERS
// ========================================

export function generateMonthlyData(months = 12, baseValue = 1000, variance = 0.3) {
    const labels = [];
    const data = [];

    for (let i = 0; i < months; i++) {
        const date = new Date();
        date.setMonth(date.getMonth() - (months - i - 1));
        labels.push(date.toLocaleString('default', { month: 'short' }));

        const variation = 1 + (Math.random() - 0.5) * variance * 2;
        data.push(Math.round(baseValue * variation));
    }

    return { labels, data };
}

export function generateCategoryData(categories, baseValue = 1000, variance = 0.4) {
    const labels = categories;
    const data = categories.map(() => {
        const variation = 1 + (Math.random() - 0.5) * variance * 2;
        return Math.round(baseValue * variation);
    });

    return { labels, data };
}

export function formatChartLabels(labels, type = 'short') {
    if (type === 'short') {
        return labels.map(label => label.substring(0, 3));
    }
    if (type === 'currency') {
        return labels.map(label => 'KES ' + label.toLocaleString());
    }
    if (type === 'percentage') {
        return labels.map(label => label + '%');
    }
    return labels;
}

// ========================================
// CHART DATA BUILDERS
// ========================================

export function buildLineChartData(labels, datasets) {
    return {
        labels: labels,
        datasets: datasets.map(dataset => ({
            label: dataset.label,
            data: dataset.data,
            borderColor: dataset.color || ChartColors.primary,
            backgroundColor: dataset.backgroundColor || createAreaGradient(null, dataset.color || ChartColors.primary),
            fill: dataset.fill !== undefined ? dataset.fill : true,
            tension: dataset.tension || 0.4,
            pointBackgroundColor: dataset.pointColor || dataset.color || ChartColors.primary,
            pointBorderColor: dataset.borderColor || '#fff',
            pointBorderWidth: dataset.borderWidth || 2,
            pointRadius: dataset.pointRadius || 4,
            pointHoverRadius: dataset.hoverRadius || 6,
            borderWidth: dataset.lineWidth || 2,
            currency: dataset.currency || false
        }))
    };
}

export function buildBarChartData(labels, datasets) {
    return {
        labels: labels,
        datasets: datasets.map(dataset => ({
            label: dataset.label,
            data: dataset.data,
            backgroundColor: dataset.colors || [ChartColors.primary],
            borderColor: dataset.borderColors || dataset.colors || [ChartColors.primary],
            borderWidth: dataset.borderWidth || 1,
            borderRadius: dataset.borderRadius || 4,
            currency: dataset.currency || false
        }))
    };
}

// ========================================
// REPORTING COMPONENTS
// ========================================

export class ReportChart {
    constructor(element, type = 'line') {
        this.element = element;
        this.type = type;
        this.builder = new ChartBuilder(element);
        this.chart = null;
        this.data = null;
        this.options = {};
    }

    setData(labels, datasets) {
        if (this.type === 'line') {
            this.data = buildLineChartData(labels, datasets);
        } else if (this.type === 'bar') {
            this.data = buildBarChartData(labels, datasets);
        }
        return this;
    }

    setOptions(options) {
        this.options = options;
        return this;
    }

    render() {
        if (this.type === 'line') {
            this.chart = this.builder.createLineChart(this.data, this.options);
        } else if (this.type === 'bar') {
            this.chart = this.builder.createBarChart(this.data, this.options);
        }
        return this.chart;
    }

    update() {
        if (this.chart) {
            this.chart.update();
        }
        return this;
    }

    download(filename) {
        this.builder.downloadImage(filename);
        return this;
    }

    print() {
        this.builder.print();
        return this;
    }

    destroy() {
        this.builder.destroy();
        return this;
    }
}

// ========================================
// EXPORT DEFAULT
// ========================================
export default {
    ChartColors,
    ChartPalettes,
    ChartBuilder,
    ReportChart,
    createGradient,
    createAreaGradient,
    generateMonthlyData,
    generateCategoryData,
    formatChartLabels,
    buildLineChartData,
    buildBarChartData
};
