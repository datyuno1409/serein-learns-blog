<?php
require_once 'config/config.php';
session_start();
require_once 'config/database.php';

// Set admin session
$db = new Database();
$conn = $db->connect();
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['username'] = $admin['username'];
    $_SESSION['user_role'] = $admin['role'];
    $_SESSION['email'] = $admin['email'];
}

// Now test dashboard
require_once 'controllers/AdminController.php';
$controller = new AdminController();
$controller->dashboard();
?>