<?php
require_once __DIR__ . '/../includes/Language.php';

class ProfileController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Get user details
        $user = $this->db->query("
            SELECT 
                u.*,
                (SELECT COUNT(*) FROM articles WHERE user_id = u.id AND status = 'published') as article_count,
                (SELECT COUNT(*) FROM comments WHERE user_id = u.id) as comment_count
            FROM users u
            WHERE u.id = ?
        ", [$_SESSION['user_id']])->fetch();

        // Get user's articles
        $articles = $this->db->query("
            SELECT 
                a.*,
                (SELECT COUNT(*) FROM comments WHERE article_id = a.id AND status = 'approved') as comment_count
            FROM articles a
            WHERE a.user_id = ? AND a.status = 'published'
            ORDER BY a.created_at DESC
        ", [$_SESSION['user_id']])->fetchAll();

        // Get user's comments
        $comments = $this->db->query("
            SELECT 
                c.*,
                a.title as article_title
            FROM comments c
            JOIN articles a ON c.article_id = a.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ", [$_SESSION['user_id']])->fetchAll();

        // Set page title
        $page_title = "My Profile";

        // Include the view
        $content = 'views/profile/index.php';
        require 'views/layouts/frontend.php';
    }

    public function update() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Get current user data
        $user = $this->db->query("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']])->fetch();

        // Verify current password
        if (!password_verify($_POST['current_password'], $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect.";
            header('Location: /profile');
            exit;
        }

        // Check if username is taken (by another user)
        if ($_POST['username'] !== $user['username']) {
            $existing = $this->db->query("SELECT id FROM users WHERE username = ? AND id != ?", 
                [$_POST['username'], $_SESSION['user_id']])->fetch();
            if ($existing) {
                $_SESSION['error'] = "Username is already taken.";
                header('Location: /profile');
                exit;
            }
        }

        // Check if email is taken (by another user)
        if ($_POST['email'] !== $user['email']) {
            $existing = $this->db->query("SELECT id FROM users WHERE email = ? AND id != ?", 
                [$_POST['email'], $_SESSION['user_id']])->fetch();
            if ($existing) {
                $_SESSION['error'] = "Email is already taken.";
                header('Location: /profile');
                exit;
            }
        }

        // Handle avatar upload
        $avatar = $user['avatar'];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['avatar']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $_SESSION['error'] = "Invalid file type. Allowed types: " . implode(', ', $allowed);
                header('Location: /profile');
                exit;
            }

            // Generate unique filename
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = 'uploads/avatars/' . $new_filename;

            // Create directory if it doesn't exist
            if (!file_exists('uploads/avatars')) {
                mkdir('uploads/avatars', 0777, true);
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                // Delete old avatar if exists
                if ($avatar && file_exists($avatar)) {
                    unlink($avatar);
                }
                $avatar = '/' . $upload_path;
            } else {
                $_SESSION['error'] = "Failed to upload avatar.";
                header('Location: /profile');
                exit;
            }
        }

        // Update user data
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'avatar' => $avatar,
            'id' => $_SESSION['user_id']
        ];

        $sql = "UPDATE users SET username = ?, email = ?, avatar = ?";
        $params = [$data['username'], $data['email'], $data['avatar']];

        // Update password if provided
        if (!empty($_POST['new_password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $data['id'];

        $this->db->query($sql, $params);

        $_SESSION['success'] = "Profile updated successfully.";
        header('Location: /profile');
        exit;
    }
}