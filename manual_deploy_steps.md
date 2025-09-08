# Manual Heroku Deployment Steps

## Bước thực hiện thủ công (sau khi xác minh tài khoản)

### 1. Xác minh tài khoản Heroku
```
Truy cập: https://heroku.com/verify
Thêm thông tin thanh toán (không tính phí cho free tier)
```

### 2. Tạo Heroku app
```bash
# Tạo app với tên unique
heroku create serein-learns-blog-$(date +%s)

# Hoặc để Heroku tự tạo tên
heroku create
```

### 3. Thêm database (tùy chọn)
```bash
# PostgreSQL (khuyến nghị)
heroku addons:create heroku-postgresql:essential-0

# Hoặc MySQL
heroku addons:create jawsdb:kitefin
```

### 4. Cấu hình biến môi trường
```bash
heroku config:set APP_ENV=production
heroku config:set DEBUG=false
heroku config:set SESSION_LIFETIME=1440
heroku config:set MAX_FILE_SIZE=5242880
```

### 5. Deploy
```bash
# Đảm bảo code đã được commit
git add .
git commit -m "Ready for Heroku deployment"

# Deploy
git push heroku main
```

### 6. Thiết lập database
```bash
# Chạy setup script
heroku run php heroku_setup.php

# Kiểm tra database
heroku config:get DATABASE_URL
```

### 7. Mở ứng dụng
```bash
heroku open
```

## Troubleshooting Commands

```bash
# Xem logs
heroku logs --tail

# Kiểm tra status
heroku ps

# Restart app
heroku restart

# Xem config
heroku config

# Chạy command trên Heroku
heroku run php -v

# Kết nối database (PostgreSQL)
heroku pg:psql
```

## Nếu gặp lỗi "Account verification required"

1. Truy cập https://heroku.com/verify
2. Thêm thẻ tín dụng/debit (không tính phí)
3. Xác nhận email nếu cần
4. Thử lại lệnh tạo app

## Files quan trọng đã chuẩn bị

- ✅ `Procfile` - Web server config
- ✅ `composer.json` - PHP dependencies
- ✅ `apache_app.conf` - Apache config
- ✅ `.env.heroku` - Environment template
- ✅ `config/heroku_database.php` - Database adapter
- ✅ `heroku_setup.php` - Database migration
- ✅ `database/schema_mysql.sql` - MySQL schema
- ✅ `database/schema_postgresql.sql` - PostgreSQL schema
- ✅ `deploy_to_heroku.ps1` - Automated deployment script

## Automated Script Usage

```powershell
# Chạy script tự động (sau khi xác minh tài khoản)
.\deploy_to_heroku.ps1
```

Script sẽ tự động:
- Kiểm tra Heroku CLI
- Tạo app với tên unique
- Thêm PostgreSQL
- Cấu hình biến môi trường
- Deploy code
- Thiết lập database
- Mở ứng dụng