<?php
session_start();
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

// Lấy ID bài viết từ URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$post_id) {
    header('Location: /admin/posts');
    exit;
}

// Lấy thông tin bài viết
try {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        header('Location: /admin/posts');
        exit;
    }
} catch (Exception $e) {
    error_log('Get post error: ' . $e->getMessage());
    header('Location: /admin/posts');
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
    $featured_image = trim($_POST['featured_image'] ?? '');
    $published_at = $post['published_at']; // Keep existing published_at
    
    // Validate required fields
    $errors = [];
    if (empty($title)) {
        $errors[] = 'Tiêu đề bài viết là bắt buộc.';
    }
    if (empty($content)) {
        $errors[] = 'Nội dung bài viết là bắt buộc.';
    }
    
    // Set published_at if status changes to published and wasn't published before
    if ($status === 'published' && $post['status'] !== 'published') {
        $published_at = date('Y-m-d H:i:s');
    } elseif ($status !== 'published') {
        $published_at = null;
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE posts SET 
                    title = ?, content = ?, excerpt = ?, category_id = ?, status = ?, 
                    is_featured = ?, meta_title = ?, meta_description = ?, featured_image = ?,
                    published_at = ?, updated_at = NOW()
                WHERE id = ?
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
                $featured_image,
                $published_at,
                $post_id
            ]);
            
            $success_message = 'Đã cập nhật bài viết thành công!';
            
            // Refresh post data
            $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
            $stmt->execute([$post_id]);
            $post = $stmt->fetch();
            
        } catch (Exception $e) {
            error_log('Update post error: ' . $e->getMessage());
            $errors[] = 'Lỗi khi cập nhật bài viết: ' . $e->getMessage();
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

$page_title = 'Sửa bài viết: ' . htmlspecialchars($post['title']);
$active_menu = 'posts';
$breadcrumbs = [
    ['title' => 'Trang chủ', 'url' => '/admin/dashboard'],
    ['title' => 'Bài viết', 'url' => '/admin/posts'],
    ['title' => 'Sửa bài viết']
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
    
    <form method="POST" action="/admin/posts/edit?id=<?= $post_id ?>">
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
                       value="<?= htmlspecialchars($_POST['title'] ?? $post['title']) ?>" 
                       placeholder="Nhập tiêu đề bài viết..." required>
              </div>
              
              <!-- Excerpt -->
              <div class="form-group">
                <label for="excerpt">Tóm tắt</label>
                <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                          placeholder="Nhập tóm tắt ngắn gọn về bài viết..."><?= htmlspecialchars($_POST['excerpt'] ?? $post['excerpt']) ?></textarea>
                <small class="form-text text-muted">Tóm tắt sẽ hiển thị trong danh sách bài viết và kết quả tìm kiếm.</small>
              </div>
              
              <!-- Content -->
              <div class="form-group">
                <label for="content">Nội dung <span class="text-danger">*</span></label>
                <textarea class="form-control" id="content" name="content" rows="15" required><?= htmlspecialchars($_POST['content'] ?? $post['content']) ?></textarea>
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
                       value="<?= htmlspecialchars($_POST['meta_title'] ?? $post['meta_title']) ?>" 
                       placeholder="Tiêu đề SEO (để trống sẽ dùng tiêu đề bài viết)">
                <small class="form-text text-muted">Tối đa 60 ký tự để hiển thị tốt trên Google.</small>
              </div>
              
              <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                          placeholder="Mô tả SEO (để trống sẽ dùng tóm tắt)"><?= htmlspecialchars($_POST['meta_description'] ?? $post['meta_description']) ?></textarea>
                <small class="form-text text-muted">Tối đa 160 ký tự để hiển thị tốt trên Google.</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
          <!-- Post Info -->
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Thông tin bài viết</h3>
            </div>
            <div class="card-body">
              <p><strong>ID:</strong> <?= $post['id'] ?></p>
              <p><strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></p>
              <p><strong>Cập nhật:</strong> <?= date('d/m/Y H:i', strtotime($post['updated_at'])) ?></p>
              <?php if ($post['published_at']): ?>
              <p><strong>Đăng lúc:</strong> <?= date('d/m/Y H:i', strtotime($post['published_at'])) ?></p>
              <?php endif; ?>
              <p><strong>Lượt xem:</strong> <?= number_format($post['view_count']) ?></p>
            </div>
          </div>
          
          <!-- Publish -->
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Xuất bản</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="status">Trạng thái</label>
                <select class="form-control" id="status" name="status">
                  <option value="draft" <?= ($_POST['status'] ?? $post['status']) === 'draft' ? 'selected' : '' ?>>Nháp</option>
                  <option value="published" <?= ($_POST['status'] ?? $post['status']) === 'published' ? 'selected' : '' ?>>Đã đăng</option>
                  <option value="private" <?= ($_POST['status'] ?? $post['status']) === 'private' ? 'selected' : '' ?>>Riêng tư</option>
                </select>
              </div>
              
              <div class="form-group">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" 
                         <?= ($_POST['is_featured'] ?? $post['is_featured']) ? 'checked' : '' ?>>
                  <label class="custom-control-label" for="is_featured">Bài viết nổi bật</label>
                </div>
                <small class="form-text text-muted">Bài viết nổi bật sẽ hiển thị ở vị trí đặc biệt.</small>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập nhật bài viết
              </button>
              <a href="/admin/posts" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
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
                          <?= ($_POST['category_id'] ?? $post['category_id']) == $category['id'] ? 'selected' : '' ?>>
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
                       value="<?= htmlspecialchars($_POST['featured_image'] ?? $post['featured_image']) ?>" 
                       placeholder="https://example.com/image.jpg">
                <small class="form-text text-muted">Nhập URL ảnh đại diện cho bài viết.</small>
              </div>
              
              <div id="image_preview" class="text-center" <?= empty($post['featured_image']) ? 'style="display: none;"' : '' ?>>
                <img id="preview_img" src="<?= htmlspecialchars($post['featured_image']) ?>" alt="Preview" class="img-fluid" style="max-height: 200px;">
                <br><small class="text-muted">Xem trước ảnh đại diện</small>
              </div>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Hành động</h3>
            </div>
            <div class="card-body">
              <a href="/post/<?= $post['id'] ?>" class="btn btn-info btn-block" target="_blank">
                <i class="fas fa-eye"></i> Xem bài viết
              </a>
              <button type="button" class="btn btn-danger btn-block" onclick="deletePost(<?= $post['id'] ?>)">
                <i class="fas fa-trash"></i> Xóa bài viết
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Xác nhận xóa</h4>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Bạn có chắc chắn muốn xóa bài viết này không?</p>
        <p class="text-danger"><strong>Hành động này không thể hoàn tác!</strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
      </div>
    </div>
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

// Delete post function
function deletePost(postId) {
    $('#deleteModal').modal('show');
    
    document.getElementById('confirmDelete').onclick = function() {
        // Create form to delete post
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/posts/delete';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = postId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    };
}
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