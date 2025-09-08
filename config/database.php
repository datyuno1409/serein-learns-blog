<?php
class Database {
    private $dbPath;
    private $conn;
    
    public function __construct() {
        $this->dbPath = __DIR__ . '/../blog.sqlite';
    }
    
    public function connect() {
        $this->conn = null;
        
        try {
            // Create database file if it doesn't exist
            if (!file_exists($this->dbPath)) {
                $this->createDatabase();
            }
            
            $this->conn = new PDO("sqlite:" . $this->dbPath);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Enable foreign key constraints
            $this->conn->exec("PRAGMA foreign_keys = ON");
        } catch(PDOException $e) {
            throw $e;
        }
        
        return $this->conn;
    }
    
    private function createDatabase() {
        // Create empty database file
        touch($this->dbPath);
        
        // Initialize with basic schema if needed
        $tempConn = new PDO("sqlite:" . $this->dbPath);
        $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Execute SQLite schema if tables don't exist
        $schemaFile = __DIR__ . '/../database/schema_sqlite.sql';
        if (file_exists($schemaFile)) {
            $schema = file_get_contents($schemaFile);
            // Split by semicolon and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $schema)));
            foreach ($statements as $statement) {
                if (!empty($statement) && !str_starts_with(trim($statement), '--')) {
                    try {
                        $tempConn->exec($statement);
                    } catch (PDOException $e) {
                        // Ignore errors for INSERT OR IGNORE statements
                        if (!str_contains($statement, 'INSERT OR IGNORE')) {
                            error_log("Database schema error: " . $e->getMessage());
                        }
                    }
                }
            }
        }
        
        $tempConn = null;
    }
}
?>