/**
 * Admin Dashboard JavaScript
 * Handles interactive features and animations for the admin dashboard
 */

class AdminDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeAnimations();
        this.setupChartInteractions();
        this.initializeTooltips();
        this.setupRealTimeUpdates();
        this.initializeKeyboardShortcuts();
    }

    setupEventListeners() {
        // Period selector buttons
        document.querySelectorAll('[data-period]').forEach(button => {
            button.addEventListener('click', (e) => this.handlePeriodChange(e));
        });

        // Stats card hover effects
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', (e) => this.handleStatsCardHover(e));
            card.addEventListener('mouseleave', (e) => this.handleStatsCardLeave(e));
        });

        // Article and comment item interactions
        document.querySelectorAll('.article-item, .comment-item').forEach(item => {
            item.addEventListener('click', (e) => this.handleItemClick(e));
        });

        // Quick action buttons
        document.querySelectorAll('.dashboard-actions .btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleQuickAction(e));
        });

        // Search functionality (if search input exists)
        const searchInput = document.querySelector('#dashboard-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e));
        }
    }

    initializeAnimations() {
        // Animate stats cards on load
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('fade-in');
            }, index * 100);
        });

        // Animate content cards
        const contentCards = document.querySelectorAll('.content-card, .analytics-card');
        contentCards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('slide-up');
            }, (statsCards.length * 100) + (index * 150));
        });

        // Number counter animation for stats
        this.animateNumbers();
    }

    animateNumbers() {
        const numbers = document.querySelectorAll('.stats-number');
        numbers.forEach(number => {
            const target = parseInt(number.textContent.replace(/,/g, ''));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current).toLocaleString();
            }, 16);
        });
    }

    handlePeriodChange(e) {
        const button = e.currentTarget;
        const period = button.getAttribute('data-period');
        
        // Update active state
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');

        // Show loading state
        this.showChartLoading();

        // Simulate API call to update chart data
        setTimeout(() => {
            this.updateChartData(period);
            this.hideChartLoading();
        }, 1000);
    }

    updateChartData(period) {
        // This would typically fetch new data from the server
        console.log(`Updating chart data for period: ${period}`);
        
        // Update performance chart with new data
        if (window.performanceChart) {
            const newData = this.generateMockData(period);
            window.performanceChart.data.datasets[0].data = newData.articles;
            window.performanceChart.data.datasets[1].data = newData.comments;
            window.performanceChart.update('active');
        }

        // Show success notification
        this.showNotification('Chart updated successfully', 'success');
    }

    generateMockData(period) {
        const dataPoints = period === '7d' ? 7 : period === '30d' ? 30 : 90;
        const articles = [];
        const comments = [];

        for (let i = 0; i < dataPoints; i++) {
            articles.push(Math.floor(Math.random() * 50) + 10);
            comments.push(Math.floor(Math.random() * 100) + 20);
        }

        return { articles, comments };
    }

    showChartLoading() {
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            container.classList.add('loading');
        });
    }

    hideChartLoading() {
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            container.classList.remove('loading');
        });
    }

    handleStatsCardHover(e) {
        const card = e.currentTarget;
        const icon = card.querySelector('.stats-icon');
        
        if (icon) {
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.transition = 'transform 0.3s ease';
        }
    }

    handleStatsCardLeave(e) {
        const card = e.currentTarget;
        const icon = card.querySelector('.stats-icon');
        
        if (icon) {
            icon.style.transform = 'scale(1) rotate(0deg)';
        }
    }

    handleItemClick(e) {
        // Prevent default if clicking on action buttons
        if (e.target.closest('.btn') || e.target.closest('a')) {
            return;
        }

        const item = e.currentTarget;
        const link = item.querySelector('a');
        
        if (link) {
            // Add click animation
            item.style.transform = 'scale(0.98)';
            setTimeout(() => {
                item.style.transform = 'scale(1)';
                window.location.href = link.href;
            }, 150);
        }
    }

    handleQuickAction(e) {
        const button = e.currentTarget;
        const action = button.getAttribute('data-action');
        
        // Add click animation
        button.style.transform = 'scale(0.95)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 150);

        // Handle specific actions
        switch (action) {
            case 'new-article':
                this.showNotification('Redirecting to create new article...', 'info');
                break;
            case 'analytics':
                this.showNotification('Opening analytics dashboard...', 'info');
                break;
            case 'settings':
                this.showNotification('Opening settings...', 'info');
                break;
        }
    }

    handleSearch(e) {
        const query = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.article-item, .comment-item');
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            const shouldShow = text.includes(query);
            
            item.style.display = shouldShow ? 'flex' : 'none';
            
            if (shouldShow) {
                item.classList.add('fade-in');
            }
        });
    }

    setupChartInteractions() {
        // Add custom interactions for charts
        if (window.Chart) {
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
            Chart.defaults.plugins.tooltip.titleColor = '#fff';
            Chart.defaults.plugins.tooltip.bodyColor = '#fff';
            Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.1)';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.displayColors = false;
        }
    }

    initializeTooltips() {
        // Simple tooltip implementation
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.currentTarget, e.currentTarget.getAttribute('data-tooltip'));
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 1000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
        `;
        
        document.body.appendChild(tooltip);
        
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
        
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 10);
        
        this.currentTooltip = tooltip;
    }

    hideTooltip() {
        if (this.currentTooltip) {
            this.currentTooltip.style.opacity = '0';
            setTimeout(() => {
                if (this.currentTooltip && this.currentTooltip.parentNode) {
                    this.currentTooltip.parentNode.removeChild(this.currentTooltip);
                }
                this.currentTooltip = null;
            }, 200);
        }
    }

    setupRealTimeUpdates() {
        // Simulate real-time updates (in a real app, this would use WebSockets or SSE)
        setInterval(() => {
            this.updateRealTimeStats();
        }, 30000); // Update every 30 seconds
    }

    updateRealTimeStats() {
        const statsNumbers = document.querySelectorAll('.stats-number');
        
        statsNumbers.forEach(number => {
            const current = parseInt(number.textContent.replace(/,/g, ''));
            const change = Math.floor(Math.random() * 5) - 2; // Random change between -2 and +2
            const newValue = Math.max(0, current + change);
            
            if (change !== 0) {
                number.textContent = newValue.toLocaleString();
                
                // Add flash effect
                number.style.background = change > 0 ? '#d4edda' : '#f8d7da';
                number.style.transition = 'background 0.3s';
                
                setTimeout(() => {
                    number.style.background = 'transparent';
                }, 1000);
            }
        });
    }

    initializeKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N: New Article
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                const newArticleBtn = document.querySelector('[href*="/create"]');
                if (newArticleBtn) {
                    newArticleBtn.click();
                }
            }
            
            // Ctrl/Cmd + /: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                e.preventDefault();
                const searchInput = document.querySelector('#dashboard-search');
                if (searchInput) {
                    searchInput.focus();
                }
            }
            
            // Escape: Clear search
            if (e.key === 'Escape') {
                const searchInput = document.querySelector('#dashboard-search');
                if (searchInput && searchInput === document.activeElement) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                    searchInput.blur();
                }
            }
        });
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 400px;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Close button functionality
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            this.hideNotification(notification);
        }, 5000);
    }

    hideNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    getNotificationColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#007bff'
        };
        return colors[type] || colors.info;
    }

    // Utility methods
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Performance monitoring
    measurePerformance(name, fn) {
        const start = performance.now();
        const result = fn();
        const end = performance.now();
        console.log(`${name} took ${end - start} milliseconds`);
        return result;
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminDashboard;
}