<?php
// Thông tin kết nối cơ sở dữ liệu
$host = 'localhost';
$dbname = 'blogdb';
$username = 'root';
$password = '';

try {
    // Kết nối PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Thiết lập chế độ báo lỗi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Thiết lập chế độ fetch mặc định là associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Hiển thị lỗi nếu kết nối thất bại
    die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
}
?>