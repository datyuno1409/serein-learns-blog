<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA busy_timeout = 30000');
    
    echo "=== THÊM CÁC CỘT THIẾU VÀO BẢNG ARTICLES ===\n";
    
    // Kiểm tra các cột hiện có
    $stmt = $pdo->query("PRAGMA table_info(articles)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    
    echo "Các cột hiện có: " . implode(', ', $columns) . "\n\n";
    
    // Thêm cột slug nếu chưa có (không có UNIQUE constraint)
    if (!in_array('slug', $columns)) {
        echo "Thêm cột slug...\n";
        $pdo->exec("ALTER TABLE articles ADD COLUMN slug VARCHAR(255)");
        echo "- Đã thêm cột slug\n";
    } else {
        echo "Cột slug đã tồn tại\n";
    }
    
    // Thêm cột image nếu chưa có
    if (!in_array('image', $columns)) {
        echo "Thêm cột image...\n";
        $pdo->exec("ALTER TABLE articles ADD COLUMN image VARCHAR(255)");
        echo "- Đã thêm cột image\n";
    } else {
        echo "Cột image đã tồn tại\n";
    }
    
    // Thêm cột views nếu chưa có
    if (!in_array('views', $columns)) {
        echo "Thêm cột views...\n";
        $pdo->exec("ALTER TABLE articles ADD COLUMN views INTEGER DEFAULT 0");
        echo "- Đã thêm cột views\n";
    } else {
        echo "Cột views đã tồn tại\n";
    }
    
    echo "\n=== HOÀN TẤT THÊM CỘT ===\n";
    
    // Hiển thị cấu trúc bảng mới
    echo "\nCấu trúc bảng articles sau khi cập nhật:\n";
    $stmt = $pdo->query("PRAGMA table_info(articles)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        printf("%-15s %-15s %s\n", $row['name'], $row['type'], $row['notnull'] ? 'NOT NULL' : 'NULL');
    }
    
} catch(Exception $e) {
    echo 'Lỗi: ' . $e->getMessage() . "\n";
    echo 'Stack trace: ' . $e->getTraceAsString() . "\n";
}
?>