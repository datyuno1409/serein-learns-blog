<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $pdo->exec('DROP TABLE IF EXISTS articles_new');
    $pdo->exec('DROP TABLE IF EXISTS articles_temp');
    echo "Đã xóa các bảng tạm thời\n";
} catch(Exception $e) {
    echo 'Lỗi: ' . $e->getMessage() . "\n";
}
?>