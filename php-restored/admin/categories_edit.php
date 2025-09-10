<?php

// $pdo variable is passed from AdminController
if (!isset($pdo)) {
    die('Database connection not available');
}

require_once __DIR__ . '/../helpers/auth_helper.php';

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /login');
    exit;
}

$errors = [];
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$category_id) {
    $_SESSION['error_message'] = 'ID danh mục không hợp lệ.';
    header('Location: /admin/categories');
    exit;
}

// Lấy thông tin danh mục
try {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        $_SESSION['error_message'] = 'Không tìm thấy danh mục.';
        header('Location: /admin/categories');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Lỗi khi tải thông tin danh mục: ' . $e->getMessage();
    header('Location: /admin/categories');
    exit;
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    
    // Validation
    if (empty($name)) {
        $errors[] = 'Tên danh mục không được để trống.';
    }
    
    // Tự động tạo slug nếu không có
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
    
    // Tự động tạo meta title nếu không có
    if (empty($meta_title)) {
        $meta_title = $name;
    }
    
    if (empty($errors)) {
        try {
            // Kiểm tra tên danh mục đã tồn tại chưa (trừ danh mục hiện tại)
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
            $stmt->execute([$name, $category_id]);
            if ($stmt->fetch()) {
                $errors[] = 'Tên danh mục đã tồn tại.';
            }
            
            // Kiểm tra slug đã tồn tại chưa (trừ danh mục hiện tại)
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = ? AND id != ?');
            $stmt->execute([$slug, $category_id]);
            if ($stmt->fetch()) {
                $errors[] = 'Slug đã tồn tại.';
            }
            
            if (empty($errors)) {
                // Cập nhật danh mục
                $stmt = $pdo->prepare('
                    UPDATE categories 
                    SET name = ?, slug = ?, description = ?, meta_title = ?, meta_description = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                
                $stmt->execute([
                    $name,
                    $slug,
                    $description ?: null,
                    $meta_title,
                    $meta_description ?: null,
                    $category_id
                ]);
                
                $_SESSION['success_message'] = 'Đã cập nhật danh mục "' . htmlspecialchars($name) . '" thành công!';
                header('Location: /admin/categories');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi khi cập nhật danh mục: ' . $e->getMessage();
        }
    }
    
    // Nếu có lỗi, cập nhật dữ liệu từ form
    $category['name'] = $name;
    $category['slug'] = $slug;
    $category['description'] = $description;
    $category['meta_title'] = $meta_title;
    $category['meta_description'] = $meta_description;
}

// Lấy số lượng bài viết trong danh mục
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE category_id = ?');
    $stmt->execute([$category_id]);
    $post_count = $stmt->fetchColumn();
} catch (Exception $e) {
    $post_count = 0;
}

require_once __DIR__ . '/../views/layouts/admin_dashboard.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sửa danh mục</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/categories">Danh mục</a></li>
                        <li class="breadcrumb-item active">Sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin danh mục</h3>
                        </div>
                        
                        <form method="POST">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($category['name']) ?>" 
                                           placeholder="Nhập tên danh mục" required>
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?= htmlspecialchars($category['slug']) ?>" 
                                           placeholder="URL thân thiện">
                                    <small class="form-text text-muted">URL thân thiện. Để trống để tự động tạo.</small>
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" 
                                              placeholder="Mô tả về danh mục này..."><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-newspaper"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Số bài viết</span>
                                    <span class="info-box-number"><?= $post_count ?></span>
                                </div>
                            </div>
                            
                            <p><strong>Ngày tạo:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?></p>
                            
                            <p><strong>Cập nhật lần cuối:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">SEO</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                       value="<?= htmlspecialchars($category['meta_title'] ?? '') ?>" 
                                       placeholder="Tự động lấy từ tên danh mục">
                                <small class="form-text text-muted">Tối đa 60 ký tự</small>
                            </div>

                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                                          placeholder="Mô tả ngắn gọn về danh mục..."><?= htmlspecialchars($category['meta_description'] ?? '') ?></textarea>
                                <small class="form-text text-muted">Tối đa 160 ký tự</small>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Cập nhật danh mục
                            </button>
                            <a href="/admin/categories" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <?php if ($post_count == 0): ?>
                                <button type="button" class="btn btn-danger btn-block" 
                                        onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')">
                                    <i class="fas fa-trash"></i> Xóa danh mục
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </section>
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
                <form method="POST" action="/admin/categories" style="display: inline;">
                    <input type="hidden" name="delete_id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Tự động tạo slug từ tên danh mục
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slugField = document.getElementById('slug');
    const metaTitleField = document.getElementById('meta_title');
    
    if (name && !slugField.dataset.userModified) {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugField.value = slug;
    }
    
    if (name && !metaTitleField.dataset.userModified) {
        metaTitleField.value = name;
    }
});

// Đánh dấu khi user tự sửa slug hoặc meta title
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.userModified = 'true';
});

document.getElementById('meta_title').addEventListener('input', function() {
    this.dataset.userModified = 'true';
});

function confirmDelete(id, name) {
    document.getElementById('deleteId').value = id;
    document.getElementById('categoryName').textContent = name;
    $('#deleteModal').modal('show');
}

// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    $('.alert').fadeOut();
}, 5000);
</script>
