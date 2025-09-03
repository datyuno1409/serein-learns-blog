<?php
// $pdo variable is passed from AdminController
if (!isset($pdo)) {
    die('Database connection not available');
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$active_menu = 'users_list';
$page_title = 'Danh sách người dùng';

$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = $_POST['user_id'] ?? null;
    
    if (!$user_id) {
        $_SESSION['error'] = 'ID người dùng không hợp lệ';
    } else {
        try {
            switch ($_POST['action']) {
                case 'toggle_status':
                    $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $current_status = $stmt->fetchColumn();
                    
                    $new_status = ($current_status === 'active') ? 'inactive' : 'active';
                    
                    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
                    $stmt->execute([$new_status, $user_id]);
                    
                    $_SESSION['success'] = 'Cập nhật trạng thái người dùng thành công';
                    break;
                    
                case 'change_role':
                    $new_role = $_POST['new_role'] ?? '';
                    if (!in_array($new_role, ['admin', 'user'])) {
                        throw new Exception('Vai trò không hợp lệ');
                    }
                    
                    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$new_role, $user_id]);
                    
                    $_SESSION['success'] = 'Cập nhật vai trò người dùng thành công';
                    break;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
    
    header('Location: /admin/users');
    exit;
}

try {
    $where_clause = '';
    $params = [];
    
    if ($search) {
        $where_clause = "WHERE (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
        $search_param = '%' . $search . '%';
        $params = [$search_param, $search_param, $search_param];
    }
    
    $count_sql = "SELECT COUNT(*) FROM users $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_users = $stmt->fetchColumn();
    
    $total_pages = ceil($total_users / $limit);
    
    $sql = "SELECT id, username, email, full_name, role, status, created_at, 
                   (SELECT COUNT(*) FROM posts WHERE author_id = users.id) as post_count
            FROM users 
            $where_clause 
            ORDER BY created_at DESC 
            LIMIT $limit OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $users = [];
    $total_pages = 0;
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
                        <li class="breadcrumb-item active">Người dùng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách người dùng</h3>
                    <div class="card-tools">
                        <a href="/admin/users/add" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm người dùng
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" class="form-inline">
                                <div class="input-group mr-3">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Tìm kiếm theo tên, email..." 
                                           value="<?= htmlspecialchars($search) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <select name="role_filter" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="">Tất cả vai trò</option>
                                    <option value="admin" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                                    <option value="user" <?= isset($_GET['role_filter']) && $_GET['role_filter'] === 'user' ? 'selected' : '' ?>>Người dùng</option>
                                </select>
                                
                                <select name="status_filter" class="form-control" onchange="this.form.submit()">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="active" <?= isset($_GET['status_filter']) && $_GET['status_filter'] === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                                    <option value="inactive" <?= isset($_GET['status_filter']) && $_GET['status_filter'] === 'inactive' ? 'selected' : '' ?>>Tạm khóa</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="mb-2">
                                <button class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display: none;" onclick="bulkAction('delete')">
                                    <i class="fas fa-trash"></i> Xóa đã chọn
                                </button>
                                <button class="btn btn-sm btn-outline-warning" id="bulkDeactivateBtn" style="display: none;" onclick="bulkAction('deactivate')">
                                    <i class="fas fa-lock"></i> Khóa đã chọn
                                </button>
                            </div>
                            <small class="text-muted">
                                Hiển thị <?= count($users) ?> / <?= $total_users ?> người dùng
                            </small>
                        </div>
                    </div>

                    <?php if (empty($users)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Không có người dùng nào được tìm thấy</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                                <label class="custom-control-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th>Avatar</th>
                                        <th>Thông tin</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Hoạt động</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input user-checkbox" 
                                                               id="user_<?= $user['id'] ?>" value="<?= $user['id'] ?>">
                                                        <label class="custom-control-label" for="user_<?= $user['id'] ?>"></label>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="user-avatar">
                                                    <?php if (!empty($user['avatar'])): ?>
                                                        <img src="<?= htmlspecialchars($user['avatar']) ?>" 
                                                             alt="Avatar" class="img-circle" width="40" height="40">
                                                    <?php else: ?>
                                                        <div class="avatar-placeholder">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="user-info">
                                                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                                    <?php if (!empty($user['full_name'])): ?>
                                                        <br>
                                                        <small><?= htmlspecialchars($user['full_name']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $user['role'] === 'admin' ? 'danger' : 'info' ?>">
                                                    <i class="fas fa-<?= $user['role'] === 'admin' ? 'crown' : 'user' ?>"></i>
                                                    <?= $user['role'] === 'admin' ? 'Quản trị' : 'Người dùng' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                    <i class="fas fa-<?= $user['status'] === 'active' ? 'check-circle' : 'times-circle' ?>"></i>
                                                    <?= $user['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="user-stats">
                                                    <span class="badge badge-primary" title="Số bài viết">
                                                        <i class="fas fa-file-alt"></i> <?= $user['post_count'] ?>
                                                    </span>
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <br>
                                                        <small class="text-success">
                                                            <i class="fas fa-circle" style="font-size: 8px;"></i> Online
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                                <br>
                                                <small class="text-muted"><?= date('H:i', strtotime($user['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            onclick="viewUserDetails(<?= $user['id'] ?>)" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="/admin/users/edit?id=<?= $user['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-<?= $user['status'] === 'active' ? 'warning' : 'success' ?>" 
                                                                onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] ?>')" 
                                                                title="<?= $user['status'] === 'active' ? 'Khóa tài khoản' : 'Kích hoạt tài khoản' ?>">
                                                            <i class="fas fa-<?= $user['status'] === 'active' ? 'lock' : 'unlock' ?>"></i>
                                                        </button>
                                                        
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                onclick="changeUserRole(<?= $user['id'] ?>, '<?= $user['role'] ?>')" 
                                                                title="Thay đổi vai trò">
                                                            <i class="fas fa-user-cog"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Phân trang">
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

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Xác nhận thay đổi trạng thái</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="statusMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thay đổi vai trò</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="roleMessage"></p>
                <div class="form-group">
                    <label>Chọn vai trò mới:</label>
                    <select class="form-control" id="newRole">
                        <option value="user">Người dùng</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmRoleChange">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Chi tiết người dùng</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Đang tải thông tin...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bulkActionTitle">Xác nhận thao tác</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="bulkActionMessage"></p>
                <div id="selectedUsersList"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBulkAction">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="action" id="actionType">
    <input type="hidden" name="user_id" id="actionUserId">
    <input type="hidden" name="new_role" id="actionNewRole">
</form>

<style>
.avatar-placeholder {
    width: 40px;
    height: 40px;
    background: #6c757d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.user-info strong {
    color: #495057;
}

.user-stats .badge {
    margin-bottom: 2px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script>
let selectedUsers = [];

function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'kích hoạt' : 'khóa';
    
    document.getElementById('statusMessage').textContent = 
        `Bạn có chắc chắn muốn ${action} tài khoản này?`;
    
    document.getElementById('confirmStatusChange').onclick = function() {
        document.getElementById('actionType').value = 'toggle_status';
        document.getElementById('actionUserId').value = userId;
        document.getElementById('actionForm').submit();
    };
    
    $('#statusModal').modal('show');
}

function changeUserRole(userId, currentRole) {
    document.getElementById('roleMessage').textContent = 
        `Thay đổi vai trò cho người dùng ID: ${userId}`;
    
    const newRoleSelect = document.getElementById('newRole');
    newRoleSelect.value = currentRole === 'admin' ? 'user' : 'admin';
    
    document.getElementById('confirmRoleChange').onclick = function() {
        document.getElementById('actionType').value = 'change_role';
        document.getElementById('actionUserId').value = userId;
        document.getElementById('actionNewRole').value = newRoleSelect.value;
        document.getElementById('actionForm').submit();
    };
    
    $('#roleModal').modal('show');
}

function viewUserDetails(userId) {
    $('#userDetailsModal').modal('show');
    
    // Simulate loading user details (in real app, this would be an AJAX call)
    setTimeout(() => {
        const userDetailsHtml = `
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="avatar-placeholder mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Người dùng #${userId}</h5>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td>${userId}</td>
                        </tr>
                        <tr>
                            <td><strong>Tên đăng nhập:</strong></td>
                            <td>user${userId}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>user${userId}@example.com</td>
                        </tr>
                        <tr>
                            <td><strong>Họ tên:</strong></td>
                            <td>Người dùng ${userId}</td>
                        </tr>
                        <tr>
                            <td><strong>Vai trò:</strong></td>
                            <td><span class="badge badge-info">Người dùng</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trạng thái:</strong></td>
                            <td><span class="badge badge-success">Hoạt động</span></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td>01/01/2024 10:00</td>
                        </tr>
                        <tr>
                            <td><strong>Lần đăng nhập cuối:</strong></td>
                            <td>Hôm nay, 14:30</td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <h6>Thống kê hoạt động</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bài viết</span>
                                    <span class="info-box-number">12</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-comments"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bình luận</span>
                                    <span class="info-box-number">45</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-eye"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Lượt xem</span>
                                    <span class="info-box-number">1,234</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-heart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Lượt thích</span>
                                    <span class="info-box-number">89</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('userDetailsContent').innerHTML = userDetailsHtml;
    }, 500);
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
    selectedUsers = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedUsers.length === 0) {
        alert('Vui lòng chọn ít nhất một người dùng');
        return;
    }
    
    const actionText = action === 'delete' ? 'xóa' : 'khóa';
    const actionTitle = action === 'delete' ? 'Xóa người dùng' : 'Khóa người dùng';
    
    document.getElementById('bulkActionTitle').textContent = actionTitle;
    document.getElementById('bulkActionMessage').textContent = 
        `Bạn có chắc chắn muốn ${actionText} ${selectedUsers.length} người dùng đã chọn?`;
    
    const usersList = selectedUsers.map(id => `<li>Người dùng ID: ${id}</li>`).join('');
    document.getElementById('selectedUsersList').innerHTML = `<ul>${usersList}</ul>`;
    
    document.getElementById('confirmBulkAction').onclick = function() {
        // In real app, this would submit the bulk action
        alert(`Đã ${actionText} ${selectedUsers.length} người dùng`);
        $('#bulkActionModal').modal('hide');
        location.reload();
    };
    
    $('#bulkActionModal').modal('show');
}

function updateBulkActionButtons() {
    const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');
    
    if (selectedCount > 0) {
        bulkDeleteBtn.style.display = 'inline-block';
        bulkDeactivateBtn.style.display = 'inline-block';
    } else {
        bulkDeleteBtn.style.display = 'none';
        bulkDeactivateBtn.style.display = 'none';
    }
}

$(document).ready(function() {
    $('.alert').delay(5000).fadeOut();
    
    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.user-checkbox').prop('checked', this.checked);
        updateBulkActionButtons();
    });
    
    // Handle individual checkboxes
    $(document).on('change', '.user-checkbox', function() {
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;
        
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        updateBulkActionButtons();
    });
});
</script>

<?php require_once '../views/layouts/admin_footer.php'; ?>