<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

function requireAdmin() {
    // Check if user is logged in
    requireLogin();
    
    // Check if user has admin role
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: /login');
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = new Database();
    $conn = $db->connect();
    
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch();
}

function logout() {
    session_destroy();
    header('Location: /login');
    exit();
}
?>