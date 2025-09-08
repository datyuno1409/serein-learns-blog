<!-- Dashboard Header -->
<div class="dashboard-header">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="dashboard-title">
          <h1 class="dashboard-heading">
            <svg class="dashboard-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
          </h1>
          <p class="dashboard-subtitle">Welcome back! Here's what's happening with your blog.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="dashboard-actions">
          <div class="quick-actions">
            <a href="/admin/articles/create" class="btn btn-primary btn-sm">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
              </svg>
              New Article
            </a>
            <button class="btn btn-outline-secondary btn-sm" onclick="refreshDashboard()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23,4 23,10 17,10"/>
                <polyline points="1,20 1,14 7,14"/>
                <path d="m3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Refresh
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row stats-row">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-primary">
          <div class="stats-card-body">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14,2 14,8 20,8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                  <polyline points="10,9 9,9 8,9"/>
                </svg>
              </div>
              <div class="stats-card-info">
                <h3 class="stats-card-number"><?= $totalArticles ?></h3>
                <p class="stats-card-label">Total Articles</p>
                <div class="stats-card-trend">
                  <span class="trend-up">+12% from last month</span>
                </div>
              </div>
            </div>
            <div class="stats-card-footer">
              <a href="/admin/articles" class="stats-card-link">
                View all articles
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-success">
          <div class="stats-card-body">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
              </div>
              <div class="stats-card-info">
                <h3 class="stats-card-number"><?= $totalCategories ?></h3>
                <p class="stats-card-label">Categories</p>
                <div class="stats-card-trend">
                  <span class="trend-neutral">No change</span>
                </div>
              </div>
            </div>
            <div class="stats-card-footer">
              <a href="/admin/categories" class="stats-card-link">
                Manage categories
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-warning">
          <div class="stats-card-body">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
              </div>
              <div class="stats-card-info">
                <h3 class="stats-card-number"><?= $totalComments ?></h3>
                <p class="stats-card-label">Comments</p>
                <div class="stats-card-trend">
                  <span class="trend-up">+8% from last week</span>
                </div>
              </div>
            </div>
            <div class="stats-card-footer">
              <a href="/admin/comments" class="stats-card-link">
                Moderate comments
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card stats-card-danger">
          <div class="stats-card-body">
            <div class="stats-card-content">
              <div class="stats-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div class="stats-card-info">
                <h3 class="stats-card-number"><?= $totalUsers ?></h3>
                <p class="stats-card-label">Users</p>
                <div class="stats-card-trend">
                  <span class="trend-up">+3 new users</span>
                </div>
              </div>
            </div>
            <div class="stats-card-footer">
              <a href="/admin/users" class="stats-card-link">
                Manage users
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9,18 15,12 9,6"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Analytics Section -->
    <div class="row analytics-section">
      <div class="col-lg-8 mb-4">
        <div class="analytics-card">
          <div class="analytics-header">
            <div class="analytics-title">
              <h3>Content Performance</h3>
              <p>Track your blog's growth and engagement</p>
            </div>
            <div class="analytics-controls">
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm active" data-period="7d">7D</button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-period="30d">30D</button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-period="90d">90D</button>
              </div>
            </div>
          </div>
          <div class="analytics-body">
            <div class="chart-container">
              <canvas id="performanceChart" style="height: 300px;"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 mb-4">
        <div class="analytics-card">
          <div class="analytics-header">
            <div class="analytics-title">
              <h3>Category Distribution</h3>
              <p>Articles by category</p>
            </div>
          </div>
          <div class="analytics-body">
            <div class="chart-container">
              <canvas id="categoryChart" style="height: 300px;"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Management Section -->
    <div class="row content-section">
      <div class="col-lg-8 mb-4">
        <div class="content-card">
          <div class="content-header">
            <div class="content-title">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14,2 14,8 20,8"/>
                </svg>
                Recent Articles
              </h3>
              <p>Latest published content</p>
            </div>
            <div class="content-actions">
              <a href="/admin/articles/create" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"/>
                  <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                New Article
              </a>
            </div>
          </div>
          <div class="content-body">
            <div class="articles-list">
              <?php if (!empty($recentArticles)): ?>
                <?php foreach ($recentArticles as $article): ?>
                  <div class="article-item">
                    <div class="article-image">
                      <img src="<?= $article['image'] ?? '/assets/images/default-article.jpg' ?>" alt="Article Image">
                    </div>
                    <div class="article-content">
                      <div class="article-header">
                        <h4 class="article-title">
                          <a href="/admin/articles/edit/<?= $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a>
                        </h4>
                        <span class="article-status status-published">Published</span>
                      </div>
                      <div class="article-meta">
                        <span class="article-date">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                          </svg>
                          <?= date('M d, Y', strtotime($article['created_at'])) ?>
                        </span>
                        <span class="article-category"><?= $article['category_name'] ?? 'Uncategorized' ?></span>
                      </div>
                    </div>
                    <div class="article-actions">
                      <a href="/admin/articles/edit/<?= $article['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                          <path d="m18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-state">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                  </svg>
                  <h4>No articles yet</h4>
                  <p>Start creating your first article</p>
                  <a href="/admin/articles/create" class="btn btn-primary">Create Article</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="content-footer">
            <a href="/admin/articles" class="view-all-link">
              View all articles
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 mb-4">
        <div class="content-card">
          <div class="content-header">
            <div class="content-title">
              <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Recent Comments
              </h3>
              <p>Latest user feedback</p>
            </div>
          </div>
          <div class="content-body">
            <div class="comments-list">
              <?php if (!empty($recentComments)): ?>
                <?php foreach ($recentComments as $comment): ?>
                  <div class="comment-item">
                    <div class="comment-avatar">
                      <img src="<?= $comment['user_avatar'] ?>" alt="User Avatar">
                    </div>
                    <div class="comment-content">
                      <div class="comment-header">
                        <h5 class="comment-author"><?= htmlspecialchars($comment['user_name']) ?></h5>
                        <span class="comment-status status-approved">Approved</span>
                      </div>
                      <p class="comment-text"><?= htmlspecialchars(substr($comment['content'], 0, 80)) ?>...</p>
                      <div class="comment-meta">
                        <span class="comment-date"><?= date('M d, Y', strtotime($comment['created_at'])) ?></span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-state">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  <h4>No comments yet</h4>
                  <p>Comments will appear here</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="content-footer">
            <a href="/admin/comments" class="view-all-link">
              View all comments
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9,18 15,12 9,6"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(performanceCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
        datasets: [{
            label: 'Articles',
            data: <?= json_encode(array_column($monthlyStats, 'articles')) ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Comments',
            data: <?= json_encode(array_column($monthlyStats, 'comments')) ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(0, 0, 0, 0.1)',
                borderWidth: 1
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($categoryStats, 'name')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($categoryStats, 'count')) ?>,
            backgroundColor: [
                '#007bff',
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#fd7e14',
                '#20c997'
            ],
            borderWidth: 0,
            hoverBorderWidth: 2,
            hoverBorderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Period selector functionality
document.querySelectorAll('[data-period]').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('[data-period]').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        // Here you would typically fetch new data based on the selected period
        const period = this.getAttribute('data-period');
        console.log('Selected period:', period);
        // updateChartData(period);
    });
});
</script>