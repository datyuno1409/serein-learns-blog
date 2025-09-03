<?php

// $pdo variable is passed from AdminController
if (!isset($pdo)) {
    die('Database connection not available');
}

require_once '../includes/auth.php';

checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/users');
    exit;
}

$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['error'] = 'ID người dùng không hợp lệ';
    header('Location: /admin/users');
    exit;
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Người dùng không tồn tại');
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ?");
    $stmt->execute([$user_id]);
    $post_count = $stmt->fetchColumn();
    
    if ($post_count > 0) {
        throw new Exception('Không thể xóa người dùng này vì có ' . $post_count . ' bài viết liên quan');
    }
    
    $stmt = $pdo->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Xóa người dùng "' . $user['username'] . '" thành công';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

header('Location: /admin/users');
exit;
?>