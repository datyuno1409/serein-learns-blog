<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->connect();
    
    $sql = "
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            description TEXT,
            content LONGTEXT,
            image_url VARCHAR(500),
            github_url VARCHAR(500),
            live_url VARCHAR(500),
            technologies JSON,
            status ENUM('active', 'inactive', 'development') DEFAULT 'development',
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($sql);
    echo "Table 'projects' created successfully!\n";
    
    // Insert some sample data
    $sampleData = [
        [
            'title' => 'UniSAST Platform',
            'slug' => 'unisast-platform',
            'description' => 'A web-based platform integrating open-source SAST tools for automated code security analysis and DevSecOps support in SMEs.',
            'content' => 'Built with React, Node.js, Docker, Security, DevSecOps technologies for comprehensive security analysis.',
            'image_url' => '/assets/images/unisast.jpg',
            'github_url' => 'https://github.com/serein/unisast',
            'live_url' => 'https://unisast.demo.com',
            'technologies' => json_encode(['React', 'Node.js', 'Docker', 'Security', 'DevSecOps']),
            'status' => 'active',
            'is_featured' => true
        ],
        [
            'title' => 'Network Security Monitor',
            'slug' => 'network-security-monitor',
            'description' => 'A network security monitoring tool that analyzes traffic patterns and detects potential security threats using machine learning algorithms.',
            'content' => 'Advanced network monitoring solution with Python, Machine Learning, and Network Security capabilities.',
            'image_url' => '/assets/images/network-monitor.jpg',
            'github_url' => 'https://github.com/serein/network-monitor',
            'live_url' => null,
            'technologies' => json_encode(['Python', 'Machine Learning', 'Network Security']),
            'status' => 'development',
            'is_featured' => false
        ],
        [
            'title' => 'Learning Blog',
            'slug' => 'learning-blog',
            'description' => 'A personal blog platform for sharing technology and security knowledge. Built with PHP and modern frontend technologies.',
            'content' => 'Personal blog platform showcasing technology articles and tutorials with modern web technologies.',
            'image_url' => '/assets/images/blog.jpg',
            'github_url' => 'https://github.com/serein/learning-blog',
            'live_url' => 'https://blog.serein.dev',
            'technologies' => json_encode(['PHP', 'JavaScript', 'MySQL', 'Tailwind CSS']),
            'status' => 'active',
            'is_featured' => true
        ]
    ];
    
    $insertSql = "INSERT INTO projects (title, slug, description, content, image_url, github_url, live_url, technologies, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($insertSql);
    
    foreach ($sampleData as $project) {
        $stmt->execute([
            $project['title'],
            $project['slug'],
            $project['description'],
            $project['content'],
            $project['image_url'],
            $project['github_url'],
            $project['live_url'],
            $project['technologies'],
            $project['status'],
            $project['is_featured']
        ]);
    }
    
    echo "Sample data inserted successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>