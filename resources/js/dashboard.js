/**
 * DASHBOARD.JS - Dashboard Functionality
 * Uses only Line and Bar Charts
 */

import { ChartBuilder, generateMonthlyData, buildLineChartData, buildBarChartData } from './charts.js';

// ========================================
// DASHBOARD CONFIGURATION
// ========================================
const DASHBOARD_CONFIG = {
    refreshInterval: 60000,
    animationDuration: 1000
};

// ========================================
// DASHBOARD CLASS
// ========================================
class Dashboard {
    constructor(config = {}) {
        this.config = { ...DASHBOARD_CONFIG, ...config };
        this.charts = new Map();
        this.refreshTimer = null;
        this.init();
    }

    init() {
        this.animateStats();
        this.initCharts();
        this.initActivityFeed();
        this.startAutoRefresh();

        // Listen for theme changes
        document.addEventListener('themeChange', () => {
            this.updateCharts();
        });
    }

    // ========================================
    // STATS ANIMATION
    // ========================================

    animateStats() {
        document.querySelectorAll('.metric .value, .stat-number').forEach((element) => {
            const text = element.textContent;
            const number = parseInt(text.replace(/[^0-9]/g, ''));

            if (number && number > 0) {
                const prefix = text.replace(/[0-9,]/g, '');
                const suffix = text.replace(/[0-9,]/g, '').replace(/^[^0-9]*/, '');
                const duration = this.config.animationDuration;
                const steps = 30;
                const increment = Math.ceil(number / steps);
                const stepTime = duration / steps;
                let current = 0;
                let counter = 0;

                const interval = setInterval(() => {
                    counter++;
                    current = Math.min(current + increment, number);
                    const formatted = current.toLocaleString();
                    element.textContent = prefix + formatted + suffix;

                    if (counter >= steps || current >= number) {
                        element.textContent = text;
                        clearInterval(interval);
                    }
                }, stepTime);
            }
        });
    }

    // ========================================
    // CHARTS INITIALIZATION (Line & Bar Only)
    // ========================================

    initCharts() {
        // ---- LINE CHART: Revenue Trend ----
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            const { labels, data } = generateMonthlyData(12, 500000, 0.3);
            const chartData = buildLineChartData(labels, [{
                label: 'Revenue',
                data: data,
                color: '#055236',
                currency: true
            }]);

            const builder = new ChartBuilder(revenueCtx);
            const chart = builder.createLineChart(chartData, {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'KES ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            });
            this.charts.set('revenue', chart);
        }

        // ---- BAR CHART: Property Types ----
        const propertyTypeCtx = document.getElementById('propertyTypeChart');
        if (propertyTypeCtx) {
            const chartData = buildBarChartData(
                ['Apartments', 'Houses', 'Commercial', 'Land', 'Others'],
                [{
                    label: 'Properties by Type',
                    data: [89, 56, 34, 12, 43],
                    colors: ['#055236', '#7FA48E', '#2D4C39', '#80B9B1', '#6C27DA'],
                    borderRadius: 6
                }]
            );

            const builder = new ChartBuilder(propertyTypeCtx);
            const chart = builder.createBarChart(chartData, {
                plugins: {
                    legend: { display: false }
                }
            });
            this.charts.set('propertyType', chart);
        }

        // ---- BAR CHART: Monthly Occupancy ----
        const occupancyCtx = document.getElementById('occupancyChart');
        if (occupancyCtx) {
            const { labels, data } = generateMonthlyData(6, 800, 0.15);
            const chartData = buildBarChartData(labels, [{
                label: 'Occupied Units',
                data: data,
                colors: ['#28a745']
            }, {
                label: 'Vacant Units',
                data: data.map(d => Math.round(d * 0.2)),
                colors: ['#dc3545']
            }]);

            const builder = new ChartBuilder(occupancyCtx);
            const chart = builder.createStackedBarChart(chartData, {
                plugins: {
                    legend: { position: 'bottom' }
                }
            });
            this.charts.set('occupancy', chart);
        }

        // ---- LINE CHART: Payment Trends ----
        const paymentCtx = document.getElementById('paymentChart');
        if (paymentCtx) {
            const { labels, data } = generateMonthlyData(12, 600000, 0.25);
            const chartData = buildLineChartData(labels, [
                {
                    label: 'Paid',
                    data: data,
                    color: '#28a745'
                },
                {
                    label: 'Pending',
                    data: data.map(d => Math.round(d * 0.15)),
                    color: '#ffc107'
                },
                {
                    label: 'Overdue',
                    data: data.map(d => Math.round(d * 0.08)),
                    color: '#dc3545'
                }
            ]);

            const builder = new ChartBuilder(paymentCtx);
            const chart = builder.createLineChart(chartData, {
                plugins: {
                    legend: { position: 'bottom' }
                }
            });
            this.charts.set('payment', chart);
        }

        // ---- HORIZONTAL BAR CHART: Top Properties ----
        const topPropsCtx = document.getElementById('topPropertiesChart');
        if (topPropsCtx) {
            const chartData = buildBarChartData(
                ['Skyline Apts', 'Green Valley', 'Ocean View', 'Sunset Res', 'Park Place'],
                [{
                    label: 'Revenue (KES)',
                    data: [95000, 82000, 76000, 68000, 55000],
                    colors: ['#055236', '#2D4C39', '#7FA48E', '#80B9B1', '#6C27DA'],
                    borderRadius: 4
                }]
            );

            const builder = new ChartBuilder(topPropsCtx);
            const chart = builder.createHorizontalBarChart(chartData, {
                plugins: {
                    legend: { display: false }
                },
                indexAxis: 'y'
            });
            this.charts.set('topProperties', chart);
        }
    }

    // ========================================
    // CHART UPDATES
    // ========================================

    updateCharts() {
        this.charts.forEach((chart) => {
            // Update chart colors for theme
            const isDark = window.themeManager?.isDark() || false;
            const textColor = isDark ? '#e0e0e0' : '#6b7280';
            const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';

            if (chart.options && chart.options.scales) {
                if (chart.options.scales.x) {
                    chart.options.scales.x.ticks.color = textColor;
                    chart.options.scales.x.grid.color = gridColor;
                }
                if (chart.options.scales.y) {
                    chart.options.scales.y.ticks.color = textColor;
                    chart.options.scales.y.grid.color = gridColor;
                }
            }
            if (chart.options && chart.options.plugins && chart.options.plugins.legend) {
                chart.options.plugins.legend.labels.color = textColor;
            }
            chart.update();
        });
    }

    // ========================================
    // ACTIVITY FEED
    // ========================================

    initActivityFeed() {
        const feedContainer = document.querySelector('.activity-feed');
        if (!feedContainer) return;

        const activities = [
            { icon: 'fa-user-plus', color: 'bg-primary', text: 'New user registered: John Doe', time: '5 min ago' },
            { icon: 'fa-building', color: 'bg-success', text: 'New property added: Skyline Apartments', time: '12 min ago' },
            { icon: 'fa-credit-card', color: 'bg-warning', text: 'Payment received: KES 25,000', time: '28 min ago' },
            { icon: 'fa-tools', color: 'bg-info', text: 'Maintenance request #123 completed', time: '1 hour ago' },
            { icon: 'fa-file-signature', color: 'bg-purple', text: 'Contract signed: Unit 4B', time: '2 hours ago' }
        ];

        feedContainer.innerHTML = activities.map(item => `
            <div class="feed-item d-flex align-items-center gap-3">
                <div class="feed-icon ${item.color} text-white">
                    <i class="fas ${item.icon}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-sm">${item.text}</div>
                    <div class="text-xs text-muted">${item.time}</div>
                </div>
            </div>
        `).join('');
    }

    // ========================================
    // AUTO REFRESH
    // ========================================

    startAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
        }

        this.refreshTimer = setInterval(() => {
            this.refreshDashboard();
        }, this.config.refreshInterval);
    }

    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }

    refreshDashboard() {
        // Simulate refresh
        document.querySelectorAll('.metric, .chart-container').forEach(el => {
            el.style.opacity = '0.6';
        });

        setTimeout(() => {
            // Update stats with random values
            this.updateStats();

            document.querySelectorAll('.metric, .chart-container').forEach(el => {
                el.style.opacity = '1';
            });

            // Add activity item
            const feed = document.querySelector('.activity-feed');
            if (feed) {
                const item = document.createElement('div');
                item.className = 'feed-item d-flex align-items-center gap-3';
                item.innerHTML = `
                    <div class="feed-icon bg-primary text-white">
                        <i class="fas fa-sync"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="text-sm">Dashboard auto-refreshed</div>
                        <div class="text-xs text-muted">Just now</div>
                    </div>
                `;
                feed.insertBefore(item, feed.firstChild);

                // Remove oldest if too many
                const items = feed.querySelectorAll('.feed-item');
                if (items.length > 10) {
                    items[items.length - 1].remove();
                }
            }
        }, 500);
    }

    updateStats() {
        const stats = [
            document.querySelector('.metric-total-properties .value'),
            document.querySelector('.metric-occupied .value'),
            document.querySelector('.metric-tenants .value'),
            document.querySelector('.metric-revenue .value')
        ];

        const values = [
            1068 + Math.floor(Math.random() * 10),
            847 + Math.floor(Math.random() * 5),
            923 + Math.floor(Math.random() * 10),
            'KES ' + (920000 + Math.floor(Math.random() * 50000)).toLocaleString()
        ];

        stats.forEach((el, index) => {
            if (el) {
                el.textContent = values[index];
            }
        });
    }

    // ========================================
    // CLEANUP
    // ========================================

    destroy() {
        this.stopAutoRefresh();
        this.charts.forEach((chart) => {
            chart.destroy();
        });
        this.charts.clear();
    }
}

// ========================================
// INITIALIZE DASHBOARD
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new Dashboard();
    window.dashboard = dashboard;
    window.SystemUI = window.SystemUI || {};
    window.SystemUI.dashboard = dashboard;
});

export default Dashboard;
