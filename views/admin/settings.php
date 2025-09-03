<?php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings | Admin Panel</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="/admin/logout">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
      <i class="fas fa-blog brand-image"></i>
      <span class="brand-text font-weight-light">Admin Panel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="/admin/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/articles" class="nav-link">
              <i class="nav-icon fas fa-newspaper"></i>
              <p>Articles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/categories" class="nav-link">
              <i class="nav-icon fas fa-folder"></i>
              <p>Categories</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/comments" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>Comments</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/users" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="/admin/settings" class="nav-link active">
              <i class="nav-icon fas fa-cog"></i>
              <p>Settings</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Settings</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
              <li class="breadcrumb-item active">Settings</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i> <?= $_SESSION['success'] ?>
          </div>
          <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i> <?= $_SESSION['error'] ?>
          </div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form method="POST" action="/admin/settings/save">
          <div class="row">
            <!-- Site Configuration -->
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-globe mr-1"></i>
                    Site Configuration
                  </h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" class="form-control" id="site_name" name="site_name" 
                           value="<?= htmlspecialchars($settings['site_name'] ?? 'Serein Blog') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? 'A modern blog platform') ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="site_url">Site URL</label>
                    <input type="url" class="form-control" id="site_url" name="site_url" 
                           value="<?= htmlspecialchars($settings['site_url'] ?? 'http://localhost:8000') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" class="form-control" id="admin_email" name="admin_email" 
                           value="<?= htmlspecialchars($settings['admin_email'] ?? 'admin@example.com') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="posts_per_page">Posts Per Page</label>
                    <input type="number" class="form-control" id="posts_per_page" name="posts_per_page" 
                           value="<?= htmlspecialchars($settings['posts_per_page'] ?? '10') ?>" min="1" max="50">
                  </div>
                </div>
              </div>
            </div>

            <!-- SEO Configuration -->
            <div class="col-md-6">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>
                    SEO Configuration
                  </h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="meta_title">Default Meta Title</label>
                    <input type="text" class="form-control" id="meta_title" name="meta_title" 
                           value="<?= htmlspecialchars($settings['meta_title'] ?? 'Serein Blog - Modern Blog Platform') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="meta_description">Default Meta Description</label>
                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($settings['meta_description'] ?? 'A modern blog platform built with PHP and MySQL') ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label for="meta_keywords">Default Meta Keywords</label>
                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                           value="<?= htmlspecialchars($settings['meta_keywords'] ?? 'blog, php, mysql, programming') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="google_analytics">Google Analytics ID</label>
                    <input type="text" class="form-control" id="google_analytics" name="google_analytics" 
                           value="<?= htmlspecialchars($settings['google_analytics'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                  </div>
                  
                  <div class="form-group">
                    <label for="google_search_console">Google Search Console</label>
                    <input type="text" class="form-control" id="google_search_console" name="google_search_console" 
                           value="<?= htmlspecialchars($settings['google_search_console'] ?? '') ?>" placeholder="Verification code">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Media & Features -->
          <div class="row">
            <div class="col-md-6">
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-share-alt mr-1"></i>
                    Social Media
                  </h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="facebook_url">Facebook URL</label>
                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                           value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="twitter_url">Twitter URL</label>
                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                           value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="instagram_url">Instagram URL</label>
                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                           value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                  </div>
                  
                  <div class="form-group">
                    <label for="linkedin_url">LinkedIn URL</label>
                    <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                           value="<?= htmlspecialchars($settings['linkedin_url'] ?? '') ?>">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-cogs mr-1"></i>
                    Features
                  </h3>
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="enable_comments" name="enable_comments" 
                             <?= ($settings['enable_comments'] ?? true) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="enable_comments">Enable Comments</label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="enable_registration" name="enable_registration" 
                             <?= ($settings['enable_registration'] ?? true) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="enable_registration">Enable User Registration</label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode" 
                             <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                      <label class="custom-control-label" for="maintenance_mode">Maintenance Mode</label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="timezone">Timezone</label>
                    <select class="form-control" id="timezone" name="timezone">
                      <option value="Asia/Ho_Chi_Minh" <?= ($settings['timezone'] ?? 'Asia/Ho_Chi_Minh') === 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?>>Asia/Ho_Chi_Minh</option>
                      <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                      <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>America/New_York</option>
                      <option value="Europe/London" <?= ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body text-center">
                  <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                  </button>
                  <a href="/admin/dashboard" class="btn btn-secondary btn-lg ml-2">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                  </a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Version 1.0.0
    </div>
    <strong>Copyright &copy; 2025 Serein Blog.</strong> All rights reserved.
  </footer>
</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Form validation
    $('form').on('submit', function(e) {
        var siteName = $('#site_name').val().trim();
        var adminEmail = $('#admin_email').val().trim();
        
        if (!siteName) {
            alert('Site name is required!');
            e.preventDefault();
            return false;
        }
        
        if (!adminEmail) {
            alert('Admin email is required!');
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
    });
});
</script>
</body>
</html>