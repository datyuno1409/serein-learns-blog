class SecurityHeaders {
    constructor(options = {}) {
        this.config = {
            enableHSTS: options.enableHSTS !== false,
            enableCSP: options.enableCSP !== false,
            enableXFrameOptions: options.enableXFrameOptions !== false,
            enableXContentTypeOptions: options.enableXContentTypeOptions !== false,
            enableReferrerPolicy: options.enableReferrerPolicy !== false,
            enablePermissionsPolicy: options.enablePermissionsPolicy !== false,
            strictTransportSecurity: {
                maxAge: options.hstsMaxAge || 31536000, // 1 year
                includeSubDomains: options.hstsIncludeSubDomains !== false,
                preload: options.hstsPreload || false
            },
            contentSecurityPolicy: {
                defaultSrc: options.cspDefaultSrc || ["'self'"],
                scriptSrc: options.cspScriptSrc || ["'self'", "'unsafe-inline'"],
                styleSrc: options.cspStyleSrc || ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', 'https://cdnjs.cloudflare.com'],
                imgSrc: options.cspImgSrc || ["'self'", 'data:', 'https:'],
                fontSrc: options.cspFontSrc || ["'self'", 'https://fonts.gstatic.com', 'https://cdnjs.cloudflare.com'],
                connectSrc: options.cspConnectSrc || ["'self'"],
                mediaSrc: options.cspMediaSrc || ["'self'"],
                objectSrc: options.cspObjectSrc || ["'none'"],
                childSrc: options.cspChildSrc || ["'self'"],
                workerSrc: options.cspWorkerSrc || ["'self'"],
                frameSrc: options.cspFrameSrc || ["'self'"],
                formAction: options.cspFormAction || ["'self'"],
                upgradeInsecureRequests: options.cspUpgradeInsecureRequests !== false,
                blockAllMixedContent: options.cspBlockAllMixedContent || false
            },
            permissionsPolicy: {
                camera: options.permissionsCamera || 'none',
                microphone: options.permissionsMicrophone || 'none',
                geolocation: options.permissionsGeolocation || 'none',
                notifications: options.permissionsNotifications || 'none',
                payment: options.permissionsPayment || 'none',
                usb: options.permissionsUsb || 'none',
                magnetometer: options.permissionsMagnetometer || 'none',
                gyroscope: options.permissionsGyroscope || 'none',
                accelerometer: options.permissionsAccelerometer || 'none'
            },
            customHeaders: options.customHeaders || {}
        };

        this.nonces = new Map();
        this.reportEndpoint = options.reportEndpoint || '/security-report';
    }

    generateSecurityHeaders(request = {}) {
        const headers = {};

        if (this.config.enableHSTS && this.isHTTPS(request)) {
            headers['Strict-Transport-Security'] = this.buildHSTSHeader();
        }

        if (this.config.enableCSP) {
            const nonce = this.generateNonce();
            headers['Content-Security-Policy'] = this.buildCSPHeader(nonce);
            headers['X-CSP-Nonce'] = nonce;
        }

        if (this.config.enableXFrameOptions) {
            headers['X-Frame-Options'] = 'DENY';
        }

        if (this.config.enableXContentTypeOptions) {
            headers['X-Content-Type-Options'] = 'nosniff';
        }

        headers['X-XSS-Protection'] = '1; mode=block';

        if (this.config.enableReferrerPolicy) {
            headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';
        }

        if (this.config.enablePermissionsPolicy) {
            headers['Permissions-Policy'] = this.buildPermissionsPolicyHeader();
        }

        headers['Cross-Origin-Embedder-Policy'] = 'require-corp';
        headers['Cross-Origin-Opener-Policy'] = 'same-origin';
        headers['Cross-Origin-Resource-Policy'] = 'same-origin';

        headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, private';
        headers['Pragma'] = 'no-cache';
        headers['Expires'] = '0';

        headers['Server'] = 'SecureServer/1.0';
        headers['X-Powered-By'] = '';

        Object.assign(headers, this.config.customHeaders);

        return headers;
    }

    buildHSTSHeader() {
        let header = `max-age=${this.config.strictTransportSecurity.maxAge}`;
        
        if (this.config.strictTransportSecurity.includeSubDomains) {
            header += '; includeSubDomains';
        }
        
        if (this.config.strictTransportSecurity.preload) {
            header += '; preload';
        }
        
        return header;
    }

    buildCSPHeader(nonce) {
        const csp = this.config.contentSecurityPolicy;
        const directives = [];

        if (csp.defaultSrc) {
            directives.push(`default-src ${csp.defaultSrc.join(' ')}`);
        }

        if (csp.scriptSrc) {
            const scriptSrc = [...csp.scriptSrc];
            if (nonce) {
                scriptSrc.push(`'nonce-${nonce}'`);
            }
            directives.push(`script-src ${scriptSrc.join(' ')}`);
        }

        if (csp.styleSrc) {
            const styleSrc = [...csp.styleSrc];
            if (nonce) {
                styleSrc.push(`'nonce-${nonce}'`);
            }
            directives.push(`style-src ${styleSrc.join(' ')}`);
        }

        if (csp.imgSrc) {
            directives.push(`img-src ${csp.imgSrc.join(' ')}`);
        }

        if (csp.fontSrc) {
            directives.push(`font-src ${csp.fontSrc.join(' ')}`);
        }

        if (csp.connectSrc) {
            directives.push(`connect-src ${csp.connectSrc.join(' ')}`);
        }

        if (csp.mediaSrc) {
            directives.push(`media-src ${csp.mediaSrc.join(' ')}`);
        }

        if (csp.objectSrc) {
            directives.push(`object-src ${csp.objectSrc.join(' ')}`);
        }

        if (csp.childSrc) {
            directives.push(`child-src ${csp.childSrc.join(' ')}`);
        }

        if (csp.workerSrc) {
            directives.push(`worker-src ${csp.workerSrc.join(' ')}`);
        }

        if (csp.frameSrc) {
            directives.push(`frame-src ${csp.frameSrc.join(' ')}`);
        }

        if (csp.formAction) {
            directives.push(`form-action ${csp.formAction.join(' ')}`);
        }

        if (csp.upgradeInsecureRequests) {
            directives.push('upgrade-insecure-requests');
        }

        if (csp.blockAllMixedContent) {
            directives.push('block-all-mixed-content');
        }

        directives.push(`report-uri ${this.reportEndpoint}`);

        return directives.join('; ');
    }

    buildPermissionsPolicyHeader() {
        const policies = [];
        const permissions = this.config.permissionsPolicy;

        for (const [feature, policy] of Object.entries(permissions)) {
            if (policy === 'none') {
                policies.push(`${feature}=()`);
            } else if (policy === 'self') {
                policies.push(`${feature}=(self)`);
            } else if (policy === '*') {
                policies.push(`${feature}=*`);
            } else if (Array.isArray(policy)) {
                policies.push(`${feature}=(${policy.join(' ')})`);
            }
        }

        return policies.join(', ');
    }

    generateNonce() {
        const nonce = this.generateRandomString(32);
        this.nonces.set(nonce, Date.now());
        this.cleanupExpiredNonces();
        return nonce;
    }

    validateNonce(nonce) {
        return this.nonces.has(nonce);
    }

    cleanupExpiredNonces() {
        const now = Date.now();
        const expiry = 10 * 60 * 1000; // 10 minutes
        
        for (const [nonce, timestamp] of this.nonces.entries()) {
            if (now - timestamp > expiry) {
                this.nonces.delete(nonce);
            }
        }
    }

    generateRandomString(length) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        
        if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
            const array = new Uint8Array(length);
            crypto.getRandomValues(array);
            for (let i = 0; i < length; i++) {
                result += chars[array[i] % chars.length];
            }
        } else {
            for (let i = 0; i < length; i++) {
                result += chars[Math.floor(Math.random() * chars.length)];
            }
        }
        
        return result;
    }

    isHTTPS(request) {
        if (request.protocol) {
            return request.protocol === 'https:';
        }
        
        if (request.headers) {
            return request.headers['x-forwarded-proto'] === 'https' ||
                   request.headers['x-forwarded-ssl'] === 'on' ||
                   request.headers['x-url-scheme'] === 'https';
        }
        
        return false;
    }

    applyHeaders(response, request = {}) {
        const headers = this.generateSecurityHeaders(request);
        
        for (const [name, value] of Object.entries(headers)) {
            if (value !== '') {
                if (typeof response.setHeader === 'function') {
                    response.setHeader(name, value);
                } else if (response.headers) {
                    response.headers[name] = value;
                }
            }
        }
        
        return response;
    }

    createMiddleware() {
        return (req, res, next) => {
            this.applyHeaders(res, req);
            
            if (typeof next === 'function') {
                next();
            }
        };
    }

    validateSecurityHeaders(headers) {
        const issues = [];
        const recommendations = [];

        if (!headers['strict-transport-security'] && this.config.enableHSTS) {
            issues.push('Missing Strict-Transport-Security header');
            recommendations.push('Enable HSTS to prevent protocol downgrade attacks');
        }

        if (!headers['content-security-policy'] && this.config.enableCSP) {
            issues.push('Missing Content-Security-Policy header');
            recommendations.push('Implement CSP to prevent XSS attacks');
        }

        if (!headers['x-frame-options'] && !headers['content-security-policy']) {
            issues.push('Missing X-Frame-Options header');
            recommendations.push('Add X-Frame-Options to prevent clickjacking');
        }

        if (!headers['x-content-type-options']) {
            issues.push('Missing X-Content-Type-Options header');
            recommendations.push('Add X-Content-Type-Options: nosniff');
        }

        if (headers['server'] && headers['server'].toLowerCase().includes('apache')) {
            issues.push('Server header reveals server information');
            recommendations.push('Hide or modify server header');
        }

        if (headers['x-powered-by']) {
            issues.push('X-Powered-By header reveals technology stack');
            recommendations.push('Remove X-Powered-By header');
        }

        return {
            issues,
            recommendations,
            score: Math.max(0, 100 - (issues.length * 10))
        };
    }

    generateSecurityReport() {
        return {
            timestamp: new Date().toISOString(),
            configuration: {
                hsts: this.config.enableHSTS,
                csp: this.config.enableCSP,
                xFrameOptions: this.config.enableXFrameOptions,
                xContentTypeOptions: this.config.enableXContentTypeOptions,
                referrerPolicy: this.config.enableReferrerPolicy,
                permissionsPolicy: this.config.enablePermissionsPolicy
            },
            activeNonces: this.nonces.size,
            recommendations: this.getSecurityRecommendations()
        };
    }

    getSecurityRecommendations() {
        const recommendations = [];

        if (!this.config.enableHSTS) {
            recommendations.push({
                type: 'hsts',
                priority: 'high',
                message: 'Enable HSTS to prevent protocol downgrade attacks'
            });
        }

        if (!this.config.enableCSP) {
            recommendations.push({
                type: 'csp',
                priority: 'high',
                message: 'Implement Content Security Policy to prevent XSS attacks'
            });
        }

        if (this.config.contentSecurityPolicy.scriptSrc.includes("'unsafe-inline'")) {
            recommendations.push({
                type: 'csp-inline',
                priority: 'medium',
                message: 'Remove unsafe-inline from script-src and use nonces instead'
            });
        }

        if (!this.config.enablePermissionsPolicy) {
            recommendations.push({
                type: 'permissions-policy',
                priority: 'medium',
                message: 'Enable Permissions Policy to control browser features'
            });
        }

        return recommendations;
    }

    updateConfiguration(newConfig) {
        Object.assign(this.config, newConfig);
        return this.config;
    }

    exportConfiguration() {
        return JSON.stringify(this.config, null, 2);
    }

    importConfiguration(configString) {
        try {
            const config = JSON.parse(configString);
            this.updateConfiguration(config);
            return true;
        } catch (error) {
            console.error('Failed to import configuration:', error);
            return false;
        }
    }
}

class HTTPSEnforcer {
    constructor(options = {}) {
        this.config = {
            port: options.port || 443,
            redirectPort: options.redirectPort || 80,
            enableRedirect: options.enableRedirect !== false,
            trustProxy: options.trustProxy || false,
            maxAge: options.maxAge || 31536000,
            includeSubDomains: options.includeSubDomains !== false
        };
    }

    enforceHTTPS(req, res, next) {
        if (this.isSecure(req)) {
            if (typeof next === 'function') {
                next();
            }
            return;
        }

        if (this.config.enableRedirect) {
            const redirectUrl = this.buildHTTPSUrl(req);
            
            if (typeof res.redirect === 'function') {
                res.redirect(301, redirectUrl);
            } else {
                res.statusCode = 301;
                res.setHeader('Location', redirectUrl);
                res.end();
            }
        } else {
            res.statusCode = 403;
            res.end('HTTPS Required');
        }
    }

    isSecure(req) {
        if (req.secure) {
            return true;
        }

        if (this.config.trustProxy) {
            const proto = req.headers['x-forwarded-proto'];
            return proto === 'https';
        }

        return req.protocol === 'https';
    }

    buildHTTPSUrl(req) {
        const host = req.headers.host || req.hostname;
        const url = req.url || req.originalUrl || '/';
        return `https://${host}${url}`;
    }

    createMiddleware() {
        return (req, res, next) => {
            this.enforceHTTPS(req, res, next);
        };
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SecurityHeaders, HTTPSEnforcer };
} else {
    window.SecurityHeaders = SecurityHeaders;
    window.HTTPSEnforcer = HTTPSEnforcer;
}