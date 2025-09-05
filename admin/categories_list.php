<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

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

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Xử lý thêm danh mục
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    
    if (empty($name)) {
        $_SESSION['error_message'] = 'Tên danh mục không được để trống.';
    } else {
        try {
            // Kiểm tra trùng tên
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE name = ?');
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error_message'] = 'Tên danh mục đã tồn tại.';
            } else {
                // Tạo slug từ tên
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                
                $stmt = $pdo->prepare('INSERT INTO categories (name, description, slug, icon, created_at) VALUES (?, ?, ?, ?, NOW())');
                $stmt->execute([$name, $description, $slug, $icon]);
                $_SESSION['success_message'] = 'Đã thêm danh mục thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi khi thêm danh mục: ' . $e->getMessage();
        }
    }
    
    header('Location: /admin/categories');
    exit;
}

// Xử lý sửa danh mục
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    
    if (empty($name)) {
        $_SESSION['error_message'] = 'Tên danh mục không được để trống.';
    } else {
        try {
            // Kiểm tra trùng tên (trừ chính nó)
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM categories WHERE name = ? AND id != ?');
            $stmt->execute([$name, $id]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error_message'] = 'Tên danh mục đã tồn tại.';
            } else {
                // Tạo slug từ tên
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                
                $stmt = $pdo->prepare('UPDATE categories SET name = ?, description = ?, slug = ?, icon = ? WHERE id = ?');
                $stmt->execute([$name, $description, $slug, $icon, $id]);
                $_SESSION['success_message'] = 'Đã cập nhật danh mục thành công!';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi khi cập nhật danh mục: ' . $e->getMessage();
        }
    }
    
    header('Location: /admin/categories');
    exit;
}

// Xử lý xóa danh mục
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $delete_id = (int)$_POST['id'];
    
    try {
        // Kiểm tra xem danh mục có bài viết nào không
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE category_id = ?');
        $stmt->execute([$delete_id]);
        $post_count = $stmt->fetchColumn();
        
        if ($post_count > 0) {
            $_SESSION['error_message'] = 'Không thể xóa danh mục này vì còn có ' . $post_count . ' bài viết.';
        } else {
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$delete_id]);
            $_SESSION['success_message'] = 'Đã xóa danh mục thành công!';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Lỗi khi xóa danh mục: ' . $e->getMessage();
    }
    
    header('Location: /admin/categories');
    exit;
}

// Lấy danh sách danh mục
try {
    $where_clause = '';
    $params = [];
    
    if ($search) {
        $where_clause = 'WHERE name LIKE ? OR description LIKE ?';
        $params = ['%' . $search . '%', '%' . $search . '%'];
    }
    
    // Đếm tổng số danh mục
    $count_sql = "SELECT COUNT(*) FROM categories $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_categories = $stmt->fetchColumn();
    $total_pages = ceil($total_categories / $limit);
    
    // Lấy danh sách danh mục với phân trang
    $sql = "SELECT c.*, 
                   COUNT(a.id) as post_count,
                   c.created_at
            FROM categories c 
            LEFT JOIN articles a ON c.id = a.category_id 
            $where_clause 
            GROUP BY c.id 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = 'Lỗi khi tải danh sách danh mục: ' . $e->getMessage();
    $categories = [];
    $total_pages = 1;
}

require_once __DIR__ . '/../views/layouts/admin_dashboard.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản lý danh mục</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh mục</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="card-title">Danh sách danh mục</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                                <i class="fas fa-plus"></i> Thêm danh mục mới
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Form tìm kiếm -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Tìm kiếm danh mục..." value="<?= htmlspecialchars($search) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php if ($search): ?>
                                <div class="col-md-2">
                                    <a href="/admin/categories" class="btn btn-secondary">Xóa bộ lọc</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if (empty($categories)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không có danh mục nào được tìm thấy.</p>
                            <a href="/admin/categories/add" class="btn btn-primary">Thêm danh mục đầu tiên</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="60">ID</th>
                                        <th width="50">Icon</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th width="100">Số bài viết</th>
                                        <th width="130">Ngày tạo</th>
                                        <th width="120">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td class="text-center">
                                                <?php if (!empty($category['icon'])): ?>
                                                    <i class="<?= htmlspecialchars($category['icon']) ?> fa-lg text-primary"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-folder fa-lg text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($category['name']) ?></strong>
                                                <?php if ($category['slug']): ?>
                                                    <br><small class="text-muted">Slug: <?= htmlspecialchars($category['slug']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($category['description']): ?>
                                                    <?= htmlspecialchars(substr($category['description'], 0, 100)) ?>
                                                    <?= strlen($category['description']) > 100 ? '...' : '' ?>
                                                <?php else: ?>
                                                    <em class="text-muted">Không có mô tả</em>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info"><?= $category['post_count'] ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= htmlspecialchars($category['description']) ?>', '<?= htmlspecialchars($category['icon']) ?>')" 
                                                            title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($category['post_count'] == 0): ?>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')" 
                                                                title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-secondary" 
                                                                title="Không thể xóa (có bài viết)" disabled>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                Trước
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                                                Sau
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal thêm danh mục -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục mới</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="addName">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="addIcon">Icon (FontAwesome class)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                            </div>
                            <input type="text" class="form-control" id="addIcon" name="icon" placeholder="fas fa-folder">
                        </div>
                        <small class="form-text text-muted">Ví dụ: fas fa-code, fas fa-book, fas fa-music</small>
                    </div>
                    <div class="form-group">
                        <label for="addDescription">Mô tả</label>
                        <textarea class="form-control" id="addDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal sửa danh mục -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa danh mục</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label for="editName">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editIcon">Icon (FontAwesome class)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-icons"></i></span>
                            </div>
                            <input type="text" class="form-control" id="editIcon" name="icon" placeholder="fas fa-folder">
                        </div>
                        <small class="form-text text-muted">Ví dụ: fas fa-code, fas fa-book, fas fa-music</small>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Mô tả</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                </div>
            </form>
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
                <p>Bạn có chắc chắn muốn xóa danh mục <strong id="categoryName"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Xử lý modal thêm danh mục
function addCategory() {
    // Reset form
    document.getElementById('addName').value = '';
    document.getElementById('addIcon').value = '';
    document.getElementById('addDescription').value = '';
    $('#addCategoryModal').modal('show');
}

// Xử lý modal sửa danh mục
function editCategory(id, name, icon, description) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editIcon').value = icon || '';
    document.getElementById('editDescription').value = description || '';
    $('#editCategoryModal').modal('show');
}

// Xử lý modal xóa
function confirmDelete(id, name) {
    document.getElementById('deleteId').value = id;
    document.getElementById('categoryName').textContent = name;
    $('#deleteModal').modal('show');
}

// Preview icon khi nhập
document.getElementById('addIcon').addEventListener('input', function() {
    const iconClass = this.value;
    const preview = this.parentElement.querySelector('.input-group-text i');
    if (iconClass) {
        preview.className = iconClass;
    } else {
        preview.className = 'fas fa-icons';
    }
});

document.getElementById('editIcon').addEventListener('input', function() {
    const iconClass = this.value;
    const preview = this.parentElement.querySelector('.input-group-text i');
    if (iconClass) {
        preview.className = iconClass;
    } else {
        preview.className = 'fas fa-icons';
    }
});

// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    $('.alert').fadeOut();
}, 5000);
</script>