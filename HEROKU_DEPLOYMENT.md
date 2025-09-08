# Hướng Dẫn Deploy PHP Blog lên Heroku

## 🚨 Lưu Ý Quan Trọng

**Heroku đã ngừng free tier từ 28/11/2022**
- Hobby Dyno: $7/tháng
- ClearDB MySQL: $9.99/tháng
- **Tổng chi phí tối thiểu: ~$17/tháng**

## 📋 Chuẩn Bị

### 1. Cài đặt Heroku CLI

**Windows:**
```powershell
# Tải từ https://devcenter.heroku.com/articles/heroku-cli
# Hoặc dùng Chocolatey
choco install heroku-cli
```

**Verify installation:**
```bash
heroku --version
```

### 2. Login Heroku

```bash
heroku login
```

## 🚀 Deploy Steps

### Bước 1: Tạo Heroku App

```bash
# Trong thư mục project
heroku create your-blog-name

# Hoặc để Heroku tự tạo tên
heroku create
```

### Bước 2: Thêm MySQL Database

```bash
# Thêm ClearDB MySQL addon
heroku addons:create cleardb:ignite

# Lấy database URL
heroku config:get CLEARDB_DATABASE_URL
```

### Bước 3: Cấu hình Environment Variables

```bash
# Parse CLEARDB_DATABASE_URL và set riêng từng biến
# URL format: mysql://username:password@hostname/database_name?reconnect=true

heroku config:set DB_HOST=your-cleardb-host
heroku config:set DB_NAME=your-cleardb-database
heroku config:set DB_USER=your-cleardb-username
heroku config:set DB_PASS=your-cleardb-password
heroku config:set APP_ENV=production
heroku config:set APP_URL=https://your-app-name.herokuapp.com
```

### Bước 4: Cập nhật composer.json

**Đã được cập nhật với:**
- PHP version requirement
- Start script
- Platform config

### Bước 5: Tạo .htaccess cho Apache

```apache
# .htaccess
RewriteEngine On

# Handle Angular and Vue.js routes
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:;"

# Compress files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
</IfModule>
```

### Bước 6: Deploy

```bash
# Add và commit changes
git add .
git commit -m "Prepare for Heroku deployment"

# Push to Heroku
git push heroku main

# Hoặc nếu branch khác
git push heroku your-branch:main
```

### Bước 7: Setup Database

```bash
# Chạy migration (nếu có)
heroku run php migrate.php

# Hoặc import SQL trực tiếp
# Sử dụng MySQL client với CLEARDB_DATABASE_URL
```

## 🔧 Troubleshooting

### 1. Application Error

```bash
# Xem logs
heroku logs --tail

# Restart app
heroku restart
```

### 2. Database Connection Issues

```bash
# Kiểm tra config vars
heroku config

# Test database connection
heroku run php -r "echo 'DB Test: ' . DB_HOST;"
```

### 3. File Upload Issues

**Heroku filesystem là ephemeral**, files upload sẽ bị mất khi dyno restart.

**Giải pháp:**
- Sử dụng AWS S3
- Cloudinary
- Google Cloud Storage

### 4. Session Issues

```bash
# Sử dụng database sessions thay vì file sessions
heroku config:set SESSION_DRIVER=database
```

## 📊 Monitoring

```bash
# Xem metrics
heroku ps

# Xem logs real-time
heroku logs --tail

# Xem specific dyno
heroku logs --dyno web.1
```

## 💰 Cost Optimization

### Alternatives to ClearDB:

1. **JawsDB MySQL** ($9.99/tháng)
2. **PlanetScale** (Free tier có sẵn)
3. **External MySQL** (DigitalOcean $15/tháng)

### Scaling:

```bash
# Scale dynos
heroku ps:scale web=1

# Upgrade dyno type
heroku ps:type web=standard-1x
```

## 🔄 CI/CD với GitHub

1. Connect GitHub repo trong Heroku Dashboard
2. Enable automatic deploys
3. Enable "Wait for CI to pass before deploy"

## 🎯 Production Checklist

- [ ] Environment variables configured
- [ ] Database migrated
- [ ] SSL enabled (automatic với Heroku)
- [ ] Custom domain configured (optional)
- [ ] Error logging setup
- [ ] Backup strategy
- [ ] Monitoring alerts

---

**⚠️ Khuyến nghị: Sử dụng Railway thay vì Heroku để tiết kiệm chi phí!**