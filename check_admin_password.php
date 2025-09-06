<?php
try {
    $db = new PDO('sqlite:blog.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking admin user password...\n";
    
    $stmt = $db->query('SELECT id, username, email, password, role FROM users WHERE role = "admin"');
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "Admin user found:\n";
        echo "ID: " . $admin['id'] . "\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Email: " . $admin['email'] . "\n";
        echo "Role: " . $admin['role'] . "\n";
        echo "Password hash: " . $admin['password'] . "\n";
        
        // Test common passwords
        $testPasswords = ['admin', 'admin123', 'password', '123456'];
        
        echo "\nTesting common passwords:\n";
        foreach ($testPasswords as $password) {
            if (password_verify($password, $admin['password'])) {
                echo "✓ Password '$password' is CORRECT!\n";
                break;
            } else {
                echo "✗ Password '$password' is incorrect\n";
            }
        }
    } else {
        echo "No admin user found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>