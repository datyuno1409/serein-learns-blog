const express = require('express');
const router = express.Router();

// Mock data for dashboard
const dashboardData = {
  stats: {
    totalUsers: 1247,
    totalPosts: 89,
    totalComments: 456,
    totalViews: 12543
  },
  recentPosts: [
    {
      id: 1,
      title: 'Getting Started with Node.js',
      author: 'admin',
      date: '2024-01-15',
      status: 'published',
      views: 234
    },
    {
      id: 2,
      title: 'Advanced JavaScript Concepts',
      author: 'admin',
      date: '2024-01-14',
      status: 'draft',
      views: 0
    },
    {
      id: 3,
      title: 'CSS Grid Layout Tutorial',
      author: 'admin',
      date: '2024-01-13',
      status: 'published',
      views: 189
    }
  ],
  recentComments: [
    {
      id: 1,
      author: 'John Doe',
      content: 'Great article! Very helpful.',
      post: 'Getting Started with Node.js',
      date: '2024-01-15 14:30'
    },
    {
      id: 2,
      author: 'Jane Smith',
      content: 'Could you explain more about async/await?',
      post: 'Advanced JavaScript Concepts',
      date: '2024-01-15 12:15'
    },
    {
      id: 3,
      author: 'Mike Johnson',
      content: 'The CSS Grid examples are perfect!',
      post: 'CSS Grid Layout Tutorial',
      date: '2024-01-15 10:45'
    }
  ],
  chartData: {
    visitors: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      data: [1200, 1900, 3000, 5000, 2300, 3200]
    },
    posts: {
      labels: ['Published', 'Draft', 'Pending'],
      data: [65, 20, 15]
    }
  },
  systemInfo: {
    serverStatus: 'Online',
    lastBackup: '2024-01-15 02:00:00',
    diskUsage: '45%',
    memoryUsage: '62%',
    cpuUsage: '23%'
  }
};

// GET /api/admin/dashboard - Get dashboard data
router.get('/dashboard', (req, res) => {
  try {
    // Simulate some dynamic data
    const currentTime = new Date();
    const updatedData = {
      ...dashboardData,
      lastUpdated: currentTime.toISOString(),
      stats: {
        ...dashboardData.stats,
        totalViews: dashboardData.stats.totalViews + Math.floor(Math.random() * 10)
      }
    };

    res.json({
      success: true,
      data: updatedData
    });
  } catch (error) {
    console.error('Dashboard data error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to load dashboard data'
    });
  }
});

// GET /api/admin/stats - Get basic statistics
router.get('/stats', (req, res) => {
  try {
    res.json({
      success: true,
      data: dashboardData.stats
    });
  } catch (error) {
    console.error('Stats error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to load statistics'
    });
  }
});

// GET /api/admin/recent-activity - Get recent activity
router.get('/recent-activity', (req, res) => {
  try {
    const recentActivity = [
      ...dashboardData.recentPosts.map(post => ({
        type: 'post',
        action: post.status === 'published' ? 'published' : 'drafted',
        title: post.title,
        date: post.date,
        author: post.author
      })),
      ...dashboardData.recentComments.map(comment => ({
        type: 'comment',
        action: 'commented',
        title: comment.content.substring(0, 50) + '...',
        date: comment.date,
        author: comment.author
      }))
    ].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 10);

    res.json({
      success: true,
      data: recentActivity
    });
  } catch (error) {
    console.error('Recent activity error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to load recent activity'
    });
  }
});

// GET /api/admin/system-status - Get system status
router.get('/system-status', (req, res) => {
  try {
    const systemStatus = {
      ...dashboardData.systemInfo,
      uptime: process.uptime(),
      nodeVersion: process.version,
      platform: process.platform,
      timestamp: new Date().toISOString()
    };

    res.json({
      success: true,
      data: systemStatus
    });
  } catch (error) {
    console.error('System status error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to load system status'
    });
  }
});

module.exports = router;