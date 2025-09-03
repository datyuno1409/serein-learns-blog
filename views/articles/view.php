<?php require_once 'views/layouts/frontend.php'; ?>

<div class="row mt-4">
  <div class="col-md-8">
    <div class="card">
      <?php if ($article['featured_image']): ?>
        <img class="card-img-top" src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
      <?php endif; ?>

      <div class="card-body">
        <h1 class="card-title"><?= htmlspecialchars($article['title']) ?></h1>

        <div class="text-muted small mb-3">
          <i class="fas fa-user"></i> <?= htmlspecialchars($article['author_name']) ?> |
          <i class="fas fa-folder"></i> <a href="/category/<?= $article['category_id'] ?>" class="text-muted"><?= htmlspecialchars($article['category_name']) ?></a> |
          <i class="fas fa-calendar"></i> <?= date('F j, Y', strtotime($article['created_at'])) ?> |
          <i class="fas fa-eye"></i> <?= $article['views'] ?> <?= __('article.views') ?> |
          <i class="fas fa-comments"></i> <?= count($comments) ?> <?= __('article.comments') ?>
        </div>

        <div class="article-content">
          <?= $article['content'] ?>
        </div>

        <div class="tags mt-4">
          <?php foreach ($article['tags'] as $tag): ?>
            <a href="/tag/<?= $tag['id'] ?>" class="badge badge-secondary">
              <?= htmlspecialchars($tag['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Comments Section -->
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('article.comments_section') ?> (<?= count($comments) ?>)</h5>
      </div>
      <div class="card-body">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Comment Form -->
          <form action="/article/<?= $article['id'] ?>/comment" method="post" class="mb-4">
            <div class="form-group">
              <textarea class="form-control" name="content" rows="3" required placeholder="<?= __('article.comment_placeholder') ?>"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= __('article.submit_comment') ?></button>
          </form>
        <?php else: ?>
          <div class="alert alert-info">
            <?= __('article.login_to_comment') ?> <a href="/login"><?= __('article.login') ?></a>.
          </div>
        <?php endif; ?>

        <!-- Comments List -->
        <?php foreach ($comments as $comment): ?>
          <div class="media mb-4" id="comment-<?= $comment['id'] ?>">
            <img class="mr-3 rounded-circle" src="<?= $comment['avatar'] ?: '/img/default-avatar.png' ?>" alt="<?= htmlspecialchars($comment['author_name']) ?>" style="width: 50px; height: 50px;">
            <div class="media-body">
              <h5 class="mt-0">
                <?= htmlspecialchars($comment['author_name']) ?>
                <small class="text-muted">- <?= date('F j, Y g:i a', strtotime($comment['created_at'])) ?></small>
              </h5>
              <?= nl2br(htmlspecialchars($comment['content'])) ?>

              <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['user_role'] == 'admin')): ?>
                <div class="mt-2">
                  <form action="/comment/<?= $comment['id'] ?>/delete" method="post" class="d-inline">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?= __('article.confirm_delete_comment') ?>')">
                      <i class="fas fa-trash"></i> <?= __('article.delete') ?>
                    </button>
                  </form>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($comments)): ?>
          <div class="text-center text-muted">
            <?= __('article.no_comments') ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <!-- Author Info -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('article.about_author') ?></h5>
      </div>
      <div class="card-body">
        <div class="text-center mb-3">
          <img src="<?= $author['avatar'] ?: '/img/default-avatar.png' ?>" alt="<?= htmlspecialchars($author['username']) ?>" class="rounded-circle" style="width: 100px; height: 100px;">
        </div>
        <h5 class="text-center"><?= htmlspecialchars($author['username']) ?></h5>
        <p class="text-muted text-center">
          <?= __('article.member_since') ?> <?= date('F Y', strtotime($author['created_at'])) ?><br>
          <?= $author['article_count'] ?> <?= __('article.articles_published') ?>
        </p>
      </div>
    </div>

    <!-- Related Articles -->
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('article.related_articles') ?></h5>
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

    <!-- Popular Tags -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title m-0"><?= __('article.popular_tags') ?></h5>
      </div>
      <div class="card-body">
        <?php foreach ($popular_tags as $tag): ?>
          <a href="/tag/<?= $tag['id'] ?>" class="badge badge-secondary">
            <?= htmlspecialchars($tag['name']) ?>
            <span class="badge badge-light"><?= $tag['article_count'] ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>