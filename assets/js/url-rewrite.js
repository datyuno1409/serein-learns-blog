// URL Rewrite Handler - Loại bỏ phần mở rộng .html
(function() {
    'use strict';
    
    // Mapping các URL cũ sang URL mới
    const urlMappings = {
        'index.html': '/',
        'articles.html': '/articles',
        'projects.html': '/projects', 
        'about.html': '/about',
        'contact.html': '/contact',
        'article-detail.html': '/article-detail',
        'login.html': '/login',
        'register.html': '/register'
    };
    
    // Cập nhật tất cả các liên kết khi trang load
    function updateLinks() {
        const links = document.querySelectorAll('a[href]');
        
        links.forEach(link => {
            const href = link.getAttribute('href');
            
            // Chỉ xử lý các liên kết nội bộ (không có http/https)
            if (href && !href.startsWith('http') && !href.startsWith('//') && !href.startsWith('#')) {
                // Loại bỏ query string và fragment để xử lý
                const [path, queryAndFragment] = href.split(/[?#]/, 2);
                const restOfUrl = queryAndFragment ? (href.includes('?') ? '?' : '#') + queryAndFragment : '';
                
                // Kiểm tra nếu có mapping
                if (urlMappings[path]) {
                    link.setAttribute('href', urlMappings[path] + restOfUrl);
                }
                // Nếu là file .html khác, loại bỏ phần mở rộng
                else if (path.endsWith('.html')) {
                    const newPath = path.replace(/\.html$/, '');
                    link.setAttribute('href', newPath + restOfUrl);
                }
            }
        });
    }
    
    // Xử lý navigation với History API
    function handleNavigation(event) {
        const link = event.target.closest('a');
        if (!link) return;
        
        const href = link.getAttribute('href');
        
        // Chỉ xử lý các liên kết nội bộ
        if (href && !href.startsWith('http') && !href.startsWith('//') && !href.startsWith('#')) {
            event.preventDefault();
            
            // Cập nhật URL trong thanh địa chỉ
            history.pushState(null, '', href);
            
            // Load nội dung trang mới (có thể implement AJAX loading sau)
            loadPage(href);
        }
    }
    
    // Load trang mới (hiện tại chỉ redirect)
    function loadPage(url) {
        // Chuyển đổi URL rewrite về file thực tế
        let actualFile = url;
        
        // Xử lý root path
        if (url === '/' || url === '') {
            actualFile = '/index.html';
        }
        // Thêm .html nếu không có phần mở rộng
        else if (!url.includes('.') && !url.endsWith('/')) {
            actualFile = url + '.html';
        }
        
        // Redirect đến file thực tế
        window.location.href = actualFile;
    }
    
    // Xử lý back/forward button
    window.addEventListener('popstate', function(event) {
        loadPage(window.location.pathname);
    });
    
    // Khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            updateLinks();
            document.addEventListener('click', handleNavigation);
        });
    } else {
        updateLinks();
        document.addEventListener('click', handleNavigation);
    }
    
    // Cập nhật lại links khi có thay đổi DOM
    const observer = new MutationObserver(function(mutations) {
        let shouldUpdate = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.tagName === 'A' || node.querySelector('a'))) {
                        shouldUpdate = true;
                    }
                });
            }
        });
        
        if (shouldUpdate) {
            updateLinks();
        }
    });
    
    // Only observe if document.body exists
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    } else {
        // If body doesn't exist yet, wait for it
        document.addEventListener('DOMContentLoaded', function() {
            if (document.body) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        });
    }
    
})();