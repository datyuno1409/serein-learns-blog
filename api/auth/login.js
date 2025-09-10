// API endpoint for user login
const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const rateLimit = require('express-rate-limit');
const { body, validationResult } = require('express-validator');
const { validatePasswordStrength } = require('../../middleware/security');
const userService = require('../shared/users');

const router = express.Router();

// Rate limiting for login attempts
const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 5, // limit each IP to 5 requests per windowMs
  message: {
    error: 'Too many login attempts, please try again later.',
    code: 'RATE_LIMIT_EXCEEDED'
  },
  standardHeaders: true,
  legacyHeaders: false,
});

// Using shared user service

// Validation rules
const loginValidation = [
  body('username')
    .notEmpty()
    .withMessage('Username is required')
    .isLength({ min: 3, max: 50 })
    .withMessage('Username must be between 3 and 50 characters'),
  body('password')
    .notEmpty()
    .withMessage('Password is required')
    .isLength({ min: 6 })
    .withMessage('Password must be at least 6 characters long')
];

// Helper function to generate JWT token
function generateToken(user) {
  const payload = {
    id: user.id,
    username: user.username,
    email: user.email,
    role: user.role,
    is_admin: user.is_admin
  };
  
  return jwt.sign(payload, process.env.JWT_SECRET || 'your-secret-key', {
    expiresIn: '24h'
  });
}

// Helper function to generate remember token
function generateRememberToken() {
  return require('crypto').randomBytes(32).toString('hex');
}

// Login endpoint
router.post('/login', loginValidation, async (req, res) => {
  try {
    // Check validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        success: false,
        message: 'Validation failed',
        errors: errors.array()
      });
    }

    const { username, password, remember_me } = req.body;

    // Find user by username or email
    const user = userService.findUser(username);

    if (!user) {
      return res.status(401).json({
        success: false,
        message: 'Invalid credentials',
        code: 'INVALID_CREDENTIALS'
      });
    }

    // Verify password
    const isValidPassword = await bcrypt.compare(password, user.password);
    if (!isValidPassword) {
      return res.status(401).json({
        success: false,
        message: 'Invalid credentials',
        code: 'INVALID_CREDENTIALS'
      });
    }

    // Update last login time
    userService.updateUser(user.id, { last_login: new Date() });

    // Generate JWT token
    const token = generateToken(user);

    // Prepare response data
    const responseData = {
      success: true,
      message: 'Login successful',
      data: {
        user: {
          id: user.id,
          username: user.username,
          email: user.email,
          role: user.role,
          is_admin: user.is_admin,
          last_login: user.last_login
        },
        token: token
      }
    };

    // Handle remember me functionality
    if (remember_me) {
      const rememberToken = generateRememberToken();
      // In a real app, store this token in database
      // For now, we'll just set a longer-lasting cookie
      res.cookie('remember_token', rememberToken, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        maxAge: 30 * 24 * 60 * 60 * 1000 // 30 days
      });
      
      responseData.data.remember_token = rememberToken;
    }

    // Set JWT token in cookie
    res.cookie('auth_token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      maxAge: 24 * 60 * 60 * 1000 // 24 hours
    });

    res.json(responseData);

  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error',
      code: 'INTERNAL_ERROR'
    });
  }
});

// Admin login endpoint
router.post('/admin-login', loginValidation, async (req, res) => {
  try {
    // Check validation errors
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        success: false,
        message: 'Validation failed',
        errors: errors.array()
      });
    }

    const { username, password, remember_me } = req.body;

    // Find admin user
    const user = users.find(u => 
      (u.username === username || u.email === username) && u.is_admin === true
    );

    if (!user) {
      return res.status(401).json({
        success: false,
        message: 'Invalid admin credentials or insufficient permissions',
        code: 'INVALID_ADMIN_CREDENTIALS'
      });
    }

    // Verify password
    const isValidPassword = await bcrypt.compare(password, user.password);
    if (!isValidPassword) {
      return res.status(401).json({
        success: false,
        message: 'Invalid admin credentials',
        code: 'INVALID_ADMIN_CREDENTIALS'
      });
    }

    // Update last login time
    user.last_login = new Date();

    // Generate JWT token
    const token = generateToken(user);

    // Prepare response data
    const responseData = {
      success: true,
      message: 'Admin login successful',
      data: {
        user: {
          id: user.id,
          username: user.username,
          email: user.email,
          role: user.role,
          is_admin: user.is_admin,
          last_login: user.last_login
        },
        token: token,
        redirect_url: '/admin.html'
      }
    };

    // Handle remember me functionality
    if (remember_me) {
      const rememberToken = generateRememberToken();
      res.cookie('admin_remember_token', rememberToken, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        maxAge: 30 * 24 * 60 * 60 * 1000 // 30 days
      });
      
      responseData.data.remember_token = rememberToken;
    }

    // Set JWT token in cookie
    res.cookie('admin_auth_token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      maxAge: 24 * 60 * 60 * 1000 // 24 hours
    });

    res.json(responseData);

  } catch (error) {
    console.error('Admin login error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error',
      code: 'INTERNAL_ERROR'
    });
  }
});

// Logout endpoint
router.post('/logout', (req, res) => {
  try {
    // Clear cookies with secure options
    const cookieOptions = {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'strict',
      path: '/'
    };
    
    res.clearCookie('auth_token', cookieOptions);
    res.clearCookie('admin_auth_token', cookieOptions);
    res.clearCookie('remember_token', cookieOptions);
    res.clearCookie('admin_remember_token', cookieOptions);

    // Log security event
    console.log(`User logged out from IP: ${req.ip} at ${new Date().toISOString()}`);

    res.json({
      success: true,
      message: 'Logout successful'
    });
  } catch (error) {
    console.error('Logout error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

// Check authentication status
router.get('/status', (req, res) => {
  try {
    const token = req.cookies.auth_token || req.cookies.admin_auth_token;
    
    if (!token) {
      return res.json({
        success: false,
        authenticated: false,
        message: 'Not authenticated'
      });
    }

    // Verify JWT token
    jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key', (err, decoded) => {
      if (err) {
        return res.json({
          success: false,
          authenticated: false,
          message: 'Invalid token'
        });
      }

      // Check token expiration
      if (decoded.exp && decoded.exp < Date.now() / 1000) {
        return res.json({
          success: false,
          authenticated: false,
          message: 'Token has expired'
        });
      }

      res.json({
        success: true,
        authenticated: true,
        user: {
          id: decoded.id,
          username: decoded.username,
          email: decoded.email,
          role: decoded.role,
          is_admin: decoded.is_admin
        }
      });
    });
  } catch (error) {
    console.error('Auth status error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

// GET /api/auth/verify - Verify token (for middleware)
router.get('/verify', (req, res) => {
  const token = req.cookies.auth_token || 
                req.cookies.admin_auth_token || 
                req.headers.authorization?.replace('Bearer ', '');
  
  if (!token) {
    return res.status(401).json({
      success: false,
      message: 'No token provided'
    });
  }
  
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key');
    
    // Check token expiration
    if (decoded.exp && decoded.exp < Date.now() / 1000) {
      return res.status(401).json({
        success: false,
        message: 'Token has expired'
      });
    }
    
    res.json({
      success: true,
      user: {
        id: decoded.id,
        username: decoded.username,
        role: decoded.role,
        is_admin: decoded.is_admin
      }
    });
  } catch (error) {
    console.error('Token verification error:', error.message);
    res.status(401).json({
      success: false,
      message: 'Invalid token'
    });
  }
});

module.exports = router;