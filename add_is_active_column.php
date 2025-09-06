<?php
try {
    $db = new PDO('sqlite:blog.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding is_active column to users table...\n";
    
    // Check if column already exists
    $stmt = $db->query('PRAGMA table_info(users)');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasIsActive = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'is_active') {
            $hasIsActive = true;
            break;
        }
    }
    
    if (!$hasIsActive) {
        // Add is_active column with default value 1 (active)
        $db->exec('ALTER TABLE users ADD COLUMN is_active INTEGER DEFAULT 1');
        echo "Added is_active column successfully\n";
        
        // Update existing users to be active
        $db->exec('UPDATE users SET is_active = 1 WHERE is_active IS NULL');
        echo "Updated existing users to active status\n";
    } else {
        echo "is_active column already exists\n";
    }
    
    // Check admin user status
    $stmt = $db->query('SELECT id, username, role, is_active FROM users WHERE role = "admin"');
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Admin user status:\n";
        print_r($admin);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>