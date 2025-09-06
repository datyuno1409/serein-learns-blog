<?php
$pdo = new PDO('mysql:host=localhost;dbname=serein_blog;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('DESCRIBE users');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Columns in users table:\n";
print_r($columns);

echo "\n\nLatest user:\n";
$stmt = $pdo->query('SELECT * FROM users ORDER BY id DESC LIMIT 1');
$user = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($user);
?>