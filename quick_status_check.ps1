#!/usr/bin/env pwsh
# Quick Status Check for Heroku Deployment

Write-Host "=== KIEM TRA TRANG THAI HEROKU ===" -ForegroundColor Yellow
Write-Host ""

# Check Heroku CLI
Write-Host "1. Kiem tra Heroku CLI..." -ForegroundColor Cyan
try {
    $herokuVersion = heroku --version 2>$null
    if ($herokuVersion) {
        Write-Host "   [OK] Heroku CLI da cai dat: $($herokuVersion.Split()[0])" -ForegroundColor Green
    } else {
        Write-Host "   [LOI] Heroku CLI chua cai dat" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "   [LOI] Khong the kiem tra Heroku CLI" -ForegroundColor Red
    exit 1
}

# Check login status
Write-Host "2. Kiem tra trang thai dang nhap..." -ForegroundColor Cyan
try {
    $authInfo = heroku auth:whoami 2>$null
    if ($authInfo) {
        Write-Host "   [OK] Da dang nhap: $authInfo" -ForegroundColor Green
    } else {
        Write-Host "   [LOI] Chua dang nhap vao Heroku" -ForegroundColor Red
        Write-Host "   Chay lenh: heroku login" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "   [LOI] Khong the kiem tra trang thai dang nhap" -ForegroundColor Red
    exit 1
}

# Check account verification by trying to create an app
Write-Host "3. Kiem tra xac minh tai khoan..." -ForegroundColor Cyan
try {
    $testResult = heroku create test-verification-check-$(Get-Date -Format 'yyyyMMdd-HHmmss') 2>&1
    if ($testResult -match "verify your account") {
        Write-Host "   [LOI] Tai khoan chua duoc xac minh" -ForegroundColor Red
        Write-Host "   Can them thong tin thanh toan tai: https://heroku.com/verify" -ForegroundColor Yellow
        Write-Host "   Luu y: Khong co phi cho viec xac minh nay" -ForegroundColor Yellow
    } elseif ($testResult -match "Creating") {
        Write-Host "   [OK] Tai khoan da duoc xac minh" -ForegroundColor Green
        # Clean up test app
        $appName = ($testResult | Select-String -Pattern "Creating ⬢ ([^\s]+)").Matches[0].Groups[1].Value
        if ($appName) {
            heroku apps:destroy $appName --confirm $appName 2>$null
            Write-Host "   [INFO] Da xoa app test: $appName" -ForegroundColor Gray
        }
    } else {
        Write-Host "   [CANH BAO] Ket qua khong xac dinh: $testResult" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   [LOI] Khong the kiem tra xac minh tai khoan" -ForegroundColor Red
}

# Check existing apps
Write-Host "4. Kiem tra cac app hien tai..." -ForegroundColor Cyan
try {
    $apps = heroku apps --json 2>$null | ConvertFrom-Json
    if ($apps -and $apps.Count -gt 0) {
        Write-Host "   [INFO] Co $($apps.Count) app(s):" -ForegroundColor Green
        foreach ($app in $apps) {
            Write-Host "   - $($app.name): $($app.web_url)" -ForegroundColor Gray
        }
    } else {
        Write-Host "   [INFO] Chua co app nao" -ForegroundColor Gray
    }
} catch {
    Write-Host "   [INFO] Khong the lay danh sach apps" -ForegroundColor Gray
}

# Check git remotes
Write-Host "5. Kiem tra Git remotes..." -ForegroundColor Cyan
try {
    $remotes = git remote -v 2>$null
    if ($remotes) {
        Write-Host "   [INFO] Git remotes:" -ForegroundColor Green
        $remotes | ForEach-Object { Write-Host "   $_" -ForegroundColor Gray }
        
        if ($remotes -match "heroku") {
            Write-Host "   [OK] Co remote heroku" -ForegroundColor Green
        } else {
            Write-Host "   [INFO] Chua co remote heroku" -ForegroundColor Yellow
        }
    } else {
        Write-Host "   [CANH BAO] Khong co remote nao" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   [LOI] Khong the kiem tra Git remotes" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== KET LUAN ===" -ForegroundColor Yellow
Write-Host "Neu tai khoan chua duoc xac minh:" -ForegroundColor White
Write-Host "1. Truy cap: https://heroku.com/verify" -ForegroundColor Cyan
Write-Host "2. Them thong tin the tin dung/ghi no" -ForegroundColor Cyan
Write-Host "3. Chay lai script: .\\deploy_to_heroku.ps1" -ForegroundColor Cyan
Write-Host ""
Write-Host "Neu da xac minh nhung van gap loi, xem file: HEROKU_ACCOUNT_VERIFICATION.md" -ForegroundColor White