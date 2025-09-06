<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/myprojects" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($project['title']) ?></h1>
                        <p class="text-gray-600"><?= htmlspecialchars($project['description']) ?></p>
                    </div>
                </div>
                <?php if ($project['is_featured']): ?>
                    <span class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-star mr-1"></i> Featured
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Project Image -->
                <?php if ($project['image_url']): ?>
                    <div class="bg-white rounded-xl shadow-sm border mb-8">
                        <img src="<?= htmlspecialchars($project['image_url']) ?>" 
                             alt="<?= htmlspecialchars($project['title']) ?>" 
                             class="w-full h-64 object-cover rounded-t-xl">
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2 text-serein-500"></i>
                        Mô tả dự án
                    </h2>
                    <div class="prose max-w-none text-gray-700">
                        <?= nl2br(htmlspecialchars($project['content'])) ?>
                    </div>
                </div>

                <!-- Technologies -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-code mr-2 text-serein-500"></i>
                        Công nghệ sử dụng
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        <?php 
                            $technologies = json_decode($project['technologies'], true) ?? [];
                            $techColors = [
                                'React' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Node.js' => 'bg-green-100 text-green-800 border-green-200', 
                                'Docker' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'Security' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'DevSecOps' => 'bg-gray-100 text-gray-800 border-gray-200',
                                'Python' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'Machine Learning' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'Network Security' => 'bg-red-100 text-red-800 border-red-200',
                                'PHP' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'JavaScript' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'MySQL' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'Tailwind CSS' => 'bg-teal-100 text-teal-800 border-teal-200'
                            ];
                        ?>
                        <?php foreach ($technologies as $tech): ?>
                            <span class="<?= $techColors[$tech] ?? 'bg-gray-100 text-gray-800 border-gray-200' ?> px-3 py-2 rounded-lg text-sm font-medium border">
                                <?= htmlspecialchars($tech) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Project Info -->
                <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info mr-2 text-serein-500"></i>
                        Thông tin dự án
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Trạng thái</label>
                            <div class="mt-1">
                                <?php 
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'development' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Đang hoạt động',
                                        'inactive' => 'Không hoạt động', 
                                        'development' => 'Đang phát triển'
                                    ];
                                ?>
                                <span class="<?= $statusColors[$project['status']] ?? 'bg-gray-100 text-gray-800' ?> px-2 py-1 rounded-full text-sm font-medium">
                                    <?= $statusLabels[$project['status']] ?? ucfirst($project['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Ngày tạo</label>
                            <p class="mt-1 text-gray-900"><?= date('d/m/Y', strtotime($project['created_at'])) ?></p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cập nhật lần cuối</label>
                            <p class="mt-1 text-gray-900"><?= date('d/m/Y', strtotime($project['updated_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Links -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-link mr-2 text-serein-500"></i>
                        Liên kết
                    </h3>
                    <div class="space-y-3">
                        <?php if ($project['github_url']): ?>
                            <a href="<?= htmlspecialchars($project['github_url']) ?>" 
                               target="_blank" 
                               class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fab fa-github text-gray-700 text-lg mr-3"></i>
                                    <span class="text-gray-900 font-medium">Source Code</span>
                                </div>
                                <i class="fas fa-external-link-alt text-gray-400"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($project['live_url']): ?>
                            <a href="<?= htmlspecialchars($project['live_url']) ?>" 
                               target="_blank" 
                               class="flex items-center justify-between p-3 bg-serein-50 hover:bg-serein-100 rounded-lg transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-external-link-alt text-serein-600 text-lg mr-3"></i>
                                    <span class="text-serein-900 font-medium">Live Demo</span>
                                </div>
                                <i class="fas fa-external-link-alt text-serein-400"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!$project['github_url'] && !$project['live_url']): ?>
                            <p class="text-gray-500 text-sm italic">Chưa có liên kết nào</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prose {
    line-height: 1.7;
}
.prose p {
    margin-bottom: 1rem;
}
</style>