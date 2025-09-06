<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.svg' ?>" 
                             alt="<?= htmlspecialchars($user['name']) ?>" 
                             class="rounded-circle mr-3" 
                             width="80" height="80" 
                             style="object-fit: cover;">
                        <div>
                            <h2 class="h3 mb-1"><?= htmlspecialchars($user['name']) ?></h2>
                            <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
                            <p class="text-muted small"><?= __('profile.member_since') ?> <?= date('F Y', strtotime($user['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <?php if ($user['bio']): ?>
                        <div class="mb-4">
                            <h4><?= __('profile.bio') ?></h4>
                            <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-primary"><?= $article_count ?></h3>
                                    <p class="mb-0"><?= __('profile.articles_written') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-success"><?= $comment_count ?></h3>
                                    <p class="mb-0"><?= __('profile.comments_made') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($recent_articles)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h4 class="mb-0"><?= __('profile.recent_articles') ?></h4>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_articles as $article): ?>
                            <div class="media mb-3">
                                <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
                                    <img src="<?= htmlspecialchars($article['featured_image']) ?>" 
                                         alt="<?= htmlspecialchars($article['title']) ?>" 
                                         class="mr-3 rounded" 
                                         width="80" height="60" 
                                         style="object-fit: cover;">
                                <?php endif; ?>
                                <div class="media-body">
                                    <h6 class="mt-0">
                                        <a href="/article/<?= $article['id'] ?>" class="text-dark">
                                            <?= htmlspecialchars($article['title']) ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        <?= date('F j, Y', strtotime($article['created_at'])) ?> | 
                                        <?= $article['views'] ?> <?= __('articles.views') ?>
                                    </p>
                                    <p class="mb-0"><?= substr(strip_tags($article['content']), 0, 100) ?>...</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?= __('profile.account_info') ?></h5>
                </div>
                <div class="card-body">
                    <p><strong><?= __('profile.username') ?>:</strong> <?= htmlspecialchars($user['username']) ?></p>
                    <p><strong><?= __('profile.email') ?>:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong><?= __('profile.role') ?>:</strong> 
                        <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </p>
                    <p><strong><?= __('profile.joined') ?>:</strong> <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
                    <p><strong><?= __('profile.last_login') ?>:</strong> 
                        <?= $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : __('profile.never') ?>
                    </p>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0"><?= __('profile.actions') ?></h5>
                    </div>
                    <div class="card-body">
                        <a href="/admin/profile/edit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-edit"></i> <?= __('profile.edit_profile') ?>
                        </a>
                        <a href="/admin/articles" class="btn btn-secondary btn-sm btn-block">
                            <i class="fas fa-newspaper"></i> <?= __('profile.manage_articles') ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>