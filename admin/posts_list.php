<?php
// Set UTF-8 encoding
header('Content-Type: text/html; charset=utf-8');

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/includes/breadcrumb.php';

// Initialize database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /login');
    exit;
}

// Xử lý tìm kiếm và phân trang
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$author_filter = $_GET['author'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Lấy danh sách categories và authors cho filter
try {
    $categories_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $categories_stmt->fetchAll();
    
    $authors_stmt = $pdo->query("SELECT id, username FROM users WHERE role IN ('admin', 'editor', 'author') ORDER BY username");
    $authors = $authors_stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
    $authors = [];
}

// Xử lý xóa bài viết
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['post_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$_POST['post_id']]);
        $success_message = "Đã xóa bài viết thành công!";
    } catch (Exception $e) {
        $error_message = "Lỗi khi xóa bài viết: " . $e->getMessage();
    }
}

// Xử lý cập nhật trạng thái
if (isset($_POST['action']) && $_POST['action'] === 'update_status' && isset($_POST['post_id']) && isset($_POST['status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE posts SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['post_id']]);
        $success_message = "Đã cập nhật trạng thái bài viết!";
    } catch (Exception $e) {
        $error_message = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
    }
}

try {
    // Xây dựng WHERE clause cho filter
    $where_conditions = [];
    $params = [];
    
    if ($search) {
        $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ? OR u.username LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    if ($category_filter) {
        $where_conditions[] = "p.category_id = ?";
        $params[] = $category_filter;
    }
    
    if ($author_filter) {
        $where_conditions[] = "p.author_id = ?";
        $params[] = $author_filter;
    }
    
    if ($status_filter) {
        $where_conditions[] = "p.status = ?";
        $params[] = $status_filter;
    }
    
    $where_clause = !empty($where_conditions) ? ' WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Đếm tổng số bài viết
    $count_sql = "
        SELECT COUNT(*) as total 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id
        $where_clause
    ";
    
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_posts = $stmt->fetch()['total'];
    $total_pages = ceil($total_posts / $limit);
    
    // Lấy danh sách bài viết
    $sql = "
        SELECT p.*, u.username, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id
        $where_clause
        ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Posts list error: " . $e->getMessage());
    $posts = [];
    $total_posts = 0;
    $total_pages = 0;
    $error_message = "Lỗi khi tải danh sách bài viết: " . $e->getMessage();
}

$page_title = 'Danh sách bài viết';
$active_menu = 'posts_list';
$breadcrumbs = [
    ['title' => 'Trang chủ', 'url' => '/admin/dashboard'],
    ['title' => 'Bài viết', 'url' => '/admin/posts'],
    ['title' => 'Danh sách bài viết']
];

ob_start();
?>

<div class="content-wrapper">
  <?php renderBreadcrumb($breadcrumbs); ?>
  
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-check"></i> Thành công!</h5>
      <?= htmlspecialchars($success_message) ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
      <?= htmlspecialchars($error_message) ?>
    </div>
    <?php endif; ?>
    
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Danh sách bài viết</h3>
        <div class="card-tools">
          <a href="/admin/posts/add" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm bài viết
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <!-- Tìm kiếm và Filter -->
        <form method="GET" action="/admin/posts" class="mb-3">
          <div class="row">
            <div class="col-md-4">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm bài viết..." value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <select name="category" class="form-control">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <select name="author" class="form-control">
                <option value="">Tất cả tác giả</option>
                <?php foreach ($authors as $author): ?>
                  <option value="<?= $author['id'] ?>" <?= $author_filter == $author['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($author['username']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <select name="status" class="form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
              </select>
            </div>
            <div class="col-md-2">
              <div class="btn-group w-100">
                <button class="btn btn-primary" type="submit">
                  <i class="fas fa-filter"></i> Lọc
                </button>
                <?php if ($search || $category_filter || $author_filter || $status_filter): ?>
                <a href="/admin/posts" class="btn btn-outline-secondary">
                  <i class="fas fa-times"></i>
                </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-12 text-right">
              <small class="text-muted">
                Hiển thị <?= count($posts) ?> trong tổng số <?= number_format($total_posts) ?> bài viết
                <?php if ($search || $category_filter || $author_filter || $status_filter): ?>
                  (đã lọc)
                <?php endif; ?>
              </small>
            </div>
          </div>
        </form>
        
        <!-- Bảng danh sách -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width: 10px">#</th>
                <th>Tiêu đề</th>
                <th style="width: 120px">Tác giả</th>
                <th style="width: 120px">Danh mục</th>
                <th style="width: 100px">Trạng thái</th>
                <th style="width: 80px">Lượt xem</th>
                <th style="width: 120px">Ngày tạo</th>
                <th style="width: 150px">Thao tác</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($posts)): ?>
              <tr>
                <td colspan="8" class="text-center">
                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                  <p class="text-muted">Không có bài viết nào.</p>
                  <a href="/admin/posts/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm bài viết đầu tiên
                  </a>
                </td>
              </tr>
              <?php else: ?>
                <?php foreach ($posts as $index => $post): ?>
                <tr>
                  <td><?= $offset + $index + 1 ?></td>
                  <td>
                    <strong>
                      <a href="/admin/posts/edit/<?= $post['id'] ?>" class="text-dark">
                        <?= htmlspecialchars($post['title']) ?>
                      </a>
                    </strong>
                    <?php if ($post['excerpt']): ?>
                    <br><small class="text-muted"><?= htmlspecialchars(substr($post['excerpt'], 0, 100)) ?>...</small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($post['username']): ?>
                      <span class="badge badge-info"><?= htmlspecialchars($post['username']) ?></span>
                    <?php else: ?>
                      <span class="badge badge-secondary">N/A</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($post['category_name']): ?>
                      <span class="badge badge-primary"><?= htmlspecialchars($post['category_name']) ?></span>
                    <?php else: ?>
                      <span class="badge badge-secondary">Chưa phân loại</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <button type="button" class="btn btn-sm dropdown-toggle 
                        <?php 
                        switch($post['status']) {
                          case 'published': echo 'btn-success'; break;
                          case 'draft': echo 'btn-warning'; break;
                          case 'private': echo 'btn-info'; break;
                          default: echo 'btn-secondary';
                        }
                        ?>" data-toggle="dropdown">
                        <?php 
                        switch($post['status']) {
                          case 'published': echo 'Đã đăng'; break;
                          case 'draft': echo 'Nháp'; break;
                          case 'private': echo 'Riêng tư'; break;
                          default: echo ucfirst($post['status']);
                        }
                        ?>
                      </button>
                      <div class="dropdown-menu">
                        <form method="POST" style="display: inline;">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                          <button type="submit" name="status" value="published" class="dropdown-item">
                            <i class="fas fa-check text-success"></i> Đã đăng
                          </button>
                          <button type="submit" name="status" value="draft" class="dropdown-item">
                            <i class="fas fa-edit text-warning"></i> Nháp
                          </button>
                          <button type="submit" name="status" value="private" class="dropdown-item">
                            <i class="fas fa-lock text-info"></i> Riêng tư
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge badge-light"><?= number_format($post['view_count']) ?></span>
                  </td>
                  <td>
                    <small>
                      <?= date('d/m/Y', strtotime($post['created_at'])) ?><br>
                      <span class="text-muted"><?= date('H:i', strtotime($post['created_at'])) ?></span>
                    </small>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="/admin/posts/edit/<?= $post['id'] ?>" class="btn btn-info btn-sm" title="Chỉnh sửa">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="/post/<?= $post['id'] ?>" target="_blank" class="btn btn-success btn-sm" title="Xem bài viết">
                        <i class="fas fa-eye"></i>
                      </a>
                      <button type="button" class="btn btn-danger btn-sm" title="Xóa" 
                              onclick="confirmDelete(<?= $post['id'] ?>, '<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
        <div class="row mt-3">
          <div class="col-sm-12 col-md-5">
            <div class="dataTables_info">
              Hiển thị <?= $offset + 1 ?> đến <?= min($offset + $limit, $total_posts) ?> trong tổng số <?= number_format($total_posts) ?> bài viết
            </div>
          </div>
          <div class="col-sm-12 col-md-7">
            <div class="dataTables_paginate paging_simple_numbers float-right">
              <ul class="pagination">
                <?php 
                // Build query string for pagination
                $query_params = [];
                if ($search) $query_params['search'] = $search;
                if ($category_filter) $query_params['category'] = $category_filter;
                if ($author_filter) $query_params['author'] = $author_filter;
                if ($status_filter) $query_params['status'] = $status_filter;
                
                function build_pagination_url($page, $params) {
                    $params['page'] = $page;
                    return '?' . http_build_query($params);
                }
                ?>
                
                <?php if ($page > 1): ?>
                <li class="paginate_button page-item previous">
                  <a href="<?= build_pagination_url($page - 1, $query_params) ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Trước
                  </a>
                </li>
                <?php endif; ?>
                
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if ($start_page > 1): ?>
                <li class="paginate_button page-item">
                  <a href="<?= build_pagination_url(1, $query_params) ?>" class="page-link">1</a>
                </li>
                <?php if ($start_page > 2): ?>
                <li class="paginate_button page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <li class="paginate_button page-item <?= $i == $page ? 'active' : '' ?>">
                  <a href="<?= build_pagination_url($i, $query_params) ?>" class="page-link"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?>
                <li class="paginate_button page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="paginate_button page-item">
                  <a href="<?= build_pagination_url($total_pages, $query_params) ?>" class="page-link"><?= $total_pages ?></a>
                </li>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="paginate_button page-item next">
                  <a href="<?= build_pagination_url($page + 1, $query_params) ?>" class="page-link">
                    Sau <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xác nhận xóa</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Bạn có chắc chắn muốn xóa bài viết <strong id="postTitle"></strong>?</p>
        <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
        <form method="POST" style="display: inline;" id="deleteForm">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="post_id" id="deletePostId">
          <button type="submit" class="btn btn-danger">Xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDelete(postId, postTitle) {
    document.getElementById('deletePostId').value = postId;
    document.getElementById('postTitle').textContent = postTitle;
    $('#deleteModal').modal('show');
}

// Auto-hide alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../views/layouts/admin_dashboard.php';
?>