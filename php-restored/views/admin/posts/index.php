<?php
// Ensure we have the necessary data
if (!isset($posts)) $posts = [];
if (!isset($total_posts)) $total_posts = 0;
if (!isset($total_pages)) $total_pages = 0;
if (!isset($page)) $page = 1;
if (!isset($categories)) $categories = [];
if (!isset($authors)) $authors = [];

// Get filter values
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$author_filter = $_GET['author'] ?? '';
$status_filter = $_GET['status'] ?? '';
$limit = 10;
$offset = ($page - 1) * $limit;
?>

<!-- Posts Management Header -->
<div class="posts-header">
  <div class="posts-header-content">
    <div class="posts-breadcrumb">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="/admin">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
              Dashboard
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Quản lý bài viết</li>
        </ol>
      </nav>
    </div>
    
    <div class="posts-hero">
      <div class="posts-hero-content">
        <h1 class="posts-title">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14,2 14,8 20,8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10,9 9,9 8,9"/>
          </svg>
          Quản lý bài viết
        </h1>
        <p class="posts-subtitle">Tạo, chỉnh sửa và quản lý tất cả bài viết trên website</p>
      </div>
      
      <div class="posts-actions">
        <a href="/admin/posts/add" class="btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Thêm bài viết mới
        </a>
        <button class="btn-secondary" onclick="toggleBulkActions()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,11 12,14 22,4"/>
            <path d="m21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c1.67 0 3.22.46 4.56 1.26"/>
          </svg>
          Chọn nhiều
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Posts Management Content -->
<div class="posts-content">
  <!-- Alert Messages -->
  <?php if (isset($_SESSION['success_message'])): ?>
  <div class="alert alert-success">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <polyline points="20,6 9,17 4,12"/>
    </svg>
    <?= htmlspecialchars($_SESSION['success_message']) ?>
    <?php unset($_SESSION['success_message']); ?>
  </div>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['error_message'])): ?>
  <div class="alert alert-error">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="12" r="10"/>
      <line x1="15" y1="9" x2="9" y2="15"/>
      <line x1="9" y1="9" x2="15" y2="15"/>
    </svg>
    <?= htmlspecialchars($_SESSION['error_message']) ?>
    <?php unset($_SESSION['error_message']); ?>
  </div>
  <?php endif; ?>

  <!-- Filters Section -->
  <div class="posts-filters">
    <form method="GET" action="/admin/posts" class="filters-form">
      <div class="filters-row">
        <div class="filter-group">
          <label for="search">Tìm kiếm</label>
          <div class="search-input">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" id="search" name="search" placeholder="Tìm kiếm theo tiêu đề, nội dung..." value="<?= htmlspecialchars($search) ?>">
          </div>
        </div>
        
        <div class="filter-group">
          <label for="category">Danh mục</label>
          <select id="category" name="category">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="author">Tác giả</label>
          <select id="author" name="author">
            <option value="">Tất cả tác giả</option>
            <?php foreach ($authors as $author): ?>
              <option value="<?= $author['id'] ?>" <?= $author_filter == $author['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($author['username']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="status">Trạng thái</label>
          <select id="status" name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
            <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Bản nháp</option>
            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
          </select>
        </div>
      </div>
      
      <div class="filters-actions">
        <button type="submit" class="btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/>
          </svg>
          Lọc
        </button>
        <?php if ($search || $category_filter || $author_filter || $status_filter): ?>
        <a href="/admin/posts" class="btn-secondary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
          Xóa bộ lọc
        </a>
        <?php endif; ?>
      </div>
    </form>
    
    <div class="results-info">
      <span class="results-count">
        Hiển thị <?= count($posts) ?> trong tổng số <?= number_format($total_posts) ?> bài viết
        <?php if ($search || $category_filter || $author_filter || $status_filter): ?>
          <span class="filtered">(đã lọc)</span>
        <?php endif; ?>
      </span>
    </div>
  </div>

  <!-- Posts Table -->
  <div class="posts-table-container">
    <?php if (empty($posts)): ?>
    <div class="empty-state">
      <div class="empty-icon">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14,2 14,8 20,8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10,9 9,9 8,9"/>
        </svg>
      </div>
      <h3>Chưa có bài viết nào</h3>
      <p>Bắt đầu tạo bài viết đầu tiên để chia sẻ nội dung với độc giả.</p>
      <a href="/admin/posts/add" class="btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tạo bài viết đầu tiên
      </a>
    </div>
    <?php else: ?>
    <div class="posts-table">
      <div class="table-header">
        <div class="table-row">
          <div class="table-cell checkbox-cell">
            <input type="checkbox" id="selectAll" onchange="toggleAllPosts()">
          </div>
          <div class="table-cell">Bài viết</div>
          <div class="table-cell">Tác giả</div>
          <div class="table-cell">Danh mục</div>
          <div class="table-cell">Trạng thái</div>
          <div class="table-cell">Lượt xem</div>
          <div class="table-cell">Ngày tạo</div>
          <div class="table-cell actions-cell">Thao tác</div>
        </div>
      </div>
      
      <div class="table-body">
        <?php foreach ($posts as $index => $post): ?>
        <div class="table-row post-row" data-post-id="<?= $post['id'] ?>">
          <div class="table-cell checkbox-cell">
            <input type="checkbox" class="post-checkbox" value="<?= $post['id'] ?>">
          </div>
          
          <div class="table-cell post-info">
            <div class="post-title">
              <a href="/admin/posts/edit/<?= $post['id'] ?>">
                <?= htmlspecialchars($post['title']) ?>
              </a>
            </div>
            <?php if (!empty($post['excerpt'])): ?>
            <div class="post-excerpt">
              <?= htmlspecialchars(substr($post['excerpt'], 0, 100)) ?>...
            </div>
            <?php endif; ?>
          </div>
          
          <div class="table-cell author-cell">
            <?php if (!empty($post['username'])): ?>
            <div class="author-info">
              <span class="author-name"><?= htmlspecialchars($post['username']) ?></span>
            </div>
            <?php else: ?>
            <span class="no-data">N/A</span>
            <?php endif; ?>
          </div>
          
          <div class="table-cell category-cell">
            <?php if (!empty($post['category_name'])): ?>
            <span class="category-badge"><?= htmlspecialchars($post['category_name']) ?></span>
            <?php else: ?>
            <span class="category-badge uncategorized">Chưa phân loại</span>
            <?php endif; ?>
          </div>
          
          <div class="table-cell status-cell">
            <div class="status-dropdown">
              <button class="status-badge status-<?= $post['status'] ?>" onclick="toggleStatusDropdown(<?= $post['id'] ?>)">
                <?php 
                switch($post['status']) {
                  case 'published': echo 'Đã đăng'; break;
                  case 'draft': echo 'Nháp'; break;
                  case 'pending': echo 'Chờ duyệt'; break;
                  default: echo ucfirst($post['status']);
                }
                ?>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="6,9 12,15 18,9"/>
                </svg>
              </button>
              <div class="status-menu" id="statusMenu<?= $post['id'] ?>">
                <form method="POST" action="/admin/posts/update-status">
                  <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                  <button type="submit" name="status" value="published" class="status-option">
                    <span class="status-dot status-published"></span>
                    Đã đăng
                  </button>
                  <button type="submit" name="status" value="draft" class="status-option">
                    <span class="status-dot status-draft"></span>
                    Nháp
                  </button>
                  <button type="submit" name="status" value="pending" class="status-option">
                    <span class="status-dot status-pending"></span>
                    Chờ duyệt
                  </button>
                </form>
              </div>
            </div>
          </div>
          
          <div class="table-cell views-cell">
            <span class="views-count"><?= number_format($post['view_count'] ?? 0) ?></span>
          </div>
          
          <div class="table-cell date-cell">
            <div class="date-info">
              <span class="date"><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
              <span class="time"><?= date('H:i', strtotime($post['created_at'])) ?></span>
            </div>
          </div>
          
          <div class="table-cell actions-cell">
            <div class="actions-menu">
              <button class="actions-trigger" onclick="toggleActionsMenu(<?= $post['id'] ?>)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="1"/>
                  <circle cx="12" cy="5" r="1"/>
                  <circle cx="12" cy="19" r="1"/>
                </svg>
              </button>
              <div class="actions-dropdown" id="actionsMenu<?= $post['id'] ?>">
                <a href="/admin/posts/edit/<?= $post['id'] ?>" class="action-item">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Chỉnh sửa
                </a>
                <a href="/articles/<?= $post['id'] ?>" target="_blank" class="action-item">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  Xem bài viết
                </a>
                <button class="action-item danger" onclick="confirmDelete(<?= $post['id'] ?>, '<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>')">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"/>
                  </svg>
                  Xóa
                </button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-container">
      <div class="pagination-info">
        Hiển thị <?= $offset + 1 ?> đến <?= min($offset + $limit, $total_posts) ?> trong tổng số <?= number_format($total_posts) ?> bài viết
      </div>
      
      <div class="pagination">
        <?php 
        $query_params = [];
        if ($search) $query_params['search'] = $search;
        if ($category_filter) $query_params['category'] = $category_filter;
        if ($author_filter) $query_params['author'] = $author_filter;
        if ($status_filter) $query_params['status'] = $status_filter;
        
        function build_pagination_url($page, $params) {
            $params['page'] = $page;
            return '/admin/posts?' . http_build_query($params);
        }
        ?>
        
        <?php if ($page > 1): ?>
        <a href="<?= build_pagination_url($page - 1, $query_params) ?>" class="pagination-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          Trước
        </a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        if ($start_page > 1): ?>
        <a href="<?= build_pagination_url(1, $query_params) ?>" class="pagination-number">1</a>
        <?php if ($start_page > 2): ?>
        <span class="pagination-ellipsis">...</span>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
        <a href="<?= build_pagination_url($i, $query_params) ?>" class="pagination-number <?= $i == $page ? 'active' : '' ?>">
          <?= $i ?>
        </a>
        <?php endfor; ?>
        
        <?php if ($end_page < $total_pages): ?>
        <?php if ($end_page < $total_pages - 1): ?>
        <span class="pagination-ellipsis">...</span>
        <?php endif; ?>
        <a href="<?= build_pagination_url($total_pages, $query_params) ?>" class="pagination-number"><?= $total_pages ?></a>
        <?php endif; ?>
        
        <?php if ($page < $total_pages): ?>
        <a href="<?= build_pagination_url($page + 1, $query_params) ?>" class="pagination-btn">
          Sau
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Bulk Actions Bar -->
<div class="bulk-actions-bar" id="bulkActionsBar">
  <div class="bulk-actions-content">
    <span class="selected-count">0 bài viết được chọn</span>
    <div class="bulk-actions">
      <button class="bulk-action" onclick="bulkUpdateStatus('published')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="20,6 9,17 4,12"/>
        </svg>
        Đăng
      </button>
      <button class="bulk-action" onclick="bulkUpdateStatus('draft')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Chuyển thành nháp
      </button>
      <button class="bulk-action danger" onclick="bulkDelete()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="3,6 5,6 21,6"/>
          <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"/>
        </svg>
        Xóa
      </button>
    </div>
    <button class="bulk-close" onclick="closeBulkActions()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Xác nhận xóa bài viết</h3>
      <button class="modal-close" onclick="closeDeleteModal()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="warning-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
          <line x1="12" y1="9" x2="12" y2="13"/>
          <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <p>Bạn có chắc chắn muốn xóa bài viết <strong id="deletePostTitle"></strong>?</p>
      <p class="warning-text">Hành động này không thể hoàn tác!</p>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeDeleteModal()">Hủy</button>
      <form method="POST" action="/admin/posts/delete" style="display: inline;" id="deleteForm">
        <input type="hidden" name="post_id" id="deletePostId">
        <button type="submit" class="btn-danger">Xóa bài viết</button>
      </form>
    </div>
  </div>
</div>