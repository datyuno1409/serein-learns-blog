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
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin người dùng</h3>
                        </div>
                        
                        <form id="userForm" method="POST" action="/admin/users/add">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= htmlspecialchars($username ?? '') ?>" required>
                                    <small class="form-text text-muted">Chỉ được chứa chữ cái, số và dấu gạch dưới</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($email ?? '') ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="full_name">Họ và tên</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($full_name ?? '') ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <small class="form-text text-muted">Ít nhất 6 ký tự</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
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
                                <select class="form-control" id="role" name="role" form="userForm">
                                    <option value="user" <?= ($role ?? 'user') === 'user' ? 'selected' : '' ?>>
                                        Người dùng
                                    </option>
                                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>
                                        Quản trị viên
                                    </option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="is_active">Trạng thái</label>
                                <select class="form-control" id="is_active" name="is_active" form="userForm">
                                    <option value="1" <?= ($is_active ?? 1) == 1 ? 'selected' : '' ?>>
                                        Hoạt động
                                    </option>
                                    <option value="0" <?= ($is_active ?? '') == 0 ? 'selected' : '' ?>>
                                        Tạm khóa
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" form="userForm">
                                <i class="fas fa-save"></i> Thêm người dùng
                            </button>
                            <a href="/admin/users" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </div>
                    
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Hướng dẫn</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info-circle text-info"></i> Tên đăng nhập phải duy nhất</li>
                                <li><i class="fas fa-info-circle text-info"></i> Email phải duy nhất và hợp lệ</li>
                                <li><i class="fas fa-info-circle text-info"></i> Mật khẩu phải có ít nhất 6 ký tự</li>
                                <li><i class="fas fa-info-circle text-info"></i> Quản trị viên có quyền truy cập toàn bộ hệ thống</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Validate password confirmation
document.getElementById('userForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        return false;
    }
});
</script>