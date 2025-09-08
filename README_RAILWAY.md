# 🚀 Deploy PHP Blog lên Railway - Hướng Dẫn Nhanh

## Bước 1: Chuẩn Bị

✅ **Đã hoàn thành:**
- `composer.json` với start script
- `.env.example` với config mẫu
- `.htaccess` với security headers
- `config/config.php` hỗ trợ environment variables

## Bước 2: Deploy

1. **Tạo tài khoản Railway**: [railway.app](https://railway.app)
2. **New Project** → **Deploy from GitHub repo**
3. **Chọn repository** `serein-learns-blog`
4. **Thêm MySQL**: New → Database → Add MySQL

## Bước 3: Cấu Hình Environment

**Trong PHP service (không phải MySQL service):**

```
DB_HOST=mysql.railway.internal
DB_NAME=railway
DB_USER=root
DB_PASS=[copy từ MySQL service]
APP_ENV=production
APP_URL=https://your-app.up.railway.app
PORT=8080
```

## Bước 4: Import Database

1. Connect vào MySQL service
2. Import file `database/schema.sql`
3. Tạo admin user bằng `database/create_admin.php`

## 🎉 Xong!

Railway sẽ tự động:
- Build PHP application
- Start server với `php -S 0.0.0.0:$PORT`
- Tạo public URL

**Chi tiết đầy đủ:** Xem `RAILWAY_DEPLOYMENT.md`

---

**💡 Lưu ý:**
- Free tier: 500 hours/tháng
- Auto-deploy khi push code
- Logs real-time có sẵn