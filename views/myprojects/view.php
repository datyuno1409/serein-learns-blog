<?php
// Get project data from controller
$project = $this->getProjectById($_GET['id'] ?? $matches[1] ?? '1');
if (!$project) {
    header('HTTP/1.0 404 Not Found');
    require 'views/404.php';
    exit;
}
?>

<!-- Project Hero Section -->
<div class="project-hero">
    <div class="hero-background">
        <?php if (isset($project['image']) && !empty($project['image'])): ?>
            <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="hero-bg-image">
        <?php endif; ?>
        <div class="hero-overlay"></div>
    </div>
    
    <div class="container mx-auto px-4 py-16 relative z-10">
        <!-- Breadcrumb -->
        <nav class="breadcrumb mb-8">
            <a href="/" class="breadcrumb-item">
                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                    <use href="/assets/icons/icons.svg#home"></use>
                </svg>
                Trang chủ
            </a>
            <svg class="breadcrumb-separator" viewBox="0 0 24 24" fill="currentColor">
                <use href="/assets/icons/icons.svg#chevron-right"></use>
            </svg>
            <a href="/myprojects" class="breadcrumb-item">Dự án của tôi</a>
            <svg class="breadcrumb-separator" viewBox="0 0 24 24" fill="currentColor">
                <use href="/assets/icons/icons.svg#chevron-right"></use>
            </svg>
            <span class="breadcrumb-current"><?= htmlspecialchars($project['title']) ?></span>
        </nav>
        
        <!-- Hero Content -->
        <div class="hero-content">
            <div class="hero-badges mb-4">
                <span class="project-status status-<?= strtolower($project['status'] ?? 'active') ?>">
                    <span class="status-dot"></span>
                    <?= htmlspecialchars($project['status'] ?? 'Active') ?>
                </span>
                <?php if (!empty($project['featured'])): ?>
                    <span class="project-badge featured">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                            <use href="/assets/icons/icons.svg#star"></use>
                        </svg>
                        Featured
                    </span>
                <?php endif; ?>
            </div>
            
            <h1 class="hero-title"><?= htmlspecialchars($project['title']) ?></h1>
            <p class="hero-description"><?= htmlspecialchars($project['description']) ?></p>
            
            <!-- Action Buttons -->
            <div class="hero-actions">
                <?php if (!empty($project['github'])): ?>
                    <a href="<?= htmlspecialchars($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                            <use href="/assets/icons/icons.svg#github"></use>
                        </svg>
                        View on GitHub
                    </a>
                <?php endif; ?>
                <?php if (!empty($project['demo'])): ?>
                    <a href="<?= htmlspecialchars($project['demo']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                            <use href="/assets/icons/icons.svg#external-link"></use>
                        </svg>
                        Live Demo
                    </a>
                <?php endif; ?>
                <button class="btn btn-outline" onclick="shareProject()">
                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                        <use href="/assets/icons/icons.svg#share"></use>
                    </svg>
                    Share
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="project-content">
    <div class="container mx-auto px-4 py-12">
        <div class="project-layout">
            <!-- Main Content -->
            <div class="project-main">
                <!-- Project Gallery -->
                <div class="project-section">
                    <div class="section-header">
                        <h2 class="section-title">Project Gallery</h2>
                        <p class="section-subtitle">Screenshots and visual overview</p>
                    </div>
                    
                    <div class="project-gallery">
                        <div class="gallery-main">
                            <?php if (isset($project['image']) && !empty($project['image'])): ?>
                                <img src="<?= htmlspecialchars($project['image']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="gallery-image active" onclick="openLightbox(this.src)">
                            <?php else: ?>
                                <div class="gallery-placeholder">
                                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="currentColor">
                                        <use href="/assets/icons/icons.svg#image"></use>
                                    </svg>
                                    <p>No screenshots available</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Project Overview -->
                <div class="project-section">
                    <div class="section-header">
                        <h2 class="section-title">Project Overview</h2>
                        <p class="section-subtitle">Detailed information about this project</p>
                    </div>
                    
                    <div class="project-overview">
                        <div class="overview-content">
                            <?= nl2br(htmlspecialchars($project['long_description'] ?? $project['description'])) ?>
                        </div>
                    </div>
                </div>
                
                <!-- Features & Highlights -->
                <div class="project-section">
                    <div class="section-header">
                        <h2 class="section-title">Key Features</h2>
                        <p class="section-subtitle">What makes this project special</p>
                    </div>
                    
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#code"></use>
                                </svg>
                            </div>
                            <h3 class="feature-title">Clean Code</h3>
                            <p class="feature-description">Well-structured and maintainable codebase following best practices</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#responsive"></use>
                                </svg>
                            </div>
                            <h3 class="feature-title">Responsive Design</h3>
                            <p class="feature-description">Optimized for all devices and screen sizes</p>
                        </div>
                        
                        <div class="feature-card">
                            <div class="feature-icon">
                                <svg class="icon-md" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#performance"></use>
                                </svg>
                            </div>
                            <h3 class="feature-title">High Performance</h3>
                            <p class="feature-description">Fast loading times and smooth user experience</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="project-sidebar">
                <!-- Project Stats -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h3 class="card-title">Project Stats</h3>
                    </div>
                    <div class="card-content">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#calendar"></use>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Created</span>
                                <span class="stat-value"><?= date('M d, Y', strtotime($project['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#activity"></use>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Status</span>
                                <span class="stat-value status-<?= strtolower($project['status'] ?? 'active') ?>"><?= htmlspecialchars($project['status'] ?? 'Active') ?></span>
                            </div>
                        </div>
                        
                        <div class="stat-item">
                            <div class="stat-icon">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#eye"></use>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Views</span>
                                <span class="stat-value"><?= number_format($project['views'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tech Stack -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h3 class="card-title">Tech Stack</h3>
                    </div>
                    <div class="card-content">
                        <div class="tech-stack">
                            <?php if (!empty($project['technologies'])): ?>
                                <?php foreach ($project['technologies'] as $category => $techs): ?>
                                    <?php foreach ($techs as $tech): ?>
                                        <div class="tech-item">
                                            <span class="tech-dot"></span>
                                            <span class="tech-name"><?= htmlspecialchars($tech) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-content">
                        <div class="action-buttons">
                            <?php if (!empty($project['github'])): ?>
                                <a href="<?= htmlspecialchars($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="action-btn github">
                                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                        <use href="/assets/icons/icons.svg#github"></use>
                                    </svg>
                                    View Source
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['demo'])): ?>
                                <a href="<?= htmlspecialchars($project['demo']) ?>" target="_blank" rel="noopener noreferrer" class="action-btn demo">
                                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                        <use href="/assets/icons/icons.svg#external-link"></use>
                                    </svg>
                                    Live Demo
                                </a>
                            <?php endif; ?>
                            
                            <button class="action-btn share" onclick="shareProject()">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                    <use href="/assets/icons/icons.svg#share"></use>
                                </svg>
                                Share Project
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.prose p:last-child {
    margin-bottom: 0;
}
</style>