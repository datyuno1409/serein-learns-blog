# Hướng dẫn Deploy PHP Project lên Heroku

## Prerequisites

1. **Tài khoản Heroku đã xác minh**
   - Truy cập https://heroku.com/verify để xác minh tài khoản
   - Thêm thông tin thanh toán (không tính phí cho free tier)
   - Điều này bắt buộc để tạo app trên Heroku

2. **Heroku CLI đã cài đặt** ✅
   ```bash
   npm install -g heroku
   ```

3. **Git repository** ✅
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   ```

## Các file đã chuẩn bị ✅

- `Procfile` - Cấu hình web server
- `composer.json` - Dependencies PHP
- `apache_app.conf` - Cấu hình Apache
- `.env.heroku` - Template biến môi trường
- `config/heroku_database.php` - Database adapter
- `heroku_setup.php` - Database migration script
- `database/schema_mysql.sql` - MySQL schema
- `database/schema_postgresql.sql` - PostgreSQL schema

## Bước 1: Xác minh tài khoản Heroku

**QUAN TRỌNG**: Bạn cần xác minh tài khoản Heroku trước khi tiếp tục:

1. Truy cập: https://heroku.com/verify
2. Thêm thông tin thanh toán (thẻ tín dụng/debit)
3. Heroku sẽ không tính phí cho free tier
4. Việc này chỉ để xác minh danh tính

## Bước 2: Tạo Heroku App (sau khi xác minh)

```bash
# Đăng nhập Heroku (đã thực hiện)
heroku login

# Tạo app mới
heroku create serein-learns-blog
# Hoặc nếu tên đã tồn tại:
heroku create serein-learns-blog-[random-suffix]
```

## Bước 3: Thêm Database Add-on

```bash
# Thêm PostgreSQL (miễn phí)
heroku addons:create heroku-postgresql:essential-0

# Hoặc thêm MySQL (nếu muốn)
heroku addons:create jawsdb:kitefin
```

## Bước 4: Cấu hình biến môi trường

```bash
# Set các biến môi trường cơ bản
heroku config:set APP_ENV=production
heroku config:set DEBUG=false
heroku config:set SESSION_LIFETIME=1440
heroku config:set MAX_FILE_SIZE=5242880

# APP_URL sẽ được set tự động sau khi deploy
```

## Bước 5: Deploy ứng dụng

```bash
# Thêm Heroku remote (nếu chưa có)
heroku git:remote -a your-app-name

# Deploy
git push heroku main
# Hoặc nếu branch khác:
git push heroku your-branch:main
```

## Bước 6: Chạy Database Migration

```bash
# Chạy setup database
heroku run php heroku_setup.php

# Kiểm tra database
heroku pg:info  # Cho PostgreSQL
# hoặc
heroku config:get JAWSDB_URL  # Cho MySQL
```

## Bước 7: Mở ứng dụng

```bash
heroku open
```

## Troubleshooting

### Lỗi thường gặp:

1. **Account verification required**
   - Xác minh tài khoản tại https://heroku.com/verify

2. **App name already exists**
   ```bash
   heroku create serein-learns-blog-$(date +%s)
   ```

3. **Database connection error**
   ```bash
   heroku config  # Kiểm tra DATABASE_URL
   heroku logs --tail  # Xem logs
   ```

4. **Build failed**
   ```bash
   heroku logs --tail
   # Kiểm tra composer.json và Procfile
   ```

## Monitoring

```bash
# Xem logs
heroku logs --tail

# Kiểm tra status
heroku ps

# Restart app
heroku restart
```

## Biến môi trường quan trọng

- `DATABASE_URL` - Tự động set bởi database add-on
- `APP_URL` - URL của ứng dụng trên Heroku
- `APP_ENV` - production
- `DEBUG` - false
- `PORT` - Tự động set bởi Heroku

## Notes

- Heroku sử dụng ephemeral filesystem - files upload sẽ bị mất khi restart
- Sử dụng cloud storage (AWS S3, Cloudinary) cho file uploads
- Database sẽ sleep sau 30 phút không hoạt động (free tier)
- Ứng dụng sẽ sleep sau 30 phút không có traffic (free tier)

---

**Trạng thái hiện tại**: Đã chuẩn bị đầy đủ files, cần xác minh tài khoản Heroku để tiếp tục.