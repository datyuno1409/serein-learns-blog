// Security middleware for enhanced protection
const rateLimit = require('express-rate-limit');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');

// CSRF Protection
class CSRFProtection {
  constructor() {
    this.tokens = new Map();
    this.cleanupInterval = setInterval(() => {
      this.cleanup();
    }, 60000); // Cleanup every minute
  }

  generateToken(sessionId) {
    const token = crypto.randomBytes(32).toString('hex');
    const expiry = Date.now() + (30 * 60 * 1000); // 30 minutes
    
    this.tokens.set(token, {
      sessionId,
      expiry
    });
    
    return token;
  }

  validateToken(token, sessionId) {
    const tokenData = this.tokens.get(token);
    
    if (!tokenData) {
      return false;
    }
    
    if (tokenData.expiry < Date.now()) {
      this.tokens.delete(token);
      return false;
    }
    
    if (tokenData.sessionId !== sessionId) {
      return false;
    }
    
    // Token is valid, remove it (one-time use)
    this.tokens.delete(token);
    return true;
  }

  cleanup() {
    const now = Date.now();
    for (const [token, data] of this.tokens.entries()) {
      if (data.expiry < now) {
        this.tokens.delete(token);
      }
    }
  }
}

const csrfProtection = new CSRFProtection();

// Rate limiting configurations
const createRateLimiter = (windowMs, max, message) => {
  return rateLimit({
    windowMs,
    max,
    message: {
      error: message,
      code: 'RATE_LIMIT_EXCEEDED'
    },
    standardHeaders: true,
    legacyHeaders: false,
    handler: (req, res) => {
      console.warn(`Rate limit exceeded for IP: ${req.ip} on ${req.path}`);
      res.status(429).json({
        success: false,
        error: message,
        code: 'RATE_LIMIT_EXCEEDED',
        retryAfter: Math.ceil(windowMs / 1000)
      });
    }
  });
};

// Different rate limiters for different endpoints
const rateLimiters = {
  // General API rate limiting
  general: createRateLimiter(
    15 * 60 * 1000, // 15 minutes
    100, // 100 requests per window
    'Too many requests, please try again later.'
  ),
  
  // Strict rate limiting for authentication endpoints
  auth: createRateLimiter(
    15 * 60 * 1000, // 15 minutes
    5, // 5 attempts per window
    'Too many authentication attempts, please try again later.'
  ),
  
  // Very strict rate limiting for password reset
  passwordReset: createRateLimiter(
    60 * 60 * 1000, // 1 hour
    3, // 3 attempts per hour
    'Too many password reset attempts, please try again later.'
  ),
  
  // Rate limiting for registration
  registration: createRateLimiter(
    60 * 60 * 1000, // 1 hour
    3, // 3 registrations per hour per IP
    'Too many registration attempts, please try again later.'
  )
};

// Input sanitization middleware
const sanitizeInput = (req, res, next) => {
  const sanitizeString = (str) => {
    if (typeof str !== 'string') return str;
    
    // Remove potentially dangerous characters
    return str
      .replace(/<script[^>]*>.*?<\/script>/gi, '') // Remove script tags
      .replace(/<[^>]*>/g, '') // Remove HTML tags
      .replace(/javascript:/gi, '') // Remove javascript: protocol
      .replace(/on\w+\s*=/gi, '') // Remove event handlers
      .trim();
  };
  
  const sanitizeObject = (obj) => {
    if (typeof obj !== 'object' || obj === null) {
      return sanitizeString(obj);
    }
    
    const sanitized = {};
    for (const [key, value] of Object.entries(obj)) {
      if (Array.isArray(value)) {
        sanitized[key] = value.map(item => sanitizeObject(item));
      } else if (typeof value === 'object' && value !== null) {
        sanitized[key] = sanitizeObject(value);
      } else {
        sanitized[key] = sanitizeString(value);
      }
    }
    return sanitized;
  };
  
  if (req.body) {
    req.body = sanitizeObject(req.body);
  }
  
  if (req.query) {
    req.query = sanitizeObject(req.query);
  }
  
  if (req.params) {
    req.params = sanitizeObject(req.params);
  }
  
  next();
};

// JWT token validation middleware
const validateJWT = (req, res, next) => {
  const token = req.cookies.auth_token || 
                req.cookies.admin_auth_token || 
                req.headers.authorization?.replace('Bearer ', '');
  
  if (!token) {
    return res.status(401).json({
      success: false,
      message: 'Access token required',
      code: 'TOKEN_REQUIRED'
    });
  }
  
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    
    // Check token expiration
    if (decoded.exp && decoded.exp < Date.now() / 1000) {
      return res.status(401).json({
        success: false,
        message: 'Token has expired',
        code: 'TOKEN_EXPIRED'
      });
    }
    
    req.user = decoded;
    next();
  } catch (error) {
    console.error('JWT validation error:', error.message);
    
    let message = 'Invalid token';
    let code = 'TOKEN_INVALID';
    
    if (error.name === 'TokenExpiredError') {
      message = 'Token has expired';
      code = 'TOKEN_EXPIRED';
    } else if (error.name === 'JsonWebTokenError') {
      message = 'Malformed token';
      code = 'TOKEN_MALFORMED';
    }
    
    return res.status(401).json({
      success: false,
      message,
      code
    });
  }
};

// Admin role validation middleware
const requireAdmin = (req, res, next) => {
  if (!req.user) {
    return res.status(401).json({
      success: false,
      message: 'Authentication required',
      code: 'AUTH_REQUIRED'
    });
  }
  
  if (!req.user.is_admin || req.user.role !== 'admin') {
    return res.status(403).json({
      success: false,
      message: 'Admin privileges required',
      code: 'ADMIN_REQUIRED'
    });
  }
  
  next();
};

// CSRF token generation endpoint
const generateCSRFToken = (req, res) => {
  const sessionId = req.sessionID || req.ip + req.headers['user-agent'];
  const token = csrfProtection.generateToken(sessionId);
  
  res.json({
    success: true,
    csrf_token: token
  });
};

// CSRF validation middleware
const validateCSRF = (req, res, next) => {
  const token = req.headers['x-csrf-token'] || req.body.csrf_token;
  const sessionId = req.sessionID || req.ip + req.headers['user-agent'];
  
  if (!token) {
    return res.status(403).json({
      success: false,
      message: 'CSRF token required',
      code: 'CSRF_TOKEN_REQUIRED'
    });
  }
  
  if (!csrfProtection.validateToken(token, sessionId)) {
    return res.status(403).json({
      success: false,
      message: 'Invalid or expired CSRF token',
      code: 'CSRF_TOKEN_INVALID'
    });
  }
  
  next();
};

// Security headers middleware
const securityHeaders = (req, res, next) => {
  // Prevent clickjacking
  res.setHeader('X-Frame-Options', 'DENY');
  
  // Prevent MIME type sniffing
  res.setHeader('X-Content-Type-Options', 'nosniff');
  
  // Enable XSS protection
  res.setHeader('X-XSS-Protection', '1; mode=block');
  
  // Referrer policy
  res.setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
  
  // Permissions policy
  res.setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
  
  next();
};

// Request logging with security info
const securityLogger = (req, res, next) => {
  const timestamp = new Date().toISOString();
  const userAgent = req.headers['user-agent'] || 'Unknown';
  const forwarded = req.headers['x-forwarded-for'] || req.connection.remoteAddress;
  
  console.log(`[${timestamp}] ${req.method} ${req.url}`);
  console.log(`  IP: ${req.ip} (Forwarded: ${forwarded})`);
  console.log(`  User-Agent: ${userAgent}`);
  
  // Log suspicious patterns
  const suspiciousPatterns = [
    /\.\.\//,  // Directory traversal
    /<script/i, // Script injection
    /union.*select/i, // SQL injection
    /javascript:/i, // JavaScript protocol
    /vbscript:/i, // VBScript protocol
  ];
  
  const url = req.url.toLowerCase();
  const body = JSON.stringify(req.body || {}).toLowerCase();
  
  for (const pattern of suspiciousPatterns) {
    if (pattern.test(url) || pattern.test(body)) {
      console.warn(`[SECURITY] Suspicious request detected from ${req.ip}: ${req.method} ${req.url}`);
      break;
    }
  }
  
  next();
};

// Password strength validator
const validatePasswordStrength = (password) => {
  const minLength = 8;
  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
  
  const errors = [];
  
  if (password.length < minLength) {
    errors.push(`Password must be at least ${minLength} characters long`);
  }
  
  if (!hasUpperCase) {
    errors.push('Password must contain at least one uppercase letter');
  }
  
  if (!hasLowerCase) {
    errors.push('Password must contain at least one lowercase letter');
  }
  
  if (!hasNumbers) {
    errors.push('Password must contain at least one number');
  }
  
  if (!hasSpecialChar) {
    errors.push('Password must contain at least one special character');
  }
  
  return {
    isValid: errors.length === 0,
    errors
  };
};

module.exports = {
  rateLimiters,
  sanitizeInput,
  validateJWT,
  requireAdmin,
  generateCSRFToken,
  validateCSRF,
  securityHeaders,
  securityLogger,
  validatePasswordStrength,
  csrfProtection
};