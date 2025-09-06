<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/myprojects" class="inline-flex items-center text-serein-600 hover:text-serein-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Projects
            </a>
        </div>

        <!-- Project Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <?php if (!empty($project['image_url'])): ?>
                <div class="aspect-video bg-gray-100">
                    <img src="<?= htmlspecialchars($project['image_url']) ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?>"
                         class="w-full h-full object-cover">
                </div>
            <?php endif; ?>
            
            <div class="p-8">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            <?= htmlspecialchars($project['title']) ?>
                            <?php if ($project['is_featured']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-3">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            <?php endif; ?>
                        </h1>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?= $project['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($project['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                            </span>
                            <span class="mx-2">•</span>
                            <span><?= date('M d, Y', strtotime($project['created_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if (!empty($project['description'])): ?>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Description</h2>
                        <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Technologies -->
                <?php if (!empty($project['technologies'])): ?>
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">Technologies Used</h2>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                            $technologies = is_string($project['technologies']) ? json_decode($project['technologies'], true) : $project['technologies'];
                            $techColors = [
                                'PHP' => 'bg-purple-100 text-purple-800',
                                'JavaScript' => 'bg-yellow-100 text-yellow-800',
                                'Python' => 'bg-blue-100 text-blue-800',
                                'React' => 'bg-cyan-100 text-cyan-800',
                                'Vue' => 'bg-green-100 text-green-800',
                                'Laravel' => 'bg-red-100 text-red-800',
                                'Node.js' => 'bg-green-100 text-green-800',
                                'MySQL' => 'bg-orange-100 text-orange-800',
                                'PostgreSQL' => 'bg-blue-100 text-blue-800',
                                'MongoDB' => 'bg-green-100 text-green-800'
                            ];
                            
                            if (is_array($technologies)):
                                foreach ($technologies as $tech): 
                                    $colorClass = $techColors[$tech] ?? 'bg-gray-100 text-gray-800';
                            ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $colorClass ?>">
                                    <?= htmlspecialchars($tech) ?>
                                </span>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Links -->
                <div class="flex flex-wrap gap-4">
                    <?php if (!empty($project['github_url'])): ?>
                        <a href="<?= htmlspecialchars($project['github_url']) ?>" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                            <i class="fab fa-github mr-2"></i>
                            View on GitHub
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['live_url'])): ?>
                        <a href="<?= htmlspecialchars($project['live_url']) ?>" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-serein-600 text-white rounded-lg hover:bg-serein-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Live Demo
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Project Content -->
        <?php if (!empty($project['content'])): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Project Details</h2>
                <div class="prose prose-lg max-w-none">
                    <?= nl2br(htmlspecialchars($project['content'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>