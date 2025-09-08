<?php
// SQLite database connection
$dbPath = __DIR__ . '/blog.sqlite';

try {
    // Create database file if it doesn't exist
    if (!file_exists($dbPath)) {
        touch($dbPath);
    }
    
    // Kết nối PDO với SQLite
    $pdo = new PDO("sqlite:" . $dbPath);
    
    // Thiết lập chế độ báo lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Thiết lập chế độ fetch mặc định là associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable foreign key constraints
    $pdo->exec("PRAGMA foreign_keys = ON");
    
} catch(PDOException $e) {
    // Hiển thị lỗi nếu kết nối thất bại
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>