<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = new Database();
$conn = $db->connect();

// Check if admin user exists
$stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE role = 'admin'");
$stmt->execute();
$adminUsers = $stmt->fetchAll();

echo "Admin users in database:\n";
if (empty($adminUsers)) {
    echo "No admin users found!\n";
    
    // Check all users
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users");
    $stmt->execute();
    $allUsers = $stmt->fetchAll();
    
    echo "\nAll users in database:\n";
    foreach ($allUsers as $user) {
        echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
} else {
    foreach ($adminUsers as $user) {
        echo "ID: {$user['id']}, Username: {$user['username']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
}
?>