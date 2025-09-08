# Hướng Dẫn Deploy PHP Blog lên Railway

## 🚀 Tại Sao Chọn Railway?

- **Miễn phí**: 500 hours/tháng (đủ cho development)
- **Tự động**: Detect PHP và build tự động
- **Database**: MySQL tích hợp sẵn
- **GitHub**: Deploy trực tiếp từ repository

## 📋 Bước 1: Chuẩn Bị Project

### 1.1 Tạo composer.json

```json
{
  "name": "serein/blog",
  "description": "Personal Blog with PHP",
  "type": "project",
  "require": {
    "php": "^8.0"
  },
  "scripts": {
    "start": "php -S 0.0.0.0:$PORT -t ."
  }
}
```

### 1.2 Tạo .htaccess cho production

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### 1.3 Cập nhật config database

Trong `config/database.php`:

```php
<?php
return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_NAME'] ?? 'blog_db',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

## 🚀 Bước 2: Deploy lên Railway

### 2.1 Tạo tài khoản Railway

1. Truy cập [railway.app](https://railway.app)
2. Đăng ký bằng GitHub account
3. Verify email

### 2.2 Tạo project mới

1. Click **"New Project"**
2. Chọn **"Deploy from GitHub repo"**
3. Authorize Railway truy cập GitHub
4. Chọn repository `serein-learns-blog`

### 2.3 Thêm MySQL Database

1. Trong project dashboard, click **"+ New"**
2. Chọn **"Database"** → **"Add MySQL"**
3. Đợi database khởi tạo (2-3 phút)

### 2.4 Cấu hình Environment Variables

1. Click vào **PHP service** (không phải database)
2. Vào tab **"Variables"**
3. Thêm các biến:

```
DB_HOST=mysql.railway.internal
DB_NAME=railway
DB_USER=root
DB_PASS=[auto-generated]
APP_ENV=production
PORT=8080
```

**Lưu ý**: Railway sẽ tự động tạo `DB_PASS`, copy từ MySQL service.

### 2.5 Connect Database Variables

1. Click **"+ New Variable"**
2. Chọn **"Reference"**
3. Chọn MySQL service
4. Map các variables:
   - `MYSQL_HOST` → `DB_HOST`
   - `MYSQL_DATABASE` → `DB_NAME`
   - `MYSQL_USER` → `DB_USER`
   - `MYSQL_PASSWORD` → `DB_PASS`

## 🗄️ Bước 3: Setup Database

### 3.1 Import database schema

1. Trong MySQL service, click **"Connect"**
2. Copy connection string
3. Sử dụng MySQL client hoặc phpMyAdmin
4. Import file `database/schema.sql`

### 3.2 Tạo migration script (tùy chọn)

```php
<?php
// migrate.php
require_once 'config/database.php';

try {
    $config = require 'config/database.php';
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        $config['options']
    );
    
    // Run your SQL migrations here
    $sql = file_get_contents('database/schema.sql');
    $pdo->exec($sql);
    
    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
```

## 🔧 Bước 4: Cấu hình Domain

### 4.1 Custom Domain (tùy chọn)

1. Trong project settings
2. Tab **"Domains"**
3. Click **"Custom Domain"**
4. Nhập domain của bạn
5. Cấu hình DNS records

### 4.2 Railway Domain

Railway sẽ tự động tạo domain dạng:
`your-project-name.up.railway.app`

## 🚨 Troubleshooting

### Lỗi thường gặp:

1. **"Application failed to respond"**
   - Kiểm tra PORT environment variable
   - Đảm bảo PHP listen trên `0.0.0.0:$PORT`

2. **Database connection failed**
   - Verify environment variables
   - Check MySQL service status
   - Ensure database is running

3. **File permissions**
   - Railway tự động handle permissions
   - Không cần chmod

### Debug logs:

1. Click vào service
2. Tab **"Deployments"**
3. Click vào deployment mới nhất
4. Xem **"Build Logs"** và **"Deploy Logs"**

## 📊 Monitoring

### Metrics có sẵn:
- CPU usage
- Memory usage
- Network traffic
- Response times

### Logs:
- Real-time logs
- Error tracking
- Performance monitoring

## 💰 Pricing

- **Hobby Plan**: $0 (500 hours/tháng)
- **Pro Plan**: $20/tháng (unlimited)
- **Team Plan**: $100/tháng

## 🔄 Auto-Deploy

Railway tự động deploy khi:
- Push code lên GitHub
- Merge pull request
- Update environment variables

---

**🎉 Xong! Ứng dụng của bạn đã live tại Railway domain.**

*Cần hỗ trợ thêm? Hỏi tôi bất kỳ lúc nào!*