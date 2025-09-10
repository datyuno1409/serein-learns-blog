<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    // Create tags table
    $createTagsSQL = "
        CREATE TABLE IF NOT EXISTS tags (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(50) NOT NULL,
            slug VARCHAR(50) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    // Create article_tags table
    $createArticleTagsSQL = "
        CREATE TABLE IF NOT EXISTS article_tags (
            article_id INTEGER NOT NULL,
            tag_id INTEGER NOT NULL,
            PRIMARY KEY (article_id, tag_id),
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
        )
    ";
    
    $pdo->exec($createTagsSQL);
    echo "Created tags table\n";
    
    $pdo->exec($createArticleTagsSQL);
    echo "Created article_tags table\n";
    
    // Insert some sample tags
    $insertTagsSQL = "
        INSERT OR IGNORE INTO tags (name, slug) VALUES 
        ('PHP', 'php'),
        ('JavaScript', 'javascript'),
        ('Web Development', 'web-development'),
        ('Tutorial', 'tutorial'),
        ('Programming', 'programming')
    ";
    
    $pdo->exec($insertTagsSQL);
    echo "Inserted sample tags\n";
    
    echo "Missing tables created successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>