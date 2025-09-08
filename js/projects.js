class ProjectManager {
    constructor() {
        this.projects = [];
        this.filteredProjects = [];
        this.currentFilter = 'all';
        this.currentSort = 'newest';
        this.searchQuery = '';
        this.projectsPerPage = 6;
        this.currentPage = 1;
        
        this.init();
    }

    init() {
        this.loadProjects();
        this.bindEvents();
        this.setupAnimations();
    }

    loadProjects() {
        const projectCards = document.querySelectorAll('.project-card');
        this.projects = Array.from(projectCards).map((card, index) => {
            const category = card.dataset.category;
            const title = card.querySelector('h3 a').textContent.trim();
            const description = card.querySelector('p').textContent.trim();
            const rating = parseFloat(card.querySelector('.fa-star').nextSibling.textContent.trim());
            const downloads = this.parseNumber(card.querySelector('.fa-download').nextSibling.textContent.trim());
            const views = this.parseNumber(card.querySelector('.fa-eye').nextSibling.textContent.trim());
            const status = card.querySelector('.absolute.top-4.right-4 span').textContent.trim();
            const techTags = Array.from(card.querySelectorAll('.tech-tag')).map(tag => tag.textContent.trim());
            
            return {
                element: card,
                index,
                category,
                title,
                description,
                rating,
                downloads,
                views,
                status,
                techTags,
                createdAt: new Date(2024, Math.floor(Math.random() * 12), Math.floor(Math.random() * 28))
            };
        });
        
        this.filteredProjects = [...this.projects];
        this.updateProjectsDisplay();
    }

    parseNumber(str) {
        const num = str.replace(/[^0-9.]/g, '');
        if (str.includes('k')) return parseFloat(num) * 1000;
        return parseFloat(num) || 0;
    }

    bindEvents() {
        // Filter buttons
        const filterButtons = document.querySelectorAll('[data-filter]');
        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleFilter(button.dataset.filter);
                this.updateActiveFilter(button);
            });
        });

        // Sort dropdown
        const sortSelect = document.querySelector('[data-sort]');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.handleSort(e.target.value);
            });
        }

        // Search input
        const searchInput = document.querySelector('#project-search');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.handleSearch(e.target.value);
                }, 300);
            });
        }

        // Load more button
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', () => {
                this.loadMoreProjects();
            });
        }

        // Project detail modals
        const detailButtons = document.querySelectorAll('.project-detail-btn');
        detailButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const projectIndex = parseInt(button.dataset.project);
                this.showProjectModal(projectIndex);
            });
        });
    }

    handleFilter(filter) {
        this.currentFilter = filter;
        this.currentPage = 1;
        this.applyFilters();
    }

    handleSort(sortType) {
        this.currentSort = sortType;
        this.applyFilters();
    }

    handleSearch(query) {
        this.searchQuery = query.toLowerCase();
        this.currentPage = 1;
        this.applyFilters();
    }

    applyFilters() {
        let filtered = [...this.projects];

        // Apply category filter
        if (this.currentFilter !== 'all') {
            filtered = filtered.filter(project => project.category === this.currentFilter);
        }

        // Apply search filter
        if (this.searchQuery) {
            filtered = filtered.filter(project => 
                project.title.toLowerCase().includes(this.searchQuery) ||
                project.description.toLowerCase().includes(this.searchQuery) ||
                project.techTags.some(tag => tag.toLowerCase().includes(this.searchQuery))
            );
        }

        // Apply sorting
        filtered.sort((a, b) => {
            switch (this.currentSort) {
                case 'newest':
                    return b.createdAt - a.createdAt;
                case 'oldest':
                    return a.createdAt - b.createdAt;
                case 'rating':
                    return b.rating - a.rating;
                case 'downloads':
                    return b.downloads - a.downloads;
                case 'views':
                    return b.views - a.views;
                case 'name':
                    return a.title.localeCompare(b.title);
                default:
                    return 0;
            }
        });

        this.filteredProjects = filtered;
        this.updateProjectsDisplay();
        this.updateResultsCount();
    }

    updateProjectsDisplay() {
        const projectsToShow = this.filteredProjects.slice(0, this.currentPage * this.projectsPerPage);
        
        // Hide all projects first
        this.projects.forEach(project => {
            project.element.style.display = 'none';
            project.element.classList.remove('animate-fadeInUp');
        });

        // Show filtered projects with animation
        projectsToShow.forEach((project, index) => {
            project.element.style.display = 'block';
            setTimeout(() => {
                project.element.classList.add('animate-fadeInUp');
            }, index * 100);
        });

        // Update load more button
        this.updateLoadMoreButton();
    }

    updateLoadMoreButton() {
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            const totalShown = this.currentPage * this.projectsPerPage;
            const hasMore = totalShown < this.filteredProjects.length;
            
            loadMoreBtn.style.display = hasMore ? 'inline-flex' : 'none';
            
            if (hasMore) {
                const remaining = this.filteredProjects.length - totalShown;
                loadMoreBtn.innerHTML = `<i class="fas fa-plus mr-2"></i>Tải thêm ${Math.min(remaining, this.projectsPerPage)} dự án`;
            }
        }
    }

    loadMoreProjects() {
        this.currentPage++;
        this.updateProjectsDisplay();
    }

    updateActiveFilter(activeButton) {
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.classList.remove('bg-serein-600', 'text-white');
            btn.classList.add('text-gray-600', 'hover:text-serein-600');
        });
        
        activeButton.classList.remove('text-gray-600', 'hover:text-serein-600');
        activeButton.classList.add('bg-serein-600', 'text-white');
    }

    updateResultsCount() {
        const countElement = document.querySelector('#results-count');
        if (countElement) {
            const count = this.filteredProjects.length;
            const total = this.projects.length;
            countElement.textContent = `Hiển thị ${count} trong tổng số ${total} dự án`;
        }
    }

    setupAnimations() {
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe project cards
        document.querySelectorAll('.project-card').forEach(card => {
            observer.observe(card);
        });

        // Add hover effects
        document.querySelectorAll('.project-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('hover-lift');
            });
            
            card.addEventListener('mouseleave', () => {
                card.classList.remove('hover-lift');
            });
        });
        
        // Add CSS for animations
        this.addCustomStyles();
    }
    
    addCustomStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .project-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .hover-lift:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            }
            
            .project-card img {
                transition: transform 0.3s ease;
            }
            
            .project-card:hover img {
                transform: scale(1.05);
            }
            
            .tech-tag {
                transition: all 0.2s ease;
            }
            
            .tech-tag:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .project-detail-btn {
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .project-detail-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            }
            
            .filter-btn {
                transition: all 0.3s ease;
            }
            
            .filter-btn.active {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }
            
            .fade-in {
                animation: fadeIn 0.6s ease-out;
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .slide-up {
                animation: slideUp 0.4s ease-out;
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            #project-modal {
                backdrop-filter: blur(4px);
            }
            
            #project-modal .bg-white {
                animation: modalSlideIn 0.3s ease-out;
            }
            
            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: scale(0.9) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }

    showProjectModal(projectIndex) {
        const project = this.projects[projectIndex];
        if (!project) return;

        // Create modal HTML
        const modalHTML = `
            <div id="project-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="relative">
                        <img src="${project.element.querySelector('img').src}" alt="${project.title}" class="w-full h-64 object-cover rounded-t-xl">
                        <button class="absolute top-4 right-4 bg-white rounded-full w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-800 transition-colors" onclick="this.closest('#project-modal').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-3xl font-bold text-gray-800">${project.title}</h2>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${
                                project.status === 'Hoàn thành' ? 'bg-green-100 text-green-800' :
                                project.status === 'Đang phát triển' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-blue-100 text-blue-800'
                            }">
                                ${project.status}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-6 text-lg leading-relaxed">${project.description}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-star text-yellow-500 text-2xl mb-2"></i>
                                <div class="text-2xl font-bold text-gray-800">${project.rating}</div>
                                <div class="text-sm text-gray-600">Đánh giá</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-download text-blue-500 text-2xl mb-2"></i>
                                <div class="text-2xl font-bold text-gray-800">${project.downloads}</div>
                                <div class="text-sm text-gray-600">Tải xuống</div>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-eye text-green-500 text-2xl mb-2"></i>
                                <div class="text-2xl font-bold text-gray-800">${project.views}</div>
                                <div class="text-sm text-gray-600">Lượt xem</div>
                            </div>
                        </div>
                        
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Công nghệ sử dụng</h3>
                            <div class="flex flex-wrap gap-2">
                                ${project.techTags.map(tag => `<span class="tech-tag bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">${tag}</span>`).join('')}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <a href="#" class="flex items-center gap-2 text-gray-600 hover:text-serein-600 transition-colors">
                                    <i class="fab fa-github text-xl"></i>
                                    <span>Xem mã nguồn</span>
                                </a>
                                <a href="#" class="flex items-center gap-2 text-gray-600 hover:text-serein-600 transition-colors">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Demo trực tiếp</span>
                                </a>
                            </div>
                            <button class="bg-serein-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-serein-700 transition-colors">
                                <i class="fas fa-heart mr-2"></i>Yêu thích
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Close modal on backdrop click
        const modal = document.getElementById('project-modal');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                document.body.style.overflow = 'auto';
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ProjectManager();
});

// Newsletter form handler
document.addEventListener('DOMContentLoaded', () => {
    const newsletterForm = document.querySelector('form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = newsletterForm.querySelector('input[type="email"]').value;
            
            if (email) {
                // Show success notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                notification.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>Đăng ký thành công! Cảm ơn bạn đã quan tâm.</span>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
                
                // Reset form
                newsletterForm.reset();
            }
        });
    }
});