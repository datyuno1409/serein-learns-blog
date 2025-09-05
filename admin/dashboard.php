<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

// Kiểm tra đăng nhập và quyền admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /login');
    exit;
}

// Kết nối database
$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    die('Database connection failed');
}

$page_title = 'Dashboard';

// Lấy thống kê
try {
    // Tổng số bài viết
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM posts");
    $total_posts = $stmt->fetch()['total'];
    
    // Bài viết trong tháng này
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM posts WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $posts_this_month = $stmt->fetch()['total'];
    
    // Tổng lượt xem
    $stmt = $pdo->query("SELECT SUM(view_count) as total FROM posts");
    $total_views = $stmt->fetch()['total'] ?? 0;
    
    // Tổng bình luận
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM comments");
    $total_comments = $stmt->fetch()['total'];
    
    // Bình luận mới (7 ngày qua)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM comments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $new_comments = $stmt->fetch()['total'];
    
    // Tổng người dùng
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch()['total'];
    
    // Bài viết gần đây
    $stmt = $pdo->prepare("
        SELECT p.*, u.username, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_posts = $stmt->fetchAll();
    
    // Bình luận gần đây
    $stmt = $pdo->prepare("
        SELECT c.*, p.title as post_title 
        FROM comments c 
        LEFT JOIN posts p ON c.post_id = p.id 
        ORDER BY c.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_comments = $stmt->fetchAll();
    
    // Dữ liệu cho biểu đồ lượt xem 7 ngày qua
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, SUM(view_count) as views 
        FROM posts 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
        GROUP BY DATE(created_at) 
        ORDER BY date
    ");
    $chart_data = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $total_posts = $posts_this_month = $total_views = $total_comments = $new_comments = $total_users = 0;
    $recent_posts = $recent_comments = $chart_data = [];
}

require_once 'includes/header.php';
?>

<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= number_format($total_posts) ?></h3>
        <p>Tổng bài viết</p>
      </div>
      <div class="icon">
        <i class="fas fa-edit"></i>
      </div>
      <a href="posts_list.php" class="small-box-footer">Xem thêm <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  
  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3><?= number_format($total_views) ?></h3>
        <p>Tổng lượt xem</p>
      </div>
      <div class="icon">
        <i class="fas fa-eye"></i>
      </div>
      <a href="statistics.php" class="small-box-footer">Xem thêm <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  
  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><?= number_format($total_users) ?></h3>
        <p>Người dùng</p>
      </div>
      <div class="icon">
        <i class="fas fa-users"></i>
      </div>
      <a href="users_list.php" class="small-box-footer">Xem thêm <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  
  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3><?= number_format($total_comments) ?></h3>
        <p>Bình luận</p>
      </div>
      <div class="icon">
        <i class="fas fa-comments"></i>
      </div>
      <a href="comments_list.php" class="small-box-footer">Xem thêm <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
  <!-- Left col -->
  <section class="col-lg-7 connectedSortable">
    <!-- Custom tabs (Charts with tabs)-->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-pie mr-1"></i>
          Thống kê lượt xem
        </h3>
        <div class="card-tools">
          <ul class="nav nav-pills ml-auto">
            <li class="nav-item">
              <a class="nav-link active" href="#revenue-chart" data-toggle="tab">7 ngày</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#sales-chart" data-toggle="tab">30 ngày</a>
            </li>
          </ul>
        </div>
      </div><!-- /.card-header -->
      <div class="card-body">
        <div class="tab-content p-0">
          <!-- Morris chart - Sales -->
          <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
            <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
          </div>
          <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
            <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
          </div>
        </div>
      </div><!-- /.card-body -->
    </div>
    <!-- /.card -->

    <!-- DIRECT CHAT -->
    <div class="card direct-chat direct-chat-primary">
      <div class="card-header">
        <h3 class="card-title">Bình luận gần đây</h3>
        <div class="card-tools">
          <span title="<?= $new_comments ?> bình luận mới" class="badge badge-primary"><?= $new_comments ?></span>
          <button type="button" class="btn btn-tool" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle">
            <i class="fas fa-comments"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!-- Conversations are loaded here -->
        <div class="direct-chat-messages">
          <?php foreach ($recent_comments as $comment): ?>
          <!-- Message. Default to the left -->
          <div class="direct-chat-msg">
            <div class="direct-chat-infos clearfix">
              <span class="direct-chat-name float-left"><?= htmlspecialchars($comment['author_name']) ?></span>
              <span class="direct-chat-timestamp float-right"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></span>
            </div>
            <!-- /.direct-chat-infos -->
            <img class="direct-chat-img" src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="message user image">
            <!-- /.direct-chat-img -->
            <div class="direct-chat-text">
              <small class="text-muted">Bài viết: <?= htmlspecialchars($comment['post_title']) ?></small><br>
              <?= htmlspecialchars(substr($comment['content'], 0, 100)) ?><?= strlen($comment['content']) > 100 ? '...' : '' ?>
            </div>
            <!-- /.direct-chat-text -->
          </div>
          <!-- /.direct-chat-msg -->
          <?php endforeach; ?>
        </div>
        <!--/.direct-chat-messages-->
      </div>
      <!-- /.card-body -->
      <div class="card-footer">
        <div class="text-center">
          <a href="/admin/comments" class="btn btn-primary btn-sm">Xem tất cả bình luận</a>
        </div>
      </div>
      <!-- /.card-footer-->
    </div>
    <!--/.direct-chat -->
  </section>
  <!-- /.Left col -->
  
  <!-- right col (We are only adding the ID to make the widgets sortable)-->
  <section class="col-lg-5 connectedSortable">

    <!-- Map card -->
    <div class="card bg-gradient-primary">
      <div class="card-header border-0">
        <h3 class="card-title">
          <i class="fas fa-map-marker-alt mr-1"></i>
          Thống kê tháng này
        </h3>
        <!-- card tools -->
        <div class="card-tools">
          <button type="button" class="btn btn-primary btn-sm daterange" title="Date range">
            <i class="far fa-calendar-alt"></i>
          </button>
          <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
        </div>
        <!-- /.card-tools -->
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6 text-center">
            <div id="sparkline-1"></div>
            <div class="text-white"><?= number_format($posts_this_month) ?></div>
            <div class="text-white">Bài viết mới</div>
          </div>
          <div class="col-6 text-center">
            <div id="sparkline-2"></div>
            <div class="text-white"><?= number_format($new_comments) ?></div>
            <div class="text-white">Bình luận mới</div>
          </div>
        </div>
        <!-- /.row -->
      </div>
    </div>
    <!-- /.card -->

    <!-- solid sales graph -->
    <div class="card bg-gradient-info">
      <div class="card-header border-0">
        <h3 class="card-title">
          <i class="fas fa-th mr-1"></i>
          Tăng trưởng
        </h3>
        <div class="card-tools">
          <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
      </div>
      <!-- /.card-body -->
      <div class="card-footer bg-transparent">
        <div class="row">
          <div class="col-4 text-center">
            <input type="text" class="knob" data-readonly="true" value="20" data-width="60" data-height="60" data-fgColor="#39CCCC">
            <div class="text-white">Bài viết</div>
          </div>
          <!-- ./col -->
          <div class="col-4 text-center">
            <input type="text" class="knob" data-readonly="true" value="50" data-width="60" data-height="60" data-fgColor="#39CCCC">
            <div class="text-white">Lượt xem</div>
          </div>
          <!-- ./col -->
          <div class="col-4 text-center">
            <input type="text" class="knob" data-readonly="true" value="30" data-width="60" data-height="60" data-fgColor="#39CCCC">
            <div class="text-white">Bình luận</div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.card-footer -->
    </div>
    <!-- /.card -->

    <!-- Calendar -->
    <div class="card bg-gradient-success">
      <div class="card-header border-0">
        <h3 class="card-title">
          <i class="far fa-calendar-alt"></i>
          Lịch
        </h3>
        <!-- tools card -->
        <div class="card-tools">
          <!-- button with a dropdown -->
          <div class="btn-group">
            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
              <i class="fas fa-bars"></i>
            </button>
            <div class="dropdown-menu" role="menu">
              <a href="#" class="dropdown-item">Thêm sự kiện</a>
              <a href="#" class="dropdown-item">Xóa sự kiện</a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item">Xem lịch</a>
            </div>
          </div>
          <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <!-- /. tools -->
      </div>
      <!-- /.card-header -->
      <div class="card-body pt-0">
        <!--The calendar -->
        <div id="calendar" style="width: 100%"></div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </section>
  <!-- right col -->
</div>
<!-- /.row (main row) -->

<!-- Bài viết gần đây -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Bài viết gần đây</h3>
        <div class="card-tools">
          <div class="input-group input-group-sm" style="width: 150px;">
            <input type="text" name="table_search" class="form-control float-right" placeholder="Tìm kiếm">
            <div class="input-group-append">
              <button type="submit" class="btn btn-default">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Tác giả</th>
              <th>Danh mục</th>
              <th>Trạng thái</th>
              <th>Lượt xem</th>
              <th>Ngày tạo</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_posts as $post): ?>
            <tr>
              <td><?= $post['id'] ?></td>
              <td>
                <a href="/admin/posts/edit/<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
              </td>
              <td><?= htmlspecialchars($post['username'] ?? 'N/A') ?></td>
              <td>
                <?php if ($post['category_name']): ?>
                  <span class="badge badge-info"><?= htmlspecialchars($post['category_name']) ?></span>
                <?php else: ?>
                  <span class="badge badge-secondary">Chưa phân loại</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($post['status'] == 'published'): ?>
                  <span class="badge badge-success">Đã đăng</span>
                <?php elseif ($post['status'] == 'draft'): ?>
                  <span class="badge badge-warning">Nháp</span>
                <?php else: ?>
                  <span class="badge badge-secondary"><?= ucfirst($post['status']) ?></span>
                <?php endif; ?>
              </td>
              <td><?= number_format($post['view_count']) ?></td>
              <td><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
              <td>
                <a href="/admin/posts/edit/<?= $post['id'] ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="/post/<?= $post['id'] ?>" target="_blank" class="btn btn-sm btn-info">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
</div>

<script>
// Dữ liệu cho biểu đồ
const chartData = <?= json_encode($chart_data) ?>;

// Chuẩn bị dữ liệu cho Chart.js
const labels = chartData.map(item => {
  const date = new Date(item.date);
  return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
});
const data = chartData.map(item => item.views);

// Khởi tạo biểu đồ khi DOM ready
$(document).ready(function() {
  // Revenue Chart
  const revenueChartCanvas = $('#revenue-chart-canvas').get(0).getContext('2d');
  const revenueChart = new Chart(revenueChartCanvas, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Lượt xem',
        backgroundColor: 'rgba(60,141,188,0.9)',
        borderColor: 'rgba(60,141,188,0.8)',
        pointRadius: false,
        pointColor: '#3b8bba',
        pointStrokeColor: 'rgba(60,141,188,1)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data: data
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: false
      },
      scales: {
        x: {
          grid: {
            display: false
          }
        },
        y: {
          grid: {
            display: false
          }
        }
      }
    }
  });

  // Line Chart
  const lineChartCanvas = $('#line-chart').get(0).getContext('2d');
  const lineChart = new Chart(lineChartCanvas, {
    type: 'line',
    data: {
      labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7'],
      datasets: [{
        label: 'Bài viết',
        backgroundColor: 'rgba(255,255,255,0.2)',
        borderColor: 'rgba(255,255,255,1)',
        pointBackgroundColor: 'rgba(255,255,255,1)',
        pointBorderColor: '#fff',
        data: [28, 48, 40, 19, 86, 27, 90]
      }]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: false
      },
      scales: {
        x: {
          grid: {
            display: false,
            color: 'rgba(255,255,255,0.2)'
          },
          ticks: {
            color: '#fff'
          }
        },
        y: {
          grid: {
            display: true,
            color: 'rgba(255,255,255,0.2)'
          },
          ticks: {
            color: '#fff'
          }
        }
      }
    }
  });

  // Sparklines
  $('#sparkline-1').sparkline([<?= implode(',', array_fill(0, 7, rand(10, 50))) ?>], {
    type: 'bar',
    height: '30',
    barColor: '#fff',
    width: '100%'
  });
  
  $('#sparkline-2').sparkline([<?= implode(',', array_fill(0, 7, rand(5, 25))) ?>], {
    type: 'bar',
    height: '30',
    barColor: '#fff',
    width: '100%'
  });

  // Knob charts
  $('.knob').knob({
    draw: function () {
      if (this.$.data('skin') == 'tron') {
        var a = this.angle(this.cv)
          , sa = this.startAngle
          , sat = this.startAngle
          , ea
          , eat = sat + a
          , r = true

        this.g.lineWidth = this.lineWidth

        this.o.cursor
        && (sat = eat - 0.3)
        && (eat = eat + 0.3)

        if (this.o.displayPrevious) {
          ea = this.startAngle + this.angle(this.value)
          this.o.cursor
          && (sa = ea - 0.3)
          && (ea = ea + 0.3)
          this.g.beginPath()
          this.g.strokeStyle = this.previousColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
          this.g.stroke()
        }

        this.g.beginPath()
        this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
        this.g.stroke()

        this.g.lineWidth = 2
        this.g.beginPath()
        this.g.strokeStyle = this.o.fgColor
        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
        this.g.stroke()

        return false
      }
    }
  })
});
</script>

<?php require_once 'includes/footer.php'; ?>