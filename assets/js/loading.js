// Loading animation and page transitions
document.addEventListener('DOMContentLoaded', function() {
    // Show loading spinner
    const loadingSpinner = document.querySelector('.loading-spinner');
    if (loadingSpinner) {
        setTimeout(() => {
            loadingSpinner.style.display = 'none';
        }, 1000);
    }

    // Page transition effects
    const pageContent = document.querySelector('main');
    if (pageContent) {
        pageContent.style.opacity = '0';
        pageContent.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            pageContent.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            pageContent.style.opacity = '1';
            pageContent.style.transform = 'translateY(0)';
        }, 100);
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});