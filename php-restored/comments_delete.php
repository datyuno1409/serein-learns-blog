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
    header('Location: /admin/comments');
    exit;
}

// Lấy ID bình luận
$comment_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$comment_id) {
    $_SESSION['error_message'] = 'ID bình luận không hợp lệ.';
    header('Location: /admin/comments');
    exit;
}

try {
    // Kiểm tra bình luận có tồn tại không
    $stmt = $pdo->prepare('SELECT id, content FROM comments WHERE id = ?');
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();
    
    if (!$comment) {
        $_SESSION['error_message'] = 'Không tìm thấy bình luận cần xóa.';
        header('Location: /admin/comments');
        exit;
    }
    
    // Xóa bình luận
    $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
    $result = $stmt->execute([$comment_id]);
    
    if ($result) {
        $_SESSION['success_message'] = 'Đã xóa bình luận thành công.';
    } else {
        $_SESSION['error_message'] = 'Có lỗi xảy ra khi xóa bình luận.';
    }
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
}

// Chuyển hướng về trang quản lý bình luận
header('Location: /admin/comments');
exit;
