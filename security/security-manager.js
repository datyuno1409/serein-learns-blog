class SecurityManager {
    constructor(options = {}) {
        this.config = {
            enableAuthentication: options.enableAuthentication !== false,
            enableEncryption: options.enableEncryption !== false,
            enableAccessControl: options.enableAccessControl !== false,
            enableMonitoring: options.enableMonitoring !== false,
            enableSecurityHeaders: options.enableSecurityHeaders !== false,
            enableInputValidation: options.enableInputValidation !== false,
            logLevel: options.logLevel || 'info',
            alertThreshold: options.alertThreshold || 5,
            blockThreshold: options.blockThreshold || 10
        };

        this.components = {
            auth: null,
            oauth2: null,
            crypto: null,
            rbac: null,
            monitor: null,
            headers: null,
            validator: null
        };

        this.eventListeners = new Map();
        this.securityEvents = [];
        this.blockedIPs = new Set();
        this.suspiciousActivities = new Map();
        
        this.initializeComponents(options);
    }

    async initializeComponents(options) {
        try {
            if (this.config.enableAuthentication) {
                const { AuthenticationManager } = await import('./auth/auth.js');
                this.components.auth = new AuthenticationManager(options.auth);

                const { OAuth2Provider } = await import('./auth/oauth2.js');
                this.components.oauth2 = new OAuth2Provider(options.oauth2);
            }

            if (this.config.enableEncryption) {
                const { CryptoManager } = await import('./encryption/crypto.js');
                this.components.crypto = new CryptoManager(options.crypto);
            }

            if (this.config.enableAccessControl) {
                const { RoleBasedAccessControl } = await import('./access/rbac.js');
                this.components.rbac = new RoleBasedAccessControl(options.rbac);
            }

            if (this.config.enableMonitoring) {
                const { SecurityMonitor } = await import('./monitoring/security-monitor.js');
                this.components.monitor = new SecurityMonitor(options.monitor);
                this.setupMonitoringEvents();
            }

            if (this.config.enableSecurityHeaders) {
                const { SecurityHeaders } = await import('./headers/security-headers.js');
                this.components.headers = new SecurityHeaders(options.headers);
            }

            if (this.config.enableInputValidation) {
                const { InputValidator } = await import('./validation/input-validator.js');
                this.components.validator = new InputValidator(options.validator);
            }

            this.logSecurityEvent('SYSTEM_INITIALIZED', 'Security Manager initialized successfully');
        } catch (error) {
            this.logSecurityEvent('INITIALIZATION_ERROR', `Failed to initialize components: ${error.message}`);
            throw error;
        }
    }

    setupMonitoringEvents() {
        if (this.components.monitor) {
            this.components.monitor.on('threat-detected', (threat) => {
                this.handleThreatDetection(threat);
            });

            this.components.monitor.on('anomaly-detected', (anomaly) => {
                this.handleAnomalyDetection(anomaly);
            });

            this.components.monitor.on('security-alert', (alert) => {
                this.handleSecurityAlert(alert);
            });
        }
    }

    async authenticateUser(credentials, method = 'local') {
        if (!this.components.auth) {
            throw new Error('Authentication not enabled');
        }

        try {
            const clientInfo = this.extractClientInfo(credentials.request);
            
            if (this.isBlocked(clientInfo.ip)) {
                throw new Error('IP address is blocked due to security violations');
            }

            let result;
            if (method === 'oauth2' && this.components.oauth2) {
                result = await this.components.oauth2.handleCallback(credentials);
            } else {
                result = await this.components.auth.login(credentials.username, credentials.password);
            }

            if (result.success) {
                this.logSecurityEvent('LOGIN_SUCCESS', `User ${credentials.username} logged in successfully`, clientInfo);
                this.clearSuspiciousActivity(clientInfo.ip);
            } else {
                this.logSecurityEvent('LOGIN_FAILED', `Failed login attempt for ${credentials.username}`, clientInfo);
                this.trackSuspiciousActivity(clientInfo.ip, 'FAILED_LOGIN');
            }

            return result;
        } catch (error) {
            this.logSecurityEvent('AUTH_ERROR', `Authentication error: ${error.message}`);
            throw error;
        }
    }

    async authorizeAccess(userId, resource, action) {
        if (!this.components.rbac) {
            return true; // Default allow if RBAC not enabled
        }

        try {
            const hasPermission = await this.components.rbac.checkPermission(userId, resource, action);
            
            if (hasPermission) {
                this.logSecurityEvent('ACCESS_GRANTED', `User ${userId} granted access to ${resource}:${action}`);
            } else {
                this.logSecurityEvent('ACCESS_DENIED', `User ${userId} denied access to ${resource}:${action}`);
                this.trackSuspiciousActivity(userId, 'UNAUTHORIZED_ACCESS_ATTEMPT');
            }

            return hasPermission;
        } catch (error) {
            this.logSecurityEvent('AUTHORIZATION_ERROR', `Authorization error: ${error.message}`);
            return false;
        }
    }

    async encryptSensitiveData(data, context = {}) {
        if (!this.components.crypto) {
            return data; // Return as-is if encryption not enabled
        }

        try {
            const encrypted = await this.components.crypto.encryptWithPassword(data, context.password || 'default');
            this.logSecurityEvent('DATA_ENCRYPTED', 'Sensitive data encrypted successfully');
            return encrypted;
        } catch (error) {
            this.logSecurityEvent('ENCRYPTION_ERROR', `Encryption failed: ${error.message}`);
            throw error;
        }
    }

    async decryptSensitiveData(encryptedData, context = {}) {
        if (!this.components.crypto) {
            return encryptedData; // Return as-is if encryption not enabled
        }

        try {
            const decrypted = await this.components.crypto.decryptWithPassword(encryptedData, context.password || 'default');
            this.logSecurityEvent('DATA_DECRYPTED', 'Sensitive data decrypted successfully');
            return decrypted;
        } catch (error) {
            this.logSecurityEvent('DECRYPTION_ERROR', `Decryption failed: ${error.message}`);
            throw error;
        }
    }

    validateInput(input, rules = {}) {
        if (!this.components.validator) {
            return { isValid: true, sanitized: input, errors: [], threats: [] };
        }

        try {
            const result = this.components.validator.validateInput(input, rules);
            
            if (result.threats.length > 0) {
                this.logSecurityEvent('INPUT_THREAT_DETECTED', `Input validation detected threats: ${result.threats.join(', ')}`);
                this.trackSuspiciousActivity('unknown', 'MALICIOUS_INPUT');
            }

            return result;
        } catch (error) {
            this.logSecurityEvent('VALIDATION_ERROR', `Input validation error: ${error.message}`);
            return { isValid: false, sanitized: null, errors: [error.message], threats: [] };
        }
    }

    applySecurityHeaders(response, request = {}) {
        if (!this.components.headers) {
            return response;
        }

        try {
            return this.components.headers.applyHeaders(response, request);
        } catch (error) {
            this.logSecurityEvent('HEADERS_ERROR', `Failed to apply security headers: ${error.message}`);
            return response;
        }
    }

    createSecurityMiddleware() {
        return async (req, res, next) => {
            try {
                // Apply security headers
                if (this.components.headers) {
                    this.components.headers.applyHeaders(res, req);
                }

                // Check if IP is blocked
                const clientIP = this.extractClientIP(req);
                if (this.isBlocked(clientIP)) {
                    res.status(403).json({ error: 'Access denied' });
                    return;
                }

                // Monitor request
                if (this.components.monitor) {
                    this.components.monitor.analyzeRequest(req);
                }

                // Continue to next middleware
                if (typeof next === 'function') {
                    next();
                }
            } catch (error) {
                this.logSecurityEvent('MIDDLEWARE_ERROR', `Security middleware error: ${error.message}`);
                if (typeof next === 'function') {
                    next(error);
                }
            }
        };
    }

    handleThreatDetection(threat) {
        this.logSecurityEvent('THREAT_DETECTED', `Security threat detected: ${threat.type}`, threat.details);
        
        if (threat.severity === 'high') {
            this.blockIP(threat.source);
            this.sendAlert({
                type: 'HIGH_SEVERITY_THREAT',
                message: `High severity threat detected from ${threat.source}`,
                details: threat
            });
        }

        this.emit('threat-detected', threat);
    }

    handleAnomalyDetection(anomaly) {
        this.logSecurityEvent('ANOMALY_DETECTED', `Security anomaly detected: ${anomaly.type}`, anomaly.details);
        
        if (anomaly.riskScore > 0.8) {
            this.trackSuspiciousActivity(anomaly.source, anomaly.type);
        }

        this.emit('anomaly-detected', anomaly);
    }

    handleSecurityAlert(alert) {
        this.logSecurityEvent('SECURITY_ALERT', alert.message, alert.details);
        
        if (alert.priority === 'critical') {
            this.sendAlert(alert);
        }

        this.emit('security-alert', alert);
    }

    trackSuspiciousActivity(identifier, activityType) {
        if (!this.suspiciousActivities.has(identifier)) {
            this.suspiciousActivities.set(identifier, []);
        }

        const activities = this.suspiciousActivities.get(identifier);
        activities.push({
            type: activityType,
            timestamp: new Date(),
            count: 1
        });

        // Clean old activities (older than 1 hour)
        const oneHourAgo = new Date(Date.now() - 60 * 60 * 1000);
        const recentActivities = activities.filter(a => a.timestamp > oneHourAgo);
        this.suspiciousActivities.set(identifier, recentActivities);

        // Check if threshold exceeded
        const totalCount = recentActivities.reduce((sum, a) => sum + a.count, 0);
        if (totalCount >= this.config.blockThreshold) {
            this.blockIP(identifier);
            this.sendAlert({
                type: 'SUSPICIOUS_ACTIVITY_THRESHOLD_EXCEEDED',
                message: `Blocking ${identifier} due to suspicious activity`,
                details: { identifier, activities: recentActivities }
            });
        } else if (totalCount >= this.config.alertThreshold) {
            this.sendAlert({
                type: 'SUSPICIOUS_ACTIVITY_DETECTED',
                message: `Suspicious activity detected from ${identifier}`,
                details: { identifier, activities: recentActivities }
            });
        }
    }

    clearSuspiciousActivity(identifier) {
        this.suspiciousActivities.delete(identifier);
    }

    blockIP(ip) {
        this.blockedIPs.add(ip);
        this.logSecurityEvent('IP_BLOCKED', `IP address ${ip} has been blocked`);
        
        // Auto-unblock after 24 hours
        setTimeout(() => {
            this.unblockIP(ip);
        }, 24 * 60 * 60 * 1000);
    }

    unblockIP(ip) {
        this.blockedIPs.delete(ip);
        this.logSecurityEvent('IP_UNBLOCKED', `IP address ${ip} has been unblocked`);
    }

    isBlocked(ip) {
        return this.blockedIPs.has(ip);
    }

    sendAlert(alert) {
        // In a real implementation, this would send notifications via email, SMS, etc.
        console.warn('SECURITY ALERT:', alert);
        
        // Store alert for reporting
        this.securityEvents.push({
            ...alert,
            timestamp: new Date(),
            type: 'ALERT'
        });

        this.emit('alert', alert);
    }

    logSecurityEvent(type, message, details = {}) {
        const event = {
            timestamp: new Date(),
            type,
            message,
            details
        };

        this.securityEvents.push(event);

        // Keep only last 10000 events
        if (this.securityEvents.length > 10000) {
            this.securityEvents = this.securityEvents.slice(-10000);
        }

        if (this.config.logLevel === 'debug' || (this.config.logLevel === 'info' && type !== 'DEBUG')) {
            console.log(`[SECURITY] ${type}: ${message}`, details);
        }
    }

    extractClientInfo(request) {
        return {
            ip: this.extractClientIP(request),
            userAgent: request.headers?.['user-agent'] || 'unknown',
            timestamp: new Date()
        };
    }

    extractClientIP(request) {
        return request.headers?.['x-forwarded-for']?.split(',')[0] ||
               request.headers?.['x-real-ip'] ||
               request.connection?.remoteAddress ||
               request.socket?.remoteAddress ||
               'unknown';
    }

    generateSecurityReport() {
        const now = new Date();
        const last24Hours = new Date(now.getTime() - 24 * 60 * 60 * 1000);
        
        const recentEvents = this.securityEvents.filter(e => e.timestamp > last24Hours);
        const eventsByType = {};
        
        recentEvents.forEach(event => {
            eventsByType[event.type] = (eventsByType[event.type] || 0) + 1;
        });

        return {
            timestamp: now.toISOString(),
            period: '24 hours',
            summary: {
                totalEvents: recentEvents.length,
                blockedIPs: this.blockedIPs.size,
                suspiciousActivities: this.suspiciousActivities.size,
                eventsByType
            },
            components: {
                authentication: this.components.auth ? this.components.auth.getStats() : null,
                encryption: this.components.crypto ? 'enabled' : 'disabled',
                accessControl: this.components.rbac ? 'enabled' : 'disabled',
                monitoring: this.components.monitor ? this.components.monitor.getStats() : null,
                headers: this.components.headers ? 'enabled' : 'disabled',
                validation: this.components.validator ? 'enabled' : 'disabled'
            },
            recommendations: this.getSecurityRecommendations()
        };
    }

    getSecurityRecommendations() {
        const recommendations = [];

        if (!this.config.enableAuthentication) {
            recommendations.push({
                priority: 'high',
                message: 'Enable authentication to secure user access'
            });
        }

        if (!this.config.enableEncryption) {
            recommendations.push({
                priority: 'high',
                message: 'Enable encryption to protect sensitive data'
            });
        }

        if (!this.config.enableAccessControl) {
            recommendations.push({
                priority: 'medium',
                message: 'Enable access control for fine-grained permissions'
            });
        }

        if (!this.config.enableMonitoring) {
            recommendations.push({
                priority: 'medium',
                message: 'Enable security monitoring for threat detection'
            });
        }

        if (this.blockedIPs.size > 100) {
            recommendations.push({
                priority: 'medium',
                message: 'High number of blocked IPs detected - review security policies'
            });
        }

        return recommendations;
    }

    on(event, listener) {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, []);
        }
        this.eventListeners.get(event).push(listener);
    }

    emit(event, data) {
        const listeners = this.eventListeners.get(event) || [];
        listeners.forEach(listener => {
            try {
                listener(data);
            } catch (error) {
                console.error(`Error in event listener for ${event}:`, error);
            }
        });
    }

    updateConfiguration(newConfig) {
        Object.assign(this.config, newConfig);
        return this.config;
    }

    getConfiguration() {
        return { ...this.config };
    }

    async shutdown() {
        this.logSecurityEvent('SYSTEM_SHUTDOWN', 'Security Manager shutting down');
        
        // Clean up components
        for (const [name, component] of Object.entries(this.components)) {
            if (component && typeof component.shutdown === 'function') {
                try {
                    await component.shutdown();
                } catch (error) {
                    console.error(`Error shutting down ${name}:`, error);
                }
            }
        }

        // Clear event listeners
        this.eventListeners.clear();
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SecurityManager };
} else {
    window.SecurityManager = SecurityManager;
}