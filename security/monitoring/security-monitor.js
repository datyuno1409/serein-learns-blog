class SecurityMonitor {
    constructor(options = {}) {
        this.config = {
            maxFailedAttempts: options.maxFailedAttempts || 5,
            lockoutDuration: options.lockoutDuration || 15 * 60 * 1000, // 15 minutes
            suspiciousActivityThreshold: options.suspiciousActivityThreshold || 10,
            alertThreshold: options.alertThreshold || 3,
            monitoringInterval: options.monitoringInterval || 60000, // 1 minute
            retentionPeriod: options.retentionPeriod || 30 * 24 * 60 * 60 * 1000, // 30 days
            enableRealTimeAlerts: options.enableRealTimeAlerts !== false,
            enableGeolocationTracking: options.enableGeolocationTracking || false
        };

        this.events = [];
        this.alerts = [];
        this.blockedIPs = new Map();
        this.suspiciousUsers = new Map();
        this.activeThreats = new Map();
        this.metrics = {
            totalEvents: 0,
            securityIncidents: 0,
            blockedAttempts: 0,
            alertsGenerated: 0
        };

        this.threatPatterns = new Map();
        this.anomalyDetector = new AnomalyDetector();
        this.geoTracker = new GeolocationTracker();
        
        this.initializeThreatPatterns();
        this.startMonitoring();
    }

    initializeThreatPatterns() {
        this.threatPatterns.set('brute_force', {
            name: 'Brute Force Attack',
            pattern: (events) => {
                const recentFailures = events.filter(e => 
                    e.type === 'login_failed' && 
                    Date.now() - new Date(e.timestamp).getTime() < 5 * 60 * 1000
                );
                return recentFailures.length >= this.config.maxFailedAttempts;
            },
            severity: 'high',
            response: 'block_ip'
        });

        this.threatPatterns.set('sql_injection', {
            name: 'SQL Injection Attempt',
            pattern: (events) => {
                return events.some(e => 
                    e.type === 'request' && 
                    this.detectSQLInjection(e.data?.query || e.data?.body || '')
                );
            },
            severity: 'critical',
            response: 'block_ip_immediate'
        });

        this.threatPatterns.set('xss_attempt', {
            name: 'Cross-Site Scripting Attempt',
            pattern: (events) => {
                return events.some(e => 
                    e.type === 'request' && 
                    this.detectXSS(e.data?.query || e.data?.body || '')
                );
            },
            severity: 'high',
            response: 'block_request'
        });

        this.threatPatterns.set('unusual_access', {
            name: 'Unusual Access Pattern',
            pattern: (events) => {
                const accessEvents = events.filter(e => e.type === 'access');
                return this.anomalyDetector.detectAnomalies(accessEvents);
            },
            severity: 'medium',
            response: 'monitor'
        });

        this.threatPatterns.set('privilege_escalation', {
            name: 'Privilege Escalation Attempt',
            pattern: (events) => {
                return events.some(e => 
                    e.type === 'permission_denied' && 
                    e.data?.attemptedPermission && 
                    this.isPrivilegeEscalation(e.data)
                );
            },
            severity: 'critical',
            response: 'alert_admin'
        });

        this.threatPatterns.set('data_exfiltration', {
            name: 'Data Exfiltration Attempt',
            pattern: (events) => {
                const downloadEvents = events.filter(e => 
                    e.type === 'file_access' && 
                    e.data?.action === 'download'
                );
                return this.detectDataExfiltration(downloadEvents);
            },
            severity: 'critical',
            response: 'block_user'
        });
    }

    logEvent(type, data = {}, userId = null, ipAddress = null) {
        const event = {
            id: this.generateEventId(),
            type,
            data,
            userId,
            ipAddress,
            timestamp: new Date().toISOString(),
            userAgent: data.userAgent || null,
            geolocation: this.config.enableGeolocationTracking ? 
                this.geoTracker.getLocation(ipAddress) : null,
            severity: this.calculateSeverity(type, data),
            processed: false
        };

        this.events.push(event);
        this.metrics.totalEvents++;

        if (this.config.enableRealTimeAlerts) {
            this.processEventRealTime(event);
        }

        this.cleanupOldEvents();
        return event.id;
    }

    processEventRealTime(event) {
        const relatedEvents = this.getRelatedEvents(event);
        
        for (const [patternName, pattern] of this.threatPatterns) {
            try {
                if (pattern.pattern([event, ...relatedEvents])) {
                    this.handleThreatDetection(patternName, pattern, event, relatedEvents);
                }
            } catch (error) {
                console.error(`Error processing threat pattern '${patternName}':`, error);
            }
        }
    }

    getRelatedEvents(event, timeWindow = 10 * 60 * 1000) {
        const eventTime = new Date(event.timestamp).getTime();
        
        return this.events.filter(e => {
            const eTime = new Date(e.timestamp).getTime();
            return Math.abs(eventTime - eTime) <= timeWindow && 
                   (e.userId === event.userId || e.ipAddress === event.ipAddress) &&
                   e.id !== event.id;
        });
    }

    handleThreatDetection(patternName, pattern, triggerEvent, relatedEvents) {
        const threat = {
            id: this.generateThreatId(),
            pattern: patternName,
            name: pattern.name,
            severity: pattern.severity,
            triggerEvent,
            relatedEvents,
            detectedAt: new Date().toISOString(),
            status: 'active',
            response: pattern.response
        };

        this.activeThreats.set(threat.id, threat);
        this.executeResponse(threat);
        this.generateAlert(threat);
        
        this.metrics.securityIncidents++;
    }

    executeResponse(threat) {
        switch (threat.response) {
            case 'block_ip':
                this.blockIP(threat.triggerEvent.ipAddress, 'Threat detected: ' + threat.name);
                break;
                
            case 'block_ip_immediate':
                this.blockIP(threat.triggerEvent.ipAddress, 'Critical threat: ' + threat.name, true);
                break;
                
            case 'block_user':
                if (threat.triggerEvent.userId) {
                    this.blockUser(threat.triggerEvent.userId, 'Security violation: ' + threat.name);
                }
                break;
                
            case 'block_request':
                this.metrics.blockedAttempts++;
                break;
                
            case 'alert_admin':
                this.sendAdminAlert(threat);
                break;
                
            case 'monitor':
                this.addToSuspiciousUsers(threat.triggerEvent.userId || threat.triggerEvent.ipAddress);
                break;
        }
    }

    blockIP(ipAddress, reason, immediate = false) {
        if (!ipAddress) return;

        const blockDuration = immediate ? 24 * 60 * 60 * 1000 : this.config.lockoutDuration;
        const blockUntil = Date.now() + blockDuration;

        this.blockedIPs.set(ipAddress, {
            reason,
            blockedAt: new Date().toISOString(),
            blockUntil,
            immediate
        });

        this.logEvent('ip_blocked', { ipAddress, reason, blockUntil });
    }

    blockUser(userId, reason) {
        this.suspiciousUsers.set(userId, {
            reason,
            blockedAt: new Date().toISOString(),
            blockUntil: Date.now() + this.config.lockoutDuration,
            status: 'blocked'
        });

        this.logEvent('user_blocked', { userId, reason });
    }

    addToSuspiciousUsers(identifier) {
        if (this.suspiciousUsers.has(identifier)) {
            const existing = this.suspiciousUsers.get(identifier);
            existing.suspicionLevel = (existing.suspicionLevel || 1) + 1;
            existing.lastActivity = new Date().toISOString();
        } else {
            this.suspiciousUsers.set(identifier, {
                suspicionLevel: 1,
                firstDetected: new Date().toISOString(),
                lastActivity: new Date().toISOString(),
                status: 'monitoring'
            });
        }
    }

    isIPBlocked(ipAddress) {
        const blockInfo = this.blockedIPs.get(ipAddress);
        if (!blockInfo) return false;

        if (Date.now() > blockInfo.blockUntil) {
            this.blockedIPs.delete(ipAddress);
            return false;
        }

        return true;
    }

    isUserBlocked(userId) {
        const userInfo = this.suspiciousUsers.get(userId);
        if (!userInfo || userInfo.status !== 'blocked') return false;

        if (Date.now() > userInfo.blockUntil) {
            userInfo.status = 'monitoring';
            return false;
        }

        return true;
    }

    generateAlert(threat) {
        const alert = {
            id: this.generateAlertId(),
            type: 'security_threat',
            severity: threat.severity,
            title: threat.name,
            description: `Security threat detected: ${threat.name}`,
            threat,
            timestamp: new Date().toISOString(),
            status: 'active',
            acknowledged: false
        };

        this.alerts.push(alert);
        this.metrics.alertsGenerated++;

        if (threat.severity === 'critical') {
            this.sendCriticalAlert(alert);
        }

        return alert;
    }

    sendAdminAlert(threat) {
        console.warn('SECURITY ALERT:', {
            threat: threat.name,
            severity: threat.severity,
            user: threat.triggerEvent.userId,
            ip: threat.triggerEvent.ipAddress,
            timestamp: threat.detectedAt
        });
    }

    sendCriticalAlert(alert) {
        console.error('CRITICAL SECURITY ALERT:', alert);
    }

    detectSQLInjection(input) {
        const sqlPatterns = [
            /('|(\-\-)|(;)|(\||\|)|(\*|\*))/i,
            /(union|select|insert|delete|update|drop|create|alter|exec|execute)/i,
            /(script|javascript|vbscript|onload|onerror|onclick)/i,
            /(\<|\>|\"|\'|\%|\;|\(|\)|\&|\+)/
        ];

        return sqlPatterns.some(pattern => pattern.test(input));
    }

    detectXSS(input) {
        const xssPatterns = [
            /<script[^>]*>.*?<\/script>/gi,
            /<iframe[^>]*>.*?<\/iframe>/gi,
            /javascript:/gi,
            /on\w+\s*=/gi,
            /<img[^>]*src[^>]*>/gi,
            /<object[^>]*>.*?<\/object>/gi
        ];

        return xssPatterns.some(pattern => pattern.test(input));
    }

    isPrivilegeEscalation(data) {
        const adminPermissions = ['admin', 'system_config', 'manage_users', 'delete'];
        return adminPermissions.includes(data.attemptedPermission) && 
               data.userRole !== 'admin';
    }

    detectDataExfiltration(downloadEvents) {
        if (downloadEvents.length < 5) return false;

        const timeWindow = 10 * 60 * 1000; // 10 minutes
        const recentDownloads = downloadEvents.filter(e => 
            Date.now() - new Date(e.timestamp).getTime() < timeWindow
        );

        const totalSize = recentDownloads.reduce((sum, e) => 
            sum + (e.data?.fileSize || 0), 0
        );

        return recentDownloads.length >= 5 || totalSize > 100 * 1024 * 1024; // 100MB
    }

    calculateSeverity(type, data) {
        const severityMap = {
            'login_failed': 'low',
            'permission_denied': 'medium',
            'sql_injection_attempt': 'critical',
            'xss_attempt': 'high',
            'file_access': 'low',
            'admin_action': 'medium',
            'system_error': 'medium',
            'data_breach': 'critical'
        };

        return severityMap[type] || 'low';
    }

    startMonitoring() {
        setInterval(() => {
            this.performPeriodicAnalysis();
            this.cleanupExpiredBlocks();
            this.updateMetrics();
        }, this.config.monitoringInterval);
    }

    performPeriodicAnalysis() {
        const unprocessedEvents = this.events.filter(e => !e.processed);
        
        if (unprocessedEvents.length === 0) return;

        const analysisResults = this.anomalyDetector.analyzeEvents(unprocessedEvents);
        
        analysisResults.anomalies.forEach(anomaly => {
            this.handleAnomalyDetection(anomaly);
        });

        unprocessedEvents.forEach(event => {
            event.processed = true;
        });
    }

    handleAnomalyDetection(anomaly) {
        const alert = {
            id: this.generateAlertId(),
            type: 'anomaly_detected',
            severity: anomaly.severity,
            title: 'Anomalous Behavior Detected',
            description: anomaly.description,
            anomaly,
            timestamp: new Date().toISOString(),
            status: 'active',
            acknowledged: false
        };

        this.alerts.push(alert);
    }

    cleanupExpiredBlocks() {
        const now = Date.now();
        
        for (const [ip, blockInfo] of this.blockedIPs.entries()) {
            if (now > blockInfo.blockUntil) {
                this.blockedIPs.delete(ip);
                this.logEvent('ip_unblocked', { ipAddress: ip });
            }
        }

        for (const [userId, userInfo] of this.suspiciousUsers.entries()) {
            if (userInfo.status === 'blocked' && now > userInfo.blockUntil) {
                userInfo.status = 'monitoring';
                this.logEvent('user_unblocked', { userId });
            }
        }
    }

    cleanupOldEvents() {
        const cutoffTime = Date.now() - this.config.retentionPeriod;
        this.events = this.events.filter(event => 
            new Date(event.timestamp).getTime() > cutoffTime
        );
    }

    updateMetrics() {
        const now = Date.now();
        const last24Hours = now - 24 * 60 * 60 * 1000;
        
        const recentEvents = this.events.filter(e => 
            new Date(e.timestamp).getTime() > last24Hours
        );

        this.metrics.eventsLast24h = recentEvents.length;
        this.metrics.activeThreats = this.activeThreats.size;
        this.metrics.blockedIPs = this.blockedIPs.size;
        this.metrics.suspiciousUsers = Array.from(this.suspiciousUsers.values())
            .filter(u => u.status === 'monitoring').length;
    }

    getSecurityReport(timeRange = 24 * 60 * 60 * 1000) {
        const cutoffTime = Date.now() - timeRange;
        const recentEvents = this.events.filter(e => 
            new Date(e.timestamp).getTime() > cutoffTime
        );

        const recentAlerts = this.alerts.filter(a => 
            new Date(a.timestamp).getTime() > cutoffTime
        );

        return {
            summary: {
                totalEvents: recentEvents.length,
                securityIncidents: recentAlerts.filter(a => a.type === 'security_threat').length,
                anomalies: recentAlerts.filter(a => a.type === 'anomaly_detected').length,
                blockedIPs: this.blockedIPs.size,
                suspiciousUsers: this.suspiciousUsers.size
            },
            events: recentEvents,
            alerts: recentAlerts,
            threats: Array.from(this.activeThreats.values()),
            metrics: this.metrics
        };
    }

    acknowledgeAlert(alertId) {
        const alert = this.alerts.find(a => a.id === alertId);
        if (alert) {
            alert.acknowledged = true;
            alert.acknowledgedAt = new Date().toISOString();
            return true;
        }
        return false;
    }

    generateEventId() {
        return 'evt_' + Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    generateThreatId() {
        return 'thr_' + Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    generateAlertId() {
        return 'alt_' + Date.now().toString(36) + Math.random().toString(36).substr(2);
    }
}

class AnomalyDetector {
    constructor() {
        this.baselines = new Map();
        this.learningPeriod = 7 * 24 * 60 * 60 * 1000; // 7 days
    }

    analyzeEvents(events) {
        const anomalies = [];
        
        const eventsByType = this.groupEventsByType(events);
        
        for (const [type, typeEvents] of eventsByType) {
            const baseline = this.getBaseline(type);
            const currentRate = this.calculateEventRate(typeEvents);
            
            if (this.isAnomalousRate(currentRate, baseline)) {
                anomalies.push({
                    type: 'unusual_event_rate',
                    eventType: type,
                    currentRate,
                    baseline: baseline.averageRate,
                    severity: this.calculateAnomalySeverity(currentRate, baseline),
                    description: `Unusual ${type} event rate detected`
                });
            }
        }

        return { anomalies };
    }

    groupEventsByType(events) {
        const grouped = new Map();
        events.forEach(event => {
            if (!grouped.has(event.type)) {
                grouped.set(event.type, []);
            }
            grouped.get(event.type).push(event);
        });
        return grouped;
    }

    getBaseline(eventType) {
        if (!this.baselines.has(eventType)) {
            this.baselines.set(eventType, {
                averageRate: 0,
                standardDeviation: 0,
                lastUpdated: Date.now()
            });
        }
        return this.baselines.get(eventType);
    }

    calculateEventRate(events) {
        if (events.length === 0) return 0;
        
        const timeSpan = Math.max(
            new Date(events[events.length - 1].timestamp).getTime() - 
            new Date(events[0].timestamp).getTime(),
            60000 // minimum 1 minute
        );
        
        return (events.length / timeSpan) * 60000; // events per minute
    }

    isAnomalousRate(currentRate, baseline) {
        if (baseline.averageRate === 0) return false;
        
        const threshold = baseline.averageRate + (2 * baseline.standardDeviation);
        return currentRate > threshold;
    }

    calculateAnomalySeverity(currentRate, baseline) {
        const ratio = currentRate / (baseline.averageRate || 1);
        
        if (ratio > 10) return 'critical';
        if (ratio > 5) return 'high';
        if (ratio > 2) return 'medium';
        return 'low';
    }

    detectAnomalies(events) {
        return events.length > 0 && this.analyzeEvents(events).anomalies.length > 0;
    }
}

class GeolocationTracker {
    constructor() {
        this.locationCache = new Map();
        this.cacheExpiry = 24 * 60 * 60 * 1000; // 24 hours
    }

    getLocation(ipAddress) {
        if (!ipAddress) return null;
        
        const cached = this.locationCache.get(ipAddress);
        if (cached && Date.now() - cached.timestamp < this.cacheExpiry) {
            return cached.location;
        }

        const location = this.lookupLocation(ipAddress);
        this.locationCache.set(ipAddress, {
            location,
            timestamp: Date.now()
        });
        
        return location;
    }

    lookupLocation(ipAddress) {
        try {
            return {
                ip: ipAddress,
                country: 'Unknown',
                region: 'Unknown',
                city: 'Unknown',
                isp: 'Unknown'
            };
        } catch (error) {
            return null;
        }
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SecurityMonitor, AnomalyDetector, GeolocationTracker };
} else {
    window.SecurityMonitor = SecurityMonitor;
    window.AnomalyDetector = AnomalyDetector;
    window.GeolocationTracker = GeolocationTracker;
}