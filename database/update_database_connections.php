<?php
/**
 * Script to update all admin files to use Database class instead of direct PDO connections
 */

$adminFiles = [
    'admin/users_list.php',
    'admin/search.php', 
    'admin/posts_edit.php',
    'admin/posts_list.php',
    'admin/posts_add.php',
    'admin/categories_add.php',
    'admin/comments_list.php'
];

$rootDir = dirname(__DIR__);

foreach ($adminFiles as $file) {
    $filePath = $rootDir . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Replace MySQL PDO connection with Database class
    $oldPattern = '/\$pdo = new PDO\("mysql:host=" \. DB_HOST \. ";dbname=" \. DB_NAME \. ";charset=utf8mb4", DB_USER, DB_PASS\);/';
    $newReplacement = '$database = new Database(); $pdo = $database->connect();';
    
    // Also fix any existing getConnection() calls
    $content = str_replace('$pdo = $database->getConnection();', '$pdo = $database->connect();', $content);
    
    $updatedContent = preg_replace($oldPattern, $newReplacement, $content);
    
    if ($updatedContent !== $content) {
        file_put_contents($filePath, $updatedContent);
        echo "Updated: $file\n";
    } else {
        echo "No changes needed: $file\n";
    }
}

echo "Database connection update completed!\n";