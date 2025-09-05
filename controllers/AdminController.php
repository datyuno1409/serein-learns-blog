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
        
        $content = 'views/admin/dashboard.php';
        include('views/layouts/admin.php');
    }

    public function posts() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db;
        require 'admin/posts_list.php';
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
        try {
            $sql = "SELECT c.*, COUNT(a.id) as article_count 
                    FROM categories c 
                    LEFT JOIN articles a ON c.id = a.category_id 
                    GROUP BY c.id 
                    ORDER BY c.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $categories = [];
            $_SESSION['error'] = 'Lỗi khi tải danh sách categories: ' . $e->getMessage();
        }
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
        
        // Get all users with their post counts
        $stmt = $pdo->query("
            SELECT u.*, 
                   (SELECT COUNT(*) FROM articles WHERE user_id = u.id) as post_count
            FROM users u 
            ORDER BY u.created_at DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
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
}