<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../helpers/auth_helper.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}

// Kiểm tra quyền admin
if (!isAdmin()) {
    header('Location: /');
    exit;
}

$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Blog Admin Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
  
  <?php if (isset($additional_css)): ?>
    <?= $additional_css ?>
  <?php endif; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/admin/dashboard" class="nav-link">Trang chủ</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" target="_blank" class="nav-link">Xem website</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" href="/admin/search" title="Tìm kiếm toàn cục">
          <i class="fas fa-search"></i>
        </a>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <div class="media">
              <img src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nguyễn Văn A
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Bình luận mới về bài viết...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 giờ trước</p>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Xem tất cả bình luận</a>
        </div>
      </li>
      
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Thông báo</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 bình luận mới
            <span class="float-right text-muted text-sm">3 phút</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 người dùng mới
            <span class="float-right text-muted text-sm">12 giờ</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 bài viết mới
            <span class="float-right text-muted text-sm">2 ngày</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Xem tất cả thông báo</a>
        </div>
      </li>
      
      <!-- User Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" alt="User Avatar" class="img-size-32 img-circle mr-2">
          <span class="d-none d-md-inline"><?= htmlspecialchars($current_user['username']) ?></span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-header">
            <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
            <div class="d-inline-block">
              <h6 class="mb-0"><?= htmlspecialchars($current_user['username']) ?></h6>
              <small class="text-muted"><?= htmlspecialchars($current_user['email']) ?></small>
            </div>
          </div>
          <div class="dropdown-divider"></div>
          <a href="/admin/profile" class="dropdown-item">
            <i class="fas fa-user mr-2"></i> Hồ sơ cá nhân
          </a>
          <a href="/admin/settings" class="dropdown-item">
            <i class="fas fa-cog mr-2"></i> Cài đặt
          </a>
          <div class="dropdown-divider"></div>
          <a href="/logout" class="dropdown-item">
            <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
          </a>
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
      <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Blog Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="/admin/profile" class="d-block"><?= htmlspecialchars($current_user['username']) ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Tìm kiếm" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="/admin/dashboard" class="nav-link <?= (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          
          <!-- Bài viết -->
          <li class="nav-item <?= (isset($active_menu) && in_array($active_menu, ['posts', 'posts_list', 'posts_add', 'posts_edit'])) ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= (isset($active_menu) && in_array($active_menu, ['posts', 'posts_list', 'posts_add', 'posts_edit'])) ? 'active' : '' ?>">
              <i class="nav-icon fas fa-newspaper"></i>
              <p>
                Quản lý bài viết
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/posts" class="nav-link <?= (isset($active_menu) && $active_menu == 'posts_list') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách bài viết</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/posts/add" class="nav-link <?= (isset($active_menu) && $active_menu == 'posts_add') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Thêm bài viết</p>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Danh mục -->
          <li class="nav-item <?= (isset($active_menu) && in_array($active_menu, ['categories', 'categories_list', 'categories_add', 'categories_edit'])) ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= (isset($active_menu) && in_array($active_menu, ['categories', 'categories_list', 'categories_add', 'categories_edit'])) ? 'active' : '' ?>">
              <i class="nav-icon fas fa-folder"></i>
              <p>
                Danh mục
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/categories" class="nav-link <?= (isset($active_menu) && $active_menu == 'categories_list') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách danh mục</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/categories/add" class="nav-link <?= (isset($active_menu) && $active_menu == 'categories_add') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Thêm danh mục</p>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Bình luận -->
          <li class="nav-item">
            <a href="/admin/comments" class="nav-link <?= (isset($active_menu) && $active_menu == 'comments') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-comments"></i>
              <p>
                Bình luận
                <span class="badge badge-info right">6</span>
              </p>
            </a>
          </li>
            
            <!-- Tìm kiếm -->
            <li class="nav-item">
              <a href="/admin/search" class="nav-link <?= (isset($active_menu) && $active_menu == 'search') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-search"></i>
                <p>Tìm kiếm</p>
              </a>
            </li>
            
            <!-- Người dùng -->
          <li class="nav-item <?= (isset($active_menu) && in_array($active_menu, ['users', 'users_list', 'users_add', 'users_edit'])) ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= (isset($active_menu) && in_array($active_menu, ['users', 'users_list', 'users_add', 'users_edit'])) ? 'active' : '' ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Người dùng
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="/admin/users" class="nav-link <?= (isset($active_menu) && $active_menu == 'users_list') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Danh sách người dùng</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/users/add" class="nav-link <?= (isset($active_menu) && $active_menu == 'users_add') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Thêm người dùng</p>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Media -->
          <li class="nav-item">
            <a href="/admin/media" class="nav-link <?= (isset($active_menu) && $active_menu == 'media') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-images"></i>
              <p>Thư viện Media</p>
            </a>
          </li>
          
          <!-- Cài đặt -->
          <li class="nav-item">
            <a href="/admin/settings" class="nav-link <?= (isset($active_menu) && $active_menu == 'settings') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-cog"></i>
              <p>Cài đặt</p>
            </a>
          </li>
          
          <li class="nav-header">KHÁC</li>
          
          <!-- Thống kê -->
          <li class="nav-item">
            <a href="/admin/analytics" class="nav-link <?= (isset($active_menu) && $active_menu == 'analytics') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>Thống kê</p>
            </a>
          </li>
          
          <!-- Backup -->
          <li class="nav-item">
            <a href="/admin/backup" class="nav-link <?= (isset($active_menu) && $active_menu == 'backup') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-database"></i>
              <p>Sao lưu</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= isset($page_title) ? $page_title : 'Dashboard' ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                  <?php if (isset($breadcrumb['url'])): ?>
                    <li class="breadcrumb-item"><a href="<?= $breadcrumb['url'] ?>"><?= $breadcrumb['title'] ?></a></li>
                  <?php else: ?>
                    <li class="breadcrumb-item active"><?= $breadcrumb['title'] ?></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="/admin/dashboard">Trang chủ</a></li>
                <li class="breadcrumb-item active"><?= isset($page_title) ? $page_title : 'Dashboard' ?></li>
              <?php endif; ?>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php if (isset($content)): ?>
          <?= $content ?>
        <?php endif; ?>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#">Blog Admin</a>.</strong>
    Tất cả quyền được bảo lưu.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<!-- Sparkline -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
<!-- JQVMap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/1.13.1/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/pages/dashboard.js"></script>

<?php if (isset($additional_js)): ?>
  <?= $additional_js ?>
<?php endif; ?>

</body>
</html>