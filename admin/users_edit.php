<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/auth_helper.php';
require_once 'includes/Language.php';

// Check if user is logged in and is admin
checkAdminAuth();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $pdo variable is passed from AdminController
if (!isset($pdo)) {
    die('Database connection not available');
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$active_menu = 'users_edit';
$page_title = 'Sửa thông tin người dùng';

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    $_SESSION['error'] = 'ID người dùng không hợp lệ';
    header('Location: /admin/users');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT u.*, 
               (SELECT COUNT(*) FROM posts WHERE author_id = u.id) as post_count
        FROM users u 
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['error'] = 'Người dùng không tồn tại';
        header('Location: /admin/users');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Lỗi khi tải thông tin người dùng: ' . $e->getMessage();
    header('Location: /admin/users');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        if ($user['post_count'] > 0) {
            $errors[] = 'Không thể xóa người dùng vì còn có ' . $user['post_count'] . ' bài viết';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                $_SESSION['success'] = 'Xóa người dùng "' . htmlspecialchars($user['username']) . '" thành công';
                header('Location: /admin/users');
                exit;
            } catch (Exception $e) {
                $errors[] = 'Lỗi khi xóa người dùng: ' . $e->getMessage();
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $is_active = $_POST['is_active'] ?? 1;
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($username)) {
            $errors[] = 'Tên đăng nhập không được để trống';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
        }
        
        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }
        
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } elseif ($password !== $confirm_password) {
                $errors[] = 'Xác nhận mật khẩu không khớp';
            }
        }
        
        if (!in_array($role, ['admin', 'user'])) {
            $errors[] = 'Vai trò không hợp lệ';
        }
        
        if (!in_array($status, ['active', 'inactive'])) {
            $errors[] = 'Trạng thái không hợp lệ';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $user_id]);
                
                if ($stmt->fetchColumn() > 0) {
                    $errors[] = 'Tên đăng nhập hoặc email đã tồn tại';
                } else {
                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            UPDATE users 
                            SET username = ?, email = ?, password = ?, full_name = ?, role = ?, is_active = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$username, $email, $hashed_password, $full_name ?: null, $role, $is_active, $user_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE users 
                            SET username = ?, email = ?, full_name = ?, role = ?, is_active = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->execute([$username, $email, $full_name ?: null, $role, $is_active, $user_id]);
                    }
                    
                    $user['username'] = $username;
                    $user['email'] = $email;
                    $user['full_name'] = $full_name;
                    $user['role'] = $role;
                    $user['is_active'] = $is_active;
                    
                    $success = 'Cập nhật thông tin người dùng thành công';
                }
            } catch (Exception $e) {
                $errors[] = 'Lỗi khi cập nhật người dùng: ' . $e->getMessage();
            }
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
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/users">Người dùng</a></li>
                        <li class="breadcrumb-item active">Sửa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin người dùng</h3>
                        </div>
                        
                        <form method="POST" id="userForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Tên đăng nhập <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?= htmlspecialchars($user['username']) ?>" 
                                                   placeholder="Nhập tên đăng nhập" required>
                                            <small class="form-text text-muted">
                                                Chỉ được chứa chữ cái, số và dấu gạch dưới. Tối thiểu 3 ký tự.
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($user['email']) ?>" 
                                                   placeholder="Nhập địa chỉ email" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="full_name">Họ và tên</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" 
                                           placeholder="Nhập họ và tên đầy đủ">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Mật khẩu mới</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" 
                                                       placeholder="Để trống nếu không đổi mật khẩu">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" 
                                                            onclick="togglePassword('password')">
                                                        <i class="fas fa-eye" id="password-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Tối thiểu 6 ký tự (để trống nếu không thay đổi)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                       placeholder="Nhập lại mật khẩu mới">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" 
                                                            onclick="togglePassword('confirm_password')">
                                                        <i class="fas fa-eye" id="confirm_password-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Cài đặt tài khoản</h3>
                        </div>
                        
                        <div class="card-body">
                            <div class="form-group">
                                <label for="role">Vai trò</label>
                                <select class="form-control" id="role" name="role" form="userForm" 
                                        <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>
                                        Người dùng
                                    </option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                        Quản trị viên
                                    </option>
                                </select>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <small class="form-text text-muted">Không thể thay đổi vai trò của chính mình</small>
                                    <input type="hidden" name="role" value="<?= $user['role'] ?>" form="userForm">
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="is_active">Trạng thái</label>
                                <select class="form-control" id="is_active" name="is_active" form="userForm" 
                                        <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                    <option value="1" <?= $user['is_active'] == 1 ? 'selected' : '' ?>>
                                        Hoạt động
                                    </option>
                                    <option value="0" <?= $user['is_active'] == 0 ? 'selected' : '' ?>>
                                        Tạm khóa
                                    </option>
                                </select>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                    <small class="form-text text-muted">Không thể thay đổi trạng thái của chính mình</small>
                                    <input type="hidden" name="is_active" value="<?= $user['is_active'] ?>" form="userForm">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" form="userForm">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                            <a href="/admin/users" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Thống kê</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><strong>ID:</strong> <?= $user['id'] ?></li>
                                <li><strong>Số bài viết:</strong> <span class="badge badge-primary"><?= $user['post_count'] ?></span></li>
                                <li><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></li>
                                <?php if ($user['updated_at']): ?>
                                    <li><strong>Cập nhật cuối:</strong> <?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <?php if ($user['id'] != $_SESSION['user_id'] && $user['post_count'] == 0): ?>
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Xóa người dùng</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Xóa vĩnh viễn người dùng này khỏi hệ thống.</p>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="fas fa-trash"></i> Xóa người dùng
                                </button>
                            </div>
                        </div>
                    <?php elseif ($user['post_count'] > 0): ?>
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Không thể xóa</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Không thể xóa người dùng này vì còn có <?= $user['post_count'] ?> bài viết.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xác nhận xóa</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa người dùng <strong><?= htmlspecialchars($user['username']) ?></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="deleteUser()">Xóa</button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
</form>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function confirmDelete() {
    $('#deleteModal').modal('show');
}

function deleteUser() {
    document.getElementById('deleteForm').submit();
}

$(document).ready(function() {
    $('.alert').delay(5000).fadeOut();
    
    $('#username').on('input', function() {
        this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
    });
    
    $('#confirm_password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password && password !== confirmPassword) {
            this.setCustomValidity('Mật khẩu không khớp');
        } else {
            this.setCustomValidity('');
        }
    });
    
    $('#password').on('input', function() {
        const confirmPassword = $('#confirm_password').val();
        const password = $(this).val();
        
        if (confirmPassword && password !== confirmPassword) {
            document.getElementById('confirm_password').setCustomValidity('Mật khẩu không khớp');
        } else {
            document.getElementById('confirm_password').setCustomValidity('');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../views/layouts/admin_footer.php'; ?>