<?php
session_start();

// Setup admin session for testing
require_once 'config/config.php';
require_once 'config/database.php';

$database = new Database();
$pdo = $database->connect();

// Find admin user and set session
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['username'] = $admin['username'];
    $_SESSION['role'] = $admin['role'];
    $_SESSION['is_active'] = $admin['is_active'];
}

// Test comments query
$stmt = $pdo->query("
    SELECT c.*, 
           COALESCE(u.username, c.author_name) as user_name,
           a.title as article_title
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN articles a ON c.article_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 5
");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Comments Test Results:</h2>";
echo "<pre>";
foreach ($comments as $comment) {
    echo "ID: " . $comment['id'] . "\n";
    echo "Author: " . ($comment['user_name'] ?? 'N/A') . "\n";
    echo "Content: " . htmlspecialchars($comment['content']) . "\n";
    echo "Article: " . ($comment['article_title'] ?? 'N/A') . "\n";
    echo "Status: " . $comment['status'] . "\n";
    echo "Created: " . $comment['created_at'] . "\n";
    echo "---\n";
}
echo "</pre>";

echo "<h3>Test Delete Comment:</h3>";
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $result = $stmt->execute([$delete_id]);
    
    if ($result) {
        echo "<p style='color: green;'>Comment ID $delete_id deleted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Failed to delete comment ID $delete_id</p>";
    }
    
    echo "<a href='test_comments.php'>Back to comments list</a>";
} else {
    echo "<p>To test delete, add ?delete_id=X to URL (where X is comment ID)</p>";
}
?>