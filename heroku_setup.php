<?php
/**
 * Heroku Database Setup Script
 * This script initializes the database on Heroku deployment
 */

require_once __DIR__ . '/config/heroku_database.php';

try {
    echo "Starting Heroku database setup...\n";
    
    $db = new HerokuDatabase();
    $conn = $db->connect();
    $dbType = $db->getDatabaseType();
    
    echo "Connected to {$dbType} database successfully.\n";
    
    // Load appropriate schema based on database type
    if ($dbType === 'postgresql') {
        $schemaFile = __DIR__ . '/database/schema_postgresql.sql';
    } elseif ($dbType === 'mysql') {
        $schemaFile = __DIR__ . '/database/schema_mysql.sql';
    } else {
        $schemaFile = __DIR__ . '/database/schema_sqlite.sql';
    }
    
    if (file_exists($schemaFile)) {
        echo "Loading schema from {$schemaFile}...\n";
        $schema = file_get_contents($schemaFile);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
                try {
                    $conn->exec($statement);
                    echo "Executed: " . substr($statement, 0, 50) . "...\n";
                } catch (PDOException $e) {
                    // Log error but continue
                    echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "Schema loaded successfully.\n";
    } else {
        echo "Schema file not found: {$schemaFile}\n";
    }
    
    // Create default admin user if not exists
    $checkAdmin = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $checkAdmin->execute();
    $adminCount = $checkAdmin->fetch()['count'];
    
    if ($adminCount == 0) {
        echo "Creating default admin user...\n";
        
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, role, created_at) 
            VALUES (?, ?, ?, 'admin', datetime('now'))
        ");
        
        $stmt->execute(['admin', 'admin@example.com', $hashedPassword]);
        echo "Default admin user created (username: admin, password: admin123)\n";
        echo "⚠️  Please change the default password after first login!\n";
    }
    
    echo "\n✅ Heroku database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error during setup: " . $e->getMessage() . "\n";
    exit(1);
}
?>