<?php
// Set UTF-8 encoding
header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

// Initialize database connection
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../blog.sqlite');
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

<!-- Include modern CSS -->
<link rel="stylesheet" href="/assets/css/admin-categories.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="content-wrapper">
    <!-- Modern Header -->
    <div class="categories-header">
        <div class="categories-header-content">
            <div class="categories-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/admin/dashboard">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Quản lý danh mục
                        </li>
                    </ol>
                </nav>
            </div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>
            <div class="categories-hero">
                <div class="categories-hero-content">
                    <h1 class="categories-title">
                        <i class="fas fa-layer-group"></i>
                        Quản lý danh mục
                    </h1>
                    <p class="categories-subtitle">
                        Tổ chức và quản lý các danh mục bài viết của bạn
                    </p>
                </div>
                <div class="categories-actions">
                    <button type="button" class="btn-modern btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-plus"></i>
                        Thêm danh mục mới
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="categories-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert-modern alert-success">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Thành công!</div>
                        <div class="alert-message"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert-modern alert-error">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Lỗi!</div>
                        <div class="alert-message"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Search and Filter Toolbar -->
            <div class="categories-toolbar">
                <div class="toolbar-row">
                    <div class="search-group">
                        <form method="GET" class="search-form">
                            <div class="search-input-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" class="search-input" 
                                       placeholder="Tìm kiếm danh mục theo tên hoặc mô tả..." 
                                       value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <button type="submit" class="btn-modern btn-secondary">
                                <i class="fas fa-search"></i>
                                Tìm kiếm
                            </button>
                        </form>
                        <?php if ($search): ?>
                            <a href="/admin/categories" class="btn-modern btn-outline">
                                <i class="fas fa-times"></i>
                                Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="filter-group">
                        <span class="results-count">
                            Tìm thấy <strong><?= $total_categories ?></strong> danh mục
                        </span>
                    </div>
                </div>
            </div>

            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="empty-title">
                        <?= $search ? 'Không tìm thấy danh mục nào' : 'Chưa có danh mục nào' ?>
                    </h3>
                    <p class="empty-description">
                        <?= $search ? 'Thử thay đổi từ khóa tìm kiếm hoặc xóa bộ lọc' : 'Hãy tạo danh mục đầu tiên để bắt đầu tổ chức nội dung' ?>
                    </p>
                    <?php if (!$search): ?>
                        <button type="button" class="btn-modern btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                            <i class="fas fa-plus"></i>
                            Tạo danh mục đầu tiên
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Categories Grid -->
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <div class="category-header">
                                <div class="category-icon">
                                    <?php if (!empty($category['icon'])): ?>
                                        <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-folder"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="category-actions">
                                    <button type="button" class="action-btn edit" 
                                            onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= htmlspecialchars($category['description']) ?>', '<?= htmlspecialchars($category['icon']) ?>')" 
                                            title="Chỉnh sửa danh mục">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($category['post_count'] == 0): ?>
                                        <button type="button" class="action-btn delete" 
                                                onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')" 
                                                title="Xóa danh mục">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="action-btn delete" 
                                                title="Không thể xóa (có <?= $category['post_count'] ?> bài viết)" 
                                                disabled style="opacity: 0.5; cursor: not-allowed;">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <h3 class="category-name"><?= htmlspecialchars($category['name']) ?></h3>
                            
                            <?php if ($category['description']): ?>
                                <p class="category-description">
                                    <?= htmlspecialchars($category['description']) ?>
                                </p>
                            <?php else: ?>
                                <p class="category-description" style="font-style: italic; color: var(--text-muted);">
                                    Chưa có mô tả cho danh mục này
                                </p>
                            <?php endif; ?>
                            
                            <div class="category-stats">
                                <div class="posts-count">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="count"><?= $category['post_count'] ?></span>
                                    bài viết
                                </div>
                                <div class="category-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modern Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination-modern">
                        <div class="pagination-info">
                            Trang <?= $page ?> / <?= $total_pages ?> (<?= $total_categories ?> danh mục)
                        </div>
                        <nav class="pagination-nav" aria-label="Phân trang danh mục">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                   class="pagination-btn pagination-prev">
                                    <i class="fas fa-chevron-left"></i>
                                    Trước
                                </a>
                            <?php endif; ?>

                            <div class="pagination-numbers">
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                       class="pagination-number <?= $i == $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                   class="pagination-btn pagination-next">
                                    Sau
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Modern Modal - Add Category -->
<div class="modal-modern" id="addCategoryModal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal('addCategoryModal')"></div>
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-plus-circle"></i>
                Thêm danh mục mới
            </div>
            <button type="button" class="modal-close" onclick="closeModal('addCategoryModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" class="modal-form">
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="addName" class="form-label">
                        <i class="fas fa-tag"></i>
                        Tên danh mục
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="form-input" id="addName" name="name" 
                           placeholder="Nhập tên danh mục..." required>
                    <div class="form-hint">Tên danh mục sẽ được sử dụng để phân loại bài viết</div>
                </div>
                
                <div class="form-group">
                    <label for="addDescription" class="form-label">
                        <i class="fas fa-align-left"></i>
                        Mô tả danh mục
                    </label>
                    <textarea class="form-input" id="addDescription" name="description" 
                              rows="4" placeholder="Mô tả chi tiết về danh mục này..."></textarea>
                    <div class="form-hint">Mô tả giúp người đọc hiểu rõ hơn về nội dung danh mục</div>
                </div>
                
                <div class="form-group">
                    <label for="addIcon" class="form-label">
                        <i class="fas fa-icons"></i>
                        Icon danh mục
                    </label>
                    <div class="icon-input-group">
                        <input type="text" class="form-input" id="addIcon" name="icon" 
                               placeholder="Ví dụ: fas fa-folder, fas fa-book, fas fa-code...">
                        <div class="icon-preview" id="iconPreview">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                    <div class="form-hint">Sử dụng class FontAwesome. Để trống để dùng icon mặc định</div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-secondary" onclick="closeModal('addCategoryModal')">
                    <i class="fas fa-times"></i>
                    Hủy bỏ
                </button>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-plus"></i>
                    Tạo danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modern Modal - Edit Category -->
<div class="modal-modern" id="editCategoryModal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal('editCategoryModal')"></div>
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-edit"></i>
                Chỉnh sửa danh mục
            </div>
            <button type="button" class="modal-close" onclick="closeModal('editCategoryModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" class="modal-form">
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="form-group">
                    <label for="editName" class="form-label">
                        <i class="fas fa-tag"></i>
                        Tên danh mục
                        <span class="required">*</span>
                    </label>
                    <input type="text" class="form-input" id="editName" name="name" 
                           placeholder="Nhập tên danh mục..." required>
                </div>
                
                <div class="form-group">
                    <label for="editDescription" class="form-label">
                        <i class="fas fa-align-left"></i>
                        Mô tả danh mục
                    </label>
                    <textarea class="form-input" id="editDescription" name="description" 
                              rows="4" placeholder="Mô tả chi tiết về danh mục này..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editIcon" class="form-label">
                        <i class="fas fa-icons"></i>
                        Icon danh mục
                    </label>
                    <div class="icon-input-group">
                        <input type="text" class="form-input" id="editIcon" name="icon" 
                               placeholder="Ví dụ: fas fa-folder, fas fa-book, fas fa-code...">
                        <div class="icon-preview" id="editIconPreview">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                    <div class="form-hint">Sử dụng class FontAwesome. Để trống để dùng icon mặc định</div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-secondary" onclick="closeModal('editCategoryModal')">
                    <i class="fas fa-times"></i>
                    Hủy bỏ
                </button>
                <button type="submit" class="btn-modern btn-warning">
                    <i class="fas fa-save"></i>
                    Cập nhật danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modern Modal - Delete Confirmation -->
<div class="modal-modern" id="deleteModal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal('deleteModal')"></div>
    <div class="modal-container modal-small">
        <div class="modal-header">
            <div class="modal-title">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger-color);"></i>
                Xác nhận xóa danh mục
            </div>
            <button type="button" class="modal-close" onclick="closeModal('deleteModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="delete-confirmation">
                <div class="delete-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div class="delete-message">
                    <h4>Bạn có chắc chắn muốn xóa danh mục này?</h4>
                    <p>Danh mục <strong id="categoryName"></strong> sẽ bị xóa vĩnh viễn.</p>
                    <div class="warning-note">
                        <i class="fas fa-exclamation-circle"></i>
                        Hành động này không thể hoàn tác!
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-modern btn-secondary" onclick="closeModal('deleteModal')">
                <i class="fas fa-times"></i>
                Hủy bỏ
            </button>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                <button type="submit" class="btn-modern btn-danger">
                    <i class="fas fa-trash"></i>
                    Xóa danh mục
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Modern Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        // Add animation class
        setTimeout(() => {
            modal.classList.add('modal-show');
        }, 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('modal-show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Xử lý modal thêm danh mục
function addCategory() {
    // Reset form
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryIcon').value = '';
    document.getElementById('categoryDescription').value = '';
    // Reset icon preview
    const iconPreview = document.querySelector('#iconPreview i');
    if (iconPreview) {
        iconPreview.className = 'fas fa-folder';
    }
    openModal('addCategoryModal');
}

// Xử lý modal sửa danh mục
function editCategory(id, name, description, icon) {
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editCategoryIcon').value = icon || '';
    document.getElementById('editCategoryDescription').value = description || '';
    
    // Update icon preview
    const iconPreview = document.querySelector('#editIconPreview i');
    if (iconPreview) {
        iconPreview.className = icon || 'fas fa-folder';
    }
    
    openModal('editCategoryModal');
}

// Xử lý modal xóa
function confirmDelete(id, name) {
    document.getElementById('deleteCategoryId').value = id;
    document.getElementById('deleteCategoryName').textContent = name;
    openModal('deleteCategoryModal');
}

// Preview icon khi nhập - Add modal
document.addEventListener('DOMContentLoaded', function() {
    const addIconInput = document.getElementById('categoryIcon');
    if (addIconInput) {
        addIconInput.addEventListener('input', function() {
            const iconClass = this.value.trim();
            const preview = document.querySelector('#iconPreview i');
            if (preview) {
                if (iconClass) {
                    preview.className = iconClass;
                } else {
                    preview.className = 'fas fa-folder';
                }
            }
        });
    }
    
    // Preview icon khi nhập - Edit modal
    const editIconInput = document.getElementById('editCategoryIcon');
    if (editIconInput) {
        editIconInput.addEventListener('input', function() {
            const iconClass = this.value.trim();
            const preview = document.querySelector('#editIconPreview i');
            if (preview) {
                if (iconClass) {
                    preview.className = iconClass;
                } else {
                    preview.className = 'fas fa-folder';
                }
            }
        });
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        const modal = e.target.parentElement;
        if (modal && modal.classList.contains('modal-modern')) {
            closeModal(modal.id);
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal-modern[style*="flex"]');
        openModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-modern');
    alerts.forEach(alert => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            alert.remove();
        }, 300);
    });
}, 5000);
</script>