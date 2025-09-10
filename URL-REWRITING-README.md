# URL Rewriting & .htaccess Configuration

## Tổng quan

Dự án này đã được cấu hình với hệ thống URL rewriting hoàn chỉnh để:
- Loại bỏ phần mở rộng `.html` khỏi URL
- Tự động redirect 301 từ URL có `.html` sang URL clean
- Xử lý trang lỗi tùy chỉnh
- Bảo mật website

## Cấu trúc Files

```
├── .htaccess              # Cấu hình Apache URL rewriting
├── 404.html              # Trang lỗi 404 tùy chỉnh (cyber theme)
├── 403.html              # Trang lỗi 403 tùy chỉnh
├── 500.html              # Trang lỗi 500 tùy chỉnh
├── test-server.js        # Node.js test server (mô phỏng .htaccess)
└── URL-REWRITING-README.md # Tài liệu này
```

## Tính năng URL Rewriting

### 1. Clean URLs
- **Trước:** `https://domain.com/about.html`
- **Sau:** `https://domain.com/about`

### 2. Redirect 301 (SEO Friendly)
- Tự động chuyển hướng từ URL có `.html` sang URL clean
- Giữ nguyên SEO ranking
- Tránh duplicate content

### 3. Internal Rewrite
- URL clean được rewrite thành file `.html` tương ứng
- Người dùng thấy URL clean, server load file `.html`

### 4. Error Handling
- **404:** Trang không tìm thấy với thiết kế cyber security
- **403:** Truy cập bị từ chối
- **500:** Lỗi server nội bộ

## Cấu hình .htaccess

### URL Rewriting Rules
```apache
# Redirect 301 từ .html về URL không có extension (phải đặt trước)
RewriteCond %{THE_REQUEST} \s/+([^.\s?]*)\.html[\s?] [NC]
RewriteRule ^ /%1? [R=301,L]

# URL Rewrite cho các trang HTML - loại bỏ phần mở rộng .html
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}\.html -f
RewriteRule ^([^./]+)/?$ $1.html [L]

# Xử lý trang chủ
RewriteCond %{REQUEST_URI} ^/?$
RewriteRule ^$ index.html [L]
```

### Security Features
- Tắt directory listing (`Options -Indexes`)
- Chặn truy cập file nhạy cảm
- Security headers (HSTS, CSP, X-Frame-Options)
- Chặn SQL injection và path traversal
- Rate limiting và bot protection

### Error Pages
```apache
ErrorDocument 403 /403.html
ErrorDocument 404 /404.html
ErrorDocument 500 /500.html
```

## Testing với Node.js Server

### Chạy Test Server
```bash
node test-server.js
```

### Test Cases
1. **Redirect 301:**
   ```bash
   curl -I http://localhost:3000/about.html
   # Kết quả: 301 Moved Permanently, Location: /about
   ```

2. **Clean URL:**
   ```bash
   curl http://localhost:3000/about
   # Kết quả: 200 OK, nội dung của about.html
   ```

3. **404 Error:**
   ```bash
   curl http://localhost:3000/nonexistent
   # Kết quả: 404 Not Found, nội dung của 404.html
   ```

4. **Static Assets:**
   ```bash
   curl http://localhost:3000/assets/css/style.css
   # Kết quả: 200 OK, không bị ảnh hưởng bởi rewriting
   ```

## Deployment

### Apache Server
1. Upload tất cả files lên server
2. Đảm bảo module `mod_rewrite` được enable
3. File `.htaccess` sẽ tự động hoạt động

### Nginx Server
Nếu sử dụng Nginx, cần convert rules sang Nginx syntax:
```nginx
# Redirect .html to clean URLs
location ~ ^/(.+)\.html$ {
    return 301 /$1;
}

# Rewrite clean URLs to .html files
location ~ ^/([^./]+)/?$ {
    try_files $uri $uri.html $uri/ =404;
}

# Error pages
error_page 404 /404.html;
error_page 403 /403.html;
error_page 500 /500.html;
```

## Kiểm tra hoạt động

### Trên Production
1. Truy cập `https://yourdomain.com/about.html`
   - Phải redirect về `https://yourdomain.com/about`

2. Truy cập `https://yourdomain.com/about`
   - Phải hiển thị nội dung trang about

3. Truy cập `https://yourdomain.com/nonexistent`
   - Phải hiển thị trang 404 tùy chỉnh

### Tools kiểm tra
- **Redirect Checker:** redirectchecker.org
- **SEO Tools:** Google Search Console
- **Browser DevTools:** Network tab để xem status codes

## Lưu ý quan trọng

1. **Thứ tự rules:** Redirect rules phải đặt trước rewrite rules
2. **Cache:** Clear browser cache khi test
3. **SEO:** 301 redirects giữ nguyên page rank
4. **Performance:** URL rewriting có overhead nhỏ
5. **Compatibility:** Hoạt động với tất cả modern browsers

## Troubleshooting

### Lỗi thường gặp
1. **500 Internal Server Error:**
   - Kiểm tra syntax .htaccess
   - Đảm bảo mod_rewrite enabled

2. **Redirect loop:**
   - Kiểm tra thứ tự rules
   - Đảm bảo conditions đúng

3. **CSS/JS không load:**
   - Kiểm tra relative paths
   - Đảm bảo static files không bị rewrite

### Debug
```apache
# Enable rewrite logging (chỉ dùng khi debug)
RewriteLog /var/log/apache2/rewrite.log
RewriteLogLevel 3
```

## Kết luận

Hệ thống URL rewriting đã được cấu hình hoàn chỉnh với:
- ✅ Clean URLs (loại bỏ .html)
- ✅ SEO-friendly 301 redirects
- ✅ Custom error pages
- ✅ Security protection
- ✅ Static assets không bị ảnh hưởng
- ✅ Test coverage hoàn chỉnh

Website sẵn sàng deploy lên production server với Apache hoặc Nginx.