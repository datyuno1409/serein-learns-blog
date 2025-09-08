class LoadingManager {
    constructor() {
        this.init();
    }

    init() {
        this.createLoadingScreen();
        this.setupPageLoadHandlers();
        this.setupImageLazyLoading();
        this.setupFormLoadingStates();
    }

    createLoadingScreen() {
        const loadingHTML = `
            <div id="loading-screen" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
                <div class="text-center">
                    <div class="relative">
                        <div class="w-20 h-20 border-4 border-serein-200 border-t-serein-600 rounded-full animate-spin mx-auto mb-4"></div>
                        <div class="absolute inset-0 w-20 h-20 border-4 border-transparent border-r-serein-400 rounded-full animate-spin mx-auto" style="animation-delay: 0.5s; animation-duration: 1.5s;"></div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Serein Learns</h2>
                    <p class="text-gray-600 animate-pulse">Đang tải nội dung...</p>
                    <div class="mt-4">
                        <div class="w-48 h-2 bg-gray-200 rounded-full mx-auto overflow-hidden">
                            <div id="loading-progress" class="h-full bg-gradient-to-r from-serein-500 to-serein-600 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('afterbegin', loadingHTML);
    }

    setupPageLoadHandlers() {
        let progress = 0;
        const progressBar = document.getElementById('loading-progress');
        
        const updateProgress = (value) => {
            progress = Math.min(value, 100);
            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            updateProgress(30);
        });

        window.addEventListener('load', () => {
            updateProgress(100);
            setTimeout(() => {
                this.hideLoadingScreen();
            }, 500);
        });

        const images = document.querySelectorAll('img');
        let loadedImages = 0;
        
        images.forEach(img => {
            if (img.complete) {
                loadedImages++;
            } else {
                img.addEventListener('load', () => {
                    loadedImages++;
                    updateProgress(30 + (loadedImages / images.length) * 60);
                });
            }
        });

        if (images.length === 0) {
            updateProgress(90);
        }
    }

    hideLoadingScreen() {
        const loadingScreen = document.getElementById('loading-screen');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            loadingScreen.style.transform = 'scale(0.95)';
            loadingScreen.style.transition = 'all 0.5s ease-out';
            
            setTimeout(() => {
                loadingScreen.remove();
                document.body.classList.add('loaded');
            }, 500);
        }
    }

    setupImageLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const placeholder = img.parentElement.querySelector('.image-placeholder');
                    
                    img.src = img.dataset.src;
                    img.classList.add('loading');
                    
                    img.addEventListener('load', () => {
                        img.classList.remove('loading');
                        img.classList.add('loaded');
                        if (placeholder) {
                            placeholder.style.opacity = '0';
                            setTimeout(() => placeholder.remove(), 300);
                        }
                    });
                    
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    setupFormLoadingStates() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                            Đang xử lý...
                        </div>
                    `;
                    
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }, 2000);
                }
            });
        });
    }

    showSkeletonLoader(container) {
        const skeletonHTML = `
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        `;
        container.innerHTML = skeletonHTML;
    }

    hideSkeletonLoader(container, content) {
        container.innerHTML = content;
        container.classList.add('animate-fadeInUp');
    }
}

class NotificationManager {
    constructor() {
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }

    show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        notification.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform translate-x-full transition-transform duration-300`;
        notification.innerHTML = `
            <i class="${icons[type]}"></i>
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        document.getElementById('notification-container').appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }
}

const loadingManager = new LoadingManager();
const notificationManager = new NotificationManager();

window.showNotification = (message, type, duration) => {
    notificationManager.show(message, type, duration);
};

window.showSkeletonLoader = (container) => {
    loadingManager.showSkeletonLoader(container);
};

window.hideSkeletonLoader = (container, content) => {
    loadingManager.hideSkeletonLoader(container, content);
};