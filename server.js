// Main server file for authentication system
const express = require('express');
const cors = require('cors');
const cookieParser = require('cookie-parser');
const path = require('path');
const helmet = require('helmet');
const compression = require('compression');
const { 
  rateLimiters, 
  sanitizeInput, 
  validateJWT, 
  requireAdmin, 
  generateCSRFToken, 
  validateCSRF, 
  securityHeaders, 
  securityLogger 
} = require('./middleware/security');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Security middleware
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'", "https://fonts.googleapis.com", "https://cdnjs.cloudflare.com", "https://cdn.jsdelivr.net"],
      fontSrc: ["'self'", "https://fonts.gstatic.com", "https://cdnjs.cloudflare.com", "https://cdn.jsdelivr.net"],
      scriptSrc: ["'self'", "'unsafe-inline'", "https://cdnjs.cloudflare.com", "https://cdn.jsdelivr.net", "https://code.jquery.com"],
      imgSrc: ["'self'", "data:", "https:"],
      connectSrc: ["'self'"],
      frameSrc: ["'none'"],
      objectSrc: ["'none'"],
      mediaSrc: ["'self'"],
      workerSrc: ["'none'"]
    }
  }
}));
app.use(securityHeaders);
app.use(securityLogger);

// Compression middleware
app.use(compression());

// CORS configuration
app.use(cors({
  origin: process.env.FRONTEND_URL || 'http://localhost:8000',
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));

// Body parsing middleware
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));
app.use(cookieParser());
app.use(sanitizeInput);

// Static files middleware
app.use(express.static(path.join(__dirname), {
  maxAge: '1d',
  etag: true
}));

// Request logging middleware
app.use((req, res, next) => {
  const timestamp = new Date().toISOString();
  console.log(`[${timestamp}] ${req.method} ${req.url} - ${req.ip}`);
  next();
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({
    status: 'OK',
    timestamp: new Date().toISOString(),
    uptime: process.uptime(),
    environment: process.env.NODE_ENV || 'development'
  });
});

// API Routes with rate limiting
const loginRoutes = require('./api/auth/login');
const registerRoutes = require('./api/auth/register');

// CSRF token endpoint
app.get('/api/csrf-token', generateCSRFToken);

// Apply rate limiting to auth routes
app.use('/api/auth/login', rateLimiters.auth);
app.use('/api/auth/register', rateLimiters.registration);
app.use('/api/auth/forgot-password', rateLimiters.passwordReset);
app.use('/api', rateLimiters.general);

app.use('/api/auth', loginRoutes);
app.use('/api/auth', registerRoutes);
app.use('/api/admin', validateJWT, requireAdmin, require('./api/admin/dashboard'));

// Routes for main pages - Serve static HTML
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'static-index.html'));
});

// Static pages routing
app.get('/articles', (req, res) => {
    res.sendFile(path.join(__dirname, 'articles.html'));
});

app.get('/contact', (req, res) => {
    res.sendFile(path.join(__dirname, 'contact.html'));
});

app.get('/article-detail', (req, res) => {
    res.sendFile(path.join(__dirname, 'article-detail.html'));
});

app.get('/projects', (req, res) => {
    res.sendFile(path.join(__dirname, 'projects.html'));
});

app.get('/about', (req, res) => {
    res.sendFile(path.join(__dirname, 'about.html'));
});

// Keep dynamic pages for authentication
app.get('/login', (req, res) => {
    res.sendFile(path.join(__dirname, 'login.html'));
});

app.get('/register', (req, res) => {
    res.sendFile(path.join(__dirname, 'register.html'));
});

app.get('/admin', (req, res) => {
    res.sendFile(path.join(__dirname, 'admin.html'));
});

// API endpoint to get user data (protected)
app.get('/api/user/profile', validateJWT, (req, res) => {
  try {
    // In a real app, fetch from database
    const user = {
      id: req.user.id,
      username: req.user.username,
      email: req.user.email,
      role: req.user.role,
      is_admin: req.user.is_admin
    };

    res.json({
      success: true,
      data: { user }
    });
  } catch (error) {
    console.error('Profile fetch error:', error);
    res.status(500).json({
      success: false,
      message: 'Internal server error'
    });
  }
});

// Admin user management
app.get('/api/admin/users', validateJWT, requireAdmin, (req, res) => {
  res.json({
    success: true,
    users: [
      { id: 1, username: 'admin', role: 'admin', status: 'active' },
      { id: 2, username: 'user1', role: 'user', status: 'active' },
      { id: 3, username: 'user2', role: 'user', status: 'inactive' }
    ]
  });
});

// Protected POST routes with CSRF protection
app.post('/api/user/update-profile', validateJWT, validateCSRF, (req, res) => {
  res.json({
    success: true,
    message: 'Profile updated successfully'
  });
});

app.post('/api/admin/create-user', validateJWT, requireAdmin, validateCSRF, (req, res) => {
  res.json({
    success: true,
    message: 'User created successfully'
  });
});

// Middleware to authenticate JWT token
function authenticateToken(req, res, next) {
  const token = req.cookies.auth_token || req.cookies.admin_auth_token;
  
  if (!token) {
    return res.status(401).json({
      success: false,
      message: 'Access token required'
    });
  }

  const jwt = require('jsonwebtoken');
  jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key', (err, user) => {
    if (err) {
      return res.status(403).json({
        success: false,
        message: 'Invalid or expired token'
      });
    }
    
    req.user = user;
    next();
  });
}



// Protected admin routes
app.get('/api/admin/dashboard', validateJWT, requireAdmin, (req, res) => {
  res.json({
    success: true,
    data: {
      stats: {
        totalUsers: 150,
        totalPosts: 45,
        totalComments: 230,
        totalCategories: 8
      },
      recentActivity: [
        { type: 'user_registered', message: 'New user registered: john_doe', time: '2 minutes ago' },
        { type: 'post_created', message: 'New post published: "Getting Started"', time: '15 minutes ago' },
        { type: 'comment_added', message: 'New comment on "Hello World"', time: '1 hour ago' }
      ]
    }
  });
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error('Error:', err);
  
  if (err.type === 'entity.parse.failed') {
    return res.status(400).json({
      success: false,
      message: 'Invalid JSON format'
    });
  }
  
  res.status(500).json({
    success: false,
    message: 'Internal server error'
  });
});

// 404 handler
app.use((req, res) => {
  if (req.path.startsWith('/api/')) {
    res.status(404).json({
      success: false,
      message: 'API endpoint not found'
    });
  } else {
    res.status(404).sendFile(path.join(__dirname, '404.html'));
  }
});

// Graceful shutdown
process.on('SIGTERM', () => {
  console.log('SIGTERM received, shutting down gracefully');
  server.close(() => {
    console.log('Process terminated');
  });
});

process.on('SIGINT', () => {
  console.log('SIGINT received, shutting down gracefully');
  server.close(() => {
    console.log('Process terminated');
  });
});

// Start server
const server = app.listen(PORT, () => {
  console.log(`\n🚀 Server is running on port ${PORT}`);
  console.log(`📱 Local: http://localhost:${PORT}`);
  console.log(`🌐 Network: http://0.0.0.0:${PORT}`);
  console.log(`🔐 Environment: ${process.env.NODE_ENV || 'development'}`);
  console.log('\n📋 Available endpoints:');
  console.log('   GET  /                    - Home page');
  console.log('   GET  /login              - Login page');
  console.log('   GET  /register           - Registration page');
  console.log('   GET  /admin              - Admin dashboard');
  console.log('   POST /api/auth/login     - User login');
  console.log('   POST /api/auth/admin-login - Admin login');
  console.log('   POST /api/auth/register  - User registration');
  console.log('   POST /api/auth/logout    - Logout');
  console.log('   GET  /api/auth/status    - Auth status');
  console.log('   GET  /health             - Health check');
  console.log('\n✅ Server ready for connections!');
});

module.exports = app;