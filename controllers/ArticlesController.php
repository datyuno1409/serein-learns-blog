<?php
require_once __DIR__ . '/../includes/Language.php';

class ArticlesController {
    private $db;
    private $articles_per_page = 12;

    public function __construct($db) {
        $this->db = $db;
    }
    
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện chức năng này';
            header('Location: /login');
            exit;
        }
    }
    
    private function createSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    private function handleImageUpload($file) {
        try {
            // Tạo thư mục uploads nếu chưa tồn tại
            $upload_dir = 'uploads/articles/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Kiểm tra loại file
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception('Chỉ chấp nhận file JPG, PNG hoặc WEBP');
            }
            
            // Kiểm tra kích thước file (tối đa 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('Kích thước file không được vượt quá 5MB');
            }
            
            // Tạo tên file mới để tránh trùng lặp
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;
            
            // Di chuyển file tải lên vào thư mục đích
            if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                throw new Exception('Không thể tải lên file');
            }
            
            return '/' . $target_path;
        } catch (Exception $e) {
            throw new Exception('Lỗi tải lên ảnh: ' . $e->getMessage());
        }
    }
    
    // Hiển thị danh sách bài viết của người dùng hiện tại
    public function myArticles() {
        $this->requireAuth();
        
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, $current_page);
        $offset = ($current_page - 1) * $this->articles_per_page;
        
        // Lấy danh sách bài viết của người dùng hiện tại
        $stmt = $this->db->prepare("
            SELECT a.*, c.name as category_name 
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$_SESSION['user_id'], $this->articles_per_page, $offset]);
        $articles = $stmt->fetchAll();
        
        // Đếm tổng số bài viết để phân trang
        $count_stmt = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE user_id = ?");
        $count_stmt->execute([$_SESSION['user_id']]);
        $total_articles = $count_stmt->fetchColumn();
        
        $total_pages = ceil($total_articles / $this->articles_per_page);
        
        $page_title = 'Bài viết của tôi';
        $content = 'views/articles/my_articles.php';
        require 'views/layouts/frontend.php';
    }

    public function create() {
        $this->requireAuth();
        
        // Get categories for dropdown
        $categories = $this->db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
        
        // Get tags for multiselect
        $tags = $this->db->query("SELECT id, name FROM tags ORDER BY name")->fetchAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = trim($_POST['title']);
                $content = $_POST['content'];
                $category_id = $_POST['category_id'];
                $slug = $this->createSlug($title);
                $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
                $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                // Validate required fields
                if (empty($title) || empty($content) || empty($category_id)) {
                    throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
                }
                
                // Handle image upload
                $image_path = null;
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $image_path = $this->handleImageUpload($_FILES['featured_image']);
                }
                
                // Begin transaction
                $this->db->beginTransaction();
                
                // Insert article
                $stmt = $this->db->prepare("
                    INSERT INTO articles (
                        title, slug, content, excerpt, category_id, 
                        featured_image, status, is_featured, 
                        user_id, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $title, $slug, $content, $excerpt, $category_id,
                    $image_path, $status, $is_featured,
                    $_SESSION['user_id']
                ]);
                
                $article_id = $this->db->lastInsertId();
                
                // Handle tags if selected
                if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                    $tag_stmt = $this->db->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
                    foreach ($_POST['tags'] as $tag_id) {
                        $tag_stmt->execute([$article_id, $tag_id]);
                    }
                }
                
                $this->db->commit();
                
                $_SESSION['success'] = 'Bài viết đã được tạo thành công';
                header('Location: /articles');
                exit;
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $page_title = 'Tạo bài viết mới';
        $content = 'views/articles/create.php';
        require 'views/layouts/frontend.php';
    }
    
    public function edit() {
        $this->requireAuth();
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = 'ID bài viết không hợp lệ';
            header('Location: /articles');
            exit;
        }
        
        $article_id = (int)$_GET['id'];
        
        // Check if article exists and belongs to current user
        $stmt = $this->db->prepare("
            SELECT * FROM articles 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$article_id, $_SESSION['user_id']]);
        $article = $stmt->fetch();
        
        if (!$article) {
            $_SESSION['error'] = 'Bài viết không tồn tại hoặc bạn không có quyền chỉnh sửa';
            header('Location: /articles');
            exit;
        }
        
        // Get categories for dropdown
        $categories = $this->db->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
        
        // Get tags for multiselect
        $tags = $this->db->query("SELECT id, name FROM tags ORDER BY name")->fetchAll();
        
        // Get selected tags for this article
        $selected_tags = $this->db->prepare("
            SELECT tag_id FROM article_tags WHERE article_id = ?
        ");
        $selected_tags->execute([$article_id]);
        $selected_tag_ids = array_column($selected_tags->fetchAll(), 'tag_id');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = trim($_POST['title']);
                $content = $_POST['content'];
                $category_id = $_POST['category_id'];
                $slug = $this->createSlug($title);
                $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
                $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                // Validate required fields
                if (empty($title) || empty($content) || empty($category_id)) {
                    throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
                }
                
                // Begin transaction
                $this->db->beginTransaction();
                
                // Handle image upload if new image is provided
                if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                    $image_path = $this->handleImageUpload($_FILES['featured_image']);
                    
                    // Update article with new image
                    $stmt = $this->db->prepare("
                        UPDATE articles SET 
                            title = ?, slug = ?, content = ?, excerpt = ?, 
                            category_id = ?, featured_image = ?, status = ?, 
                            is_featured = ?, updated_at = NOW()
                        WHERE id = ? AND user_id = ?
                    ");
                    
                    $stmt->execute([
                        $title, $slug, $content, $excerpt,
                        $category_id, $image_path, $status,
                        $is_featured, $article_id, $_SESSION['user_id']
                    ]);
                } else {
                    // Update article without changing image
                    $stmt = $this->db->prepare("
                        UPDATE articles SET 
                            title = ?, slug = ?, content = ?, excerpt = ?, 
                            category_id = ?, status = ?, is_featured = ?, 
                            updated_at = NOW()
                        WHERE id = ? AND user_id = ?
                    ");
                    
                    $stmt->execute([
                        $title, $slug, $content, $excerpt,
                        $category_id, $status, $is_featured,
                        $article_id, $_SESSION['user_id']
                    ]);
                }
                
                // Delete existing tag associations
                $delete_tags = $this->db->prepare("DELETE FROM article_tags WHERE article_id = ?");
                $delete_tags->execute([$article_id]);
                
                // Add new tag associations
                if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                    $tag_stmt = $this->db->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
                    foreach ($_POST['tags'] as $tag_id) {
                        $tag_stmt->execute([$article_id, $tag_id]);
                    }
                }
                
                $this->db->commit();
                
                $_SESSION['success'] = 'Bài viết đã được cập nhật thành công';
                header('Location: /articles');
                exit;
                
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        $page_title = 'Chỉnh sửa bài viết';
        $content = 'views/articles/edit.php';
        require 'views/layouts/frontend.php';
    }
    
    public function delete() {
        $this->requireAuth();
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $_SESSION['error'] = 'ID bài viết không hợp lệ';
            header('Location: /articles');
            exit;
        }
        
        $article_id = (int)$_GET['id'];
        
        try {
            // Check if article exists and belongs to current user
            $stmt = $this->db->prepare("
                SELECT id, featured_image FROM articles 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$article_id, $_SESSION['user_id']]);
            $article = $stmt->fetch();
            
            if (!$article) {
                $_SESSION['error'] = 'Bài viết không tồn tại hoặc bạn không có quyền xóa';
                header('Location: /articles');
                exit;
            }
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // Delete tag associations
            $delete_tags = $this->db->prepare("DELETE FROM article_tags WHERE article_id = ?");
            $delete_tags->execute([$article_id]);
            
            // Delete article
            $delete_article = $this->db->prepare("DELETE FROM articles WHERE id = ?");
            $delete_article->execute([$article_id]);
            
            // Delete image file if exists
            if ($article['featured_image'] && file_exists(ltrim($article['featured_image'], '/'))) {
                unlink(ltrim($article['featured_image'], '/'));
            }
            
            $this->db->commit();
            
            $_SESSION['success'] = 'Bài viết đã được xóa thành công';
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: /articles');
        exit;
    }
    
    public function index() {
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, $current_page);
        $offset = ($current_page - 1) * $this->articles_per_page;

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $category = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $tag = isset($_GET['tag']) ? (int)$_GET['tag'] : null;

        $where_conditions = ["a.status = 'published'"];
        $params = [];

        if (!empty($search)) {
            $where_conditions[] = "(a.title LIKE ? OR a.content LIKE ?)";
            $search_term = "%{$search}%";
            $params[] = $search_term;
            $params[] = $search_term;
        }

        if ($category) {
            $where_conditions[] = "a.category_id = ?";
            $params[] = $category;
        }

        if ($tag) {
            $where_conditions[] = "EXISTS (SELECT 1 FROM article_tags at WHERE at.article_id = a.id AND at.tag_id = ?)";
            $params[] = $tag;
        }

        $where_clause = implode(' AND ', $where_conditions);

        $total_count = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM articles a
            WHERE {$where_clause}
        ");
        $total_count->execute($params);
        $total_count = $total_count->fetch()['count'];

        $total_pages = ceil($total_count / $this->articles_per_page);

        $articles_query = $this->db->prepare("
            SELECT 
                a.*, 
                u.username as author_name,
                c.name as category_name,
                (SELECT COUNT(*) FROM comments WHERE article_id = a.id AND status = 'approved') as comment_count
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE {$where_clause}
            ORDER BY a.created_at DESC
            LIMIT {$this->articles_per_page} OFFSET {$offset}
        ");
        $articles_query->execute($params);
        $articles = $articles_query->fetchAll();

        foreach ($articles as &$article) {
            $tags_query = $this->db->prepare("
                SELECT t.* 
                FROM tags t
                JOIN article_tags at ON t.id = at.tag_id
                WHERE at.article_id = ?
            ");
            $tags_query->execute([$article['id']]);
            $article['tags'] = $tags_query->fetchAll();
        }

        $categories = $this->db->query("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM articles WHERE category_id = c.id AND status = 'published') as article_count
            FROM categories c
            ORDER BY c.name
        ")->fetchAll();

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

        $featured_articles = $this->db->query("
            SELECT 
                a.*, 
                u.username as author_name,
                c.name as category_name
            FROM articles a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.status = 'published'
            ORDER BY a.views DESC, a.created_at DESC
            LIMIT 3
        ")->fetchAll();

        $page_title = 'Articles';
        if (!empty($search)) {
            $page_title = "Search Results for \"" . htmlspecialchars($search) . "\"";
        } elseif ($category) {
            $cat_info = $this->db->prepare("SELECT name FROM categories WHERE id = ?");
            $cat_info->execute([$category]);
            $cat_name = $cat_info->fetch()['name'] ?? 'Unknown';
            $page_title = "Articles in " . htmlspecialchars($cat_name);
        } elseif ($tag) {
            $tag_info = $this->db->prepare("SELECT name FROM tags WHERE id = ?");
            $tag_info->execute([$tag]);
            $tag_name = $tag_info->fetch()['name'] ?? 'Unknown';
            $page_title = "Articles tagged with \"" . htmlspecialchars($tag_name) . "\"";
        }

        $content = 'views/articles/index.php';
        require 'views/layouts/frontend.php';
    }
}