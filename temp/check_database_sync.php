<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== KIỂM TRA CẤU TRÚC CƠ SỞ DỮ LIỆU ===\n";
    
    // Kiểm tra các bảng có tồn tại
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
    echo "\nCác bảng trong database:\n";
    while ($table = $tables->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $table['name'] . "\n";
    }
    
    // Kiểm tra cấu trúc bảng categories
    echo "\n=== CẤU TRÚC BẢNG CATEGORIES ===\n";
    $schema = $pdo->query("PRAGMA table_info(categories);");
    while ($column = $schema->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-15s %-15s %s\n", $column['name'], $column['type'], $column['notnull'] ? 'NOT NULL' : 'NULL');
    }
    
    // Kiểm tra dữ liệu categories
    echo "\n=== DỮ LIỆU BẢNG CATEGORIES ===\n";
    $categories = $pdo->query("SELECT * FROM categories ORDER BY id;");
    while ($cat = $categories->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %d | Name: %s | Description: %s\n", $cat['id'], $cat['name'], $cat['description'] ?? 'NULL');
    }
    
    // Kiểm tra cấu trúc bảng articles
    echo "\n=== CẤU TRÚC BẢNG ARTICLES ===\n";
    $schema = $pdo->query("PRAGMA table_info(articles);");
    while ($column = $schema->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("%-15s %-15s %s\n", $column['name'], $column['type'], $column['notnull'] ? 'NOT NULL' : 'NULL');
    }
    
    // Kiểm tra dữ liệu articles
    echo "\n=== DỮ LIỆU BẢNG ARTICLES ===\n";
    $articles = $pdo->query("SELECT id, title, category_id, status FROM articles ORDER BY id;");
    while ($art = $articles->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %d | Title: %s | Category ID: %s | Status: %s\n", 
            $art['id'], 
            substr($art['title'], 0, 30) . '...', 
            $art['category_id'] ?? 'NULL', 
            $art['status']
        );
    }
    
    // Kiểm tra mối quan hệ giữa articles và categories
    echo "\n=== KIỂM TRA MỐI QUAN HỆ ARTICLES-CATEGORIES ===\n";
    $relations = $pdo->query("
        SELECT a.id, a.title, a.category_id, c.name as category_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        ORDER BY a.id
    ");
    while ($rel = $relations->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("Article ID: %d | Category ID: %s | Category Name: %s\n", 
            $rel['id'], 
            $rel['category_id'] ?? 'NULL', 
            $rel['category_name'] ?? 'KHÔNG TÌM THẤY'
        );
    }
    
    // Kiểm tra các category_id không hợp lệ
    echo "\n=== KIỂM TRA CATEGORY_ID KHÔNG HỢP LỆ ===\n";
    $invalid = $pdo->query("
        SELECT a.id, a.title, a.category_id 
        FROM articles a 
        WHERE a.category_id IS NOT NULL 
        AND a.category_id NOT IN (SELECT id FROM categories)
    ");
    $hasInvalid = false;
    while ($inv = $invalid->fetch(PDO::FETCH_ASSOC)) {
        $hasInvalid = true;
        echo sprintf("Article ID: %d | Invalid Category ID: %d\n", $inv['id'], $inv['category_id']);
    }
    if (!$hasInvalid) {
        echo "Không có category_id không hợp lệ.\n";
    }
    
    // Thống kê tổng quan
    echo "\n=== THỐNG KÊ TỔNG QUAN ===\n";
    $stats = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch();
    echo "Tổng số categories: " . $stats['count'] . "\n";
    
    $stats = $pdo->query("SELECT COUNT(*) as count FROM articles")->fetch();
    echo "Tổng số articles: " . $stats['count'] . "\n";
    
    $stats = $pdo->query("SELECT COUNT(*) as count FROM articles WHERE category_id IS NULL")->fetch();
    echo "Articles không có category: " . $stats['count'] . "\n";
    
} catch(Exception $e) {
    echo 'Lỗi: ' . $e->getMessage() . "\n";
}
?>