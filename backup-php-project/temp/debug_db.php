<?php
$pdo = new PDO('sqlite:blog.sqlite');

// Check users table
echo "=== Users Table ===\n";
$stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
$userCount = $stmt->fetch();
echo "Users count: " . $userCount['count'] . "\n";

// Check articles table
echo "\n=== Articles Table ===\n";
$stmt = $pdo->query('SELECT COUNT(*) as count FROM articles');
$articleCount = $stmt->fetch();
echo "Articles count: " . $articleCount['count'] . "\n";

// Check comments count
echo "\n=== Comments Table ===\n";
$stmt = $pdo->query('SELECT COUNT(*) as count FROM comments');
$commentCount = $stmt->fetch();
echo "Comments count: " . $commentCount['count'] . "\n";

// Insert test comments if none exist
if ($commentCount['count'] == 0) {
    echo "\nInserting test comments...\n";
    
    // Get first article ID
    $stmt = $pdo->query('SELECT id FROM articles LIMIT 1');
    $article = $stmt->fetch();
    
    if ($article) {
        $articleId = $article['id'];
        
        // Insert test comments with Vietnamese characters
        $testComments = [
            [
                'article_id' => $articleId,
                'author_name' => 'Nguyễn Văn A',
                'author_email' => 'nguyenvana@example.com',
                'content' => 'Bài viết rất hay và bổ ích! Cảm ơn tác giả đã chia sẻ.'
            ],
            [
                'article_id' => $articleId,
                'author_name' => 'Trần Thị B',
                'author_email' => 'tranthib@example.com', 
                'content' => 'Mình có thắc mắc về phần này. Có thể giải thích rõ hơn được không?'
            ],
            [
                'article_id' => $articleId,
                'author_name' => 'Lê Văn C',
                'author_email' => 'levanc@example.com',
                'content' => 'Thông tin rất hữu ích. Đã bookmark để đọc lại sau này.'
            ]
        ];
        
        $stmt = $pdo->prepare('INSERT INTO comments (article_id, author_name, author_email, content, status) VALUES (?, ?, ?, ?, ?)');
        
        foreach ($testComments as $comment) {
            $stmt->execute([
                $comment['article_id'],
                $comment['author_name'],
                $comment['author_email'],
                $comment['content'],
                'approved'
            ]);
        }
        
        echo "Added 3 test comments with Vietnamese characters.\n";
    } else {
        echo "No articles found to attach comments to.\n";
    }
}

// Show current comments with the same query as AdminController
echo "\n=== Comments with JOIN (AdminController query) ===\n";
$stmt = $pdo->query("
    SELECT c.*, 
           u.username as user_name,
           a.title as article_title
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN articles a ON c.article_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 3
");
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($comments);
?>