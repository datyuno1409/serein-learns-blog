# Heroku Deployment Script
# Chay script nay sau khi da xac minh tai khoan Heroku

Write-Host "=== Heroku Deployment Script ===" -ForegroundColor Green
Write-Host "Dam bao ban da xac minh tai khoan Heroku tai: https://heroku.com/verify" -ForegroundColor Yellow
Write-Host ""

# Kiem tra Heroku CLI
Write-Host "Kiem tra Heroku CLI..." -ForegroundColor Cyan
try {
    $herokuVersion = heroku --version
    Write-Host "OK Heroku CLI: $herokuVersion" -ForegroundColor Green
} catch {
    Write-Host "Loi Heroku CLI khong duoc tim thay" -ForegroundColor Red
    exit 1
}

# Kiem tra dang nhap Heroku
Write-Host "Kiem tra dang nhap Heroku..." -ForegroundColor Cyan
try {
    $herokuAuth = heroku auth:whoami
    Write-Host "OK Da dang nhap: $herokuAuth" -ForegroundColor Green
} catch {
    Write-Host "Loi Chua dang nhap Heroku. Chay: heroku login" -ForegroundColor Red
    exit 1
}

# Tao Heroku app
Write-Host "Tao Heroku app..." -ForegroundColor Cyan
$appName = "serein-learns-blog-" + (Get-Date -Format "yyyyMMdd-HHmmss")
Write-Host "Ten app: $appName" -ForegroundColor Yellow

try {
    heroku create $appName
    Write-Host "OK App da duoc tao: $appName" -ForegroundColor Green
} catch {
    Write-Host "Loi Khong the tao app. Kiem tra xem tai khoan da duoc xac minh chua." -ForegroundColor Red
    Write-Host "Truy cap: https://heroku.com/verify" -ForegroundColor Yellow
    exit 1
}

# Them PostgreSQL add-on
Write-Host "Them PostgreSQL database..." -ForegroundColor Cyan
try {
    heroku addons:create heroku-postgresql:essential-0 -a $appName
    Write-Host "OK PostgreSQL da duoc them" -ForegroundColor Green
} catch {
    Write-Host "Canh bao Khong the them PostgreSQL. Tiep tuc voi SQLite..." -ForegroundColor Yellow
}

# Set bien moi truong
Write-Host "Cau hinh bien moi truong..." -ForegroundColor Cyan
heroku config:set APP_ENV=production -a $appName
heroku config:set DEBUG=false -a $appName
heroku config:set SESSION_LIFETIME=1440 -a $appName
heroku config:set MAX_FILE_SIZE=5242880 -a $appName
Write-Host "OK Bien moi truong da duoc cau hinh" -ForegroundColor Green

# Deploy
Write-Host "Deploy ung dung..." -ForegroundColor Cyan
try {
    git push heroku main
    Write-Host "OK Deploy thanh cong" -ForegroundColor Green
} catch {
    Write-Host "Loi Deploy that bai. Kiem tra logs: heroku logs --tail -a $appName" -ForegroundColor Red
    exit 1
}

# Chay database setup
Write-Host "Thiet lap database..." -ForegroundColor Cyan
try {
    heroku run php heroku_setup.php -a $appName
    Write-Host "OK Database da duoc thiet lap" -ForegroundColor Green
} catch {
    Write-Host "Canh bao Co loi khi thiet lap database. Kiem tra logs." -ForegroundColor Yellow
}

# Lay URL ung dung
$appUrl = heroku info -a $appName | Select-String "Web URL" | ForEach-Object { $_.ToString().Split(" ")[-1] }
Write-Host ""
Write-Host "=== DEPLOYMENT HOAN THANH ===" -ForegroundColor Green
Write-Host "App Name: $appName" -ForegroundColor Cyan
Write-Host "URL: $appUrl" -ForegroundColor Cyan
Write-Host ""
Write-Host "Cac lenh huu ich:" -ForegroundColor Yellow
Write-Host "- Xem logs: heroku logs --tail -a $appName" -ForegroundColor White
Write-Host "- Mo app: heroku open -a $appName" -ForegroundColor White
Write-Host "- Restart: heroku restart -a $appName" -ForegroundColor White
Write-Host "- Config: heroku config -a $appName" -ForegroundColor White

# Mo ung dung
Write-Host "Mo ung dung trong browser..." -ForegroundColor Cyan
heroku open -a $appName