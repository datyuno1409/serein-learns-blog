// Project Detail Page JavaScript

$(document).ready(function() {
    // Initialize all components
    initializeGallery();
    initializeShareButtons();
    initializeScrollEffects();
    initializeLazyLoading();
    initializeTooltips();
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 600);
        }
    });
});

// Gallery functionality
function initializeGallery() {
    const $galleryImage = $('.gallery-image');
    
    if ($galleryImage.length) {
        // Image modal functionality
        $galleryImage.on('click', function() {
            const imageSrc = $(this).attr('src');
            const imageAlt = $(this).attr('alt') || 'Project Image';
            
            // Create modal
            const modal = `
                <div class="image-modal" id="imageModal">
                    <div class="modal-backdrop"></div>
                    <div class="modal-content">
                        <button class="modal-close" aria-label="Close">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <img src="${imageSrc}" alt="${imageAlt}" class="modal-image">
                        <div class="modal-caption">${imageAlt}</div>
                    </div>
                </div>
            `;
            
            $('body').append(modal);
            $('#imageModal').fadeIn(300);
            $('body').addClass('modal-open');
        });
        
        // Close modal
        $(document).on('click', '.modal-close, .modal-backdrop', function() {
            $('#imageModal').fadeOut(300, function() {
                $(this).remove();
                $('body').removeClass('modal-open');
            });
        });
        
        // Close modal with ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#imageModal').length) {
                $('#imageModal').fadeOut(300, function() {
                    $(this).remove();
                    $('body').removeClass('modal-open');
                });
            }
        });
    }
}

// Share functionality
function initializeShareButtons() {
    $('.action-btn.share').on('click', function(e) {
        e.preventDefault();
        
        const url = window.location.href;
        const title = document.title;
        
        // Check if Web Share API is supported
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(err => {
                console.log('Error sharing:', err);
                fallbackShare(url, title);
            });
        } else {
            fallbackShare(url, title);
        }
    });
}

// Fallback share functionality
function fallbackShare(url, title) {
    const shareOptions = [
        {
            name: 'Copy Link',
            action: () => {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Link copied to clipboard!', 'success');
                }).catch(() => {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = url;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showNotification('Link copied to clipboard!', 'success');
                });
            }
        },
        {
            name: 'Share on Twitter',
            action: () => {
                const twitterUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
                window.open(twitterUrl, '_blank', 'width=600,height=400');
            }
        },
        {
            name: 'Share on Facebook',
            action: () => {
                const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                window.open(facebookUrl, '_blank', 'width=600,height=400');
            }
        },
        {
            name: 'Share on LinkedIn',
            action: () => {
                const linkedinUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                window.open(linkedinUrl, '_blank', 'width=600,height=400');
            }
        }
    ];
    
    // Create share modal
    const shareModal = `
        <div class="share-modal" id="shareModal">
            <div class="modal-backdrop"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Share this project</h3>
                    <button class="modal-close" aria-label="Close">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="share-options">
                        ${shareOptions.map(option => `
                            <button class="share-option" data-action="${option.name}">
                                ${option.name}
                            </button>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(shareModal);
    $('#shareModal').fadeIn(300);
    $('body').addClass('modal-open');
    
    // Handle share option clicks
    $('.share-option').on('click', function() {
        const actionName = $(this).data('action');
        const option = shareOptions.find(opt => opt.name === actionName);
        if (option) {
            option.action();
            $('#shareModal').fadeOut(300, function() {
                $(this).remove();
                $('body').removeClass('modal-open');
            });
        }
    });
}

// Scroll effects
function initializeScrollEffects() {
    const $window = $(window);
    const $heroSection = $('.project-hero');
    
    if ($heroSection.length) {
        $window.on('scroll', function() {
            const scrollTop = $window.scrollTop();
            const heroHeight = $heroSection.outerHeight();
            
            // Parallax effect for hero background
            if (scrollTop < heroHeight) {
                const parallaxSpeed = 0.5;
                const yPos = scrollTop * parallaxSpeed;
                $('.hero-bg-image').css('transform', `translateY(${yPos}px)`);
            }
        });
    }
    
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                $(entry.target).addClass('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    $('.project-section, .feature-card, .sidebar-card').each(function() {
        observer.observe(this);
    });
}

// Lazy loading for images
function initializeLazyLoading() {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.dataset.src;
                if (src) {
                    img.src = src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Initialize tooltips
function initializeTooltips() {
    $('[data-tooltip]').each(function() {
        const $element = $(this);
        const tooltipText = $element.data('tooltip');
        
        $element.on('mouseenter', function() {
            const tooltip = $(`<div class="tooltip">${tooltipText}</div>`);
            $('body').append(tooltip);
            
            const elementRect = this.getBoundingClientRect();
            const tooltipRect = tooltip[0].getBoundingClientRect();
            
            const left = elementRect.left + (elementRect.width / 2) - (tooltipRect.width / 2);
            const top = elementRect.top - tooltipRect.height - 8;
            
            tooltip.css({
                left: Math.max(8, Math.min(left, window.innerWidth - tooltipRect.width - 8)),
                top: top
            }).fadeIn(200);
        });
        
        $element.on('mouseleave', function() {
            $('.tooltip').fadeOut(200, function() {
                $(this).remove();
            });
        });
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="notification notification-${type}">
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close" aria-label="Close">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    // Show notification
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 3000);
    
    // Manual close
    notification.find('.notification-close').on('click', () => {
        hideNotification(notification);
    });
}

function hideNotification(notification) {
    notification.removeClass('show');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// External link handling
$(document).on('click', 'a[href^="http"]', function(e) {
    const href = $(this).attr('href');
    if (href && !href.includes(window.location.hostname)) {
        $(this).attr('target', '_blank');
        $(this).attr('rel', 'noopener noreferrer');
    }
});

// Performance optimization: Debounce scroll events
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

// Apply debouncing to scroll events
const debouncedScrollHandler = debounce(function() {
    // Scroll-based animations or effects can be added here
}, 16); // ~60fps

$(window).on('scroll', debouncedScrollHandler);