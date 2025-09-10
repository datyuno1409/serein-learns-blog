class SecurityConfig {
    constructor() {
        this.defaultConfig = {
            authentication: {
                jwtSecret: process.env.JWT_SECRET || this.generateSecureSecret(),
                jwtExpiresIn: '1h',
                refreshTokenExpiresIn: '7d',
                maxLoginAttempts: 5,
                lockoutDuration: 15 * 60 * 1000, // 15 minutes
                passwordMinLength: 8,
                passwordRequireUppercase: true,
                passwordRequireLowercase: true,
                passwordRequireNumbers: true,
                passwordRequireSpecialChars: true,
                sessionTimeout: 30 * 60 * 1000, // 30 minutes
                enableTwoFactor: false,
                enableAccountLockout: true
            },
            oauth2: {
                providers: {
                    google: {
                        clientId: process.env.GOOGLE_CLIENT_ID,
                        clientSecret: process.env.GOOGLE_CLIENT_SECRET,
                        redirectUri: process.env.GOOGLE_REDIRECT_URI || 'http://localhost:8000/auth/google/callback',
                        scope: ['openid', 'profile', 'email']
                    },
                    github: {
                        clientId: process.env.GITHUB_CLIENT_ID,
                        clientSecret: process.env.GITHUB_CLIENT_SECRET,
                        redirectUri: process.env.GITHUB_REDIRECT_URI || 'http://localhost:8000/auth/github/callback',
                        scope: ['user:email']
                    },
                    microsoft: {
                        clientId: process.env.MICROSOFT_CLIENT_ID,
                        clientSecret: process.env.MICROSOFT_CLIENT_SECRET,
                        redirectUri: process.env.MICROSOFT_REDIRECT_URI || 'http://localhost:8000/auth/microsoft/callback',
                        scope: ['openid', 'profile', 'email']
                    }
                },
                stateExpirationTime: 10 * 60 * 1000, // 10 minutes
                enablePKCE: true
            },
            encryption: {
                algorithm: 'aes-256-gcm',
                keyDerivation: {
                    algorithm: 'pbkdf2',
                    iterations: 100000,
                    keyLength: 32,
                    digest: 'sha256'
                },
                rsa: {
                    keySize: 2048,
                    publicExponent: 65537
                },
                saltLength: 32,
                ivLength: 16,
                tagLength: 16
            },
            accessControl: {
                defaultRole: 'user',
                adminRole: 'admin',
                superAdminRole: 'superadmin',
                enableResourceHierarchy: true,
                enableInheritance: true,
                cachePermissions: true,
                permissionCacheTTL: 5 * 60 * 1000, // 5 minutes
                auditActions: true
            },
            monitoring: {
                enableRealTimeMonitoring: true,
                enableAnomalyDetection: true,
                enableGeolocationTracking: true,
                alertThreshold: 5,
                blockThreshold: 10,
                monitoringInterval: 60 * 1000, // 1 minute
                anomalyDetectionWindow: 60 * 60 * 1000, // 1 hour
                maxEventsInMemory: 10000,
                enableNotifications: true,
                notificationChannels: {
                    email: {
                        enabled: false,
                        recipients: [],
                        smtpConfig: {
                            host: process.env.SMTP_HOST,
                            port: process.env.SMTP_PORT || 587,
                            secure: false,
                            auth: {
                                user: process.env.SMTP_USER,
                                pass: process.env.SMTP_PASS
                            }
                        }
                    },
                    webhook: {
                        enabled: false,
                        url: process.env.SECURITY_WEBHOOK_URL,
                        secret: process.env.SECURITY_WEBHOOK_SECRET
                    }
                },
                threatDetection: {
                    bruteForce: {
                        enabled: true,
                        maxAttempts: 5,
                        timeWindow: 15 * 60 * 1000, // 15 minutes
                        blockDuration: 60 * 60 * 1000 // 1 hour
                    },
                    sqlInjection: {
                        enabled: true,
                        patterns: [
                            /('|(\-\-)|(;)|(\||\|)|(\*|\*))/i,
                            /(union|select|insert|delete|update|drop|create|alter|exec|execute)/i,
                            /(script|javascript|vbscript|onload|onerror|onclick)/i
                        ]
                    },
                    xss: {
                        enabled: true,
                        patterns: [
                            /<script[^>]*>.*?<\/script>/gi,
                            /<iframe[^>]*>.*?<\/iframe>/gi,
                            /javascript:/gi,
                            /on\w+\s*=/gi
                        ]
                    },
                    pathTraversal: {
                        enabled: true,
                        patterns: [
                            /\.\.\/|\.\.\\/g,
                            /%2e%2e%2f|%2e%2e%5c/gi,
                            /\.\.%2f|\.\.%5c/gi
                        ]
                    }
                }
            },
            headers: {
                hsts: {
                    enabled: true,
                    maxAge: 31536000, // 1 year
                    includeSubDomains: true,
                    preload: true
                },
                csp: {
                    enabled: true,
                    directives: {
                        'default-src': ["'self'"],
                        'script-src': ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
                        'style-src': ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'],
                        'font-src': ["'self'", 'https://fonts.gstatic.com'],
                        'img-src': ["'self'", 'data:', 'https:'],
                        'connect-src': ["'self'"],
                        'frame-ancestors': ["'none'"],
                        'base-uri': ["'self'"],
                        'form-action': ["'self'"]
                    },
                    reportUri: '/security/csp-report',
                    reportOnly: false
                },
                frameOptions: {
                    enabled: true,
                    value: 'DENY'
                },
                contentTypeOptions: {
                    enabled: true
                },
                referrerPolicy: {
                    enabled: true,
                    value: 'strict-origin-when-cross-origin'
                },
                permissionsPolicy: {
                    enabled: true,
                    directives: {
                        camera: [],
                        microphone: [],
                        geolocation: [],
                        payment: []
                    }
                },
                crossOriginEmbedderPolicy: {
                    enabled: false,
                    value: 'require-corp'
                },
                crossOriginOpenerPolicy: {
                    enabled: true,
                    value: 'same-origin'
                },
                crossOriginResourcePolicy: {
                    enabled: true,
                    value: 'same-origin'
                }
            },
            validation: {
                enableSanitization: true,
                enableThreatDetection: true,
                maxInputLength: 10000,
                allowedFileTypes: ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx'],
                maxFileSize: 10 * 1024 * 1024, // 10MB
                enableFileScanning: false,
                customValidationRules: {},
                encoding: 'utf-8',
                strictMode: true
            },
            rateLimit: {
                enabled: true,
                windowMs: 15 * 60 * 1000, // 15 minutes
                maxRequests: 100,
                skipSuccessfulRequests: false,
                skipFailedRequests: false,
                keyGenerator: (req) => req.ip,
                onLimitReached: (req, res) => {
                    res.status(429).json({
                        error: 'Too many requests',
                        retryAfter: Math.ceil(15 * 60) // seconds
                    });
                }
            },
            cors: {
                enabled: true,
                origin: process.env.ALLOWED_ORIGINS ? process.env.ALLOWED_ORIGINS.split(',') : ['http://localhost:8000'],
                methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
                credentials: true,
                maxAge: 86400 // 24 hours
            },
            logging: {
                level: process.env.LOG_LEVEL || 'info',
                enableFileLogging: true,
                logDirectory: './logs/security',
                maxLogFiles: 10,
                maxLogSize: '10m',
                enableConsoleLogging: true,
                sensitiveFields: ['password', 'token', 'secret', 'key'],
                enableAuditLog: true
            },
            environment: {
                isDevelopment: process.env.NODE_ENV === 'development',
                isProduction: process.env.NODE_ENV === 'production',
                isTest: process.env.NODE_ENV === 'test',
                domain: process.env.DOMAIN || 'localhost',
                port: process.env.PORT || 8000,
                baseUrl: process.env.BASE_URL || 'http://localhost:8000'
            }
        };

        this.config = this.mergeWithEnvironment(this.defaultConfig);
        this.validateConfig();
    }

    mergeWithEnvironment(config) {
        const envConfig = {};

        // Override with environment variables
        if (process.env.JWT_EXPIRES_IN) {
            envConfig.authentication = { ...config.authentication, jwtExpiresIn: process.env.JWT_EXPIRES_IN };
        }

        if (process.env.MAX_LOGIN_ATTEMPTS) {
            envConfig.authentication = { 
                ...envConfig.authentication, 
                maxLoginAttempts: parseInt(process.env.MAX_LOGIN_ATTEMPTS) 
            };
        }

        if (process.env.ENABLE_TWO_FACTOR === 'true') {
            envConfig.authentication = { 
                ...envConfig.authentication, 
                enableTwoFactor: true 
            };
        }

        if (process.env.ALERT_THRESHOLD) {
            envConfig.monitoring = { 
                ...config.monitoring, 
                alertThreshold: parseInt(process.env.ALERT_THRESHOLD) 
            };
        }

        if (process.env.BLOCK_THRESHOLD) {
            envConfig.monitoring = { 
                ...envConfig.monitoring, 
                blockThreshold: parseInt(process.env.BLOCK_THRESHOLD) 
            };
        }

        if (process.env.ENABLE_CSP === 'false') {
            envConfig.headers = { 
                ...config.headers, 
                csp: { ...config.headers.csp, enabled: false } 
            };
        }

        if (process.env.RATE_LIMIT_REQUESTS) {
            envConfig.rateLimit = { 
                ...config.rateLimit, 
                maxRequests: parseInt(process.env.RATE_LIMIT_REQUESTS) 
            };
        }

        return this.deepMerge(config, envConfig);
    }

    deepMerge(target, source) {
        const result = { ...target };
        
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        
        return result;
    }

    validateConfig() {
        const errors = [];

        // Validate JWT secret
        if (!this.config.authentication.jwtSecret || this.config.authentication.jwtSecret.length < 32) {
            errors.push('JWT secret must be at least 32 characters long');
        }

        // Validate OAuth2 configuration
        for (const [provider, config] of Object.entries(this.config.oauth2.providers)) {
            if (config.clientId && !config.clientSecret) {
                errors.push(`OAuth2 ${provider}: clientSecret is required when clientId is provided`);
            }
        }

        // Validate monitoring thresholds
        if (this.config.monitoring.alertThreshold >= this.config.monitoring.blockThreshold) {
            errors.push('Alert threshold must be less than block threshold');
        }

        // Validate CORS origins
        if (this.config.cors.enabled && (!this.config.cors.origin || this.config.cors.origin.length === 0)) {
            errors.push('CORS origins must be specified when CORS is enabled');
        }

        // Validate file upload settings
        if (this.config.validation.maxFileSize > 100 * 1024 * 1024) { // 100MB
            console.warn('Warning: Maximum file size is very large (>100MB)');
        }

        if (errors.length > 0) {
            throw new Error(`Security configuration validation failed:\n${errors.join('\n')}`);
        }
    }

    generateSecureSecret(length = 64) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let result = '';
        
        if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
            // Browser environment
            const array = new Uint8Array(length);
            crypto.getRandomValues(array);
            for (let i = 0; i < length; i++) {
                result += chars[array[i] % chars.length];
            }
        } else if (typeof require !== 'undefined') {
            // Node.js environment
            try {
                const crypto = require('crypto');
                const bytes = crypto.randomBytes(length);
                for (let i = 0; i < length; i++) {
                    result += chars[bytes[i] % chars.length];
                }
            } catch (error) {
                // Fallback to Math.random
                for (let i = 0; i < length; i++) {
                    result += chars[Math.floor(Math.random() * chars.length)];
                }
            }
        } else {
            // Fallback to Math.random
            for (let i = 0; i < length; i++) {
                result += chars[Math.floor(Math.random() * chars.length)];
            }
        }
        
        return result;
    }

    get(path) {
        return this.getNestedValue(this.config, path);
    }

    set(path, value) {
        this.setNestedValue(this.config, path, value);
        this.validateConfig();
    }

    getNestedValue(obj, path) {
        return path.split('.').reduce((current, key) => current && current[key], obj);
    }

    setNestedValue(obj, path, value) {
        const keys = path.split('.');
        const lastKey = keys.pop();
        const target = keys.reduce((current, key) => {
            if (!current[key]) current[key] = {};
            return current[key];
        }, obj);
        target[lastKey] = value;
    }

    getAuthConfig() {
        return this.config.authentication;
    }

    getOAuth2Config() {
        return this.config.oauth2;
    }

    getEncryptionConfig() {
        return this.config.encryption;
    }

    getAccessControlConfig() {
        return this.config.accessControl;
    }

    getMonitoringConfig() {
        return this.config.monitoring;
    }

    getHeadersConfig() {
        return this.config.headers;
    }

    getValidationConfig() {
        return this.config.validation;
    }

    getRateLimitConfig() {
        return this.config.rateLimit;
    }

    getCorsConfig() {
        return this.config.cors;
    }

    getLoggingConfig() {
        return this.config.logging;
    }

    getEnvironmentConfig() {
        return this.config.environment;
    }

    isDevelopment() {
        return this.config.environment.isDevelopment;
    }

    isProduction() {
        return this.config.environment.isProduction;
    }

    isTest() {
        return this.config.environment.isTest;
    }

    updateConfig(updates) {
        this.config = this.deepMerge(this.config, updates);
        this.validateConfig();
        return this.config;
    }

    resetToDefaults() {
        this.config = this.mergeWithEnvironment(this.defaultConfig);
        this.validateConfig();
        return this.config;
    }

    exportConfig() {
        return JSON.stringify(this.config, null, 2);
    }

    importConfig(configJson) {
        try {
            const importedConfig = JSON.parse(configJson);
            this.config = this.deepMerge(this.defaultConfig, importedConfig);
            this.validateConfig();
            return true;
        } catch (error) {
            throw new Error(`Failed to import configuration: ${error.message}`);
        }
    }

    getSecurityProfile() {
        const profile = {
            level: 'medium',
            score: 0,
            maxScore: 100,
            recommendations: []
        };

        // Authentication security
        if (this.config.authentication.enableTwoFactor) profile.score += 15;
        else profile.recommendations.push('Enable two-factor authentication');

        if (this.config.authentication.passwordMinLength >= 12) profile.score += 10;
        else profile.recommendations.push('Increase minimum password length to 12+ characters');

        if (this.config.authentication.enableAccountLockout) profile.score += 10;

        // Encryption
        if (this.config.encryption.algorithm === 'aes-256-gcm') profile.score += 15;
        if (this.config.encryption.keyDerivation.iterations >= 100000) profile.score += 10;

        // Monitoring
        if (this.config.monitoring.enableRealTimeMonitoring) profile.score += 10;
        if (this.config.monitoring.enableAnomalyDetection) profile.score += 10;

        // Headers
        if (this.config.headers.hsts.enabled) profile.score += 5;
        if (this.config.headers.csp.enabled) profile.score += 10;
        if (this.config.headers.frameOptions.enabled) profile.score += 5;

        // Rate limiting
        if (this.config.rateLimit.enabled) profile.score += 10;

        // Determine security level
        if (profile.score >= 80) profile.level = 'high';
        else if (profile.score >= 60) profile.level = 'medium';
        else profile.level = 'low';

        return profile;
    }
}

const securityConfig = new SecurityConfig();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SecurityConfig, securityConfig };
} else {
    window.SecurityConfig = SecurityConfig;
    window.securityConfig = securityConfig;
}