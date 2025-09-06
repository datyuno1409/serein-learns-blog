<?php
session_start();

echo "Current session data:\n";
if (empty($_SESSION)) {
    echo "No session data found.\n";
} else {
    print_r($_SESSION);
}

// Check if admin is logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    echo "\nAdmin is logged in. User ID: " . $_SESSION['user_id'] . "\n";
} else {
    echo "\nAdmin is NOT logged in. Need to login first.\n";
    
    // Try to login as admin
    require_once 'config/config.php';
    require_once 'config/database.php';
    $database = new Database();
    $pdo = $database->connect();
    
    // Find admin user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Found admin user: " . $admin['username'] . "\n";
        echo "Setting up admin session...\n";
        
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['role'] = $admin['role'];
        $_SESSION['is_active'] = $admin['is_active'];
        
        echo "Admin session created successfully!\n";
    } else {
        echo "No admin user found in database.\n";
    }
}
?>