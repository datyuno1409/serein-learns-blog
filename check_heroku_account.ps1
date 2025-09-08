# Script kiem tra trang thai tai khoan Heroku

Write-Host "=== KIEM TRA TAI KHOAN HEROKU ===" -ForegroundColor Green
Write-Host ""

# Kiem tra Heroku CLI
Write-Host "1. Kiem tra Heroku CLI..." -ForegroundColor Cyan
try {
    $herokuVersion = heroku --version 2>$null
    Write-Host "   OK Heroku CLI da cai dat: $herokuVersion" -ForegroundColor Green
} catch {
    Write-Host "   Loi Heroku CLI chua duoc cai dat" -ForegroundColor Red
    Write-Host "   Cai dat: npm install -g heroku" -ForegroundColor Yellow
    exit 1
}

# Kiem tra dang nhap
Write-Host "2. Kiem tra dang nhap..." -ForegroundColor Cyan
try {
    $herokuAuth = heroku auth:whoami 2>$null
    Write-Host "   OK Da dang nhap: $herokuAuth" -ForegroundColor Green
} catch {
    Write-Host "   Loi Chua dang nhap Heroku" -ForegroundColor Red
    Write-Host "   Chay: heroku login" -ForegroundColor Yellow
    exit 1
}

# Kiem tra kha nang tao app (test account verification)
Write-Host "3. Kiem tra xac minh tai khoan..." -ForegroundColor Cyan
$testAppName = "test-verification-" + (Get-Random -Maximum 99999)

try {
    # Thu tao app test
    $createResult = heroku create $testAppName 2>&1
    
    if ($createResult -match "verification required" -or $createResult -match "Please verify") {
        Write-Host "   Loi Tai khoan chua duoc xac minh" -ForegroundColor Red
        Write-Host "   Truy cap: https://heroku.com/verify" -ForegroundColor Yellow
        Write-Host "   Them thong tin thanh toan (khong tinh phi cho free tier)" -ForegroundColor Yellow
        exit 1
    } elseif ($createResult -match "Creating") {
        Write-Host "   OK Tai khoan da duoc xac minh" -ForegroundColor Green
        
        # Xoa app test
        Write-Host "   Dang xoa app test..." -ForegroundColor Gray
        heroku apps:destroy $testAppName --confirm $testAppName 2>$null
        Write-Host "   OK App test da duoc xoa" -ForegroundColor Gray
    } else {
        Write-Host "   Canh bao Khong the xac dinh trang thai. Ket qua: $createResult" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   Loi khi kiem tra: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Kiem tra Git repository
Write-Host "4. Kiem tra Git repository..." -ForegroundColor Cyan
try {
    $gitStatus = git status --porcelain 2>$null
    if ($gitStatus) {
        Write-Host "   Canh bao Co thay doi chua commit" -ForegroundColor Yellow
        Write-Host "   Chay: git add . && git commit -m 'Ready for deployment'" -ForegroundColor Yellow
    } else {
        Write-Host "   OK Git repository sach" -ForegroundColor Green
    }
} catch {
    Write-Host "   Loi Khong phai Git repository" -ForegroundColor Red
    Write-Host "   Chay: git init && git add . && git commit -m 'Initial commit'" -ForegroundColor Yellow
}

# Kiem tra cac file can thiet
Write-Host "5. Kiem tra files deployment..." -ForegroundColor Cyan
$requiredFiles = @(
    "Procfile",
    "composer.json",
    "config/heroku_database.php",
    "heroku_setup.php",
    "database/schema_postgresql.sql"
)

$allFilesExist = $true
foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "   OK $file" -ForegroundColor Green
    } else {
        Write-Host "   Loi $file (thieu)" -ForegroundColor Red
        $allFilesExist = $false
    }
}

if (-not $allFilesExist) {
    Write-Host "   Mot so file can thiet bi thieu!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "=== KET QUA ===" -ForegroundColor Green
Write-Host "OK Tai khoan Heroku da san sang cho deployment" -ForegroundColor Green
Write-Host "OK Tat ca files can thiet da co" -ForegroundColor Green
Write-Host ""
Write-Host "Ban co the chay deployment script:" -ForegroundColor Cyan
Write-Host ".\deploy_to_heroku.ps1" -ForegroundColor White
Write-Host ""
Write-Host "Hoac deploy thu cong theo huong dan trong:" -ForegroundColor Cyan
Write-Host "manual_deploy_steps.md" -ForegroundColor White