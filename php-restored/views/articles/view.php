<!-- Article Hero Section -->
<div class="article-hero">
  <?php if (isset($article['featured_image']) && !empty($article['featured_image'])): ?>
    <div class="hero-image-container">
      <img src="<?= htmlspecialchars($article['featured_image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="hero-image">
      <div class="hero-overlay"></div>
    </div>
  <?php endif; ?>
  
  <div class="hero-content">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="article-header">
            <div class="category-badge">
              <a href="/category/<?= $article['category_id'] ?>" class="category-link">
                <svg class="icon-sm"><use href="/assets/images/icons.svg#tag"></use></svg>
                <?= htmlspecialchars($article['category_name']) ?>
              </a>
            </div>
            
            <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
            
            <div class="article-meta">
              <div class="author-info">
                <img src="/img/default-avatar.png" alt="<?= htmlspecialchars($article['author_name']) ?>" class="author-avatar">
                <div class="author-details">
                  <span class="author-name"><?= htmlspecialchars($article['author_name']) ?></span>
                  <div class="meta-items">
                    <span class="meta-item">
                      <svg class="icon-xs"><use href="/assets/images/icons.svg#calendar"></use></svg>
                      <?= date('F j, Y', strtotime($article['created_at'])) ?>
                    </span>
                    <span class="meta-item">
                      <svg class="icon-xs"><use href="/assets/images/icons.svg#eye"></use></svg>
                      <?= $article['views'] ?> lượt xem
                    </span>
                    <span class="meta-item">
                      <svg class="icon-xs"><use href="/assets/images/icons.svg#message"></use></svg>
                      <?= count($comments) ?> bình luận
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container article-container">
  <div class="row">
    <div class="col-lg-8">
      <!-- Article Content -->
      <article class="article-content-wrapper">
        <div class="article-content">
          <?= $article['content'] ?>
        </div>
        
        <!-- Article Tags -->
        <?php if (isset($article['tags']) && is_array($article['tags']) && !empty($article['tags'])): ?>
          <div class="article-tags">
            <h6 class="tags-title">Tags:</h6>
            <div class="tags-list">
              <?php foreach ($article['tags'] as $tag): ?>
                <a href="/tag/<?= $tag['id'] ?>" class="tag-item">
                  <svg class="icon-xs"><use href="/assets/images/icons.svg#tag"></use></svg>
                  <?= htmlspecialchars($tag['name']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <!-- Article Actions -->
        <div class="article-actions">
          <div class="action-buttons">
            <button class="action-btn like-btn" onclick="toggleLike(<?= $article['id'] ?>)">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#heart"></use></svg>
              <span>Thích</span>
            </button>
            <button class="action-btn share-btn" onclick="shareArticle()">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#share"></use></svg>
              <span>Chia sẻ</span>
            </button>
            <button class="action-btn bookmark-btn" onclick="bookmarkArticle(<?= $article['id'] ?>)">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#bookmark"></use></svg>
              <span>Lưu</span>
            </button>
          </div>
        </div>
      </article>

      <!-- Comments Section -->
      <section class="comments-section">
        <div class="comments-header">
          <h3 class="comments-title">
            <svg class="icon-sm"><use href="/assets/images/icons.svg#message"></use></svg>
            Bình luận (<?= count($comments) ?>)
          </h3>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Comment Form -->
          <div class="comment-form-wrapper">
            <form action="/article/<?= $article['id'] ?>/comment" method="post" class="comment-form">
              <div class="form-group">
                <div class="comment-input-wrapper">
                  <img src="/img/default-avatar.png" alt="Your Avatar" class="comment-avatar">
                  <textarea class="comment-input" name="content" rows="3" required placeholder="Viết bình luận của bạn..."></textarea>
                </div>
              </div>
              <div class="comment-actions">
                <button type="submit" class="btn btn-primary comment-submit">
                  <svg class="icon-xs"><use href="/assets/images/icons.svg#send"></use></svg>
                  Gửi bình luận
                </button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <div class="login-prompt">
            <div class="login-card">
              <svg class="icon-lg"><use href="/assets/images/icons.svg#user"></use></svg>
              <h4>Đăng nhập để bình luận</h4>
              <p>Bạn cần đăng nhập để có thể tham gia thảo luận</p>
              <a href="/login" class="btn btn-primary">Đăng nhập ngay</a>
            </div>
          </div>
        <?php endif; ?>

        <!-- Comments List -->
        <div class="comments-list">
          <?php foreach ($comments as $comment): ?>
            <div class="comment-item" id="comment-<?= $comment['id'] ?>">
              <div class="comment-avatar-wrapper">
                <img src="<?= isset($comment['avatar']) ? $comment['avatar'] : '/img/default-avatar.png' ?>" alt="<?= htmlspecialchars($comment['author_name']) ?>" class="comment-avatar">
              </div>
              
              <div class="comment-content">
                <div class="comment-header">
                  <h5 class="comment-author"><?= htmlspecialchars($comment['author_name']) ?></h5>
                  <span class="comment-date">
                    <svg class="icon-xs"><use href="/assets/images/icons.svg#calendar"></use></svg>
                    <?= date('j M Y, H:i', strtotime($comment['created_at'])) ?>
                  </span>
                </div>
                
                <div class="comment-text">
                  <?= nl2br(htmlspecialchars($comment['content'])) ?>
                </div>
                
                <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['user_role'] == 'admin')): ?>
                  <div class="comment-actions">
                    <form action="/comment/<?= $comment['id'] ?>/delete" method="post" class="d-inline">
                      <button type="submit" class="comment-delete-btn" onclick="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                        <svg class="icon-xs"><use href="/assets/images/icons.svg#trash"></use></svg>
                        Xóa
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($comments)): ?>
            <div class="no-comments">
              <svg class="icon-lg"><use href="/assets/images/icons.svg#message"></use></svg>
              <h4>Chưa có bình luận nào</h4>
              <p>Hãy là người đầu tiên bình luận về bài viết này!</p>
            </div>
          <?php endif; ?>
        </div>
      </section>
  </div>

    <div class="col-lg-4">
      <aside class="article-sidebar">
        <!-- Author Info -->
        <div class="sidebar-card author-card">
          <div class="author-card-header">
            <h4 class="sidebar-title">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#user"></use></svg>
              Về tác giả
            </h4>
          </div>
          
          <div class="author-profile">
            <div class="author-avatar-large">
              <img src="/img/default-avatar.png" alt="<?= htmlspecialchars($article['author_name']) ?>" class="author-image">
            </div>
            
            <div class="author-info-detailed">
              <h5 class="author-name-large"><?= htmlspecialchars($article['author_name']) ?></h5>
              <div class="author-stats">
                <div class="stat-item">
                  <svg class="icon-xs"><use href="/assets/images/icons.svg#calendar"></use></svg>
                  <span>Tham gia từ <?= date('M Y', strtotime($article['created_at'])) ?></span>
                </div>
                <div class="stat-item">
                  <svg class="icon-xs"><use href="/assets/images/icons.svg#book"></use></svg>
                  <span>Đã xuất bản bài viết</span>
                </div>
              </div>
              
              <div class="author-actions">
                <button class="btn btn-outline-primary btn-sm follow-btn">
                  <svg class="icon-xs"><use href="/assets/images/icons.svg#plus"></use></svg>
                  Theo dõi
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Table of Contents -->
        <div class="sidebar-card toc-card">
          <div class="toc-header">
            <h4 class="sidebar-title">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#list"></use></svg>
              Mục lục
            </h4>
          </div>
          
          <div class="toc-content">
            <nav class="toc-nav" id="article-toc">
              <!-- Table of contents will be generated by JavaScript -->
            </nav>
          </div>
        </div>

        <!-- Related Articles -->
        <?php if (!empty($related_articles)): ?>
        <div class="sidebar-card related-articles-card">
          <div class="related-header">
            <h4 class="sidebar-title">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#book"></use></svg>
              Bài viết liên quan
            </h4>
          </div>
          
          <div class="related-articles-list">
            <?php foreach ($related_articles as $related): ?>
              <article class="related-article-item">
                <a href="/article/<?= $related['id'] ?>" class="related-article-link">
                  <div class="related-article-content">
                    <h6 class="related-article-title"><?= htmlspecialchars($related['title']) ?></h6>
                    <div class="related-article-meta">
                      <svg class="icon-xs"><use href="/assets/images/icons.svg#calendar"></use></svg>
                      <span><?= date('j M Y', strtotime($related['created_at'])) ?></span>
                    </div>
                  </div>
                  <div class="related-article-arrow">
                    <svg class="icon-xs"><use href="/assets/images/icons.svg#arrow-right"></use></svg>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Popular Tags -->
        <?php if (!empty($popular_tags) && is_array($popular_tags)): ?>
        <div class="sidebar-card tags-card">
          <div class="tags-header">
            <h4 class="sidebar-title">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#tag"></use></svg>
              Tags phổ biến
            </h4>
          </div>
          
          <div class="popular-tags-cloud">
            <?php foreach ($popular_tags as $tag): ?>
              <a href="/tag/<?= $tag['id'] ?>" class="tag-cloud-item">
                <span class="tag-name"><?= htmlspecialchars($tag['name']) ?></span>
                <span class="tag-count"><?= $tag['article_count'] ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Share Widget -->
        <div class="sidebar-card share-card">
          <div class="share-header">
            <h4 class="sidebar-title">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#share"></use></svg>
              Chia sẻ bài viết
            </h4>
          </div>
          
          <div class="share-buttons">
            <button class="share-btn facebook-btn" onclick="shareToFacebook()">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#facebook"></use></svg>
              Facebook
            </button>
            <button class="share-btn twitter-btn" onclick="shareToTwitter()">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#twitter"></use></svg>
              Twitter
            </button>
            <button class="share-btn linkedin-btn" onclick="shareToLinkedIn()">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#linkedin"></use></svg>
              LinkedIn
            </button>
            <button class="share-btn copy-btn" onclick="copyArticleLink()">
              <svg class="icon-sm"><use href="/assets/images/icons.svg#link"></use></svg>
              Sao chép link
            </button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>