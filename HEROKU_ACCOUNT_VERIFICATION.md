# Hướng Dẫn Xác Minh Tài Khoản Heroku

## Tình Trạng Hiện Tại

### ✅ Đã Hoàn Thành:
- Heroku CLI đã được cài đặt và cấu hình
- Đã đăng nhập vào tài khoản Heroku: `ngthanhdat.fudn@gmail.com`
- Tất cả file cấu hình Heroku đã được tạo
- Git repository đã sẵn sàng

### ❌ Vấn Đề Gặp Phải:
- **Tài khoản Heroku chưa được xác minh** (bước bắt buộc)
- Script deployment đã chạy nhưng thất bại do không thể tạo app
- Các lệnh `heroku create` đều trả về lỗi yêu cầu xác minh tài khoản

## Lý Do Cần Xác Minh

Heroku yêu cầu xác minh tài khoản để:
- Tạo ứng dụng mới
- Sử dụng add-ons (database, cache, etc.)
- Tránh spam và lạm dụng dịch vụ

## Cách Xác Minh Tài Khoản

### Bước 1: Truy Cập Trang Xác Minh
```
https://heroku.com/verify
```

### Bước 2: Thêm Thông Tin Thanh Toán
- Đăng nhập vào tài khoản Heroku
- Vào **Account Settings** > **Billing**
- Thêm thẻ tín dụng hoặc thẻ ghi nợ
- **Lưu ý:** Không có phí cho việc xác minh này

### Bước 3: Xác Nhận Xác Minh
Sau khi thêm thông tin thanh toán:
- Tài khoản sẽ được xác minh tự động
- Bạn có thể tạo ứng dụng và sử dụng add-ons

## Sau Khi Xác Minh

### Chạy Script Tự Động
```powershell
.\deploy_to_heroku.ps1
```

### Hoặc Deploy Thủ Công

1. **Tạo Heroku App:**
```bash
heroku create serein-learns-blog
# Hoặc với tên tự động:
heroku create
```

2. **Thêm PostgreSQL Database:**
```bash
heroku addons:create heroku-postgresql:essential-0
```

3. **Cấu Hình Biến Môi Trường:**
```bash
heroku config:set APP_ENV=production
heroku config:set DEBUG=false
heroku config:set SESSION_LIFETIME=1440
heroku config:set MAX_FILE_SIZE=5242880
```

4. **Deploy Ứng Dụng:**
```bash
git push heroku main
```

5. **Thiết Lập Database:**
```bash
heroku run php heroku_setup.php
```

6. **Mở Ứng Dụng:**
```bash
heroku open
```

## Các Lệnh Hữu Ích

```bash
# Xem logs
heroku logs --tail

# Kiểm tra trạng thái app
heroku ps

# Xem cấu hình
heroku config

# Restart app
heroku restart

# Xem thông tin app
heroku info
```

## Xử Lý Sự Cố

### Nếu Vẫn Gặp Lỗi Xác Minh
1. Đảm bảo thẻ tín dụng hợp lệ
2. Kiểm tra email xác nhận từ Heroku
3. Đợi vài phút để hệ thống cập nhật
4. Thử đăng xuất và đăng nhập lại:
```bash
heroku logout
heroku login
```

### Nếu Deploy Thất Bại
1. Kiểm tra logs: `heroku logs --tail`
2. Đảm bảo tất cả file cần thiết đã commit
3. Kiểm tra Procfile và composer.json

## Liên Hệ Hỗ Trợ

Nếu gặp vấn đề:
- Heroku Support: https://help.heroku.com/
- Heroku Dev Center: https://devcenter.heroku.com/

---

**Ghi Chú:** Việc xác minh tài khoản là bước bắt buộc và chỉ cần thực hiện một lần. Sau khi xác minh, bạn có thể tạo nhiều ứng dụng và sử dụng đầy đủ tính năng của Heroku.