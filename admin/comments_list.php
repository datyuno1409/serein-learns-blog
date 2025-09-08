<?php
// Set UTF-8 encoding
header('Content-Type: text/html; charset=utf-8');

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

if (!isLoggedIn() || !isAdmin()) {
    header('Location: /login');
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

if (isset($_POST['action']) && isset($_POST['comment_ids'])) {
    $action = $_POST['action'];
    $comment_ids = array_map('intval', $_POST['comment_ids']);
    
    if (!empty($comment_ids)) {
        try {
            if ($action === 'approve') {
                $placeholders = str_repeat('?,', count($comment_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $_SESSION['success_message'] = 'Đã duyệt ' . count($comment_ids) . ' bình luận.';
            } elseif ($action === 'reject') {
                $placeholders = str_repeat('?,', count($comment_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE comments SET status = 'rejected' WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $_SESSION['success_message'] = 'Đã từ chối ' . count($comment_ids) . ' bình luận.';
            } elseif ($action === 'delete') {
                $placeholders = str_repeat('?,', count($comment_ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM comments WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $_SESSION['success_message'] = 'Đã xóa ' . count($comment_ids) . ' bình luận.';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
        }
    }
    
    header('Location: /admin/comments');
    exit;
}

if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    
    try {
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
        $stmt->execute([$delete_id]);
        $_SESSION['success_message'] = 'Đã xóa bình luận thành công!';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Lỗi khi xóa bình luận: ' . $e->getMessage();
    }
    
    header('Location: /admin/comments');
    exit;
}

try {
    $where_conditions = [];
    $params = [];
    
    if ($search) {
        $where_conditions[] = '(c.content LIKE ? OR c.author_name LIKE ? OR c.author_email LIKE ? OR p.title LIKE ?)';
        $search_param = '%' . $search . '%';
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }
    
    if ($status_filter && in_array($status_filter, ['pending', 'approved', 'rejected'])) {
        $where_conditions[] = 'c.status = ?';
        $params[] = $status_filter;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $count_sql = "SELECT COUNT(*) FROM comments c 
                  LEFT JOIN posts p ON c.post_id = p.id 
                  $where_clause";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_comments = $stmt->fetchColumn();
    $total_pages = ceil($total_comments / $limit);
    
    $sql = "SELECT c.*, p.title as post_title, p.slug as post_slug,
                   u.username as author_username
            FROM comments c 
            LEFT JOIN posts p ON c.post_id = p.id 
            LEFT JOIN users u ON c.user_id = u.id
            $where_clause 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $comments = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = 'Lỗi khi tải danh sách bình luận: ' . $e->getMessage();
    $comments = [];
    $total_pages = 1;
}

$active_menu = 'comments';
require_once __DIR__ . '/../views/layouts/admin_dashboard.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản lý bình luận</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bình luận</li>
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
                    <h3 class="card-title">Danh sách bình luận</h3>
                </div>
                
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Tìm kiếm bình luận..." value="<?= htmlspecialchars($search) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Chờ duyệt</option>
                                    <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Đã duyệt</option>
                                    <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Đã từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Lọc</button>
                            </div>
                            <?php if ($search || $status_filter): ?>
                            <div class="col-md-3">
                                <a href="/admin/comments" class="btn btn-secondary">Xóa bộ lọc</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if (empty($comments)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5>Chưa có bình luận nào</h5>
                            <p class="text-muted">Các bình luận từ người dùng sẽ hiển thị ở đây.</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" id="bulk-form">
                            <div class="mb-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="bulkAction('approve')">
                                        <i class="fas fa-check"></i> Duyệt đã chọn
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkAction('reject')">
                                        <i class="fas fa-times"></i> Từ chối đã chọn
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                                        <i class="fas fa-trash"></i> Xóa đã chọn
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="30">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>Tác giả</th>
                                            <th>Nội dung</th>
                                            <th>Bài viết</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th width="120">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($comments as $comment): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="comment_ids[]" value="<?= $comment['id'] ?>" class="comment-checkbox">
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($comment['author_name'] ?: $comment['author_username'] ?: 'Ẩn danh') ?></strong>
                                                <?php if ($comment['author_email']): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($comment['author_email']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px; word-wrap: break-word;">
                                                    <?= nl2br(htmlspecialchars(substr($comment['content'], 0, 150))) ?>
                                                    <?php if (strlen($comment['content']) > 150): ?>
                                                        <span class="text-muted">...</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($comment['post_title']): ?>
                                                    <a href="/posts/<?= $comment['post_slug'] ?>" target="_blank">
                                                        <?= htmlspecialchars($comment['post_title']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Bài viết đã bị xóa</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status_class = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success', 
                                                    'rejected' => 'danger'
                                                ];
                                                $status_text = [
                                                    'pending' => 'Chờ duyệt',
                                                    'approved' => 'Đã duyệt',
                                                    'rejected' => 'Đã từ chối'
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $status_class[$comment['status']] ?? 'secondary' ?>">
                                                    <?= $status_text[$comment['status']] ?? $comment['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($comment['status'] !== 'approved'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="approve">
                                                        <input type="hidden" name="comment_ids[]" value="<?= $comment['id'] ?>">
                                                        <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                                        <input type="hidden" name="delete_id" value="<?= $comment['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Phân trang">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status_filter ? '&status=' . urlencode($status_filter) : '' ?>">
                                            Trước
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status_filter ? '&status=' . urlencode($status_filter) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $status_filter ? '&status=' . urlencode($status_filter) : '' ?>">
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

<script>
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.comment-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Vui lòng chọn ít nhất một bình luận.');
        return;
    }
    
    let message = '';
    switch(action) {
        case 'approve':
            message = 'Bạn có chắc muốn duyệt các bình luận đã chọn?';
            break;
        case 'reject':
            message = 'Bạn có chắc muốn từ chối các bình luận đã chọn?';
            break;
        case 'delete':
            message = 'Bạn có chắc muốn xóa các bình luận đã chọn? Hành động này không thể hoàn tác.';
            break;
    }
    
    if (confirm(message)) {
        const form = document.getElementById('bulk-form');
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        form.submit();
    }
}
</script>
