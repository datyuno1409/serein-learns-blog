<?php
// This file is included as content in the frontend layout
?>
<!-- Background Effects -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50"></div>
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-to-r from-blue-200/30 to-purple-200/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gradient-to-r from-purple-200/30 to-pink-200/30 rounded-full blur-3xl"></div>
</div>

<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative py-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
        </div>
        
        <div class="container mx-auto px-4 relative">
            <!-- Welcome Badge -->
            <div class="flex justify-center mb-6">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-purple-100 border border-blue-200/50 backdrop-blur-sm">
                    <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-sm font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Khám Phá Dự Án</span>
                </div>
            </div>
            
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        <?= __('projects.title') ?? 'Dự Án Của Tôi' ?>
                    </span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    <?= __('projects.subtitle') ?? 'Bộ sưu tập các dự án cá nhân và đóng góp cho phần mềm mã nguồn mở' ?>
                </p>
            </div>
            
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <div class="flex justify-center">
                    <a href="/projects/create" class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-plus mr-3 relative z-10"></i>
                        <span class="relative z-10"><?= __('projects.create_project') ?? 'Tạo Dự Án Mới' ?></span>
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-75 transition duration-300"></div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="projects-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <?php 
                            $technologies = json_decode($project['technologies'], true) ?? [];
                            $techColors = [
                                'React' => 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border-blue-200',
                                'Node.js' => 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border-green-200', 
                                'Docker' => 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border-purple-200',
                                'Security' => 'bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 border-orange-200',
                                'DevSecOps' => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-200',
                                'Python' => 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-200',
                                'Machine Learning' => 'bg-gradient-to-r from-indigo-100 to-indigo-200 text-indigo-800 border-indigo-200',
                                'Network Security' => 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border-red-200',
                                'PHP' => 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border-purple-200',
                                'JavaScript' => 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border-yellow-200',
                                'MySQL' => 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border-blue-200',
                                'Tailwind CSS' => 'bg-gradient-to-r from-teal-100 to-teal-200 text-teal-800 border-teal-200'
                            ];
                            
                            $iconMap = [
                                'UniSAST Platform' => 'fas fa-shield-alt',
                                'Network Security Monitor' => 'fas fa-network-wired',
                                'Learning Blog' => 'fas fa-blog'
                            ];
                            
                            $gradientMap = [
                                'UniSAST Platform' => 'from-blue-500 to-purple-600',
                                'Network Security Monitor' => 'from-red-500 to-pink-600',
                                'Learning Blog' => 'from-green-500 to-teal-600'
                            ];
                        ?>
                        <div class="group relative bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-200/50 hover:border-gray-300/50 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                            <!-- Glow Effect -->
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600/20 to-purple-600/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>
                            
                            <!-- Background Pattern -->
                            <div class="absolute inset-0 opacity-5 rounded-2xl overflow-hidden">
                                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.15) 1px, transparent 0); background-size: 15px 15px;"></div>
                            </div>
                            
                            <div class="relative p-8">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="w-16 h-16 bg-gradient-to-br <?= $gradientMap[$project['title']] ?? 'from-gray-500 to-gray-600' ?> rounded-2xl flex items-center justify-center shadow-lg">
                                                <i class="<?= $iconMap[$project['title']] ?? 'fas fa-code' ?> text-white text-2xl"></i>
                                            </div>
                                            <div class="absolute -inset-1 bg-gradient-to-br <?= $gradientMap[$project['title']] ?? 'from-gray-500 to-gray-600' ?> rounded-2xl blur opacity-25"></div>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($project['title']) ?></h3>
                                            <?php if ($project['is_featured']): ?>
                                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-amber-100 to-orange-100 border border-amber-200">
                                                    <i class="fas fa-star text-amber-500 text-xs mr-1"></i>
                                                    <span class="text-amber-800 text-xs font-semibold">Nổi Bật</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                        <div class="relative">
                                            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors dropdown-toggle" data-project-id="<?= $project['id'] ?>">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl z-10 border border-gray-200/50 backdrop-blur-sm">
                                                <a href="/projects/edit/<?= $project['id'] ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-t-xl transition-colors">
                                                    <i class="fas fa-edit mr-3 text-blue-500"></i>Chỉnh Sửa
                                                </a>
                                                <button onclick="deleteProject(<?= $project['id'] ?>)" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl transition-colors">
                                                    <i class="fas fa-trash mr-3"></i>Xóa
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            
                                <p class="text-gray-600 text-base mb-6 line-clamp-3 leading-relaxed">
                                    <?= htmlspecialchars($project['description']) ?>
                                </p>
                                
                                <div class="flex flex-wrap gap-2 mb-6">
                                    <?php foreach ($technologies as $tech): ?>
                                        <span class="<?= $techColors[$tech] ?? 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-gray-200' ?> text-xs px-3 py-1.5 rounded-full font-medium border transition-all duration-300 hover:scale-105"><?= htmlspecialchars($tech) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div class="flex items-center space-x-4">
                                        <?php if ($project['github_url']): ?>
                                            <a href="<?= htmlspecialchars($project['github_url']) ?>" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                                <i class="fab fa-github mr-2"></i> GitHub
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project['live_url']): ?>
                                            <a href="<?= htmlspecialchars($project['live_url']) ?>" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors">
                                                <i class="fas fa-external-link-alt mr-2"></i> Demo
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/project/<?= $project['id'] ?>" class="group inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                        <span>Xem Chi Tiết</span>
                                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full">
                        <div class="text-center py-20">
                            <div class="relative inline-block mb-8">
                                <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-folder-open text-gray-400 text-5xl"></i>
                                </div>
                                <div class="absolute -inset-2 bg-gradient-to-br from-gray-200/50 to-gray-300/50 rounded-full blur-xl opacity-50"></div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-700 mb-4">Chưa Có Dự Án Nào</h3>
                            <p class="text-gray-500 text-lg mb-8 max-w-md mx-auto">Hãy bắt đầu tạo dự án đầu tiên của bạn và chia sẻ với cộng đồng!</p>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <a href="/projects/create" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tạo Dự Án Đầu Tiên
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.dropdown-menu {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>

<script>
// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            menu.classList.toggle('hidden');
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });
});

// Delete project function
function deleteProject(projectId) {
    if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/projects/delete/' + projectId;
        
        // Add CSRF token if needed
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>