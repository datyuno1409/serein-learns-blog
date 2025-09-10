<?php
require_once '../db.php';

// Kiểm tra phương thức gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts_list.php');
    exit;
}

// Lấy dữ liệu từ form
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// Kiểm tra dữ liệu
if (empty($title) || empty($content)) {
    echo "Vui lòng điền đầy đủ thông tin!";
    exit;
}

try {
    // Thêm bài viết mới vào cơ sở dữ liệu
    $stmt = $pdo->prepare('INSERT INTO posts (title, content, created_at) VALUES (?, ?, NOW())');
    $stmt->execute([$title, $content]);
    
    // Chuyển hướng về trang danh sách
    header('Location: posts_list.php');
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
