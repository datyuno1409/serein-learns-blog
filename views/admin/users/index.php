<?php
if (!isset($users)) {
    $users = [];
}
if (!isset($total_users)) {
    $total_users = 0;
}
if (!isset($total_pages)) {
    $total_pages = 0;
}
if (!isset($page)) {
    $page = 1;
}
if (!isset($search)) {
    $search = '';
}
if (!isset($role_filter)) {
    $role_filter = '';
}
if (!isset($status_filter)) {
    $status_filter = '';
}
?>

<div class="users-management">
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-users"></i>
                    Quản lý người dùng
                </h1>
                <nav class="breadcrumb-nav">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/admin/dashboard">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <i class="fas fa-users"></i>
                            Người dùng
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="header-actions">
                <a href="/admin/users/add" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Thêm người dùng
                </a>
            </div>
        </div>
    </div>

    <div class="hero-content">
        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($total_users) ?></h3>
                    <p>Tổng người dùng</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $admin_count = 0;
                    foreach ($users as $user) {
                        if ($user['role'] === 'admin') $admin_count++;
                    }
                    ?>
                    <h3><?= $admin_count ?></h3>
                    <p>Quản trị viên</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $active_count = 0;
                    foreach ($users as $user) {
                        if ($user['is_active'] == 1) $active_count++;
                    }
                    ?>
                    <h3><?= $active_count ?></h3>
                    <p>Đang hoạt động</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="content-card">
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="search-group">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên, email..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                        Tìm kiếm
                    </button>
                </div>
                
                <div class="filter-group">
                    <select name="role_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                        <option value="user" <?= $role_filter === 'user' ? 'selected' : '' ?>>Người dùng</option>
                    </select>
                    
                    <select name="status_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Tạm khóa</option>
                    </select>
                    
                    <?php if ($search || $role_filter || $status_filter): ?>
                        <a href="/admin/users" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                            Xóa bộ lọc
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-section">
            <div class="table-header">
                <div class="table-info">
                    <span class="results-count">
                        Hiển thị <?= count($users) ?> / <?= number_format($total_users) ?> người dùng
                    </span>
                </div>
                <div class="table-actions">
                    <div class="bulk-actions" style="display: none;">
                        <span class="selected-count">0 người dùng được chọn</span>
                        <button type="button" class="btn btn-outline-warning" onclick="bulkToggleStatus()">
                            <i class="fas fa-toggle-on"></i>
                            Thay đổi trạng thái
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash"></i>
                            Xóa đã chọn
                        </button>
                    </div>
                </div>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Không có người dùng nào</h3>
                    <p>Không tìm thấy người dùng nào phù hợp với tiêu chí tìm kiếm.</p>
                    <a href="/admin/users/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Thêm người dùng đầu tiên
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table users-table">
                        <thead>
                            <tr>
                                <th class="select-col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Hoạt động</th>
                                <th>Ngày tạo</th>
                                <th class="actions-col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="user-row" data-user-id="<?= $user['id'] ?>">
                                    <td class="select-col">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <div class="form-check">
                                                <input class="form-check-input user-checkbox" type="checkbox" 
                                                       id="user_<?= $user['id'] ?>" value="<?= $user['id'] ?>">
                                                <label class="form-check-label" for="user_<?= $user['id'] ?>"></label>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="user-info">
                                        <div class="user-avatar">
                                            <?php if (!empty($user['avatar'])): ?>
                                                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
                                            <?php else: ?>
                                                <div class="avatar-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="user-details">
                                            <h6 class="user-name"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h6>
                                            <p class="user-email"><?= htmlspecialchars($user['email']) ?></p>
                                            <small class="user-username">@<?= htmlspecialchars($user['username']) ?></small>
                                        </div>
                                    </td>
                                    <td class="role-col">
                                        <span class="badge role-badge role-<?= $user['role'] ?>">
                                            <i class="fas fa-<?= $user['role'] === 'admin' ? 'crown' : 'user' ?>"></i>
                                            <?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Người dùng' ?>
                                        </span>
                                    </td>
                                    <td class="status-col">
                            <span class="badge status-badge status-<?= $user['is_active'] == 1 ? 'active' : 'inactive' ?>">
                                <i class="fas fa-<?= $user['is_active'] == 1 ? 'check-circle' : 'times-circle' ?>"></i>
                                <?= $user['is_active'] == 1 ? 'Hoạt động' : 'Tạm khóa' ?>
                            </span>
                        </td>
                                    <td class="activity-col">
                                        <div class="activity-stats">
                                            <span class="stat-item" title="Số bài viết">
                                                <i class="fas fa-file-alt"></i>
                                                <?= $user['post_count'] ?? 0 ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="date-col">
                                        <span class="date-text">
                                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                        </span>
                                        <small class="time-text">
                                            <?= date('H:i', strtotime($user['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="actions-col">
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="viewUserDetails(<?= $user['id'] ?>)" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="/admin/users/edit?id=<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-<?= $user['is_active'] == 1 ? 'warning' : 'success' ?>" 
                                        onclick="toggleUserStatus(<?= $user['id'] ?>, <?= $user['is_active'] ?>)" 
                                        title="<?= $user['is_active'] == 1 ? 'Khóa tài khoản' : 'Kích hoạt tài khoản' ?>">
                                    <i class="fas fa-<?= $user['is_active'] == 1 ? 'lock' : 'unlock' ?>"></i>
                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
                                                        title="Xóa người dùng">
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

                <?php if ($total_pages > 1): ?>
                    <div class="pagination-section">
                        <div class="pagination-info">
                            <span>Trang <?= $page ?> / <?= $total_pages ?></span>
                        </div>
                        <nav class="pagination-nav">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">
                                            <i class="fas fa-angle-double-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                            <i class="fas fa-angle-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                            <i class="fas fa-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">
                                            <i class="fas fa-angle-double-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="bulk-actions-bar" style="display: none;">
        <div class="bulk-info">
            <span class="selected-count">0 người dùng được chọn</span>
        </div>
        <div class="bulk-buttons">
            <button type="button" class="btn btn-outline-warning" onclick="bulkToggleStatus()">
                <i class="fas fa-toggle-on"></i>
                Thay đổi trạng thái
            </button>
            <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                <i class="fas fa-trash"></i>
                Xóa đã chọn
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                <i class="fas fa-times"></i>
                Bỏ chọn
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Xác nhận xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteMessage">Bạn có chắc chắn muốn xóa người dùng này?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Hủy
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash"></i>
                    Xóa người dùng
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-toggle-on text-warning"></i>
                    Thay đổi trạng thái
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="statusMessage">Bạn có chắc chắn muốn thay đổi trạng thái người dùng này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Hủy
                </button>
                <button type="button" class="btn btn-warning" id="confirmStatusChange">
                    <i class="fas fa-toggle-on"></i>
                    Thay đổi trạng thái
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user text-info"></i>
                    Chi tiết người dùng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="action" id="actionType">
    <input type="hidden" name="user_id" id="actionUserId">
    <input type="hidden" name="new_role" id="actionNewRole">
</form>