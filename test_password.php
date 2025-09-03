<?php
// Test password hash
$password = 'admin123';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "Verify result: " . (password_verify($password, $hash) ? 'TRUE' : 'FALSE') . "\n";

// Generate new hash
$newHash = password_hash($password, PASSWORD_DEFAULT);
echo "New hash: " . $newHash . "\n";
echo "New verify: " . (password_verify($password, $newHash) ? 'TRUE' : 'FALSE') . "\n";
?>