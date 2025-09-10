// API endpoint for user registration
const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const rateLimit = require('express-rate-limit');
const { body, validationResult } = require('express-validator');
const { validatePasswordStrength } = require('../../middleware/security');
const userService = require('../shared/users');

const router = express.Router();

// Rate limiting for registration attempts
const registerLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 3, // limit each IP to 3 registration requests per windowMs
  message: {
    error: 'Too many registration attempts, please try again later.',
    code: 'RATE_LIMIT_EXCEEDED'
  },
  standardHeaders: true,
  legacyHeaders: false,
});

// Using shared user service

// Validation rules for registration
const registerValidation = [
  body('full_name')
    .notEmpty()
    .withMessage('Full name is required')
    .isLength({ min: 2, max: 100 })
    .withMessage('Full name must be between 2 and 100 characters')
    .matches(/^[a-zA-ZÀ-ỹ\s]+$/)
    .withMessage('Full name can only contain letters and spaces'),
  
  body('username')
    .notEmpty()
    .withMessage('Username is required')
    .isLength({ min: 3, max: 30 })
    .withMessage('Username must be between 3 and 30 characters')
    .matches(/^[a-zA-Z0-9_]+$/)
    .withMessage('Username can only contain letters, numbers, and underscores')
    .custom(async (value) => {
      if (userService.usernameExists(value)) {
        throw new Error('Username already exists');
      }
      return true;
    }),
  
  body('email')
    .isEmail()
    .withMessage('Please provide a valid email address')
    .normalizeEmail()
    .custom(async (value) => {
      if (userService.emailExists(value)) {
        throw new Error('Email already exists');
      }
      return true;
    }),
  
  body('password')
    .isLength({ min: 8 })
    .withMessage('Password must be at least 8 characters long')
    .matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/)
    .withMessage('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'),
  
  body('confirm_password')
    .custom((value, { req }) => {
      if (value !== req.body.password) {
        throw new Error('Password confirmation does not match password');
      }
      return true;
    }),
  
  body('agree_terms')
    .equals('true')
    .withMessage('You must agree to the terms and conditions')
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

// Registration endpoint
router.post('/register', registerValidation, async (req, res) => {
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

    const { full_name, username, email, password } = req.body;
    
    // Additional password strength validation
    const passwordStrength = validatePasswordStrength(password);
    if (!passwordStrength.isValid) {
      return res.status(400).json({
        success: false,
        message: 'Password does not meet security requirements',
        errors: passwordStrength.errors
      });
    }

    // Hash password with higher salt rounds for security
    const saltRounds = 14;
    const hashedPassword = await bcrypt.hash(password, saltRounds);

    // Create new user
    const newUser = userService.addUser({
      username: username.toLowerCase(),
      email: email.toLowerCase(),
      password: hashedPassword,
      full_name: full_name.trim(),
      role: 'user',
      is_admin: false,
      email_verified: false
    });

    // Create JWT token with shorter expiration for security
    const token = jwt.sign(
      { 
        id: newUser.id, 
        username: newUser.username, 
        role: 'user',
        is_admin: false,
        iat: Math.floor(Date.now() / 1000)
      },
      process.env.JWT_SECRET || 'your-secret-key',
      { expiresIn: '2h' }
    );

    // Prepare response data (exclude password)
    const responseData = {
      success: true,
      message: 'Registration successful',
      data: {
        user: {
          id: newUser.id,
          username: newUser.username,
          email: newUser.email,
          full_name: newUser.full_name,
          role: newUser.role,
          is_admin: newUser.is_admin,
          email_verified: newUser.email_verified,
          created_at: newUser.created_at
        },
        token: token,
        redirect_url: '/login.html'
      }
    };

    // Set secure cookie
    res.cookie('auth_token', token, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'strict',
      maxAge: 2 * 60 * 60 * 1000 // 2 hours
    });
    
    // Log security event
    console.log(`New user registered: ${username} from IP: ${req.ip} at ${new Date().toISOString()}`);

    res.status(201).json(responseData);

  } catch (error) {
    console.error('Registration error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error',
      code: 'INTERNAL_ERROR'
    });
  }
});

// Check username availability
router.get('/check-username/:username', (req, res) => {
  try {
    const { username } = req.params;
    
    if (!username || username.length < 3) {
      return res.json({
        success: false,
        available: false,
        message: 'Username must be at least 3 characters long'
      });
    }

    const existingUser = userService.findByUsername(username);

    res.json({
      success: true,
      available: !existingUser,
      message: existingUser ? 'Username is already taken' : 'Username is available'
    });
  } catch (error) {
    console.error('Check username error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

// Check email availability
router.get('/check-email/:email', (req, res) => {
  try {
    const { email } = req.params;
    
    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
      return res.json({
        success: false,
        available: false,
        message: 'Please provide a valid email address'
      });
    }

    const existingUser = userService.findByEmail(email);

    res.json({
      success: true,
      available: !existingUser,
      message: existingUser ? 'Email is already registered' : 'Email is available'
    });
  } catch (error) {
    console.error('Check email error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

// Password strength checker
router.post('/check-password-strength', (req, res) => {
  try {
    const { password } = req.body;
    
    if (!password) {
      return res.json({
        success: false,
        strength: 'weak',
        score: 0,
        message: 'Password is required'
      });
    }

    let score = 0;
    let feedback = [];

    // Length check
    if (password.length >= 8) {
      score += 1;
    } else {
      feedback.push('At least 8 characters');
    }

    // Lowercase check
    if (/[a-z]/.test(password)) {
      score += 1;
    } else {
      feedback.push('At least one lowercase letter');
    }

    // Uppercase check
    if (/[A-Z]/.test(password)) {
      score += 1;
    } else {
      feedback.push('At least one uppercase letter');
    }

    // Number check
    if (/\d/.test(password)) {
      score += 1;
    } else {
      feedback.push('At least one number');
    }

    // Special character check
    if (/[@$!%*?&]/.test(password)) {
      score += 1;
    } else {
      feedback.push('At least one special character (@$!%*?&)');
    }

    // Determine strength
    let strength = 'weak';
    let message = 'Password is weak';
    
    if (score >= 5) {
      strength = 'strong';
      message = 'Password is strong';
    } else if (score >= 3) {
      strength = 'medium';
      message = 'Password is medium strength';
    }

    res.json({
      success: true,
      strength: strength,
      score: score,
      maxScore: 5,
      message: message,
      feedback: feedback
    });
  } catch (error) {
    console.error('Password strength check error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

module.exports = router;