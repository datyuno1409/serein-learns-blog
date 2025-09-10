# Hướng dẫn Test File .htaccess

## Yêu cầu hệ thống
File `.htaccess` chỉ hoạt động với **Apache Web Server**. Các server khác như Python HTTP Server, Node.js không hỗ trợ `.htaccess`.

## Cách test với Apache (XAMPP/WAMP/LAMP)

### 1. Cài đặt XAMPP
- Tải và cài đặt XAMPP từ https://www.apachefriends.org/
- Khởi động Apache từ XAMPP Control Panel

### 2. Copy project vào htdocs
```bash
# Copy toàn bộ project vào thư mục htdocs của XAMPP
cp -r serein-learns-blog/ C:/xampp/htdocs/
```

### 3. Test các URL
Truy cập các URL sau để kiểm tra:

#### ✅ URL không có đuôi .html (mong muốn)
- http://localhost/serein-learns-blog/about
- http://localhost/serein-learns-blog/articles  
- http://localhost/serein-learns-blog/projects
- http://localhost/serein-learns-blog/contact

#### ✅ Redirect 301 từ .html
- http://localhost/serein-learns-blog/about.html → http://localhost/serein-learns-blog/about
- http://localhost/serein-learns-blog/articles.html → http://localhost/serein-learns-blog/articles

#### ✅ Trang 404 tùy chỉnh
- http://localhost/serein-learns-blog/nonexistent-page

#### ✅ Chặn truy cập thư mục nhạy cảm
- http://localhost/serein-learns-blog/backup-php-project/ (403 Forbidden)
- http://localhost/serein-learns-blog/security/ (403 Forbidden)
- http://localhost/serein-learns-blog/models/ (403 Forbidden)

#### ✅ Chặn file cấu hình
- http://localhost/serein-learns-blog/.env (403 Forbidden)
- http://localhost/serein-learns-blog/package.json (403 Forbidden)
- http://localhost/serein-learns-blog/server.js (403 Forbidden)

## Tính năng đã implement trong .htaccess

### 1. URL Rewriting
- ✅ Bỏ đuôi .html khỏi URL
- ✅ Redirect 301 từ URL có .html sang không có đuôi
- ✅ Tự động load file abc.html khi truy cập /abc
- ✅ Xử lý trailing slash

### 2. Bảo mật
- ✅ Tắt directory listing (Options -Indexes)
- ✅ Chặn truy cập thư mục nhạy cảm
- ✅ Chặn file cấu hình và backup
- ✅ Chặn SQL Injection và XSS
- ✅ Chặn Path Traversal attacks
- ✅ Chặn User-Agent độc hại

### 3. SEO & Performance
- ✅ Security headers (HSTS, CSP, X-Frame-Options)
- ✅ Content Security Policy cho Tailwind CSS
- ✅ Error pages tùy chỉnh (403.html, 404.html, 500.html)
- ✅ Giới hạn kích thước request

### 4. Tương thích
- ✅ Không ảnh hưởng CSS/JS/Images
- ✅ Hỗ trợ Tailwind CSS từ CDN
- ✅ Giữ nguyên logic web

## Lưu ý quan trọng
- File `.htaccess` hiện tại đã hoàn chỉnh và sẵn sàng sử dụng
- Chỉ cần deploy lên Apache server để các tính năng hoạt động
- Đã tối ưu cho SEO và bảo mật
- Hỗ trợ đầy đủ các yêu cầu đã đề ra