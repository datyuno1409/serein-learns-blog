<?php
require_once __DIR__ . '/../includes/Language.php';

class HomeController {
    private $db;
    private $articles_per_page = 10;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // Get current page
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, $current_page);

        // Calculate offset
        $offset = ($current_page - 1) * $this->articles_per_page;

        // Get total articles count
        $total_count = $this->db->query("SELECT COUNT(*) as count FROM articles WHERE status = 'published'")->fetch()['count'];
        $total_pages = ceil($total_count / $this->articles_per_page);

        // Get articles for current page
        $articles = $this->db->query("
            SELECT 
                a.*, 
                u.username as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM comments WHERE article_id = a.id AND status = 'approved') as comment_count
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published'
            ORDER BY a.created_at DESC
            LIMIT {$this->articles_per_page} OFFSET {$offset}
        ")->fetchAll();

        // Get tags for each article (table not exists yet)
        foreach ($articles as &$article) {
            $article['tags'] = [];
        }

        // Get categories with article count
        $categories = $this->db->query("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM articles WHERE category_id = c.id AND status = 'published') as article_count
            FROM categories c
            ORDER BY c.name
        ")->fetchAll();

        // Get popular tags (table not exists yet)
        $popular_tags = [];

        // Get recent comments
        $recent_comments = $this->db->query("
            SELECT 
                c.*,
                u.username as author_name,
                a.title as article_title
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN articles a ON c.article_id = a.id
            WHERE c.status = 'approved'
            ORDER BY c.created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Set page title
        $page_title = $current_page > 1 ? "Page {$current_page}" : "Home";

        // Include the view
        $content = 'views/home/index.php';
        require 'views/layouts/frontend.php';
    }

    public function search() {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        if (empty($query)) {
            header('Location: /');
            exit;
        }

        // Get current page
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, $current_page);
        $offset = ($current_page - 1) * $this->articles_per_page;

        // Prepare search terms
        $search_terms = '%' . $query . '%';

        // Get total count
        $total_count = $this->db->query("
            SELECT COUNT(*) as count 
            FROM articles 
            WHERE status = 'published' 
            AND (title LIKE ? OR content LIKE ?)
        ", [$search_terms, $search_terms])->fetch()['count'];

        $total_pages = ceil($total_count / $this->articles_per_page);

        // Get articles
        $articles = $this->db->query("
            SELECT 
                a.*, 
                u.username as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM comments WHERE article_id = a.id AND status = 'approved') as comment_count
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published'
            AND (a.title LIKE ? OR a.content LIKE ?)
            ORDER BY a.created_at DESC
            LIMIT {$this->articles_per_page} OFFSET {$offset}
        ", [$search_terms, $search_terms])->fetchAll();

        // Get tags for each article (table not exists yet)
        foreach ($articles as &$article) {
            $article['tags'] = [];
        }

        // Get categories and other sidebar data (reuse from index method)
        $categories = $this->db->query("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM articles WHERE category_id = c.id AND status = 'published') as article_count
            FROM categories c
            ORDER BY c.name
        ")->fetchAll();

        $popular_tags = [];

        $recent_comments = $this->db->query("
            SELECT 
                c.*,
                u.username as author_name,
                a.title as article_title
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN articles a ON c.article_id = a.id
            WHERE c.status = 'approved'
            ORDER BY c.created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Set page title
        $page_title = "Search Results for \"" . htmlspecialchars($query) . "\"";

        // Include the view (reuse home/index.php)
        $content = 'views/home/index.php';
        require 'views/layouts/frontend.php';
    }
}