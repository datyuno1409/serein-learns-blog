<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Language.php';

class AdminController {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $this->db = $database->connect();
        }
    }

    private function isAdmin() {
        require_once __DIR__ . '/../helpers/auth_helper.php';
        return isAdmin();
    }

    public function dashboard() {
        file_put_contents('debug.log', "AdminController::dashboard() called\n", FILE_APPEND);
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            file_put_contents('debug.log', "Database connection failed in dashboard\n", FILE_APPEND);
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        file_put_contents('debug.log', "Database connection OK in dashboard\n", FILE_APPEND);
        
        // Get total counts
        $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        // Get recent posts
        $stmt = $pdo->query("
            SELECT a.*, c.name as category_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            ORDER BY a.created_at DESC 
            LIMIT 5
        ");
        $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent comments
        $stmt = $pdo->query("
            SELECT c.*, u.username as user_name, u.avatar as user_avatar 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC 
            LIMIT 5
        ");
        $recentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get category statistics
        $stmt = $pdo->query("
            SELECT c.name, COUNT(a.id) as count 
            FROM categories c 
            LEFT JOIN articles a ON c.id = a.category_id 
            GROUP BY c.id, c.name 
            ORDER BY count DESC
        ");
        $categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get monthly statistics for the last 12 months
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as articles,
                0 as comments
            FROM articles 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $articleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as comments
            FROM comments 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $commentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Merge monthly stats
        $monthlyStats = [];
        $months = [];
        
        // Get all months from both queries
        foreach ($articleStats as $stat) {
            $months[$stat['month']] = ['month' => $stat['month'], 'articles' => $stat['articles'], 'comments' => 0];
        }
        
        foreach ($commentStats as $stat) {
            if (isset($months[$stat['month']])) {
                $months[$stat['month']]['comments'] = $stat['comments'];
            } else {
                $months[$stat['month']] = ['month' => $stat['month'], 'articles' => 0, 'comments' => $stat['comments']];
            }
        }
        
        $monthlyStats = array_values($months);
        
        // Rename $recentPosts to $recentArticles for consistency with view
        $recentArticles = $recentPosts;
        
        // Include CSS and JS files for admin dashboard
        $page_css = 'assets/css/admin-dashboard.css';
        $page_js = 'assets/js/admin-dashboard.js';
        
        $content = 'views/admin/dashboard.php';
        include('views/layouts/admin.php');
    }

    public function posts() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        
        // Get posts data (similar to posts_list.php logic)
        try {
            // Handle search and filters
            $search = $_GET['search'] ?? '';
            $category_filter = $_GET['category'] ?? '';
            $author_filter = $_GET['author'] ?? '';
            $status_filter = $_GET['status'] ?? '';
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            // Get categories for filter
            $categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
            $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get authors for filter
            $authorsStmt = $pdo->query("SELECT DISTINCT u.id, u.username FROM users u INNER JOIN articles a ON u.id = a.user_id ORDER BY u.username");
            $authors = $authorsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Build WHERE clause
            $whereConditions = [];
            $params = [];
            
            if (!empty($search)) {
                $whereConditions[] = "(a.title LIKE ? OR a.content LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            
            if (!empty($category_filter)) {
                $whereConditions[] = "a.category_id = ?";
                $params[] = $category_filter;
            }
            
            if (!empty($author_filter)) {
                $whereConditions[] = "a.user_id = ?";
                $params[] = $author_filter;
            }
            
            if (!empty($status_filter)) {
                $whereConditions[] = "a.status = ?";
                $params[] = $status_filter;
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM articles a $whereClause";
            $countStmt = $pdo->prepare($countSql);
            $countStmt->execute($params);
            $totalPosts = $countStmt->fetchColumn();
            $totalPages = ceil($totalPosts / $limit);
            
            // Get posts
            $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    $whereClause 
                    ORDER BY a.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $pdo->prepare($sql);
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $posts = [];
            $categories = [];
            $authors = [];
            $totalPosts = 0;
            $totalPages = 0;
            $_SESSION['error'] = 'Lỗi khi tải danh sách bài viết: ' . $e->getMessage();
        }
        
        // Include CSS and JS files for posts management
        $page_css = 'assets/css/admin-posts.css';
        $page_js = 'assets/js/admin-posts.js';
        
        $content = 'views/admin/posts/index.php';
        include('views/layouts/admin.php');
    }

    public function postsAdd() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/posts_add.php';
    }

    public function postsEdit() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/posts_edit.php';
    }

    public function postsDelete() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/posts_delete.php';
    }

    public function categories() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Get categories with post count
        try {
            $stmt = $pdo->query("
                SELECT c.*, COUNT(a.id) as post_count 
                FROM categories c 
                LEFT JOIN articles a ON c.id = a.category_id 
                GROUP BY c.id 
                ORDER BY c.name ASC
            ");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $categories = [];
            $_SESSION['error'] = 'Lỗi khi tải danh sách danh mục: ' . $e->getMessage();
        }
        
        // Set active menu
        $active_menu = 'categories';
        
        $content = 'views/admin/categories/index.php';
        include('views/layouts/admin.php');
    }

    public function categoriesAdd() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/categories_add.php';
    }

    public function categoriesEdit() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/categories_edit.php';
    }

    public function categoriesDelete() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/categories_delete.php';
    }

    public function users() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Handle search and filters
        $search = $_GET['search'] ?? '';
        $role_filter = $_GET['role'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = "(u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($role_filter)) {
            $whereConditions[] = "u.role = ?";
            $params[] = $role_filter;
        }
        
        if (!empty($status_filter)) {
            if ($status_filter === 'active') {
                $whereConditions[] = "u.is_active = 1";
            } elseif ($status_filter === 'inactive') {
                $whereConditions[] = "u.is_active = 0";
            }
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM users u $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total_users = $countStmt->fetchColumn();
        $total_pages = ceil($total_users / $per_page);
        
        // Get users with pagination
        $sql = "
            SELECT u.*, 
                   (SELECT COUNT(*) FROM articles WHERE user_id = u.id) as post_count
            FROM users u 
            $whereClause
            ORDER BY u.created_at DESC 
            LIMIT $per_page OFFSET $offset
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statistics
        $statsStmt = $pdo->query("
            SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_count,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_count
            FROM users
        ");
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Set page-specific CSS and JS
        $page_css = 'assets/css/admin/users.css';
        $page_js = 'assets/js/admin/users.js';
        
        $content = 'views/admin/users/index.php';
        include('views/layouts/admin.php');
    }

    public function comments() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Get all comments with user and article info
        $stmt = $pdo->query("
            SELECT c.*, 
                   u.username as user_name,
                   a.title as article_title
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN articles a ON c.article_id = a.id
            ORDER BY c.created_at DESC
        ");
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = 'views/admin/comments/index.php';
        include('views/layouts/admin.php');
    }

    public function settings() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Create settings table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(255) UNIQUE NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Default settings
        $defaults = [
            'site_name' => 'Serein Blog',
            'site_description' => 'A modern blog platform',
            'site_url' => 'http://localhost:8000',
            'admin_email' => 'admin@example.com',
            'posts_per_page' => '10',
            'enable_comments' => '1',
            'enable_registration' => '1',
            'maintenance_mode' => '0',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'meta_title' => 'Serein Blog - Modern Blog Platform',
            'meta_description' => 'A modern blog platform built with PHP and MySQL',
            'meta_keywords' => 'blog, php, mysql, programming',
            'google_analytics' => '',
            'google_search_console' => '',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'linkedin_url' => ''
        ];
        
        // Get current settings from database
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $currentSettings = [];
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currentSettings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        // Merge defaults with current settings
        $settings = array_merge($defaults, $currentSettings);
        
        // Set page-specific CSS and JS
        $pageCSS = ['assets/css/admin/settings.css'];
        $pageJS = ['assets/js/admin/settings.js'];
        
        // Set page title and breadcrumb
        $pageTitle = 'Settings';
        $breadcrumb = 'Settings';
        
        $content = 'views/admin/settings.php';
        include('views/layouts/admin.php');
    }

    public function settingsSave() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/settings');
            exit;
        }
        
        $pdo = $this->db;
        if (!$pdo) {
            header('Location: /admin/settings?error=database');
            exit;
        }
        
        try {
            // List of allowed settings
            $allowedSettings = [
                'site_name', 'site_description', 'site_url', 'admin_email',
                'posts_per_page', 'timezone', 'meta_title', 'meta_description',
                'meta_keywords', 'google_analytics', 'google_search_console',
                'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url'
            ];
            
            // Handle checkboxes (they won't be in POST if unchecked)
            $checkboxSettings = ['enable_comments', 'enable_registration', 'maintenance_mode'];
            
            $pdo->beginTransaction();
            
            // Save regular settings
            foreach ($allowedSettings as $key) {
                if (isset($_POST[$key])) {
                    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
                    $stmt->execute([$key, $_POST[$key], $_POST[$key]]);
                }
            }
            
            // Save checkbox settings
            foreach ($checkboxSettings as $key) {
                $value = isset($_POST[$key]) ? '1' : '0';
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
                $stmt->execute([$key, $value, $value]);
            }
            
            $pdo->commit();
            header('Location: /admin/settings?success=1');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            header('Location: /admin/settings?error=save');
        }
        
        exit;
    }

    public function usersAdd() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        $errors = [];
        $username = '';
        $email = '';
        $is_admin = 0;
        $page_title = 'Thêm người dùng mới';
        
        // Xử lý form submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $is_admin = (int)($_POST['is_admin'] ?? 0);
            
            // Validate required fields
            if (empty($username)) {
                $errors[] = 'Tên đăng nhập là bắt buộc.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới, từ 3-20 ký tự.';
            }
            
            if (empty($email)) {
                $errors[] = 'Email là bắt buộc.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            }
            
            if (empty($password)) {
                $errors[] = 'Mật khẩu là bắt buộc.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            }
            
            if ($password !== $confirm_password) {
                $errors[] = 'Xác nhận mật khẩu không khớp.';
            }
            
            // Check if username or email already exists
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Lỗi kiểm tra dữ liệu: ' . $e->getMessage();
                }
            }
            
            // Create user if no errors
            if (empty($errors)) {
                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare('
                        INSERT INTO users (username, email, password, is_admin, created_at) 
                        VALUES (?, ?, ?, ?, NOW())
                    ');
                    
                    $stmt->execute([
                        $username,
                        $email,
                        $hashed_password,
                        $is_admin
                    ]);
                    
                    $_SESSION['success'] = 'Thêm người dùng thành công!';
                    header('Location: /admin/users');
                    exit;
                    
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi tạo người dùng: ' . $e->getMessage();
                }
            }
        }
        
        require 'admin/users_add.php';
    }
    
    public function usersEdit() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        $user_id = (int)($_GET['id'] ?? 0);
        if ($user_id <= 0) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ.';
            header('Location: /admin/users');
            exit;
        }
        
        // Get user data
        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                $_SESSION['error'] = 'Không tìm thấy người dùng.';
                header('Location: /admin/users');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi khi tải dữ liệu người dùng: ' . $e->getMessage();
            header('Location: /admin/users');
            exit;
        }
        
        $errors = [];
        $page_title = 'Chỉnh sửa người dùng';
        
        // Xử lý form submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $is_admin = (int)($_POST['is_admin'] ?? 0);
            
            // Validate required fields
            if (empty($username)) {
                $errors[] = 'Tên đăng nhập là bắt buộc.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới, từ 3-20 ký tự.';
            }
            
            if (empty($email)) {
                $errors[] = 'Email là bắt buộc.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            }
            
            // Validate password if provided
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
                }
                
                if ($password !== $confirm_password) {
                    $errors[] = 'Xác nhận mật khẩu không khớp.';
                }
            }
            
            // Check if username or email already exists (excluding current user)
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
                    $stmt->execute([$username, $email, $user_id]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Lỗi kiểm tra dữ liệu: ' . $e->getMessage();
                }
            }
            
            // Update user if no errors
            if (empty($errors)) {
                try {
                    if (!empty($password)) {
                        // Update with new password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare('
                            UPDATE users 
                            SET username = ?, email = ?, password = ?, is_admin = ?, updated_at = NOW() 
                            WHERE id = ?
                        ');
                        $stmt->execute([$username, $email, $hashed_password, $is_admin, $user_id]);
                    } else {
                        // Update without changing password
                        $stmt = $pdo->prepare('
                            UPDATE users 
                            SET username = ?, email = ?, is_admin = ?, updated_at = NOW() 
                            WHERE id = ?
                        ');
                        $stmt->execute([$username, $email, $is_admin, $user_id]);
                    }
                    
                    $_SESSION['success'] = 'Cập nhật người dùng thành công!';
                    header('Location: /admin/users');
                    exit;
                    
                } catch (Exception $e) {
                    $errors[] = 'Lỗi khi cập nhật người dùng: ' . $e->getMessage();
                }
            }
        }
        
        $pdo = $this->db;
        require 'admin/users_edit.php';
    }

    public function usersDelete() {
        if (!$this->isAdmin()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            exit;
        }

        $user_id = $_POST['user_id'] ?? null;

        if (!$user_id) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ';
            header('Location: /admin/users');
            exit;
        }

        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('Người dùng không tồn tại');
            }
            
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE author_id = ?");
            $stmt->execute([$user_id]);
            $post_count = $stmt->fetchColumn();
            
            if ($post_count > 0) {
                throw new Exception('Không thể xóa người dùng này vì có ' . $post_count . ' bài viết liên quan');
            }
            
            $stmt = $this->db->prepare("DELETE FROM comments WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Xóa người dùng "' . $user['username'] . '" thành công';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /admin/users');
        exit;
    }

    public function analytics() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Get analytics data
        $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        // Get monthly article stats for the last 12 months
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM articles 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $monthlyArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get top categories
        $stmt = $pdo->query("
            SELECT c.name, COUNT(a.id) as count 
            FROM categories c 
            LEFT JOIN articles a ON c.id = a.category_id 
            GROUP BY c.id, c.name 
            ORDER BY count DESC
            LIMIT 10
        ");
        $topCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent activity
        $stmt = $pdo->query("
            SELECT 'article' as type, title as name, created_at 
            FROM articles 
            UNION ALL
            SELECT 'comment' as type, CONCAT('Comment on: ', (SELECT title FROM articles WHERE id = article_id)) as name, created_at 
            FROM comments 
            ORDER BY created_at DESC 
            LIMIT 20
        ");
        $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $content = 'views/admin/analytics.php';
        include('views/layouts/admin.php');
    }

    public function backup() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        
        $pdo = $this->db;
        if (!$pdo) {
            require_once __DIR__ . '/../views/errors/database.php';
            exit;
        }
        
        // Handle backup creation
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
            $this->createBackup();
            return;
        }
        
        // Get existing backup files
        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupFiles = [];
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backupFiles[] = [
                        'name' => $file,
                        'size' => filesize($backupDir . '/' . $file),
                        'date' => date('Y-m-d H:i:s', filemtime($backupDir . '/' . $file))
                    ];
                }
            }
        }
        
        // Sort by date (newest first)
        usort($backupFiles, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        $content = 'views/admin/backup.php';
        include('views/layouts/admin.php');
    }
    
    private function createBackup() {
        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;
        
        try {
            // Get all tables
            $tables = [];
            $stmt = $this->db->query("SHOW TABLES");
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $backup = "-- Database Backup\n";
            $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
            
            foreach ($tables as $table) {
                // Get table structure
                $stmt = $this->db->query("SHOW CREATE TABLE `$table`");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $backup .= "-- Table structure for `$table`\n";
                $backup .= "DROP TABLE IF EXISTS `$table`;\n";
                $backup .= $row['Create Table'] . ";\n\n";
                
                // Get table data
                $stmt = $this->db->query("SELECT * FROM `$table`");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    $backup .= "-- Data for table `$table`\n";
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            return $value === null ? 'NULL' : $this->db->quote($value);
                        }, array_values($row));
                        $backup .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $backup .= "\n";
                }
            }
            
            file_put_contents($filepath, $backup);
            $_SESSION['success'] = 'Backup created successfully: ' . $filename;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Backup failed: ' . $e->getMessage();
        }
        
        header('Location: /admin/backup');
        exit;
    }
}