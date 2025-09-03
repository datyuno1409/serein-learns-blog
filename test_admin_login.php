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
        
        echo "Admin login successful!<br>";
        echo "User ID: " . $admin['id'] . "<br>";
        echo "Username: " . $admin['username'] . "<br>";
        echo "Role: " . $admin['role'] . "<br>";
        echo "Session ID: " . session_id() . "<br>";
        echo "<a href='/admin/dashboard'>Go to Dashboard</a><br>";
        echo "<script>setTimeout(function(){ window.location.href = '/admin/dashboard'; }, 2000);</script>";
    } else {
        echo "No admin user found!";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>