// Main JavaScript file for Learning with Serein Blog Demo

// Global variables
let searchTimeout;
const SEARCH_DELAY = 300;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeComponents();
    initializeAnimations();
    initializeSearch();
    initializeNavigation();
    initializeScrollEffects();
});

// Initialize all components
function initializeComponents() {
    initializeMobileMenu();
    initializeSearchBar();
    initializeNewsletterForm();
    initializeContactForm();
    initializeFilterSystem();
    initializeLoadMore();
    initializeReadingProgress();
    initializeTableOfContents();
    initializeSocialShare();
}

// Mobile Menu System
function initializeMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const overlay = document.createElement('div');
    
    if (!mobileMenuBtn || !mobileMenu) return;
    
    // Create overlay
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-40 hidden';
    overlay.id = 'mobileMenuOverlay';
    document.body.appendChild(overlay);
    
    // Open mobile menu
    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.add('active');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    // Close mobile menu
    function closeMobileMenuHandler() {
        mobileMenu.classList.remove('active');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    if (closeMobileMenu) {
        closeMobileMenu.addEventListener('click', closeMobileMenuHandler);
    }
    
    overlay.addEventListener('click', closeMobileMenuHandler);
    
    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMobileMenuHandler();
        }
    });
}

// Search Bar System
function initializeSearchBar() {
    const searchBtn = document.getElementById('searchBtn');
    const searchBar = document.getElementById('searchBar');
    const searchInput = searchBar?.querySelector('input');
    
    if (!searchBtn || !searchBar) return;
    
    searchBtn.addEventListener('click', () => {
        searchBar.classList.toggle('hidden');
        if (!searchBar.classList.contains('hidden')) {
            setTimeout(() => searchInput?.focus(), 100);
        }
    });
    
    // Close search on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !searchBar.classList.contains('hidden')) {
            searchBar.classList.add('hidden');
        }
    });
}

// Advanced Search System
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="tìm kiếm"], input[placeholder*="Tìm kiếm"]');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(e.target.value, e.target);
            }, SEARCH_DELAY);
        });
    });
}

function performSearch(query, inputElement) {
    if (!query.trim()) {
        clearSearchResults(inputElement);
        return;
    }
    
    // Simulate search functionality
    const searchableElements = getSearchableElements(inputElement);
    const results = searchableElements.filter(element => 
        element.textContent.toLowerCase().includes(query.toLowerCase())
    );
    
    displaySearchResults(results, query, inputElement);
}

function getSearchableElements(inputElement) {
    // Determine context based on page
    if (window.location.pathname.includes('articles')) {
        return document.querySelectorAll('.article-card');
    } else if (window.location.pathname.includes('projects')) {
        return document.querySelectorAll('.project-card');
    } else {
        return document.querySelectorAll('.article-card, .project-card, .topic-card');
    }
}

function displaySearchResults(results, query, inputElement) {
    const container = findResultsContainer(inputElement);
    if (!container) return;
    
    // Hide non-matching elements
    const allElements = getSearchableElements(inputElement);
    allElements.forEach(element => {
        if (results.includes(element)) {
            element.style.display = '';
            highlightSearchTerm(element, query);
        } else {
            element.style.display = 'none';
        }
    });
    
    // Show results count
    updateResultsCount(results.length, query, container);
}

function findResultsContainer(inputElement) {
    return inputElement.closest('section')?.querySelector('.grid') || 
           document.querySelector('.articles-grid, .projects-grid, .grid');
}

function highlightSearchTerm(element, query) {
    const textNodes = getTextNodes(element);
    textNodes.forEach(node => {
        const text = node.textContent;
        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
        if (regex.test(text)) {
            const highlightedText = text.replace(regex, '<mark class="bg-yellow-200">$1</mark>');
            const wrapper = document.createElement('span');
            wrapper.innerHTML = highlightedText;
            node.parentNode.replaceChild(wrapper, node);
        }
    });
}

function getTextNodes(element) {
    const textNodes = [];
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_TEXT,
        null,
        false
    );
    
    let node;
    while (node = walker.nextNode()) {
        if (node.textContent.trim()) {
            textNodes.push(node);
        }
    }
    return textNodes;
}

function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function updateResultsCount(count, query, container) {
    let countElement = container.querySelector('.search-results-count');
    if (!countElement) {
        countElement = document.createElement('div');
        countElement.className = 'search-results-count text-sm text-gray-600 mb-4 p-3 bg-blue-50 rounded-lg';
        container.parentNode.insertBefore(countElement, container);
    }
    
    countElement.innerHTML = `
        <i class="fas fa-search mr-2"></i>
        Tìm thấy <strong>${count}</strong> kết quả cho "<strong>${query}</strong>"
        ${count === 0 ? '<br><span class="text-gray-500">Thử tìm kiếm với từ khóa khác</span>' : ''}
    `;
}

function clearSearchResults(inputElement) {
    const container = findResultsContainer(inputElement);
    if (!container) return;
    
    // Show all elements
    const allElements = getSearchableElements(inputElement);
    allElements.forEach(element => {
        element.style.display = '';
        // Remove highlights
        const marks = element.querySelectorAll('mark');
        marks.forEach(mark => {
            mark.outerHTML = mark.innerHTML;
        });
    });
    
    // Remove results count
    const countElement = container.querySelector('.search-results-count');
    if (countElement) {
        countElement.remove();
    }
}

// Filter System
function initializeFilterSystem() {
    const categoryFilters = document.querySelectorAll('.category-filter');
    const sortSelects = document.querySelectorAll('select[name="sort"]');
    
    categoryFilters.forEach(filter => {
        filter.addEventListener('click', handleCategoryFilter);
    });
    
    sortSelects.forEach(select => {
        select.addEventListener('change', handleSortChange);
    });
}

function handleCategoryFilter(e) {
    e.preventDefault();
    const category = e.target.dataset.category;
    const filterButtons = document.querySelectorAll('.category-filter');
    
    // Update active state
    filterButtons.forEach(btn => btn.classList.remove('bg-serein-600', 'text-white'));
    filterButtons.forEach(btn => btn.classList.add('bg-gray-200', 'text-gray-700'));
    
    e.target.classList.remove('bg-gray-200', 'text-gray-700');
    e.target.classList.add('bg-serein-600', 'text-white');
    
    // Filter content
    filterByCategory(category);
}

function filterByCategory(category) {
    const items = document.querySelectorAll('.article-card, .project-card');
    
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = '';
            item.classList.add('fade-in');
        } else {
            item.style.display = 'none';
        }
    });
}

function handleSortChange(e) {
    const sortBy = e.target.value;
    const container = e.target.closest('section').querySelector('.grid');
    const items = Array.from(container.children);
    
    items.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'oldest':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'popular':
                return parseInt(b.dataset.views) - parseInt(a.dataset.views);
            case 'title':
                return a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent);
            default:
                return 0;
        }
    });
    
    // Re-append sorted items
    items.forEach(item => container.appendChild(item));
}

// Load More System
function initializeLoadMore() {
    const loadMoreBtns = document.querySelectorAll('.load-more-btn');
    
    loadMoreBtns.forEach(btn => {
        btn.addEventListener('click', handleLoadMore);
    });
}

function handleLoadMore(e) {
    const btn = e.target;
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang tải...';
    btn.disabled = true;
    
    // Simulate loading
    setTimeout(() => {
        loadMoreContent(btn);
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 1500);
}

function loadMoreContent(btn) {
    const container = btn.previousElementSibling;
    const itemType = container.classList.contains('articles-grid') ? 'article' : 'project';
    
    // Create mock items
    for (let i = 0; i < 3; i++) {
        const item = createMockItem(itemType);
        container.appendChild(item);
        
        // Animate in
        setTimeout(() => {
            item.classList.add('fade-in');
        }, i * 100);
    }
}

function createMockItem(type) {
    const item = document.createElement('div');
    item.className = `${type}-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 opacity-0`;
    
    if (type === 'article') {
        item.innerHTML = `
            <div class="h-48 bg-gradient-to-r from-gray-300 to-gray-400"></div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Mới</span>
                    <span class="text-gray-500 text-sm">5 phút đọc</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-serein-600 transition-colors">
                    Bài viết mới được tải
                </h3>
                <p class="text-gray-600 mb-4">Nội dung mô tả ngắn gọn về bài viết này...</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center gap-4">
                        <span><i class="fas fa-eye mr-1"></i>0 lượt xem</span>
                        <span><i class="fas fa-comment mr-1"></i>0 bình luận</span>
                    </div>
                    <span>Vừa xong</span>
                </div>
            </div>
        `;
    } else {
        item.innerHTML = `
            <div class="h-48 bg-gradient-to-r from-purple-300 to-pink-400"></div>
            <div class="p-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Mới</span>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Web</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3 hover:text-serein-600 transition-colors">
                    Dự án mới được tải
                </h3>
                <p class="text-gray-600 mb-4">Mô tả ngắn gọn về dự án này...</p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fab fa-js-square text-yellow-500"></i>
                        <i class="fab fa-react text-blue-500"></i>
                        <i class="fab fa-node-js text-green-500"></i>
                    </div>
                    <span class="text-sm text-gray-500">Vừa xong</span>
                </div>
            </div>
        `;
    }
    
    return item;
}

// Newsletter Form
function initializeNewsletterForm() {
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    
    newsletterForms.forEach(form => {
        form.addEventListener('submit', handleNewsletterSubmit);
    });
}

function handleNewsletterSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const email = form.querySelector('input[type="email"]').value;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validate email
    if (!isValidEmail(email)) {
        showNotification('Vui lòng nhập email hợp lệ!', 'error');
        return;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang đăng ký...';
    submitBtn.disabled = true;
    
    // Simulate subscription
    setTimeout(() => {
        showNotification('Đăng ký thành công! Cảm ơn bạn đã quan tâm.', 'success');
        form.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Contact Form
function initializeContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmit);
    }
}

function handleContactSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validate required fields
    if (!data.name || !data.email || !data.subject || !data.message) {
        showNotification('Vui lòng điền đầy đủ các trường bắt buộc!', 'error');
        return;
    }
    
    if (!data.privacy) {
        showNotification('Vui lòng đồng ý với chính sách bảo mật!', 'error');
        return;
    }
    
    if (!isValidEmail(data.email)) {
        showNotification('Vui lòng nhập email hợp lệ!', 'error');
        return;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang gửi...';
    submitBtn.disabled = true;
    
    // Simulate form submission
    setTimeout(() => {
        showNotification('Cảm ơn bạn đã liên hệ! Tôi sẽ phản hồi trong thời gian sớm nhất.', 'success');
        form.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Reading Progress (for article pages)
function initializeReadingProgress() {
    const progressBar = document.getElementById('readingProgress');
    if (!progressBar) return;
    
    window.addEventListener('scroll', updateReadingProgress);
}

function updateReadingProgress() {
    const progressBar = document.getElementById('readingProgress');
    const article = document.querySelector('.article-content');
    
    if (!progressBar || !article) return;
    
    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const windowHeight = window.innerHeight;
    const scrollTop = window.pageYOffset;
    
    const progress = Math.min(
        Math.max((scrollTop - articleTop + windowHeight) / articleHeight, 0),
        1
    );
    
    progressBar.style.width = `${progress * 100}%`;
}

// Table of Contents
function initializeTableOfContents() {
    const toc = document.getElementById('tableOfContents');
    if (!toc) return;
    
    const headings = document.querySelectorAll('.article-content h2, .article-content h3');
    const tocList = toc.querySelector('ul');
    
    headings.forEach((heading, index) => {
        // Add ID to heading if it doesn't have one
        if (!heading.id) {
            heading.id = `heading-${index}`;
        }
        
        // Create TOC item
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = `#${heading.id}`;
        a.textContent = heading.textContent;
        a.className = `block py-1 text-sm hover:text-serein-600 transition-colors ${
            heading.tagName === 'H3' ? 'ml-4 text-gray-600' : 'font-medium text-gray-800'
        }`;
        
        li.appendChild(a);
        tocList.appendChild(li);
        
        // Smooth scroll
        a.addEventListener('click', (e) => {
            e.preventDefault();
            heading.scrollIntoView({ behavior: 'smooth' });
            
            // Update active state
            toc.querySelectorAll('a').forEach(link => link.classList.remove('text-serein-600', 'font-semibold'));
            a.classList.add('text-serein-600', 'font-semibold');
        });
    });
    
    // Highlight current section on scroll
    window.addEventListener('scroll', highlightCurrentSection);
}

function highlightCurrentSection() {
    const toc = document.getElementById('tableOfContents');
    if (!toc) return;
    
    const headings = document.querySelectorAll('.article-content h2, .article-content h3');
    const tocLinks = toc.querySelectorAll('a');
    
    let currentHeading = null;
    
    headings.forEach(heading => {
        const rect = heading.getBoundingClientRect();
        if (rect.top <= 100) {
            currentHeading = heading;
        }
    });
    
    if (currentHeading) {
        tocLinks.forEach(link => {
            link.classList.remove('text-serein-600', 'font-semibold');
            if (link.getAttribute('href') === `#${currentHeading.id}`) {
                link.classList.add('text-serein-600', 'font-semibold');
            }
        });
    }
}

// Social Share
function initializeSocialShare() {
    const shareButtons = document.querySelectorAll('.share-btn');
    
    shareButtons.forEach(btn => {
        btn.addEventListener('click', handleSocialShare);
    });
    
    // Copy link functionality
    const copyLinkBtn = document.getElementById('copyLinkBtn');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', copyCurrentUrl);
    }
}

function handleSocialShare(e) {
    e.preventDefault();
    
    const platform = e.target.closest('.share-btn').dataset.platform;
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    const description = encodeURIComponent(document.querySelector('meta[name="description"]')?.content || '');
    
    let shareUrl = '';
    
    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
        case 'telegram':
            shareUrl = `https://t.me/share/url?url=${url}&text=${title}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

function copyCurrentUrl() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showNotification('Đã sao chép liên kết!', 'success');
    }).catch(() => {
        showNotification('Không thể sao chép liên kết', 'error');
    });
}

// Navigation Enhancement
function initializeNavigation() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Back to top button
    createBackToTopButton();
}

function createBackToTopButton() {
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.className = 'fixed bottom-6 right-6 w-12 h-12 bg-serein-600 text-white rounded-full shadow-lg hover:bg-serein-700 transition-all duration-300 opacity-0 pointer-events-none z-50';
    backToTop.id = 'backToTop';
    
    document.body.appendChild(backToTop);
    
    // Show/hide based on scroll position
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            backToTop.classList.add('opacity-0', 'pointer-events-none');
        }
    });
    
    // Scroll to top
    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// Scroll Effects
function initializeScrollEffects() {
    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('section, .card, .article-card, .project-card, .contact-card').forEach(element => {
        observer.observe(element);
    });
}

// Animation System
function initializeAnimations() {
    // Add CSS for animations if not already present
    if (!document.querySelector('#custom-animations')) {
        const style = document.createElement('style');
        style.id = 'custom-animations';
        style.textContent = `
            .fade-in {
                animation: fadeInUp 0.6s ease-out forwards;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .slide-in-left {
                animation: slideInLeft 0.6s ease-out forwards;
            }
            
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .bounce-in {
                animation: bounceIn 0.8s ease-out forwards;
            }
            
            @keyframes bounceIn {
                0% {
                    opacity: 0;
                    transform: scale(0.3);
                }
                50% {
                    opacity: 1;
                    transform: scale(1.05);
                }
                70% {
                    transform: scale(0.9);
                }
                100% {
                    opacity: 1;
                    transform: scale(1);
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm transform translate-x-full transition-transform duration-300`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="${icons[type] || icons.info}"></i>
            <span>${message}</span>
            <button class="ml-2 hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function debounce(func, wait) {
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Export functions for global access
window.BlogDemo = {
    showNotification,
    performSearch,
    filterByCategory,
    copyCurrentUrl,
    debounce,
    throttle
};