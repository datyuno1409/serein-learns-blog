<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

// Initialize database connection
try {
    $database = new Database(); $pdo = $database->connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /login');
    exit;
}

$errors = [];
$success_message = '';

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
            // Kiểm tra tên danh mục đã tồn tại chưa
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
            $stmt->execute([$name]);
            if ($stmt->fetch()) {
                $errors[] = 'Tên danh mục đã tồn tại.';
            }
            
            // Kiểm tra slug đã tồn tại chưa
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE slug = ?');
            $stmt->execute([$slug]);
            if ($stmt->fetch()) {
                $errors[] = 'Slug đã tồn tại.';
            }
            
            if (empty($errors)) {
                // Thêm danh mục mới
                $stmt = $pdo->prepare('
                    INSERT INTO categories (name, slug, description, meta_title, meta_description, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ');
                
                $stmt->execute([
                    $name,
                    $slug,
                    $description ?: null,
                    $meta_title,
                    $meta_description ?: null
                ]);
                
                $_SESSION['success_message'] = 'Đã thêm danh mục "' . htmlspecialchars($name) . '" thành công!';
                header('Location: /admin/categories');
                exit;
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi khi thêm danh mục: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../views/layouts/admin_dashboard.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Thêm danh mục mới</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/categories">Danh mục</a></li>
                        <li class="breadcrumb-item active">Thêm mới</li>
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
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                           placeholder="Nhập tên danh mục" required>
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" 
                                           value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>" 
                                           placeholder="Tự động tạo từ tên danh mục">
                                    <small class="form-text text-muted">URL thân thiện. Để trống để tự động tạo.</small>
                                </div>

                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" 
                                              placeholder="Mô tả về danh mục này..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">SEO</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                       value="<?= htmlspecialchars($_POST['meta_title'] ?? '') ?>" 
                                       placeholder="Tự động lấy từ tên danh mục">
                                <small class="form-text text-muted">Tối đa 60 ký tự</small>
                            </div>

                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                                          placeholder="Mô tả ngắn gọn về danh mục..."><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                                <small class="form-text text-muted">Tối đa 160 ký tự</small>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Lưu danh mục
                            </button>
                            <a href="/admin/categories" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </section>
</div>

<script>
// Tự động tạo slug từ tên danh mục
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slugField = document.getElementById('slug');
    const metaTitleField = document.getElementById('meta_title');
    
    if (name && !slugField.value) {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugField.value = slug;
    }
    
    if (name && !metaTitleField.value) {
        metaTitleField.value = name;
    }
});

// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    $('.alert').fadeOut();
}, 5000);
</script>
