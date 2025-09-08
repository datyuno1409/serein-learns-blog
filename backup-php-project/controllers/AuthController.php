<?php
require_once __DIR__ . '/../includes/Language.php';

class AuthController {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once 'config/database.php';
            $database = new Database();
            $this->db = $database->connect();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $remember = isset($_POST['remember']) ? true : false;

            try {
                $conn = $this->db;
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password']) && $user['is_active']) {
                    // Update last login
                    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_admin'] = ($user['role'] === 'admin');

                    // Set remember me cookie if checked
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days

                        // Store token in database
                        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $stmt->execute([$token, $user['id']]);
                    }

                    // Redirect to admin dashboard
                    header('Location: /admin/dashboard');
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid username or password';
                    header('Location: /login');
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Database error: ' . $e->getMessage();
                header('Location: /login');
                exit;
            }
        }

        // Show login form
        require_once 'views/auth/login.php';
    }

    public function adminLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $remember = isset($_POST['remember']) ? true : false;

            try {
                $conn = $this->db;
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password']) && $user['is_active']) {
                    // Update last login
                    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_admin'] = true;

                    // Set remember me cookie if checked
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days

                        // Store token in database
                        $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                        $stmt->execute([$token, $user['id']]);
                    }

                    // Redirect to admin dashboard
                    header('Location: /admin/dashboard');
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid admin credentials';
                    header('Location: /admin/login');
                    exit;
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Database error: ' . $e->getMessage();
                header('Location: /admin/login');
                exit;
            }
        }

        // Show admin login form
        require_once 'views/auth/admin_login.php';
    }

    public function logout() {
        // Clear session
        session_destroy();
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Redirect to login page
        header('Location: /login');
        exit;
    }

    public function checkRememberMe() {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            
            try {
                $conn = $this->db->connect();
                $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
                $stmt->execute([$token]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $user['is_active']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['is_admin'] = ($user['role'] === 'admin');
                    return true;
                }
            } catch (PDOException $e) {
                error_log('Remember me check failed: ' . $e->getMessage());
            }
        }
        return false;
    }
}