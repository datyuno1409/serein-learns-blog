<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../includes/global_search.php';
require_once __DIR__ . '/../includes/breadcrumb.php';

// Initialize database connection
try {
    $database = new Database(); $pdo = $database->connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

requireAdmin();

$active_menu = 'search';
$page_title = 'Tìm kiếm';

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    $results = performGlobalSearch($query, 20);
}

$breadcrumbs = generateBreadcrumb('search');
$breadcrumbs[] = [
    'title' => 'Tìm kiếm',
    'url' => null,
    'icon' => 'fas fa-search'
];

require_once __DIR__ . '/../views/layouts/admin_dashboard.php';
?>

<div class="content-wrapper">
  <?php renderBreadcrumb($breadcrumbs); ?>
  
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-search"></i>
                Tìm kiếm toàn cục
              </h3>
            </div>
            <div class="card-body">
              <form method="GET" action="/admin/search" class="mb-4">
                <div class="input-group input-group-lg">
                  <input type="text" 
                         name="q" 
                         class="form-control" 
                         placeholder="Tìm kiếm bài viết, danh mục, người dùng..." 
                         value="<?= htmlspecialchars($query) ?>"
                         autofocus>
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                      <i class="fas fa-search"></i>
                      Tìm kiếm
                    </button>
                  </div>
                </div>
              </form>
              
              <?php if (!empty($query)): ?>
                <div class="search-results-container">
                  <?php renderSearchResults($results, $query); ?>
                </div>
              <?php else: ?>
                <div class="search-help">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="info-box">
                        <span class="info-box-icon bg-info">
                          <i class="fas fa-newspaper"></i>
                        </span>
                        <div class="info-box-content">
                          <span class="info-box-text">Bài viết</span>
                          <span class="info-box-number">Tìm theo tiêu đề, nội dung</span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="info-box">
                        <span class="info-box-icon bg-success">
                          <i class="fas fa-tags"></i>
                        </span>
                        <div class="info-box-content">
                          <span class="info-box-text">Danh mục</span>
                          <span class="info-box-number">Tìm theo tên, mô tả</span>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="info-box">
                        <span class="info-box-icon bg-warning">
                          <i class="fas fa-users"></i>
                        </span>
                        <div class="info-box-content">
                          <span class="info-box-text">Người dùng</span>
                          <span class="info-box-number">Tìm theo tên, email</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="alert alert-info mt-3">
                    <h5><i class="icon fas fa-info"></i> Hướng dẫn tìm kiếm:</h5>
                    <ul class="mb-0">
                      <li>Nhập từ khóa để tìm kiếm trong tất cả nội dung</li>
                      <li>Kết quả sẽ hiển thị theo từng loại: bài viết, danh mục, người dùng</li>
                      <li>Click vào kết quả để chuyển đến trang chỉnh sửa</li>
                      <li>Tìm kiếm không phân biệt chữ hoa chữ thường</li>
                    </ul>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
.search-results .search-section {
    border-left: 4px solid #007bff;
    padding-left: 15px;
}

.search-results .list-group-item {
    border-left: none;
    border-right: none;
    border-radius: 0;
}

.search-results .list-group-item:hover {
    background-color: #f8f9fa;
}

.search-no-results {
    text-align: center;
    padding: 40px 20px;
}

.search-help .info-box {
    margin-bottom: 20px;
}

.input-group-lg .form-control {
    font-size: 1.1rem;
}
</style>

<script>
$(document).ready(function() {
    $('input[name="q"]').on('keyup', function(e) {
        if (e.keyCode === 13) {
            $(this).closest('form').submit();
        }
    });
    
    $('input[name="q"]').focus();
});
</script>
