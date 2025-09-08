<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Cấu trúc bảng comments ===\n";
    $stmt = $pdo->query('PRAGMA table_info(comments)');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Column: {$row['name']}, Type: {$row['type']}, NotNull: {$row['notnull']}, Default: {$row['dflt_value']}\n";
    }
    
    echo "\n=== Dữ liệu comments (5 bản ghi đầu) ===\n";
    $stmt = $pdo->query('SELECT id, author_name, content, article_id, status, created_at FROM comments LIMIT 5');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}\n";
        echo "Author: {$row['author_name']}\n";
        echo "Content: {$row['content']}\n";
        echo "Article ID: {$row['article_id']}\n";
        echo "Status: {$row['status']}\n";
        echo "Created: {$row['created_at']}\n";
        echo "---\n";
    }
    
    echo "\n=== Kiểm tra encoding ===\n";
    $stmt = $pdo->query('SELECT content FROM comments WHERE content LIKE "%ệ%" OR content LIKE "%ư%" OR content LIKE "%ă%" LIMIT 3');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Content: {$row['content']}\n";
        echo "Encoding: " . mb_detect_encoding($row['content']) . "\n";
        echo "---\n";
    }
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?>