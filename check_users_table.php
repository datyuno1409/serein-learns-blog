<?php
try {
    $db = new PDO('sqlite:blog.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking users table structure...\n";
    
    $stmt = $db->query('PRAGMA table_info(users)');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table structure:\n";
    print_r($columns);
    
    echo "\nChecking admin user data:\n";
    $stmt = $db->query('SELECT id, username, email, role, is_active FROM users WHERE role = "admin"');
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        print_r($admin);
    } else {
        echo "No admin user found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>