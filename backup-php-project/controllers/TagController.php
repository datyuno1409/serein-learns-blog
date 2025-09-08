<?php
require_once __DIR__ . '/../includes/Language.php';

class TagController {
    private $db;
    private $articles_per_page = 10;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // Get all tags with article count
        $tags = $this->db->query("
            SELECT 
                t.*,
                COUNT(DISTINCT at.article_id) as article_count
            FROM tags t
            LEFT JOIN article_tags at ON t.id = at.tag_id
            LEFT JOIN articles a ON at.article_id = a.id AND a.status = 'published'
            GROUP BY t.id
            ORDER BY t.name
        ")->fetchAll();

        // Set page title
        $page_title = __('tags.title');

        // Include the view
        $content = 'views/tags/index.php';
        require 'views/layouts/frontend.php';
    }

    public function view($id) {
        // Get tag details
        $tag = $this->db->query("SELECT * FROM tags WHERE id = ?", [$id])->fetch();

        if (!$tag) {
            header('HTTP/1.0 404 Not Found');
            echo "Tag not found";
            exit;
        }

        // Get current page
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, $current_page);
        $offset = ($current_page - 1) * $this->articles_per_page;

        // Get total articles count
        $total_count = $this->db->query("
            SELECT COUNT(DISTINCT a.id) as count 
            FROM articles a
            JOIN article_tags at ON a.id = at.article_id
            WHERE at.tag_id = ? AND a.status = 'published'
        ", [$id])->fetch()['count'];

        $total_pages = ceil($total_count / $this->articles_per_page);

        // Get articles for current page
        $articles = $this->db->query("
            SELECT DISTINCT
                a.*, 
                u.username as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM comments WHERE article_id = a.id AND status = 'approved') as comment_count
            FROM articles a
            JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE at.tag_id = ? AND a.status = 'published'
            ORDER BY a.created_at DESC
            LIMIT {$this->articles_per_page} OFFSET {$offset}
        ", [$id])->fetchAll();

        // Get tags for each article
        foreach ($articles as &$article) {
            $article['tags'] = $this->db->query("
                SELECT t.* 
                FROM tags t
                JOIN article_tags at ON t.id = at.tag_id
                WHERE at.article_id = ?
            ", [$article['id']])->fetchAll();
        }

        // Get categories with article count
        $categories = $this->db->query("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM articles WHERE category_id = c.id AND status = 'published') as article_count
            FROM categories c
            ORDER BY c.name
        ")->fetchAll();

        // Get popular tags
        $popular_tags = $this->db->query("
            SELECT 
                t.*,
                COUNT(at.article_id) as article_count
            FROM tags t
            JOIN article_tags at ON t.id = at.tag_id
            JOIN articles a ON at.article_id = a.id
            WHERE a.status = 'published'
            GROUP BY t.id
            ORDER BY article_count DESC
            LIMIT 20
        ")->fetchAll();

        // Get related articles (articles that share other tags with the current articles)
        $related_articles = $this->db->query("
            SELECT DISTINCT a.*
            FROM articles a
            JOIN article_tags at1 ON a.id = at1.article_id
            JOIN article_tags at2 ON at2.tag_id = at1.tag_id
            WHERE a.status = 'published'
            AND at2.article_id IN (
                SELECT article_id 
                FROM article_tags 
                WHERE tag_id = ?
            )
            AND a.id NOT IN (
                SELECT article_id 
                FROM article_tags 
                WHERE tag_id = ?
            )
            ORDER BY a.created_at DESC
            LIMIT 5
        ", [$id, $id])->fetchAll();

        // Set page title
        $page_title = "Tag: " . $tag['name'];

        // Include the view
        $content = 'views/tags/view.php';
        require 'views/layouts/frontend.php';
    }
}