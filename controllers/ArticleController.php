<?php
require_once __DIR__ . '/../includes/Language.php';
require_once __DIR__ . '/../config/config.php';

class ArticleController {
    private $db;
    
    public function __construct() {
        requireAdmin();
        $this->db = new Database();
    }
    
    public function index() {
        require 'admin/articles_list.php';
    }
    
    public function create() {
        $conn = $this->db->connect();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = $_POST['title'];
                $content = $_POST['content'];
                $category_id = $_POST['category_id'];
                $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
                $user_id = $_SESSION['user_id'];
                $slug = $this->createSlug($title);
                
                // Handle image upload
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_path = $this->handleImageUpload($_FILES['image']);
                }
                
                $stmt = $conn->prepare("INSERT INTO articles (title, slug, content, category_id, user_id, status, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $slug, $content, $category_id, $user_id, $status, $image_path]);
                
                $_SESSION['flash_message'] = 'Article created successfully';
                $_SESSION['flash_type'] = 'success';
                header('Location: /admin/articles');
                exit();
            } catch (Exception $e) {
                $_SESSION['flash_message'] = 'Error creating article: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'danger';
            }
        }
        
        // Get categories for dropdown
        $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        $content = 'views/admin/articles/create.php';
        include 'views/layouts/admin.php';
    }
    
    public function edit() {
        $conn = $this->db->connect();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: /admin/articles');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $title = $_POST['title'];
                $content = $_POST['content'];
                $category_id = $_POST['category_id'];
                $status = isset($_POST['status']) ? $_POST['status'] : 'draft';
                $slug = $this->createSlug($title);
                
                // Handle image upload
                $image_path = $_POST['current_image'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_path = $this->handleImageUpload($_FILES['image']);
                    // Delete old image if exists
                    if ($_POST['current_image']) {
                        @unlink(UPLOAD_PATH . '/' . $_POST['current_image']);
                    }
                }
                
                $stmt = $conn->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, category_id = ?, status = ?, image = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$title, $slug, $content, $category_id, $status, $image_path, $id]);
                
                $_SESSION['flash_message'] = 'Article updated successfully';
                $_SESSION['flash_type'] = 'success';
                header('Location: /admin/articles');
                exit();
            } catch (Exception $e) {
                $_SESSION['flash_message'] = 'Error updating article: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'danger';
            }
        }
        
        // Get article data
        $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            header('Location: /admin/articles');
            exit();
        }
        
        // Get categories for dropdown
        $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll();
        
        $content = 'views/admin/articles/edit.php';
        include 'views/layouts/admin.php';
    }
    
    public function delete() {
        $conn = $this->db->connect();
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /admin/articles');
            exit();
        }
        
        try {
            // Get article image before delete
            $stmt = $conn->prepare("SELECT image FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            $article = $stmt->fetch();
            
            // Delete article
            $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete image file if exists
            if ($article && $article['image']) {
                @unlink(UPLOAD_PATH . '/' . $article['image']);
            }
            
            $_SESSION['flash_message'] = 'Article deleted successfully';
            $_SESSION['flash_type'] = 'success';
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Error deleting article: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: /admin/articles');
        exit();
    }
    
    private function createSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    private function handleImageUpload($file) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file type
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', ALLOWED_EXTENSIONS));
        }
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size too large. Maximum size: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB');
        }
        
        // Create upload directory if not exists
        if (!file_exists(UPLOAD_PATH)) {
            mkdir(UPLOAD_PATH, 0777, true);
        }
        
        // Generate unique filename
        $filename = uniqid() . '.' . $extension;
        $filepath = UPLOAD_PATH . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Error uploading file');
        }
        
        return $filename;
    }
}