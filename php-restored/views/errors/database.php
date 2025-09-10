<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi Kết Nối Cơ Sở Dữ Liệu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .error-container {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 20px;
            margin-top: 30px;
        }
        h1 {
            color: #721c24;
        }
        .solutions {
            background-color: #e2e3e5;
            border: 1px solid #d6d8db;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        code {
            background-color: #f8f9fa;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Lỗi Kết Nối Cơ Sở Dữ Liệu</h1>
        <p>Ứng dụng không thể kết nối đến cơ sở dữ liệu MySQL. Điều này có thể do một trong các nguyên nhân sau:</p>
        <ul>
            <li>Dịch vụ MySQL/MariaDB không được khởi động</li>
            <li>Thông tin kết nối trong tệp cấu hình không chính xác</li>
            <li>Cơ sở dữ liệu chưa được tạo</li>
            <li>Người dùng hoặc mật khẩu không chính xác</li>
        </ul>
        
        <?php if (isset($error_message) && !empty($error_message)): ?>
        <p><strong>Chi tiết lỗi:</strong> <?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="solutions">
        <h2>Giải pháp có thể:</h2>
        <ol>
            <li>Khởi động dịch vụ MySQL/MariaDB trên máy chủ</li>
            <li>Kiểm tra thông tin kết nối trong tệp <code>config/config.php</code>:
                <ul>
                    <li>Host: <code><?php echo htmlspecialchars(DB_HOST); ?></code></li>
                    <li>User: <code><?php echo htmlspecialchars(DB_USER); ?></code></li>
                    <li>Database: <code><?php echo htmlspecialchars(DB_NAME); ?></code></li>
                </ul>
            </li>
            <li>Chạy script thiết lập cơ sở dữ liệu: <code>php setup_database.php</code></li>
        </ol>
        
        <p>Sau khi giải quyết vấn đề, hãy <a href="/">làm mới trang</a> để thử lại.</p>
    </div>
</body>
</html>