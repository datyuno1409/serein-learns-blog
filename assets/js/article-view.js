// Article View JavaScript

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeArticleView();
});

function initializeArticleView() {
    generateTableOfContents();
    initializeShareButtons();
    initializeCommentForm();
    initializeScrollEffects();
    initializeLazyLoading();
}

// Table of Contents Generation
function generateTableOfContents() {
    const tocNav = document.getElementById('article-toc');
    if (!tocNav) return;
    
    const contentBody = document.querySelector('.content-body');
    if (!contentBody) return;
    
    const headings = contentBody.querySelectorAll('h1, h2, h3, h4, h5, h6');
    if (headings.length === 0) {
        tocNav.innerHTML = '<p class="text-muted">Không có mục lục</p>';
        return;
    }
    
    let tocHTML = '<ul>';
    let currentLevel = 0;
    
    headings.forEach((heading, index) => {
        const level = parseInt(heading.tagName.charAt(1));
        const id = `heading-${index}`;
        const text = heading.textContent.trim();
        
        // Add ID to heading for anchor links
        heading.id = id;
        
        // Adjust nesting level
        if (level > currentLevel) {
            for (let i = currentLevel; i < level - 1; i++) {
                tocHTML += '<ul>';
            }
        } else if (level < currentLevel) {
            for (let i = level; i < currentLevel; i++) {
                tocHTML += '</ul>';
            }
        }
        
        tocHTML += `<li><a href="#${id}" class="toc-link" data-target="${id}">${text}</a></li>`;
        currentLevel = level;
    });
    
    // Close remaining open lists
    for (let i = 1; i < currentLevel; i++) {
        tocHTML += '</ul>';
    }
    tocHTML += '</ul>';
    
    tocNav.innerHTML = tocHTML;
    
    // Add click handlers for smooth scrolling
    const tocLinks = tocNav.querySelectorAll('.toc-link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Update active state
                tocLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
    
    // Update active TOC item on scroll
    updateTOCOnScroll(tocLinks, headings);
}

// Update TOC active state on scroll
function updateTOCOnScroll(tocLinks, headings) {
    let ticking = false;
    
    function updateTOC() {
        const scrollPosition = window.scrollY + 100;
        let activeHeading = null;
        
        headings.forEach(heading => {
            if (heading.offsetTop <= scrollPosition) {
                activeHeading = heading;
            }
        });
        
        tocLinks.forEach(link => {
            link.classList.remove('active');
            if (activeHeading && link.getAttribute('data-target') === activeHeading.id) {
                link.classList.add('active');
            }
        });
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateTOC);
            ticking = true;
        }
    });
}

// Share Buttons Functionality
function initializeShareButtons() {
    const articleTitle = document.querySelector('.hero-title')?.textContent || document.title;
    const articleUrl = window.location.href;
    const articleDescription = document.querySelector('meta[name="description"]')?.content || '';
    
    // Store share data globally
    window.shareData = {
        title: articleTitle,
        url: articleUrl,
        description: articleDescription
    };
}

function shareToFacebook() {
    const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.shareData.url)}`;
    openShareWindow(url, 'Facebook');
}

function shareToTwitter() {
    const text = `${window.shareData.title} ${window.shareData.url}`;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
    openShareWindow(url, 'Twitter');
}

function shareToLinkedIn() {
    const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.shareData.url)}`;
    openShareWindow(url, 'LinkedIn');
}

function copyArticleLink() {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(window.shareData.url).then(() => {
            showNotification('Đã sao chép link bài viết!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(window.shareData.url);
        });
    } else {
        fallbackCopyToClipboard(window.shareData.url);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Đã sao chép link bài viết!', 'success');
    } catch (err) {
        showNotification('Không thể sao chép link. Vui lòng thử lại!', 'error');
    }
    
    document.body.removeChild(textArea);
}

function openShareWindow(url, platform) {
    const width = 600;
    const height = 400;
    const left = (window.innerWidth - width) / 2;
    const top = (window.innerHeight - height) / 2;
    
    window.open(
        url,
        `share-${platform}`,
        `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
    );
}

// Comment Form Enhancement
function initializeCommentForm() {
    const commentForm = document.getElementById('comment-form');
    if (!commentForm) return;
    
    const textarea = commentForm.querySelector('textarea[name="content"]');
    const submitBtn = commentForm.querySelector('.submit-btn');
    const charCount = document.createElement('div');
    
    // Add character counter
    if (textarea) {
        charCount.className = 'char-counter';
        charCount.style.cssText = 'text-align: right; font-size: 0.85rem; color: #7f8c8d; margin-top: 0.5rem;';
        textarea.parentNode.appendChild(charCount);
        
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 1000;
            charCount.textContent = `${length}/${maxLength} ký tự`;
            
            if (length > maxLength * 0.9) {
                charCount.style.color = '#e74c3c';
            } else {
                charCount.style.color = '#7f8c8d';
            }
        });
        
        // Trigger initial count
        textarea.dispatchEvent(new Event('input'));
    }
    
    // Form submission with loading state
    commentForm.addEventListener('submit', function(e) {
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="icon-sm animate-spin"><use href="/assets/images/icons.svg#loading"></use></svg> Đang gửi...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        }
    });
}

// Scroll Effects
function initializeScrollEffects() {
    let ticking = false;
    
    function updateScrollEffects() {
        const scrolled = window.scrollY;
        const rate = scrolled * -0.5;
        
        // Parallax effect for hero background
        const heroBackground = document.querySelector('.hero-background');
        if (heroBackground) {
            heroBackground.style.transform = `translateY(${rate}px)`;
        }
        
        // Fade in elements on scroll
        const fadeElements = document.querySelectorAll('.fade-in');
        fadeElements.forEach(element => {
            const elementTop = element.offsetTop;
            const elementHeight = element.offsetHeight;
            const windowHeight = window.innerHeight;
            
            if (scrolled + windowHeight > elementTop + elementHeight * 0.1) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
    
    // Initial call
    updateScrollEffects();
}

// Lazy Loading for Images
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => {
            img.classList.add('lazy');
            imageObserver.observe(img);
        });
    }
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Reading Progress Bar
function initializeReadingProgress() {
    const progressBar = document.createElement('div');
    progressBar.className = 'reading-progress';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2980b9);
        z-index: 9999;
        transition: width 0.1s ease;
    `;
    
    document.body.appendChild(progressBar);
    
    function updateProgress() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        
        progressBar.style.width = Math.min(scrollPercent, 100) + '%';
    }
    
    window.addEventListener('scroll', updateProgress);
    updateProgress();
}

// Initialize reading progress on load
document.addEventListener('DOMContentLoaded', function() {
    initializeReadingProgress();
});

// Follow Button Functionality
function initializeFollowButton() {
    const followBtn = document.querySelector('.follow-btn');
    if (!followBtn) return;
    
    followBtn.addEventListener('click', function() {
        const isFollowing = this.classList.contains('following');
        
        if (isFollowing) {
            this.innerHTML = '<svg class="icon-xs"><use href="/assets/images/icons.svg#plus"></use></svg> Theo dõi';
            this.classList.remove('following', 'btn-success');
            this.classList.add('btn-outline-primary');
            showNotification('Đã bỏ theo dõi tác giả', 'info');
        } else {
            this.innerHTML = '<svg class="icon-xs"><use href="/assets/images/icons.svg#check"></use></svg> Đang theo dõi';
            this.classList.remove('btn-outline-primary');
            this.classList.add('following', 'btn-success');
            showNotification('Đã theo dõi tác giả', 'success');
        }
    });
}

// Initialize follow button
document.addEventListener('DOMContentLoaded', function() {
    initializeFollowButton();
});

// Smooth scroll for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Print functionality
function printArticle() {
    window.print();
}

// Add print styles
const printStyles = `
    @media print {
        .article-sidebar,
        .comments-section,
        .hero-actions,
        .share-buttons,
        .follow-btn {
            display: none !important;
        }
        
        .article-content {
            box-shadow: none !important;
            border: none !important;
        }
        
        .hero-title {
            color: #000 !important;
            text-shadow: none !important;
        }
        
        .content-body {
            font-size: 12pt !important;
            line-height: 1.5 !important;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);

// Export functions for global use
window.shareToFacebook = shareToFacebook;
window.shareToTwitter = shareToTwitter;
window.shareToLinkedIn = shareToLinkedIn;
window.copyArticleLink = copyArticleLink;
window.printArticle = printArticle;