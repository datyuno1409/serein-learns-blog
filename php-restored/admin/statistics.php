<?php
require_once 'helpers/auth_helper.php';
require_once '../config/config.php';
require_once '../config/database.php';

requireAdmin();

// Database connection
$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    die('Database connection failed');
}

$page_title = 'Thống kê';
$current_page = 'statistics';

// Get date range from request
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today
$period = $_GET['period'] ?? '30days'; // Default period

// Calculate date range based on period
switch ($period) {
    case '7days':
        $startDate = date('Y-m-d', strtotime('-7 days'));
        break;
    case '30days':
        $startDate = date('Y-m-d', strtotime('-30 days'));
        break;
    case '3months':
        $startDate = date('Y-m-d', strtotime('-3 months'));
        break;
    case '6months':
        $startDate = date('Y-m-d', strtotime('-6 months'));
        break;
    case '1year':
        $startDate = date('Y-m-d', strtotime('-1 year'));
        break;
    case 'custom':
        // Use provided dates
        break;
    default:
        $startDate = date('Y-m-d', strtotime('-30 days'));
}

try {
    // Basic statistics
    $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Period-based statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$startDate, $endDate]);
    $periodArticles = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$startDate, $endDate]);
    $periodComments = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$startDate, $endDate]);
    $periodUsers = $stmt->fetchColumn();
    
    // Articles by status
    $publishedArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
    $draftArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'draft'")->fetchColumn();
    
    // Recent activity (last 30 days)
    $recentArticles = $pdo->query("SELECT COUNT(*) FROM articles WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    $recentComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    $recentUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    
    // Articles by category
    $stmt = $pdo->query("
        SELECT c.name, COUNT(a.id) as article_count 
        FROM categories c 
        LEFT JOIN articles a ON c.id = a.category_id 
        GROUP BY c.id, c.name 
        ORDER BY article_count DESC
    ");
    $articlesByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Top authors
    $stmt = $pdo->query("
        SELECT u.username, COUNT(a.id) as article_count 
        FROM users u 
        LEFT JOIN articles a ON u.id = a.author_id 
        WHERE a.id IS NOT NULL
        GROUP BY u.id, u.username 
        ORDER BY article_count DESC 
        LIMIT 10
    ");
    $topAuthors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Daily trend for selected period
    $stmt = $pdo->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as articles,
            (SELECT COUNT(*) FROM comments WHERE DATE(created_at) = DATE(a.created_at)) as comments
        FROM articles a
        WHERE created_at BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$startDate, $endDate]);
    $dailyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Monthly article creation trend (last 12 months)
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as count
        FROM articles 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthlyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Growth statistics
    $prevPeriodStart = date('Y-m-d', strtotime($startDate . ' -' . (strtotime($endDate) - strtotime($startDate)) . ' seconds'));
    $prevPeriodEnd = $startDate;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$prevPeriodStart, $prevPeriodEnd]);
    $prevPeriodArticles = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$prevPeriodStart, $prevPeriodEnd]);
    $prevPeriodComments = $stmt->fetchColumn();
    
    // Calculate growth percentages
    $articlesGrowth = $prevPeriodArticles > 0 ? round((($periodArticles - $prevPeriodArticles) / $prevPeriodArticles) * 100, 1) : 0;
    $commentsGrowth = $prevPeriodComments > 0 ? round((($periodComments - $prevPeriodComments) / $prevPeriodComments) * 100, 1) : 0;
    
    // Initialize arrays to prevent undefined variable errors
    if (!isset($dailyTrend)) $dailyTrend = [];
    if (!isset($monthlyTrend)) $monthlyTrend = [];
    if (!isset($articlesByCategory)) $articlesByCategory = [];
    if (!isset($topAuthors)) $topAuthors = [];
    if (!isset($mostCommentedArticles)) $mostCommentedArticles = [];
    
    // Comments by status
    $approvedComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
    $pendingComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
    $rejectedComments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'rejected'")->fetchColumn();
    
    // Most commented articles
    $stmt = $pdo->query("
        SELECT a.title, COUNT(c.id) as comment_count 
        FROM articles a 
        LEFT JOIN comments c ON a.id = c.article_id 
        WHERE c.id IS NOT NULL
        GROUP BY a.id, a.title 
        ORDER BY comment_count DESC 
        LIMIT 10
    ");
    $mostCommentedArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

require_once 'includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Thống kê</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Thống kê</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Date Range Filter -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row align-items-end">
                                <div class="col-md-3">
                                    <label for="period" class="form-label">Khoảng thời gian</label>
                                    <select name="period" id="period" class="form-control" onchange="toggleCustomDates()">
                                        <option value="7days" <?php echo $period === '7days' ? 'selected' : ''; ?>>7 ngày qua</option>
                                        <option value="30days" <?php echo $period === '30days' ? 'selected' : ''; ?>>30 ngày qua</option>
                                        <option value="3months" <?php echo $period === '3months' ? 'selected' : ''; ?>>3 tháng qua</option>
                                        <option value="6months" <?php echo $period === '6months' ? 'selected' : ''; ?>>6 tháng qua</option>
                                        <option value="1year" <?php echo $period === '1year' ? 'selected' : ''; ?>>1 năm qua</option>
                                        <option value="custom" <?php echo $period === 'custom' ? 'selected' : ''; ?>>Tùy chỉnh</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="startDateGroup" style="display: <?php echo $period === 'custom' ? 'block' : 'none'; ?>">
                                    <label for="start_date" class="form-label">Từ ngày</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $startDate; ?>">
                                </div>
                                <div class="col-md-3" id="endDateGroup" style="display: <?php echo $period === 'custom' ? 'block' : 'none'; ?>">
                                    <label for="end_date" class="form-label">Đến ngày</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $endDate; ?>">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    <a href="?" class="btn btn-secondary ml-2">
                                        <i class="fas fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Period Statistics Cards -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $periodArticles; ?></h3>
                            <p>Bài viết trong kỳ</p>
                            <?php if ($articlesGrowth != 0): ?>
                                <small class="text-light">
                                    <i class="fas fa-<?php echo $articlesGrowth > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                    <?php echo abs($articlesGrowth); ?>% so với kỳ trước
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $periodComments; ?></h3>
                            <p>Bình luận trong kỳ</p>
                            <?php if ($commentsGrowth != 0): ?>
                                <small class="text-light">
                                    <i class="fas fa-<?php echo $commentsGrowth > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                    <?php echo abs($commentsGrowth); ?>% so với kỳ trước
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="icon">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $periodUsers; ?></h3>
                            <p>Người dùng mới</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo number_format($periodArticles > 0 ? $periodComments / $periodArticles : 0, 1); ?></h3>
                            <p>Bình luận/Bài viết</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Overview Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $totalArticles; ?></h3>
                            <p>Tổng bài viết</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $totalComments; ?></h3>
                            <p>Tổng bình luận</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $totalUsers; ?></h3>
                            <p>Tổng người dùng</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $totalCategories; ?></h3>
                            <p>Tổng danh mục</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Hoạt động 30 ngày qua</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-plus"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bài viết mới</span>
                                            <span class="info-box-number"><?php echo $recentArticles; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-comment"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bình luận mới</span>
                                            <span class="info-box-number"><?php echo $recentComments; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-user-plus"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Người dùng mới</span>
                                            <span class="info-box-number"><?php echo $recentUsers; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Article Status -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Trạng thái bài viết</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="articleStatusChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Comment Status -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Trạng thái bình luận</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="commentStatusChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Articles by Category -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bài viết theo danh mục</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Danh mục</th>
                                            <th>Số bài viết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($articlesByCategory as $category): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                                <td><span class="badge badge-primary"><?php echo $category['article_count']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Authors -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tác giả hàng đầu</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tác giả</th>
                                            <th>Số bài viết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($topAuthors as $author): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($author['username']); ?></td>
                                                <td><span class="badge badge-success"><?php echo $author['article_count']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Daily Trend for Selected Period -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Xu hướng hoạt động trong kỳ được chọn</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Trend -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Xu hướng tạo bài viết (12 tháng qua)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Category Distribution -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Phân bố theo danh mục</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Most Commented Articles -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bài viết được bình luận nhiều nhất</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề bài viết</th>
                                            <th>Số bình luận</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mostCommentedArticles as $article): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                                <td><span class="badge badge-info"><?php echo $article['comment_count']; ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Toggle custom date inputs
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const startDateGroup = document.getElementById('startDateGroup');
    const endDateGroup = document.getElementById('endDateGroup');
    
    if (period === 'custom') {
        startDateGroup.style.display = 'block';
        endDateGroup.style.display = 'block';
    } else {
        startDateGroup.style.display = 'none';
        endDateGroup.style.display = 'none';
    }
}

// Chart.js default configuration
Chart.defaults.font.family = 'Source Sans Pro';
Chart.defaults.color = '#666';

// Article Status Chart
const articleStatusCtx = document.getElementById('articleStatusChart').getContext('2d');
new Chart(articleStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Đã xuất bản', 'Bản nháp'],
        datasets: [{
            data: [<?php echo $publishedArticles; ?>, <?php echo $draftArticles; ?>],
            backgroundColor: ['#28a745', '#ffc107'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Comment Status Chart
const commentStatusCtx = document.getElementById('commentStatusChart').getContext('2d');
new Chart(commentStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Đã duyệt', 'Chờ duyệt', 'Từ chối'],
        datasets: [{
            data: [<?php echo $approvedComments; ?>, <?php echo $pendingComments; ?>, <?php echo $rejectedComments; ?>],
            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((context.parsed * 100) / total).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Daily Trend Chart
const dailyTrendCtx = document.getElementById('dailyTrendChart').getContext('2d');
new Chart(dailyTrendCtx, {
    type: 'line',
    data: {
        labels: [<?php echo $dailyTrend ? "'" . implode("', '", array_column($dailyTrend, 'date')) . "'" : "'Không có dữ liệu'"; ?>],
        datasets: [{
            label: 'Bài viết',
            data: [<?php echo $dailyTrend ? implode(', ', array_column($dailyTrend, 'articles')) : '0'; ?>],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Bình luận',
            data: [<?php echo $dailyTrend ? implode(', ', array_column($dailyTrend, 'comments')) : '0'; ?>],
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
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Monthly Trend Chart
const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(monthlyTrendCtx, {
    type: 'bar',
    data: {
        labels: [<?php echo $monthlyTrend ? "'" . implode("', '", array_column($monthlyTrend, 'month')) . "'" : "'Không có dữ liệu'"; ?>],
        datasets: [{
            label: 'Số bài viết',
            data: [<?php echo $monthlyTrend ? implode(', ', array_column($monthlyTrend, 'count')) : '0'; ?>],
            backgroundColor: 'rgba(0, 123, 255, 0.8)',
            borderColor: '#007bff',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    color: 'rgba(0,0,0,0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Category Distribution Chart
const categoryDistributionCtx = document.getElementById('categoryDistributionChart').getContext('2d');
const categoryColors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'];
new Chart(categoryDistributionCtx, {
    type: 'pie',
    data: {
        labels: [<?php echo $articlesByCategory ? "'" . implode("', '", array_column($articlesByCategory, 'name')) . "'" : "'Không có dữ liệu'"; ?>],
        datasets: [{
            data: [<?php echo $articlesByCategory ? implode(', ', array_column($articlesByCategory, 'article_count')) : '0'; ?>],
            backgroundColor: categoryColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? ((context.parsed * 100) / total).toFixed(1) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
