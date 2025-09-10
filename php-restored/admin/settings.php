<?php
require_once __DIR__ . '/../helpers/auth_helper.php';
requireAdmin();

$page_title = 'Cài đặt';
$current_page = 'settings';

// Handle form submission
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? '',
        'site_description' => $_POST['site_description'] ?? '',
        'site_keywords' => $_POST['site_keywords'] ?? '',
        'admin_email' => $_POST['admin_email'] ?? '',
        'posts_per_page' => (int)($_POST['posts_per_page'] ?? 10),
        'allow_comments' => isset($_POST['allow_comments']) ? 1 : 0,
        'moderate_comments' => isset($_POST['moderate_comments']) ? 1 : 0,
        'site_maintenance' => isset($_POST['site_maintenance']) ? 1 : 0,
        'google_analytics' => $_POST['google_analytics'] ?? '',
        'facebook_url' => $_POST['facebook_url'] ?? '',
        'twitter_url' => $_POST['twitter_url'] ?? '',
        'instagram_url' => $_POST['instagram_url'] ?? '',
        'youtube_url' => $_POST['youtube_url'] ?? ''
    ];
    
    try {
        // Create settings table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt->execute([$key, $value, $value]);
        }
        
        $_SESSION['success'] = 'Cài đặt đã được lưu thành công!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Lỗi khi lưu cài đặt: ' . $e->getMessage();
    }
    
    header('Location: /admin/settings');
    exit;
}

// Get current settings
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $currentSettings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $currentSettings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $currentSettings = [];
}

// Default values
$defaults = [
    'site_name' => 'Learning with Serein',
    'site_description' => 'Blog học tập và chia sẻ kiến thức',
    'site_keywords' => 'blog, học tập, lập trình, công nghệ',
    'admin_email' => 'admin@example.com',
    'posts_per_page' => 10,
    'allow_comments' => 1,
    'moderate_comments' => 1,
    'site_maintenance' => 0,
    'google_analytics' => '',
    'facebook_url' => '',
    'twitter_url' => '',
    'instagram_url' => '',
    'youtube_url' => ''
];

// Merge with current settings
$settings = array_merge($defaults, $currentSettings);

require_once 'admin/includes/header.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Cài đặt</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cài đặt</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                                <i class="fas fa-cog"></i> Cài đặt chung
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">
                                <i class="fas fa-search"></i> SEO & Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="appearance-tab" data-toggle="tab" href="#appearance" role="tab" aria-controls="appearance" aria-selected="false">
                                <i class="fas fa-palette"></i> Giao diện & Mạng xã hội
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="tab-content" id="settingsTabContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Thông tin cơ bản</h5>
                                        <div class="form-group">
                                            <label for="site_name">Tên website</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="site_description">Mô tả website</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="admin_email">Email quản trị</label>
                                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="posts_per_page">Số bài viết mỗi trang</label>
                                            <input type="number" class="form-control" id="posts_per_page" name="posts_per_page" value="<?php echo $settings['posts_per_page']; ?>" min="1" max="50">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-comments text-info"></i> Cài đặt bình luận</h5>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="allow_comments" name="allow_comments" <?php echo $settings['allow_comments'] ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="allow_comments">Cho phép bình luận</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="moderate_comments" name="moderate_comments" <?php echo $settings['moderate_comments'] ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="moderate_comments">Kiểm duyệt bình luận</label>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mb-3 mt-4"><i class="fas fa-tools text-warning"></i> Bảo trì</h5>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="site_maintenance" name="site_maintenance" <?php echo $settings['site_maintenance'] ? 'checked' : ''; ?>>
                                                <label class="custom-control-label" for="site_maintenance">Chế độ bảo trì</label>
                                            </div>
                                            <small class="form-text text-muted">Khi bật, chỉ admin mới có thể truy cập website</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SEO Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-search text-success"></i> Tối ưu SEO</h5>
                                        <div class="form-group">
                                            <label for="site_keywords">Từ khóa SEO</label>
                                            <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="<?php echo htmlspecialchars($settings['site_keywords']); ?>">
                                            <small class="form-text text-muted">Các từ khóa cách nhau bằng dấu phẩy</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-chart-line text-primary"></i> Google Analytics</h5>
                                        <div class="form-group">
                                            <label for="google_analytics">Tracking ID</label>
                                            <input type="text" class="form-control" id="google_analytics" name="google_analytics" value="<?php echo htmlspecialchars($settings['google_analytics']); ?>" placeholder="G-XXXXXXXXXX">
                                            <small class="form-text text-muted">Nhập Google Analytics Tracking ID</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Appearance Tab -->
                            <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-share-alt text-primary"></i> Mạng xã hội</h5>
                                        <div class="form-group">
                                            <label for="facebook_url"><i class="fab fa-facebook text-primary"></i> Facebook URL</label>
                                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url']); ?>" placeholder="https://facebook.com/yourpage">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="twitter_url"><i class="fab fa-twitter text-info"></i> Twitter URL</label>
                                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url']); ?>" placeholder="https://twitter.com/youraccount">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="instagram_url"><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>" placeholder="https://instagram.com/youraccount">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="youtube_url"><i class="fab fa-youtube text-danger"></i> YouTube URL</label>
                                            <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url']); ?>" placeholder="https://youtube.com/yourchannel">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-palette text-success"></i> Giao diện</h5>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Thông tin:</strong> Các tùy chọn giao diện sẽ được thêm trong phiên bản tương lai.
                                        </div>
                                        
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title"><i class="fas fa-lightbulb text-warning"></i> Tính năng sắp có</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li><i class="fas fa-check text-success"></i> Chọn theme màu sắc</li>
                                                    <li><i class="fas fa-check text-success"></i> Tùy chỉnh logo</li>
                                                    <li><i class="fas fa-check text-success"></i> Cài đặt favicon</li>
                                                    <li><i class="fas fa-check text-success"></i> Tùy chỉnh footer</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Lưu cài đặt
                                        </button>
                                        <a href="/admin/dashboard" class="btn btn-secondary btn-lg ml-2">
                                            <i class="fas fa-arrow-left"></i> Quay lại
                                        </a>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Thay đổi sẽ có hiệu lực ngay sau khi lưu
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'admin/includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Remember active tab
    var activeTab = localStorage.getItem('settingsActiveTab');
    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
    }
    
    // Save active tab to localStorage
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        localStorage.setItem('settingsActiveTab', $(e.target).attr('href'));
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        var siteName = $('#site_name').val().trim();
        var adminEmail = $('#admin_email').val().trim();
        
        if (!siteName) {
            e.preventDefault();
            alert('Vui lòng nhập tên website!');
            $('.nav-tabs a[href="#general"]').tab('show');
            $('#site_name').focus();
            return false;
        }
        
        if (!adminEmail) {
            e.preventDefault();
            alert('Vui lòng nhập email quản trị!');
            $('.nav-tabs a[href="#general"]').tab('show');
            $('#admin_email').focus();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...').prop('disabled', true);
    });
});
</script>
