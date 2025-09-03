<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Language.php';

class AdminController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    public function dashboard() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();

        try {
            $conn = $this->db->connect();

            $totalArticles = $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn();
            $totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
            $totalComments = $conn->query("SELECT COUNT(*) FROM comments")->fetchColumn();
            $totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

            $stmt = $conn->query("
                SELECT a.*, c.name as category_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                ORDER BY a.created_at DESC 
                LIMIT 5
            ");
            $recentArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $conn->query("
                SELECT c.*, u.username as user_name, u.avatar as user_avatar 
                FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                ORDER BY c.created_at DESC 
                LIMIT 5
            ");
            $recentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $conn->query("
                SELECT c.name, COUNT(a.id) as count 
                FROM categories c 
                LEFT JOIN articles a ON c.id = a.category_id 
                GROUP BY c.id, c.name 
                ORDER BY count DESC
            ");
            $categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $conn->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as articles
                FROM articles 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month
            ");
            $monthlyArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $conn->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as comments
                FROM comments 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month
            ");
            $monthlyComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $monthlyStats = [];
            $months = [];
            foreach ($monthlyArticles as $data) {
                $months[$data['month']] = ['month' => $data['month'], 'articles' => $data['articles'], 'comments' => 0];
            }
            foreach ($monthlyComments as $data) {
                if (isset($months[$data['month']])) {
                    $months[$data['month']]['comments'] = $data['comments'];
                } else {
                    $months[$data['month']] = ['month' => $data['month'], 'articles' => 0, 'comments' => $data['comments']];
                }
            }
            $monthlyStats = array_values($months);

            $content = 'views/admin/dashboard.php';
            require_once 'views/layouts/admin.php';
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    public function posts() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/posts_list.php';
    }

    public function postsAdd() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/posts_add.php';
    }

    public function postsEdit() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/posts_edit.php';
    }

    public function postsDelete() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/posts_delete.php';
    }

    public function categories() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
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
        require 'views/admin/categories/index.php';
    }

    public function categoriesAdd() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/categories_add.php';
    }

    public function categoriesEdit() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        $pdo = $this->db->connect();
        require 'admin/categories_edit.php';
    }

    public function categoriesDelete() {
        require_once 'helpers/auth_helper.php';
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = $this->db->connect();
            $id = (int)($_POST['id'] ?? 0);
            try {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE category_id = ?');
                $stmt->execute([$id]);
                $article_count = $stmt->fetchColumn();
                if ($article_count > 0) {
                    $_SESSION['error'] = 'Không thể xóa danh mục này vì còn có ' . $article_count . ' bài viết.';
                } else {
                    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
                    $stmt->execute([$id]);
                    $_SESSION['success'] = 'Đã xóa danh mục thành công!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi xóa danh mục: ' . $e->getMessage();
            }
        }
        header('Location: /admin/categories');
        exit();
    }
}