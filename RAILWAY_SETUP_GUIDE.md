# Hướng dẫn khắc phục lỗi Railway Deployment

## Lỗi gặp phải
Từ screenshot, lỗi chính là Railway không thể build được ứng dụng PHP do thiếu cấu hình build process.

## Các file đã được tạo/cập nhật để khắc phục:

### 1. `railway.json` - Cấu hình Railway
```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS",
    "buildCommand": "composer install --no-dev --optimize-autoloader"
  },
  "deploy": {
    "startCommand": "vendor/bin/heroku-php-apache2",
    "healthcheckPath": "/",
    "healthcheckTimeout": 100
  }
}
```

### 2. `nixpacks.toml` - Cấu hình Nixpacks
```toml
[phases.setup]
nixPkgs = ['php82', 'php82Packages.composer']

[phases.install]
cmds = ['composer install --no-dev --optimize-autoloader']

[phases.build]
cmds = ['echo "Build completed"']

[start]
cmd = 'vendor/bin/heroku-php-apache2'

[variables]
PHP_VERSION = '8.2'
COMPOSER_ALLOW_SUPERUSER = '1'
APP_ENV = 'production'
APP_DEBUG = 'false'
```

### 3. `composer.json` - Đã cập nhật
- Thêm `heroku/heroku-buildpack-php` dependency
- Cập nhật PHP version lên 8.2
- Thêm post-install script
- Tối ưu hóa autoloader

### 4. `apache_app.conf` - Cấu hình Apache
- URL rewriting cho PHP routes
- Security headers
- Compression và caching
- PHP settings

### 5. `.railwayignore` - Loại trừ file không cần thiết

## Các bước tiếp theo trên Railway:

### Bước 1: Redeploy
1. Commit và push tất cả các file mới lên GitHub
2. Trên Railway dashboard, click "Deploy" để trigger build mới

### Bước 2: Cấu hình Environment Variables
Trên Railway dashboard, vào tab "Variables" và thêm:

```
APP_URL=https://your-project-name.up.railway.app
APP_ENV=production
DEBUG=0
SESSION_LIFETIME=7200
MAX_FILE_SIZE=5242880
PORT=8080
```

### Bước 3: Thêm MySQL Database
1. Click "+ New" → "Database" → "Add MySQL"
2. Railway sẽ tự động tạo các biến môi trường:
   - `MYSQL_URL`
   - `MYSQL_HOST`
   - `MYSQL_PORT`
   - `MYSQL_USER`
   - `MYSQL_PASSWORD`
   - `MYSQL_DATABASE`

### Bước 4: Import Database
1. Sử dụng Railway CLI hoặc phpMyAdmin để import file `database.sql`
2. Hoặc truy cập MySQL qua Railway dashboard

### Bước 5: Kiểm tra Logs
1. Vào tab "Deployments" để xem build logs
2. Vào tab "Observability" để xem runtime logs

## Troubleshooting

### Nếu vẫn gặp lỗi build:
1. Kiểm tra PHP version trong `composer.json`
2. Đảm bảo tất cả dependencies có trong `composer.json`
3. Kiểm tra syntax của `railway.json` và `nixpacks.toml`

### Nếu ứng dụng không chạy:
1. Kiểm tra environment variables
2. Kiểm tra database connection
3. Xem logs trong tab "Observability"

### Nếu gặp lỗi 500:
1. Bật debug mode tạm thời: `DEBUG=1`
2. Kiểm tra file permissions
3. Kiểm tra `.env` file có được tạo không

## Lưu ý quan trọng:
- Railway sử dụng Nixpacks để build PHP apps
- Cần có `composer.json` với đầy đủ dependencies
- File `.env` sẽ được tạo tự động từ `.env.example`
- Database credentials sẽ được Railway inject tự động
- Sử dụng `vendor/bin/heroku-php-apache2` để start web server