# 🔐 Hệ thống Bảo mật Toàn diện (Comprehensive Security System)

## Tổng quan

Hệ thống bảo mật toàn diện được thiết kế để bảo vệ ứng dụng web khỏi các mối đe dọa bảo mật phổ biến. Hệ thống bao gồm các module bảo mật chính:

- **Xác thực người dùng** (JWT & OAuth2)
- **Mã hóa dữ liệu nhạy cảm** (AES-256-GCM)
- **Kiểm soát truy cập** (RBAC)
- **Giám sát bảo mật** liên tục
- **Header bảo mật** và HTTPS
- **Xác thực đầu vào** và chống tấn công
- **Kiểm tra bảo mật** tự động

## 🏗️ Cấu trúc Dự án

```
security/
├── auth/                    # Xác thực người dùng
│   ├── auth.js             # Quản lý xác thực JWT
│   └── oauth2.js           # Xác thực OAuth2
├── encryption/             # Mã hóa dữ liệu
│   └── crypto.js           # Quản lý mã hóa/giải mã
├── access/                 # Kiểm soát truy cập
│   └── rbac.js             # Role-Based Access Control
├── monitoring/             # Giám sát bảo mật
│   └── security-monitor.js # Phát hiện mối đe dọa
├── headers/                # Header bảo mật
│   └── security-headers.js # Cấu hình header HTTP
├── validation/             # Xác thực đầu vào
│   └── input-validator.js  # Chống tấn công injection
├── config/                 # Cấu hình
│   └── security-config.js  # Thiết lập bảo mật
├── testing/                # Kiểm tra bảo mật
│   └── security-tester.js  # Penetration testing
├── demo/                   # Demo và ví dụ
│   ├── security-demo.js    # Demo JavaScript
│   └── security-demo.html  # Giao diện web demo
├── security-manager.js     # Quản lý tổng thể
└── README.md              # Tài liệu này
```

## 🚀 Cài đặt và Sử dụng

### 1. Khởi tạo Hệ thống

```javascript
import { SecurityManager } from './security-manager.js';

// Tạo instance SecurityManager
const securityManager = new SecurityManager({
    environment: 'production', // hoặc 'development'
    authentication: {
        jwtSecret: 'your-super-secret-jwt-key',
        jwtExpiresIn: '1h',
        enableOAuth2: true
    },
    encryption: {
        algorithm: 'aes-256-gcm',
        keyDerivation: 'pbkdf2'
    },
    monitoring: {
        enableThreatDetection: true,
        bruteForceThreshold: 5
    }
});

// Khởi tạo hệ thống
await securityManager.initialize();
```

### 2. Xác thực Người dùng

```javascript
// Đăng ký người dùng mới
const registerResult = await securityManager.authenticateUser({
    action: 'register',
    username: 'user123',
    email: 'user@example.com',
    password: 'SecurePassword123!',
    request: { ip: '192.168.1.100' }
});

// Đăng nhập
const loginResult = await securityManager.authenticateUser({
    action: 'login',
    username: 'user123',
    password: 'SecurePassword123!',
    request: { ip: '192.168.1.100' }
});

if (loginResult.success) {
    console.log('JWT Token:', loginResult.token);
}
```

### 3. Mã hóa Dữ liệu

```javascript
// Mã hóa dữ liệu nhạy cảm
const sensitiveData = 'Credit Card: 4532-1234-5678-9012';
const encrypted = await securityManager.encryptSensitiveData(sensitiveData, {
    password: 'encryption-password'
});

// Giải mã dữ liệu
const decrypted = await securityManager.decryptSensitiveData(encrypted, {
    password: 'encryption-password'
});
```

### 4. Kiểm soát Truy cập

```javascript
// Kiểm tra quyền truy cập
const hasAccess = await securityManager.authorizeAccess(
    'user123',      // userId
    'admin-panel',  // resource
    'read'          // action
);

if (hasAccess) {
    // Cho phép truy cập
} else {
    // Từ chối truy cập
}
```

### 5. Xác thực Đầu vào

```javascript
// Xác thực và làm sạch input
const validation = securityManager.validateInput(userInput, {
    type: 'string',
    maxLength: 100,
    enableThreatDetection: true
});

if (validation.isValid) {
    // Input an toàn
    const cleanInput = validation.sanitized;
} else {
    // Input có vấn đề
    console.log('Threats detected:', validation.threats);
}
```

### 6. Áp dụng Header Bảo mật

```javascript
// Trong Express.js middleware
app.use((req, res, next) => {
    const secureResponse = securityManager.applySecurityHeaders(res, req);
    next();
});
```

## 🛡️ Các Tính năng Bảo mật

### Xác thực (Authentication)
- ✅ JWT với thuật toán HS256/RS256
- ✅ OAuth2 (Google, GitHub, Microsoft)
- ✅ Bảo vệ chống tấn công vét cạn
- ✅ Quản lý phiên đăng nhập
- ✅ Khóa tài khoản tự động

### Mã hóa (Encryption)
- ✅ AES-256-GCM cho dữ liệu đối xứng
- ✅ RSA-OAEP cho dữ liệu bất đối xứng
- ✅ PBKDF2 cho dẫn xuất khóa
- ✅ Tạo khóa ngẫu nhiên an toàn
- ✅ HMAC cho xác thực dữ liệu

### Kiểm soát Truy cập (Access Control)
- ✅ Role-Based Access Control (RBAC)
- ✅ Phân quyền chi tiết theo tài nguyên
- ✅ Kiểm tra quyền động
- ✅ Ghi nhật ký truy cập

### Giám sát Bảo mật (Security Monitoring)
- ✅ Phát hiện tấn công vét cạn
- ✅ Phát hiện SQL Injection
- ✅ Phát hiện XSS
- ✅ Phát hiện hành vi bất thường
- ✅ Cảnh báo thời gian thực
- ✅ Chặn IP tự động

### Header Bảo mật (Security Headers)
- ✅ HSTS (HTTP Strict Transport Security)
- ✅ CSP (Content Security Policy)
- ✅ X-Frame-Options
- ✅ X-Content-Type-Options
- ✅ X-XSS-Protection
- ✅ Referrer-Policy

### Xác thực Đầu vào (Input Validation)
- ✅ Chống SQL Injection
- ✅ Chống XSS (Cross-Site Scripting)
- ✅ Chống Path Traversal
- ✅ Chống Command Injection
- ✅ Xác thực định dạng dữ liệu
- ✅ Làm sạch input tự động

## 🧪 Demo và Kiểm tra

### Chạy Demo Web

1. Mở file `demo/security-demo.html` trong trình duyệt
2. Nhấn "Khởi tạo Hệ thống" để bắt đầu
3. Chạy từng demo riêng lẻ hoặc "Chạy Toàn bộ Demo"
4. Xem kết quả và điểm bảo mật

### Chạy Demo JavaScript

```bash
# Chạy toàn bộ demo
node security/demo/security-demo.js all

# Chạy demo riêng lẻ
node security/demo/security-demo.js auth
node security/demo/security-demo.js encryption
node security/demo/security-demo.js access
```

### Kiểm tra Bảo mật Tự động

```javascript
import { SecurityTester } from './testing/security-tester.js';

const tester = new SecurityTester(securityManager);
const report = await tester.runAllTests();

console.log('Security Score:', report.summary.securityScore);
console.log('Risk Level:', report.summary.riskLevel);
```

## ⚙️ Cấu hình

### Biến Môi trường

```env
# JWT Configuration
JWT_SECRET=your-super-secret-jwt-key-with-at-least-32-characters
JWT_EXPIRES_IN=1h
JWT_REFRESH_EXPIRES_IN=7d

# OAuth2 Configuration
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret

# Encryption Configuration
ENCRYPTION_KEY=your-32-character-encryption-key
ENCRYPTION_ALGORITHM=aes-256-gcm

# Security Configuration
BRUTE_FORCE_THRESHOLD=5
SESSION_TIMEOUT=3600
MAX_LOGIN_ATTEMPTS=5
ACCOUNT_LOCKOUT_DURATION=900

# Monitoring Configuration
ENABLE_THREAT_DETECTION=true
ENABLE_ANOMALY_DETECTION=true
LOG_LEVEL=info
```

### Cấu hình Chi tiết

```javascript
const config = {
    environment: 'production',
    
    authentication: {
        jwtSecret: process.env.JWT_SECRET,
        jwtExpiresIn: '1h',
        jwtRefreshExpiresIn: '7d',
        enableOAuth2: true,
        bruteForceThreshold: 5,
        maxLoginAttempts: 5,
        accountLockoutDuration: 900,
        sessionTimeout: 3600,
        
        oauth2Providers: {
            google: {
                clientId: process.env.GOOGLE_CLIENT_ID,
                clientSecret: process.env.GOOGLE_CLIENT_SECRET,
                redirectUri: 'https://yourapp.com/auth/google/callback'
            },
            github: {
                clientId: process.env.GITHUB_CLIENT_ID,
                clientSecret: process.env.GITHUB_CLIENT_SECRET,
                redirectUri: 'https://yourapp.com/auth/github/callback'
            }
        }
    },
    
    encryption: {
        algorithm: 'aes-256-gcm',
        keyDerivation: 'pbkdf2',
        iterations: 100000,
        keyLength: 32,
        ivLength: 16,
        saltLength: 32,
        tagLength: 16
    },
    
    accessControl: {
        enableRBAC: true,
        defaultRole: 'user',
        adminRole: 'admin',
        guestRole: 'guest'
    },
    
    monitoring: {
        enableThreatDetection: true,
        enableAnomalyDetection: true,
        bruteForceThreshold: 5,
        anomalyThreshold: 0.8,
        logLevel: 'info',
        alertThreshold: 'medium'
    },
    
    headers: {
        hsts: {
            maxAge: 31536000,
            includeSubDomains: true,
            preload: true
        },
        csp: {
            defaultSrc: ["'self'"],
            scriptSrc: ["'self'", "'unsafe-inline'"],
            styleSrc: ["'self'", "'unsafe-inline'"],
            imgSrc: ["'self'", "data:", "https:"],
            connectSrc: ["'self'"],
            fontSrc: ["'self'"],
            objectSrc: ["'none'"],
            mediaSrc: ["'self'"],
            frameSrc: ["'none'"]
        }
    },
    
    validation: {
        enableThreatDetection: true,
        sanitizeInput: true,
        maxInputLength: 10000,
        allowedFileTypes: ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx'],
        maxFileSize: 10485760 // 10MB
    }
};
```

## 🔧 Tích hợp với Framework

### Express.js

```javascript
import express from 'express';
import { SecurityManager } from './security/security-manager.js';

const app = express();
const security = new SecurityManager(config);
await security.initialize();

// Middleware bảo mật
app.use((req, res, next) => {
    // Áp dụng security headers
    security.applySecurityHeaders(res, req);
    
    // Xác thực input
    if (req.body) {
        for (const [key, value] of Object.entries(req.body)) {
            const validation = security.validateInput(value);
            if (!validation.isValid) {
                return res.status(400).json({
                    error: 'Invalid input detected',
                    threats: validation.threats
                });
            }
            req.body[key] = validation.sanitized;
        }
    }
    
    next();
});

// Route đăng nhập
app.post('/auth/login', async (req, res) => {
    try {
        const result = await security.authenticateUser({
            action: 'login',
            username: req.body.username,
            password: req.body.password,
            request: req
        });
        
        if (result.success) {
            res.json({ token: result.token });
        } else {
            res.status(401).json({ error: result.message });
        }
    } catch (error) {
        res.status(500).json({ error: 'Authentication failed' });
    }
});

// Middleware xác thực JWT
app.use('/api', (req, res, next) => {
    const token = req.headers.authorization?.replace('Bearer ', '');
    
    if (!token) {
        return res.status(401).json({ error: 'No token provided' });
    }
    
    try {
        const decoded = security.verifyToken(token);
        req.user = decoded;
        next();
    } catch (error) {
        res.status(401).json({ error: 'Invalid token' });
    }
});
```

## 📊 Giám sát và Báo cáo

### Ghi nhật ký Bảo mật

```javascript
// Ghi nhật ký sự kiện bảo mật
security.logSecurityEvent('suspicious-login', {
    userId: 'user123',
    ip: '192.168.1.100',
    userAgent: 'Mozilla/5.0...',
    timestamp: new Date(),
    details: 'Multiple failed login attempts'
});

// Tạo báo cáo bảo mật
const report = security.generateSecurityReport({
    timeRange: '24h',
    includeThreats: true,
    includeAnomalies: true,
    format: 'json'
});
```

### Dashboard Giám sát

```javascript
// Lấy thống kê bảo mật thời gian thực
const stats = security.getSecurityStats();
console.log({
    activeThreats: stats.activeThreats,
    blockedIPs: stats.blockedIPs.length,
    failedLogins: stats.failedLogins,
    securityScore: stats.securityScore
});
```

## 🚨 Xử lý Sự cố

### Phát hiện và Phản ứng

```javascript
// Đăng ký handler cho các sự kiện bảo mật
security.onThreatDetected((threat) => {
    console.log('Threat detected:', threat);
    
    // Tự động chặn IP nếu cần
    if (threat.severity === 'high') {
        security.blockIP(threat.sourceIP, '1h');
    }
    
    // Gửi cảnh báo
    security.sendAlert({
        type: 'threat-detected',
        severity: threat.severity,
        details: threat
    });
});

// Xử lý anomaly
security.onAnomalyDetected((anomaly) => {
    console.log('Anomaly detected:', anomaly);
    
    // Ghi nhật ký chi tiết
    security.logSecurityEvent('anomaly-detected', anomaly);
});
```

## 🔍 Troubleshooting

### Lỗi Thường gặp

1. **JWT Token Invalid**
   ```javascript
   // Kiểm tra cấu hình JWT
   console.log('JWT Secret length:', process.env.JWT_SECRET?.length);
   // JWT secret phải có ít nhất 32 ký tự
   ```

2. **Encryption Failed**
   ```javascript
   // Kiểm tra khóa mã hóa
   console.log('Encryption key:', process.env.ENCRYPTION_KEY?.length);
   // Khóa mã hóa phải có đúng 32 ký tự cho AES-256
   ```

3. **OAuth2 Callback Error**
   ```javascript
   // Kiểm tra redirect URI
   console.log('Redirect URI:', config.oauth2Providers.google.redirectUri);
   // Phải khớp với cấu hình trong Google Console
   ```

### Debug Mode

```javascript
// Bật debug mode
const security = new SecurityManager({
    ...config,
    debug: true,
    logLevel: 'debug'
});
```

## 📚 Tài liệu Tham khảo

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [JWT Best Practices](https://tools.ietf.org/html/rfc7519)
- [OAuth 2.0 Security](https://tools.ietf.org/html/rfc6749)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [HSTS](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security)

## 🤝 Đóng góp

Nếu bạn muốn đóng góp cho dự án:

1. Fork repository
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Tạo Pull Request

## 📄 License

MIT License - xem file LICENSE để biết thêm chi tiết.

## 👨‍💻 Tác giả

**Serein** - Phát triển hệ thống bảo mật toàn diện

---

🔐 **Lưu ý Bảo mật**: Luôn cập nhật các dependency và theo dõi các lỗ hổng bảo mật mới. Thực hiện kiểm tra bảo mật định kỳ và tuân thủ các best practices về bảo mật web.