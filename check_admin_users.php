<?php
require_once 'config/database.php';

try {
    $db = new PDO('sqlite:blog.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking admin users in database...\n";
    
    $stmt = $db->query('SELECT * FROM users WHERE role = "admin"');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No admin users found!\n";
        
        // Check all users
        $stmt = $db->query('SELECT id, username, email, role FROM users');
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "All users in database:\n";
        print_r($allUsers);
    } else {
        echo "Admin users found:\n";
        print_r($users);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>