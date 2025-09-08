<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Check if tables exist
    $tables = ['users', 'categories', 'articles', 'tags', 'article_tags', 'comments', 'projects'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "Table '$table' exists\n";
            
            // Count records
            $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM $table");
            $countStmt->execute();
            $count = $countStmt->fetch()['count'];
            echo "  - Records: $count\n";
        } else {
            echo "Table '$table' does NOT exist\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>