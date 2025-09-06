<?php
session_start();

// Set up database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=serein_blog;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>Database connected successfully</p>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'comments'");
    $tableExists = $stmt->rowCount() > 0;
    echo "<p>Comments table exists: " . ($tableExists ? 'Yes' : 'No') . "</p>";
    
    // Show table structure
    if ($tableExists) {
        echo "<h4>Table Structure:</h4>";
        $descStmt = $pdo->query("DESCRIBE comments");
        $columns = $descStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "<p>Column: " . $col['Field'] . " (" . $col['Type'] . ")</p>";
        }
    }
    
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Set admin session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';
$_SESSION['is_logged_in'] = true;

echo "<h2>Test Delete Comment Functionality</h2>";

// Get all comments first
try {
    // First check total count
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM comments");
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total comments in database: " . $totalCount . "</p>";
    
    // If no comments exist, create some test data
    if ($totalCount == 0) {
        echo "<p style='color: orange;'>No comments found. Creating test data...</p>";
        
        // Check if articles exist first
        $articleCheck = $pdo->query("SELECT id FROM articles LIMIT 1");
        $firstArticle = $articleCheck->fetch(PDO::FETCH_ASSOC);
        
        if (!$firstArticle) {
             // Create a test article first
             $articleStmt = $pdo->prepare("INSERT INTO articles (title, content, user_id, status, created_at) VALUES (?, ?, ?, 'published', NOW())");
             $articleStmt->execute(['Test Article for Comments', 'This is a test article content', 1]);
             $articleId = $pdo->lastInsertId();
             echo "<p style='color: blue;'>Created test article with ID: $articleId</p>";
         } else {
            $articleId = $firstArticle['id'];
            echo "<p style='color: blue;'>Using existing article ID: $articleId</p>";
        }
        
        // Create test comments
        $insertStmt = $pdo->prepare("INSERT INTO comments (content, user_id, article_id, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        
        $testComments = [
             ['Đây là bình luận test đầu tiên', 1, $articleId, 'approved'],
             ['Bình luận thứ hai với ký tự đặc biệt: àáảãạ', 1, $articleId, 'approved'],
             ['Comment số ba để test xóa', 1, $articleId, 'pending']
         ];
         
         foreach ($testComments as $comment) {
             $insertStmt->execute($comment);
         }
        
        echo "<p style='color: green;'>Test comments created successfully!</p>";
        
        // Refresh count
        $countStmt = $pdo->query("SELECT COUNT(*) as total FROM comments");
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<p>New total comments: " . $totalCount . "</p>";
    }
    
    $stmt = $pdo->query("SELECT id, user_id, content, article_id, status, created_at FROM comments ORDER BY created_at DESC");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p>Found " . count($comments) . " comments in query result</p>";
    
    // Debug: show raw query result
    if (!empty($comments)) {
        echo "<p>First comment data: " . print_r($comments[0], true) . "</p>";
    }
    
    echo "<h3>Current Comments:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Author</th><th>Content</th><th>Post</th><th>Status</th><th>Created</th><th>Action</th></tr>";
    
    foreach ($comments as $comment) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($comment['id']) . "</td>";
        echo "<td>" . htmlspecialchars($comment['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($comment['content'], 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($comment['article_id'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($comment['status']) . "</td>";
        echo "<td>" . htmlspecialchars($comment['created_at']) . "</td>";
        echo "<td><form method='post' style='display:inline;'><input type='hidden' name='delete_id' value='" . $comment['id'] . "'><button type='submit' onclick='return confirm(\"Delete comment " . $comment['id'] . "?\")'>Delete</button></form></td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "Error fetching comments: " . $e->getMessage();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $comment_id = intval($_POST['delete_id']);
    
    echo "<h3>Attempting to delete comment ID: $comment_id</h3>";
    
    try {
        // Check if comment exists
        $stmt = $pdo->prepare('SELECT id, content FROM comments WHERE id = ?');
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();
        
        if (!$comment) {
            echo "<p style='color: red;'>Comment not found!</p>";
        } else {
            echo "<p>Found comment: " . htmlspecialchars(substr($comment['content'], 0, 100)) . "...</p>";
            
            // Delete the comment
            $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
            $result = $stmt->execute([$comment_id]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Comment deleted successfully!</p>";
                echo "<p><a href='?'>Refresh page</a></p>";
            } else {
                echo "<p style='color: red;'>Failed to delete comment (no rows affected)</p>";
            }
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
}
?>