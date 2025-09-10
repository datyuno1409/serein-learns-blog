<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

requireAdmin();

$db = new Database();
$pdo = $db->connect();

// Handle search and filters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $title = trim($_POST['title']);
                    $content = trim($_POST['content']);
                    $category_id = intval($_POST['category_id']);
                    $status = $_POST['status'] ?? 'draft';
                    $user_id = $_SESSION['user_id'];
                    
                    if (empty($title) || empty($content)) {
                        throw new Exception('Tiêu đề và nội dung không được để trống');
                    }
                    
                    // Create slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    // Handle image upload
                    $image_path = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../uploads/articles/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid() . '.' . $file_extension;
                        $target_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                            $image_path = 'uploads/articles/' . $filename;
                        }
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, category_id, user_id, status, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$title, $slug, $content, $category_id, $user_id, $status, $image_path]);
                    
                    $_SESSION['success'] = 'Bài viết đã được tạo thành công!';
                    break;
                    
                case 'update':
                    $id = intval($_POST['id']);
                    $title = trim($_POST['title']);
                    $content = trim($_POST['content']);
                    $category_id = intval($_POST['category_id']);
                    $status = $_POST['status'] ?? 'draft';
                    
                    if (empty($title) || empty($content)) {
                        throw new Exception('Tiêu đề và nội dung không được để trống');
                    }
                    
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                    
                    // Handle image upload
                    $image_update = '';
                    $params = [$title, $slug, $content, $category_id, $status, $id];
                    
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../uploads/articles/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid() . '.' . $file_extension;
                        $target_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                            $image_path = 'uploads/articles/' . $filename;
                            $image_update = ', image = ?';
                            array_splice($params, -1, 0, [$image_path]);
                        }
                    }
                    
                    $stmt = $pdo->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, category_id = ?, status = ?, updated_at = NOW()" . $image_update . " WHERE id = ?");
                    $stmt->execute($params);
                    
                    $_SESSION['success'] = 'Bài viết đã được cập nhật thành công!';
                    break;
                    
                case 'delete':
                    $id = intval($_POST['id']);
                    
                    // Get image path to delete file
                    $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ?");
                    $stmt->execute([$id]);
                    $article = $stmt->fetch();
                    
                    if ($article && $article['image'] && file_exists('../' . $article['image'])) {
                        unlink('../' . $article['image']);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
                    $stmt->execute([$id]);
                    
                    $_SESSION['success'] = 'Bài viết đã được xóa thành công!';
                    break;
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
    }
    
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit;
}

// Build query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category_filter)) {
    $where_conditions[] = "a.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "a.status = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON a.category_id = c.id LEFT JOIN users u ON a.user_id = u.id $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_articles = $count_stmt->fetchColumn();
$total_pages = ceil($total_articles / $limit);

// Get articles with pagination
$sql = "SELECT a.*, c.name as category_name, u.username as author_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        LEFT JOIN users u ON a.user_id = u.id 
        $where_clause 
        ORDER BY a.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories for filter
$categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categories_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Bài viết | Serein Blog Admin</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css">
    <!-- Summernote -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.css">
    
    <style>
        .article-image {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .category-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }
        .article-title {
            font-weight: 600;
            color: #495057;
            text-decoration: none;
        }
        .article-title:hover {
            color: #007bff;
            text-decoration: none;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .filter-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin: 0 0.125rem;
        }
        .table-actions {
            white-space: nowrap;
        }
        .content-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .modal-lg {
            max-width: 900px;
        }
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="../admin/dashboard.php" class="nav-link">Trang chủ</a>
            </li>
        </ul>
        
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="../admin/dashboard.php" class="brand-link">
            <i class="fas fa-blog brand-image img-circle elevation-3" style="opacity: .8; margin-left: 10px; color: white;"></i>
            <span class="brand-text font-weight-light">Serein Blog</span>
        </a>
        
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="#" class="d-block"><?= htmlspecialchars($_SESSION['username']) ?></a>
                </div>
            </div>
            
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="../admin/dashboard.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="articles_list.php" class="nav-link active">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Bài viết</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="categories_list.php" class="nav-link">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Danh mục</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../admin/users.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Người dùng</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../admin/settings.php" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Cài đặt</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
    
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><i class="fas fa-newspaper"></i> Quản lý Bài viết</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="../admin/dashboard.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Bài viết</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= $total_articles ?></h3>
                                <p>Tổng bài viết</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <?php
                                $published_count = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
                                ?>
                                <h3><?= $published_count ?></h3>
                                <p>Đã xuất bản</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <?php
                                $draft_count = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
                                ?>
                                <h3><?= $draft_count ?></h3>
                                <p>Bản nháp</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-edit"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <?php
                                $total_views = $pdo->query("SELECT SUM(views) FROM articles")->fetchColumn() ?: 0;
                                ?>
                                <h3><?= number_format($total_views) ?></h3>
                                <p>Tổng lượt xem</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <!-- Main Card -->
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Danh sách bài viết</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                                <i class="fas fa-plus"></i> Thêm bài viết mới
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="card-body">
                        <div class="filter-card">
                            <form method="GET" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><i class="fas fa-search"></i> Tìm kiếm:</label>
                                        <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề hoặc nội dung..." value="<?= htmlspecialchars($search) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-tags"></i> Danh mục:</label>
                                        <select name="category" class="form-control">
                                            <option value="">Tất cả danh mục</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($category['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><i class="fas fa-flag"></i> Trạng thái:</label>
                                        <select name="status" class="form-control">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="published" <?= $status_filter === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
                                            <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-info btn-block">
                                                <i class="fas fa-filter"></i> Lọc
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Articles Table -->
                        <?php if (count($articles) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="60">ID</th>
                                            <th width="80">Hình ảnh</th>
                                            <th>Tiêu đề</th>
                                            <th width="120">Danh mục</th>
                                            <th width="100">Tác giả</th>
                                            <th width="100">Trạng thái</th>
                                            <th width="80">Lượt xem</th>
                                            <th width="120">Ngày tạo</th>
                                            <th width="150">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articles as $article): ?>
                                            <tr>
                                                <td><strong>#<?= $article['id'] ?></strong></td>
                                                <td>
                                                    <?php if ($article['image']): ?>
                                                        <img src="../<?= htmlspecialchars($article['image']) ?>" alt="Article Image" class="article-image">
                                                    <?php else: ?>
                                                        <div class="article-image bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="article-title" data-toggle="modal" data-target="#viewModal" data-article-id="<?= $article['id'] ?>">
                                                        <?= htmlspecialchars($article['title']) ?>
                                                    </a>
                                                    <br>
                                                    <small class="text-muted content-preview">
                                                        <?= htmlspecialchars(strip_tags(substr($article['content'], 0, 100))) ?>...
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($article['category_name']): ?>
                                                        <span class="badge category-badge" style="background-color: #6c757d">
                                            <?= htmlspecialchars($article['category_name']) ?>
                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary category-badge">Chưa phân loại</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user"></i>
                                                    <?= htmlspecialchars($article['author_name'] ?? 'Unknown') ?>
                                                </td>
                                                <td>
                                                    <?php if ($article['status'] === 'published'): ?>
                                                        <span class="badge badge-success status-badge">
                                                            <i class="fas fa-check"></i> Đã xuất bản
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning status-badge">
                                                            <i class="fas fa-edit"></i> Bản nháp
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-eye text-info"></i>
                                                    <?= number_format($article['views'] ?? 0) ?>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?= date('d/m/Y', strtotime($article['created_at'])) ?><br>
                                                        <?= date('H:i', strtotime($article['created_at'])) ?>
                                                    </small>
                                                </td>
                                                <td class="table-actions">
                                                    <button type="button" class="btn btn-info btn-action" data-toggle="modal" data-target="#editModal" data-article-id="<?= $article['id'] ?>" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-action" onclick="deleteArticle(<?= $article['id'] ?>)" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <small class="text-muted">
                                            Hiển thị <?= ($page - 1) * $limit + 1 ?> - <?= min($page * $limit, $total_articles) ?> 
                                            trong tổng số <?= $total_articles ?> bài viết
                                        </small>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Không có bài viết nào</h5>
                                <p class="text-muted">Hãy tạo bài viết đầu tiên của bạn!</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                                    <i class="fas fa-plus"></i> Tạo bài viết mới
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 <a href="#">Serein Blog</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white"><i class="fas fa-plus"></i> Thêm bài viết mới</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="create_title"><i class="fas fa-heading"></i> Tiêu đề *</label>
                                <input type="text" class="form-control" id="create_title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="create_content"><i class="fas fa-align-left"></i> Nội dung *</label>
                                <textarea class="form-control summernote" id="create_content" name="content" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="create_category_id"><i class="fas fa-tags"></i> Danh mục</label>
                                <select class="form-control" id="create_category_id" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="create_status"><i class="fas fa-flag"></i> Trạng thái</label>
                                <select class="form-control" id="create_status" name="status">
                                    <option value="draft">Bản nháp</option>
                                    <option value="published">Xuất bản</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="create_image"><i class="fas fa-image"></i> Hình ảnh</label>
                                <input type="file" class="form-control-file" id="create_image" name="image" accept="image/*" onchange="previewImage(this, 'create_preview')">
                                <img id="create_preview" class="image-preview" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-info">
                    <h4 class="modal-title text-white"><i class="fas fa-edit"></i> Chỉnh sửa bài viết</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_title"><i class="fas fa-heading"></i> Tiêu đề *</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_content"><i class="fas fa-align-left"></i> Nội dung *</label>
                                <textarea class="form-control summernote" id="edit_content" name="content" rows="10" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_category_id"><i class="fas fa-tags"></i> Danh mục</label>
                                <select class="form-control" id="edit_category_id" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_status"><i class="fas fa-flag"></i> Trạng thái</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="draft">Bản nháp</option>
                                    <option value="published">Xuất bản</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_image"><i class="fas fa-image"></i> Hình ảnh mới</label>
                                <input type="file" class="form-control-file" id="edit_image" name="image" accept="image/*" onchange="previewImage(this, 'edit_preview')">
                                <img id="edit_current_image" class="image-preview" style="display: none;">
                                <img id="edit_preview" class="image-preview" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white"><i class="fas fa-eye"></i> Xem bài viết</h4>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Reset modals on close
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.summernote').summernote('reset');
        $(this).find('.image-preview').hide();
    });
    
    // Edit modal handler
    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var articleId = button.data('article-id');
        
        // Load article data via AJAX
        $.get('get_article.php', {id: articleId}, function(data) {
            if (data.success) {
                var article = data.article;
                $('#edit_id').val(article.id);
                $('#edit_title').val(article.title);
                $('#edit_content').summernote('code', article.content);
                $('#edit_category_id').val(article.category_id);
                $('#edit_status').val(article.status);
                
                if (article.image) {
                    $('#edit_current_image').attr('src', '../' + article.image).show();
                }
            }
        });
    });
    
    // View modal handler
    $('#viewModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var articleId = button.data('article-id');
        
        // Load article data via AJAX
        $.get('get_article.php', {id: articleId}, function(data) {
            if (data.success) {
                var article = data.article;
                var html = '<div class="article-view">';
                
                if (article.image) {
                    html += '<img src="../' + article.image + '" class="img-fluid mb-3" alt="Article Image">';
                }
                
                html += '<h3>' + article.title + '</h3>';
                html += '<div class="mb-2">';
                html += '<span class="badge badge-info mr-2"><i class="fas fa-tags"></i> ' + (article.category_name || 'Uncategorized') + '</span>';
                html += '<span class="badge badge-' + (article.status === 'published' ? 'success' : 'warning') + ' mr-2">';
                html += '<i class="fas fa-flag"></i> ' + (article.status === 'published' ? 'Đã xuất bản' : 'Bản nháp') + '</span>';
                html += '<span class="badge badge-secondary"><i class="fas fa-eye"></i> ' + (article.views || 0) + ' lượt xem</span>';
                html += '</div>';
                html += '<div class="article-content">' + article.content + '</div>';
                html += '</div>';
                
                $('#viewModalBody').html(html);
            }
        });
    });
});

// Image preview function
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#' + previewId).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Delete article function
function deleteArticle(id) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Bạn có chắc chắn muốn xóa bài viết này? Hành động này không thể hoàn tác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            var form = $('<form method="POST"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' + id + '"></form>');
            $('body').append(form);
            form.submit();
        }
    });
}
</script>

</body>
</html>
