# Hướng Dẫn Triển Khai Blog lên InfinityFree

## Tổng Quan
Hướng dẫn này sẽ giúp bạn triển khai blog PHP lên InfinityFree hosting miễn phí với MySQL database.

## Yêu Cầu Trước Khi Triển Khai
- [x] Tài khoản InfinityFree đã được tạo
- [x] Domain đã được cấu hình (serein.lovestoblog.com)
- [x] Truy cập vào Control Panel và File Manager

## Bước 1: Chuẩn Bị Database

### 1.1 Tạo MySQL Database
1. Đăng nhập vào InfinityFree Control Panel
2. Vào **MySQL Databases**
3. Tạo database mới:
   - Database Name: `if0_39889420_serein_blog`
   - Username: `if0_39889420`
   - Password: (tạo password mạnh)
4. Ghi lại thông tin:
   - Host: `sql200.infinityfree.com`
   - Database: `if0_39889420_serein_blog`
   - Username: `if0_39889420`
   - Password: [password bạn tạo]

### 1.2 Import Database Schema
1. Vào **phpMyAdmin** từ Control Panel
2. Chọn database `if0_39889420_serein_blog`
3. Vào tab **Import**
4. Upload file `database/schema_mysql.sql`
5. Click **Go** để import

## Bước 2: Cấu Hình Files

### 2.1 Cập Nhật File .env
1. Copy file `.env.infinityfree` thành `.env`
2. Cập nhật thông tin database:
```env
DB_HOST="sql200.infinityfree.com"
DB_USER="if0_39889420"
DB_PASS="[your_password]"
DB_NAME="if0_39889420_serein_blog"
APP_URL="https://serein.lovestoblog.com"
```

### 2.2 Cập Nhật File index.php
Thêm vào đầu file `index.php`:
```php
<?php
// Load InfinityFree configuration
require_once __DIR__ . '/config/config_infinityfree.php';
```

## Bước 3: Upload Files

### 3.1 Danh Sách Files Cần Upload
**Thư mục gốc (htdocs):**
- `index.php`
- `.htaccess`
- `.env` (đã cập nhật)
- `router.php`
- `db.php`

**Thư mục con:**
- `admin/` (toàn bộ)
- `api/` (toàn bộ)
- `assets/` (toàn bộ)
- `config/` (toàn bộ)
- `controllers/` (toàn bộ)
- `helpers/` (toàn bộ)
- `includes/` (toàn bộ)
- `lang/` (toàn bộ)
- `models/` (toàn bộ)
- `views/` (toàn bộ)
- `uploads/` (chỉ thư mục trống với .htaccess)

**Files KHÔNG upload:**
- `blog.sqlite`
- `temp/`
- `backups/`
- `scripts/`
- `.git/`
- `README.md`
- `*.md` files
- `composer.*`

### 3.2 Cách Upload
**Sử dụng File Manager:**
1. Vào **File Manager** từ Control Panel
2. Navigate đến thư mục `htdocs`
3. Upload từng file/folder theo danh sách trên
4. Đảm bảo permissions đúng (755 cho folders, 644 cho files)

**Sử dụng FTP:**
- Host: `ftpupload.net`
- Username: `if0_39889420`
- Password: [FTP password]
- Port: 21

## Bước 4: Tạo Admin User

### 4.1 Chạy Script Tạo Admin
1. Truy cập: `https://serein.lovestoblog.com/database/create_admin.php`
2. Hoặc chạy SQL trực tiếp trong phpMyAdmin:
```sql
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@serein.lovestoblog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

## Bước 5: Cấu Hình SSL (Tùy Chọn)

1. Vào **SSL Certificates** trong Control Panel
2. Chọn **Let's Encrypt SSL**
3. Thêm domain: `serein.lovestoblog.com`
4. Chờ 5-10 phút để SSL được kích hoạt
5. Test HTTPS: `https://serein.lovestoblog.com`

## Bước 6: Kiểm Tra Triển Khai

### 6.1 Test Cơ Bản
- [ ] Trang chủ: `https://serein.lovestoblog.com`
- [ ] Admin login: `https://serein.lovestoblog.com/admin`
- [ ] Database connection test
- [ ] File upload test

### 6.2 Test Chức Năng
- [ ] Đăng nhập admin
- [ ] Tạo bài viết mới
- [ ] Upload hình ảnh
- [ ] Xem bài viết public
- [ ] Comment system

## Bước 7: Tối Ưu Hóa

### 7.1 Performance
- Bật compression trong .htaccess ✓
- Cấu hình cache headers ✓
- Optimize images trước khi upload

### 7.2 Security
- Thay đổi password admin
- Cập nhật .env với thông tin thực
- Kiểm tra file permissions
- Test security headers

## Troubleshooting

### Lỗi Database Connection
```
Solution: Kiểm tra lại thông tin DB trong .env
- Host: sql200.infinityfree.com
- Username/Database name phải chính xác
- Password phải đúng
```

### Lỗi 500 Internal Server Error
```
Solution: 
1. Kiểm tra .htaccess syntax
2. Kiểm tra PHP errors trong Control Panel > Error Logs
3. Đảm bảo file permissions đúng
```

### Lỗi File Upload
```
Solution:
1. Kiểm tra thư mục uploads/ có writable permission
2. Kiểm tra PHP upload limits trong .htaccess
3. Tạo .htaccess trong uploads/ để bảo mật
```

### Lỗi Session
```
Solution:
1. Tạo thư mục storage/sessions/
2. Set permission 755
3. Kiểm tra session configuration
```

## Thông Tin Hỗ Trợ

**InfinityFree Limits:**
- Disk Space: Unlimited
- Bandwidth: Unlimited  
- MySQL Databases: Unlimited
- Email Accounts: Unlimited
- Subdomains: Unlimited
- PHP Version: 8.x

**Contact:**
- InfinityFree Support: https://forum.infinityfree.net/
- Documentation: https://docs.infinityfree.net/

## Backup Strategy

### Tự Động Backup
1. Sử dụng Control Panel > Backup
2. Schedule weekly backups
3. Download backups định kỳ

### Manual Backup
1. Export database từ phpMyAdmin
2. Download files qua File Manager
3. Lưu trữ local và cloud

---

**Lưu Ý:** InfinityFree là hosting miễn phí nên có một số giới hạn về CPU và concurrent connections. Đối với production site có traffic cao, nên cân nhắc upgrade lên premium hosting.