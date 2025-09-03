<?php require_once 'views/layouts/frontend.php'; ?>

<div class="row mt-4">
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-body">
        <h1 class="h3"><?= __('tags.articles_with_tag') ?> "<?= htmlspecialchars($tag['name']) ?>"</h1>
        <p class="text-muted"><?= htmlspecialchars($tag['description']) ?></p>
        <div class="text-muted small">
          <i class="fas fa-newspaper"></i> <?= count($articles) ?> <?= __('articles.article_count') ?>
        </div>
      </div>
    </div>

    <?php foreach ($articles as $article): ?>
      <div class="card mb-4">
        <?php if ($article['featured_image']): ?>
          <img class="card-img-top" src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
        <?php endif; ?>
        
        <div class="card-body">
          <h2 class="card-title h4">
            <a href="/article/<?= $article['id'] ?>" class="text-dark">
              <?= htmlspecialchars($article['title']) ?>
            </a>
          </h2>
          
          <div class="card-text text-muted small mb-2">
            <i class="fas fa-user"></i> <?= htmlspecialchars($article['author_name']) ?> |
            <i class="fas fa-folder"></i> <a href="/category/<?= $article['category_id'] ?>" class="text-muted"><?= htmlspecialchars($article['category_name']) ?></a> |
            <i class="fas fa-calendar"></i> <?= date('F j, Y', strtotime($article['created_at'])) ?> |
            <i class="fas fa-eye"></i> <?= $article['views'] ?> <?= __('articles.views') ?> |
            <i class="fas fa-comments"></i> <?= $article['comment_count'] ?> <?= __('articles.comments') ?>
          </div>
          
          <div class="card-text mb-2">
            <?= substr(strip_tags($article['content']), 0, 200) ?>...
          </div>
          
          <div class="tags mb-2">
            <?php foreach ($article['tags'] as $t): ?>
              <a href="/tag/<?= $t['id'] ?>" class="badge badge-secondary <?= $t['id'] === $tag['id'] ? 'font-weight-bold' : '' ?>">
                <?= htmlspecialchars($t['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
          
          <a href="/article/<?= $article['id'] ?>" class="btn btn-primary btn-sm"><?= __('articles.read_more') ?></a>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <nav aria-label="Page navigation" class="mb-4">
        <ul class="pagination justify-content-center">
          <?php if ($current_page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="/tag/<?= $tag['id'] ?>?page=<?= $current_page - 1 ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
              <a class="page-link" href="/tag/<?= $tag['id'] ?>?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
              <a class="page-link" href="/tag/<?= $tag['id'] ?>?page=<?= $current_page + 1 ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>

  <div class="col-md-4">
    <!-- Categories Widget -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('articles.categories') ?></h5>
      </div>
      <div class="card-body">
        <div class="row">
          <?php foreach ($categories as $category): ?>
            <div class="col-lg-6">
              <a href="/category/<?= $category['id'] ?>" class="text-muted">
                <?= htmlspecialchars($category['name']) ?>
                <span class="badge badge-secondary float-right"><?= $category['article_count'] ?></span>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Popular Tags Widget -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('articles.popular_tags') ?></h5>
      </div>
      <div class="card-body">
        <?php foreach ($popular_tags as $t): ?>
          <a href="/tag/<?= $t['id'] ?>" class="badge badge-secondary <?= $t['id'] === $tag['id'] ? 'font-weight-bold' : '' ?>">
            <?= htmlspecialchars($t['name']) ?>
            <span class="badge badge-light"><?= $t['article_count'] ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Related Articles Widget -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('articles.related_articles') ?></h5>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <?php foreach ($related_articles as $related): ?>
            <li class="list-group-item">
              <a href="/article/<?= $related['id'] ?>" class="text-dark">
                <?= htmlspecialchars($related['title']) ?>
              </a>
              <div class="text-muted small">
                <i class="fas fa-calendar"></i> <?= date('F j, Y', strtotime($related['created_at'])) ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</div>