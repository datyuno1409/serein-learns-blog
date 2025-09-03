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

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $published_at = null;
    
    // Validate required fields
    $errors = [];
    if (empty($title)) {
        $errors[] = 'Tiêu đề bài viết là bắt buộc.';
    }
    if (empty($content)) {
        $errors[] = 'Nội dung bài viết là bắt buộc.';
    }
    
    // Set published_at if status is published
    if ($status === 'published') {
        $published_at = date('Y-m-d H:i:s');
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO posts (title, content, excerpt, category_id, status, is_featured, 
                                 meta_title, meta_description, published_at, author_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $title,
                $content,
                $excerpt,
                $category_id,
                $status,
                $is_featured,
                $meta_title ?: $title,
                $meta_description ?: $excerpt,
                $published_at,
                $_SESSION['user_id']
            ]);
            
            $success_message = 'Đã thêm bài viết thành công!';
            
            // Redirect to posts list after 2 seconds
            header('refresh:2;url=/admin/posts');
            
        } catch (Exception $e) {
            error_log('Add post error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi thêm bài viết: ' . $e->getMessage();
        }
    }
}

// Lấy danh sách categories
try {
    $stmt = $pdo->query('SELECT id, name FROM categories ORDER BY name ASC');
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Categories error: ' . $e->getMessage());
    $categories = [];
}

$page_title = 'Thêm bài viết mới';
$active_menu = 'posts_add';
$breadcrumbs = [
    ['title' => 'Trang chủ', 'url' => '/admin/dashboard'],
    ['title' => 'Bài viết', 'url' => '/admin/posts'],
    ['title' => 'Thêm bài viết']
];

ob_start();
?>

<div class="row">
  <div class="col-12">
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-check"></i> Thành công!</h5>
      <?= htmlspecialchars($success_message) ?>
      <br><small>Đang chuyển hướng về danh sách bài viết...</small>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-ban"></i> Lỗi!</h5>
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="/admin/posts/add">
      <div class="row">
        <!-- Main content -->
        <div class="col-md-8">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Nội dung bài viết</h3>
            </div>
            <div class="card-body">
              <!-- Title -->
              <div class="form-group">
                <label for="title">Tiêu đề bài viết <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                       placeholder="Nhập tiêu đề bài viết..." required>
              </div>
              
              <!-- Excerpt -->
              <div class="form-group">
                <label for="excerpt">Tóm tắt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                          placeholder="Nhập tóm tắt ngắn gọn về bài viết..."><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
                <small class="form-text text-muted">Tóm tắt sẽ hiển thị trong danh sách bài viết và kết quả tìm kiếm.</small>
              </div>
              
              <!-- Content -->
              <div class="form-group">
                <label for="content">Nội dung <span class="text-danger">*</span></label>
                <textarea class="form-control" id="content" name="content" rows="15" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
              </div>
            </div>
          </div>
          
          <!-- SEO Settings -->
          <div class="card card-secondary collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Cài đặt SEO</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
            <div class="card-body" style="display: none;">
              <div class="form-group">
                <label for="meta_title">Meta Title</label>
                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                       value="<?= htmlspecialchars($_POST['meta_title'] ?? '') ?>" 
                       placeholder="Tiêu đề SEO (để trống sẽ dùng tiêu đề bài viết)">
                <small class="form-text text-muted">Tối đa 60 ký tự để hiển thị tốt trên Google.</small>
              </div>
              
              <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                          placeholder="Mô tả SEO (để trống sẽ dùng tóm tắt)"><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                <small class="form-text text-muted">Tối đa 160 ký tự để hiển thị tốt trên Google.</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
          <!-- Publish -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Xuất bản</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="status">Trạng thái</label>
                <select class="form-control" id="status" name="status">
                  <option value="draft" <?= ($_POST['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Nháp</option>
                  <option value="published" <?= ($_POST['status'] ?? '') === 'published' ? 'selected' : '' ?>>Đã đăng</option>
                  <option value="private" <?= ($_POST['status'] ?? '') === 'private' ? 'selected' : '' ?>>Riêng tư</option>
                </select>
              </div>
              
              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" 
                         <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                  <label class="custom-control-label" for="is_featured">Bài viết nổi bật</label>
                </div>
                <small class="form-text text-muted">Bài viết nổi bật sẽ hiển thị ở vị trí đặc biệt.</small>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu bài viết
              </button>
              <a href="/admin/posts" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy
              </a>
            </div>
          </div>
          
          <!-- Category -->
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Danh mục</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="category_id">Chọn danh mục</label>
                <select class="form-control" id="category_id" name="category_id">
                  <option value="">-- Chưa phân loại --</option>
                  <?php foreach ($categories as $category): ?>
                  <option value="<?= $category['id'] ?>" 
                          <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <?php if (empty($categories)): ?>
              <div class="alert alert-info">
                <small>
                  <i class="fas fa-info-circle"></i>
                  Chưa có danh mục nào. <a href="/admin/categories/add">Tạo danh mục mới</a>
                </small>
              </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Featured Image -->
          <div class="card card-warning">
            <div class="card-header">
              <h3 class="card-title">Ảnh đại diện</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="featured_image">URL ảnh đại diện</label>
                <input type="url" class="form-control" id="featured_image" name="featured_image" 
                       value="<?= htmlspecialchars($_POST['featured_image'] ?? '') ?>" 
                       placeholder="https://example.com/image.jpg">
                <small class="form-text text-muted">Nhập URL ảnh đại diện cho bài viết.</small>
              </div>
              
              <div id="image_preview" class="text-center" style="display: none;">
                <img id="preview_img" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                <br><small class="text-muted">Xem trước ảnh đại diện</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// CKEditor initialization
ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'imageUpload', 'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        },
        language: 'vi',
        image: {
            toolbar: [
                'imageTextAlternative',
                'imageStyle:full',
                'imageStyle:side'
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells'
            ]
        }
    })
    .then(editor => {
        window.editor = editor;
    })
    .catch(error => {
        console.error('CKEditor initialization error:', error);
    });

// Image preview
document.getElementById('featured_image').addEventListener('input', function() {
    const url = this.value;
    const preview = document.getElementById('image_preview');
    const img = document.getElementById('preview_img');
    
    if (url && isValidImageUrl(url)) {
        img.src = url;
        preview.style.display = 'block';
        
        img.onerror = function() {
            preview.style.display = 'none';
        };
    } else {
        preview.style.display = 'none';
    }
});

function isValidImageUrl(url) {
    return /\.(jpg|jpeg|png|gif|webp)$/i.test(url) || url.includes('unsplash.com') || url.includes('pexels.com');
}

// Auto-generate meta title from title
document.getElementById('title').addEventListener('input', function() {
    const metaTitle = document.getElementById('meta_title');
    if (!metaTitle.value) {
        metaTitle.value = this.value;
    }
});

// Auto-generate meta description from excerpt
document.getElementById('excerpt').addEventListener('input', function() {
    const metaDesc = document.getElementById('meta_description');
    if (!metaDesc.value) {
        metaDesc.value = this.value;
    }
});

// Auto-hide alerts
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const content = window.editor ? window.editor.getData().trim() : document.getElementById('content').value.trim();
    
    if (!title) {
        e.preventDefault();
        alert('Vui lòng nhập tiêu đề bài viết!');
        document.getElementById('title').focus();
        return;
    }
    
    if (!content) {
        e.preventDefault();
        alert('Vui lòng nhập nội dung bài viết!');
        return;
    }
    
    // Update textarea with CKEditor content
    if (window.editor) {
        document.getElementById('content').value = window.editor.getData();
    }
});
</script>

<?php
$content = ob_get_clean();

// Add CKEditor to head
$additional_head = '
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/translations/vi.js"></script>
';

include __DIR__ . '/../views/layouts/admin_dashboard.php';
?>