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

// Chỉ cho phép POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/posts');
    exit;
}

// Lấy ID bài viết
$post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$post_id) {
    $_SESSION['error_message'] = 'ID bài viết không hợp lệ.';
    header('Location: /admin/posts');
    exit;
}

try {
    // Kiểm tra bài viết có tồn tại không
    $stmt = $pdo->prepare('SELECT id, title FROM posts WHERE id = ?');
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $_SESSION['error_message'] = 'Không tìm thấy bài viết cần xóa.';
        header('Location: /admin/posts');
        exit;
    }
    
    // Bắt đầu transaction
    $pdo->beginTransaction();
    
    // Xóa các bình luận liên quan (nếu có)
    $stmt = $pdo->prepare('DELETE FROM comments WHERE post_id = ?');
    $stmt->execute([$post_id]);
    
    // Xóa các tag liên quan (nếu có bảng post_tags)
    $stmt = $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?');
    $stmt->execute([$post_id]);
    
    // Xóa bài viết
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$post_id]);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['success_message'] = 'Đã xóa bài viết "' . htmlspecialchars($post['title']) . '" thành công!';
    
} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log('Delete post error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'Lỗi khi xóa bài viết: ' . $e->getMessage();
}

// Chuyển hướng về trang danh sách bài viết
header('Location: /admin/posts');
exit;
?>