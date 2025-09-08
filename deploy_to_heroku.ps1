# Heroku Deployment Script
# Chạy script này sau khi đã xác minh tài khoản Heroku

Write-Host "=== Heroku Deployment Script ===" -ForegroundColor Green
Write-Host "Đảm bảo bạn đã xác minh tài khoản Heroku tại: https://heroku.com/verify" -ForegroundColor Yellow
Write-Host ""

# Kiểm tra Heroku CLI
Write-Host "Kiểm tra Heroku CLI..." -ForegroundColor Cyan
try {
    $herokuVersion = heroku --version
    Write-Host "✓ Heroku CLI: $herokuVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Heroku CLI không được tìm thấy" -ForegroundColor Red
    exit 1
}

# Kiểm tra đăng nhập Heroku
Write-Host "Kiểm tra đăng nhập Heroku..." -ForegroundColor Cyan
try {
    $herokuAuth = heroku auth:whoami
    Write-Host "✓ Đã đăng nhập: $herokuAuth" -ForegroundColor Green
} catch {
    Write-Host "✗ Chưa đăng nhập Heroku. Chạy: heroku login" -ForegroundColor Red
    exit 1
}

# Tạo Heroku app
Write-Host "Tạo Heroku app..." -ForegroundColor Cyan
$appName = "serein-learns-blog-" + (Get-Date -Format "yyyyMMdd-HHmmss")
Write-Host "Tên app: $appName" -ForegroundColor Yellow

try {
    heroku create $appName
    Write-Host "✓ App đã được tạo: $appName" -ForegroundColor Green
} catch {
    Write-Host "✗ Không thể tạo app. Kiểm tra xem tài khoản đã được xác minh chưa." -ForegroundColor Red
    Write-Host "Truy cập: https://heroku.com/verify" -ForegroundColor Yellow
    exit 1
}

# Thêm PostgreSQL add-on
Write-Host "Thêm PostgreSQL database..." -ForegroundColor Cyan
try {
    heroku addons:create heroku-postgresql:essential-0 -a $appName
    Write-Host "✓ PostgreSQL đã được thêm" -ForegroundColor Green
} catch {
    Write-Host "⚠ Không thể thêm PostgreSQL. Tiếp tục với SQLite..." -ForegroundColor Yellow
}

# Set biến môi trường
Write-Host "Cấu hình biến môi trường..." -ForegroundColor Cyan
heroku config:set APP_ENV=production -a $appName
heroku config:set DEBUG=false -a $appName
heroku config:set SESSION_LIFETIME=1440 -a $appName
heroku config:set MAX_FILE_SIZE=5242880 -a $appName
Write-Host "✓ Biến môi trường đã được cấu hình" -ForegroundColor Green

# Deploy
Write-Host "Deploy ứng dụng..." -ForegroundColor Cyan
try {
    git push heroku main
    Write-Host "✓ Deploy thành công" -ForegroundColor Green
} catch {
    Write-Host "✗ Deploy thất bại. Kiểm tra logs: heroku logs --tail -a $appName" -ForegroundColor Red
    exit 1
}

# Chạy database setup
Write-Host "Thiết lập database..." -ForegroundColor Cyan
try {
    heroku run php heroku_setup.php -a $appName
    Write-Host "✓ Database đã được thiết lập" -ForegroundColor Green
} catch {
    Write-Host "⚠ Có lỗi khi thiết lập database. Kiểm tra logs." -ForegroundColor Yellow
}

# Lấy URL ứng dụng
$appUrl = heroku info -a $appName | Select-String "Web URL" | ForEach-Object { $_.ToString().Split(" ")[-1] }
Write-Host ""
Write-Host "=== DEPLOYMENT HOÀN THÀNH ===" -ForegroundColor Green
Write-Host "App Name: $appName" -ForegroundColor Cyan
Write-Host "URL: $appUrl" -ForegroundColor Cyan
Write-Host ""
Write-Host "Các lệnh hữu ích:" -ForegroundColor Yellow
Write-Host "- Xem logs: heroku logs --tail -a $appName" -ForegroundColor White
Write-Host "- Mở app: heroku open -a $appName" -ForegroundColor White
Write-Host "- Restart: heroku restart -a $appName" -ForegroundColor White
Write-Host "- Config: heroku config -a $appName" -ForegroundColor White

# Mở ứng dụng
Write-Host "Mở ứng dụng trong browser..." -ForegroundColor Cyan
heroku open -a $appName