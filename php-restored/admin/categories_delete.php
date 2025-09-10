<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../helpers/auth_helper.php';
require_once '../includes/Language.php';

// Check if user is logged in and is admin
checkAdminAuth();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/categories');
    exit;
}

$category_id = $_POST['id'] ?? null;

if (!$category_id) {
    $_SESSION['error'] = 'ID danh mục không hợp lệ';
    header('Location: /admin/categories');
    exit;
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        throw new Exception('Danh mục không tồn tại');
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $post_count = $stmt->fetchColumn();
    
    if ($post_count > 0) {
        throw new Exception('Không thể xóa danh mục vì còn có ' . $post_count . ' bài viết thuộc danh mục này');
    }
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    
    $pdo->commit();
    
    $_SESSION['success'] = 'Xóa danh mục "' . htmlspecialchars($category['name']) . '" thành công';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

header('Location: /admin/categories');
exit;
?>
