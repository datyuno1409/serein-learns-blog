<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->connect();
    
    $stmt = $pdo->query('SELECT id, username, email, is_admin FROM users ORDER BY id');
    
    echo "Users in database:\n";
    echo "ID\tUsername\tEmail\t\tAdmin\n";
    echo "--\t--------\t-----\t\t-----\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['id'] . "\t" . $row['username'] . "\t" . $row['email'] . "\t" . ($row['is_admin'] ? 'Yes' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>