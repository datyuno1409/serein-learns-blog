<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h2 mb-4"><?= __('tags.title') ?></h1>
            
            <div class="tag-cloud mb-4">
                <?php foreach ($tags as $tag): ?>
                    <?php 
                        $size = min(max($tag['article_count'], 1), 5);
                        $fontSize = 0.8 + ($size * 0.2);
                    ?>
                    <a href="/tag/<?= $tag['id'] ?>" 
                       class="badge badge-outline-primary mr-2 mb-2 p-2" 
                       style="font-size: <?= $fontSize ?>rem; text-decoration: none;">
                        <?= htmlspecialchars($tag['name']) ?>
                        <span class="badge badge-light ml-1"><?= $tag['article_count'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="row">
                <?php foreach ($tags as $tag): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="/tag/<?= $tag['id'] ?>" class="text-dark">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($tag['name']) ?>
                                    </a>
                                </h6>
                                
                                <?php if ($tag['description']): ?>
                                    <p class="card-text text-muted small">
                                        <?= htmlspecialchars($tag['description']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="text-muted small">
                                    <i class="fas fa-newspaper"></i> 
                                    <?= $tag['article_count'] ?> <?= __('articles.article_count') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($tags)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <?= __('tags.no_tags') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>