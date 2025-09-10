class AuthenticationManager {
    constructor() {
        this.users = new Map();
        this.sessions = new Map();
        this.refreshTokens = new Map();
        this.secretKey = this.generateSecretKey();
        this.tokenExpiry = 3600000; // 1 hour
        this.refreshTokenExpiry = 604800000; // 7 days
        this.maxLoginAttempts = 5;
        this.lockoutDuration = 900000; // 15 minutes
        this.loginAttempts = new Map();
        this.lockedAccounts = new Map();
    }

    generateSecretKey() {
        const array = new Uint8Array(32);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    async hashPassword(password, salt) {
        const encoder = new TextEncoder();
        const data = encoder.encode(password + salt);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        return Array.from(new Uint8Array(hashBuffer), b => b.toString(16).padStart(2, '0')).join('');
    }

    generateSalt() {
        const array = new Uint8Array(16);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    generateToken(payload) {
        const header = {
            alg: 'HS256',
            typ: 'JWT'
        };

        const now = Date.now();
        const tokenPayload = {
            ...payload,
            iat: now,
            exp: now + this.tokenExpiry,
            jti: this.generateId()
        };

        const encodedHeader = btoa(JSON.stringify(header)).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
        const encodedPayload = btoa(JSON.stringify(tokenPayload)).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
        
        const signature = this.createSignature(encodedHeader + '.' + encodedPayload);
        
        return `${encodedHeader}.${encodedPayload}.${signature}`;
    }

    createSignature(data) {
        return btoa(data + this.secretKey).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
    }

    verifyToken(token) {
        try {
            const parts = token.split('.');
            if (parts.length !== 3) return null;

            const [header, payload, signature] = parts;
            const expectedSignature = this.createSignature(header + '.' + payload);
            
            if (signature !== expectedSignature) return null;

            const decodedPayload = JSON.parse(atob(payload.replace(/-/g, '+').replace(/_/g, '/')));
            
            if (decodedPayload.exp < Date.now()) return null;

            return decodedPayload;
        } catch (error) {
            return null;
        }
    }

    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    async register(username, email, password, role = 'user') {
        if (this.users.has(username) || Array.from(this.users.values()).some(u => u.email === email)) {
            throw new Error('User already exists');
        }

        if (!this.validatePassword(password)) {
            throw new Error('Password does not meet security requirements');
        }

        const salt = this.generateSalt();
        const hashedPassword = await this.hashPassword(password, salt);
        const userId = this.generateId();

        const user = {
            id: userId,
            username,
            email,
            password: hashedPassword,
            salt,
            role,
            createdAt: new Date().toISOString(),
            isActive: true,
            lastLogin: null,
            failedLoginAttempts: 0,
            isLocked: false,
            lockedUntil: null,
            twoFactorEnabled: false,
            twoFactorSecret: null
        };

        this.users.set(username, user);
        return { id: userId, username, email, role };
    }

    validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        return password.length >= minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChar;
    }

    async login(username, password) {
        const user = this.users.get(username);
        if (!user) {
            throw new Error('Invalid credentials');
        }

        if (user.isLocked && user.lockedUntil && new Date() < new Date(user.lockedUntil)) {
            throw new Error('Account is locked. Please try again later.');
        }

        if (user.isLocked && user.lockedUntil && new Date() >= new Date(user.lockedUntil)) {
            user.isLocked = false;
            user.lockedUntil = null;
            user.failedLoginAttempts = 0;
        }

        const hashedPassword = await this.hashPassword(password, user.salt);
        if (hashedPassword !== user.password) {
            user.failedLoginAttempts++;
            
            if (user.failedLoginAttempts >= this.maxLoginAttempts) {
                user.isLocked = true;
                user.lockedUntil = new Date(Date.now() + this.lockoutDuration).toISOString();
            }
            
            throw new Error('Invalid credentials');
        }

        user.failedLoginAttempts = 0;
        user.lastLogin = new Date().toISOString();

        const sessionId = this.generateId();
        const accessToken = this.generateToken({
            userId: user.id,
            username: user.username,
            role: user.role,
            sessionId
        });

        const refreshToken = this.generateId();
        
        this.sessions.set(sessionId, {
            userId: user.id,
            username: user.username,
            role: user.role,
            createdAt: Date.now(),
            lastActivity: Date.now(),
            ipAddress: null,
            userAgent: null
        });

        this.refreshTokens.set(refreshToken, {
            userId: user.id,
            sessionId,
            createdAt: Date.now(),
            expiresAt: Date.now() + this.refreshTokenExpiry
        });

        return {
            accessToken,
            refreshToken,
            user: {
                id: user.id,
                username: user.username,
                email: user.email,
                role: user.role
            }
        };
    }

    refreshAccessToken(refreshToken) {
        const tokenData = this.refreshTokens.get(refreshToken);
        if (!tokenData || tokenData.expiresAt < Date.now()) {
            throw new Error('Invalid or expired refresh token');
        }

        const session = this.sessions.get(tokenData.sessionId);
        if (!session) {
            throw new Error('Session not found');
        }

        const user = Array.from(this.users.values()).find(u => u.id === tokenData.userId);
        if (!user) {
            throw new Error('User not found');
        }

        const newAccessToken = this.generateToken({
            userId: user.id,
            username: user.username,
            role: user.role,
            sessionId: tokenData.sessionId
        });

        session.lastActivity = Date.now();

        return {
            accessToken: newAccessToken,
            user: {
                id: user.id,
                username: user.username,
                email: user.email,
                role: user.role
            }
        };
    }

    logout(sessionId) {
        this.sessions.delete(sessionId);
        
        for (const [token, data] of this.refreshTokens.entries()) {
            if (data.sessionId === sessionId) {
                this.refreshTokens.delete(token);
                break;
            }
        }
    }

    validateSession(token) {
        const payload = this.verifyToken(token);
        if (!payload) return null;

        const session = this.sessions.get(payload.sessionId);
        if (!session) return null;

        session.lastActivity = Date.now();
        return {
            userId: payload.userId,
            username: payload.username,
            role: payload.role,
            sessionId: payload.sessionId
        };
    }

    hasPermission(userRole, requiredRole) {
        const roleHierarchy = {
            'admin': 3,
            'moderator': 2,
            'user': 1,
            'guest': 0
        };

        return (roleHierarchy[userRole] || 0) >= (roleHierarchy[requiredRole] || 0);
    }

    cleanupExpiredSessions() {
        const now = Date.now();
        const sessionTimeout = 24 * 60 * 60 * 1000; // 24 hours

        for (const [sessionId, session] of this.sessions.entries()) {
            if (now - session.lastActivity > sessionTimeout) {
                this.logout(sessionId);
            }
        }

        for (const [token, data] of this.refreshTokens.entries()) {
            if (data.expiresAt < now) {
                this.refreshTokens.delete(token);
            }
        }
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthenticationManager;
} else {
    window.AuthenticationManager = AuthenticationManager;
}