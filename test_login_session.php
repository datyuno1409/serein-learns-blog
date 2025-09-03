<?php
require_once 'config/config.php';
session_start();
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    // Find admin user
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        // Set session variables
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
        $_SESSION['email'] = $admin['email'];
        
        echo "<h2>Login Successful!</h2>";
        echo "User ID: " . $admin['id'] . "<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Role: " . $admin['role'] . "<br>";
        echo "Session ID: " . session_id() . "<br>";
        
        echo "<br><h3>Session Data:</h3>";
        print_r($_SESSION);
        
        // Test auth functions
        require_once 'helpers/auth_helper.php';
        echo "<br><h3>Auth Check:</h3>";
        echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "<br>";
        echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "<br>";
        
        echo "<br><br><a href='/admin/dashboard'>Go to Dashboard</a><br>";
        echo "<a href='/test_session.php'>Check Session Status</a><br>";
        
        // Set a cookie to help with session persistence
        setcookie('PHPSESSID', session_id(), time() + 3600, '/');
        
    } else {
        echo "No admin user found!";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>