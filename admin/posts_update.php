<?php
require_once '../db.php';

// Kiểm tra phương thức gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts_list.php');
    exit;
}

// Lấy dữ liệu từ form
$id = $_POST['id'] ?? 0;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// Kiểm tra dữ liệu
if (empty($id) || empty($title) || empty($content)) {
    echo "Vui lòng điền đầy đủ thông tin!";
    exit;
}

try {
    // Cập nhật bài viết trong cơ sở dữ liệu
    $stmt = $pdo->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
    $stmt->execute([$title, $content, $id]);
    
    // Chuyển hướng về trang danh sách
    header('Location: posts_list.php');
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>