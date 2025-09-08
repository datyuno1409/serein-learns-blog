<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Admin user found:\n";
        echo "ID: {$admin['id']}\n";
        echo "Username: {$admin['username']}\n";
        echo "Password hash: {$admin['password']}\n";
        
        // Test common passwords
        $testPasswords = ['admin', 'password', '123456', 'admin123'];
        
        foreach ($testPasswords as $testPassword) {
            if (password_verify($testPassword, $admin['password'])) {
                echo "\n*** PASSWORD FOUND: '{$testPassword}' ***\n";
                break;
            }
        }
        
        echo "\nTo test login, try these credentials:\n";
        echo "Username: admin\n";
        echo "Password: (try common passwords like 'admin', 'password', '123456')\n";
        
    } else {
        echo "Admin user not found.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>