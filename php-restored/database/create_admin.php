<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->connect();
    
    // Check if admin user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = 'admin' AND role = 'admin'");
    $stmt->execute();
    $existingAdmin = $stmt->fetch();
    
    if ($existingAdmin) {
        echo "Admin user already exists!\n";
        exit;
    }
    
    // Create admin user
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $email = 'admin@sereinblog.com';
    $role = 'admin';
    
    $stmt = $conn->prepare("
        INSERT INTO users (username, password, email, role, is_active, created_at) 
        VALUES (?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([$username, $password, $email, $role]);
    
    echo "Admin user created successfully!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>