<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

requireAdmin();

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

$id = intval($_GET['id']);

try {
    $db = new Database();
    $pdo = $db->connect();
    
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as category_name, u.username as author_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        LEFT JOIN users u ON a.user_id = u.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($article) {
        echo json_encode([
            'success' => true,
            'article' => $article
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy bài viết'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
?>