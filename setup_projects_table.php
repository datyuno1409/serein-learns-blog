<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL file
    $sql = file_get_contents('create_projects_table.sql');
    
    // Execute SQL
    $pdo->exec($sql);
    
    echo "Projects table created successfully with sample data!\n";
    
    // Verify table creation
    $result = $pdo->query("SELECT COUNT(*) as count FROM projects;");
    $count = $result->fetch(PDO::FETCH_ASSOC);
    echo "Total projects in database: " . $count['count'] . "\n";
    
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>