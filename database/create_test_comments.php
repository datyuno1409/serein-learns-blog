<?php
session_start();

// Set up database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=serein_blog;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

echo "<h2>Create Test Comments</h2>";

// Create some test comments
try {
    // Insert test comments
    $testComments = [
        [
            'author_name' => 'Test User 1',
            'author_email' => 'test1@example.com',
            'content' => 'This is a test comment number 1',
            'post_id' => 1,
            'status' => 'approved'
        ],
        [
            'author_name' => 'Test User 2', 
            'author_email' => 'test2@example.com',
            'content' => 'This is a test comment number 2',
            'post_id' => 1,
            'status' => 'pending'
        ],
        [
            'author_name' => 'Test User 3',
            'author_email' => 'test3@example.com', 
            'content' => 'This is a test comment number 3',
            'post_id' => 2,
            'status' => 'approved'
        ]
    ];
    
    foreach ($testComments as $comment) {
        $stmt = $pdo->prepare("
            INSERT INTO comments (author_name, author_email, content, post_id, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $result = $stmt->execute([
            $comment['author_name'],
            $comment['author_email'],
            $comment['content'],
            $comment['post_id'],
            $comment['status']
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>✓ Created comment by {$comment['author_name']}</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create comment by {$comment['author_name']}</p>";
        }
    }
    
    echo "<p><a href='test_delete_comment.php'>Go to Delete Test Page</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}
?>