<?php
class HerokuDatabase {
    private $conn;
    
    public function __construct() {
        // Load environment variables
        if (file_exists(__DIR__ . '/../.env')) {
            $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                list($name, $value) = explode('=', $line, 2);
                $_ENV[trim($name)] = trim($value);
            }
        }
    }
    
    public function connect() {
        $this->conn = null;
        
        try {
            // Check if running on Heroku with DATABASE_URL
            if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
                $this->conn = $this->connectFromDatabaseUrl($_ENV['DATABASE_URL']);
            }
            // Check for individual database environment variables
            elseif (isset($_ENV['DB_HOST']) && !empty($_ENV['DB_HOST'])) {
                $this->conn = $this->connectFromEnvVars();
            }
            // Fallback to SQLite for local development
            else {
                $this->conn = $this->connectSQLite();
            }
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
        
        return $this->conn;
    }
    
    private function connectFromDatabaseUrl($databaseUrl) {
        $url = parse_url($databaseUrl);
        
        $host = $url['host'];
        $port = isset($url['port']) ? $url['port'] : null;
        $database = ltrim($url['path'], '/');
        $username = $url['user'];
        $password = $url['pass'];
        $scheme = $url['scheme'];
        
        if ($scheme === 'postgres' || $scheme === 'postgresql') {
            $dsn = "pgsql:host={$host}";
            if ($port) $dsn .= ";port={$port}";
            $dsn .= ";dbname={$database}";
        } else {
            // MySQL
            $dsn = "mysql:host={$host}";
            if ($port) $dsn .= ";port={$port}";
            $dsn .= ";dbname={$database};charset=utf8mb4";
        }
        
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    private function connectFromEnvVars() {
        $host = $_ENV['DB_HOST'];
        $database = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $port = $_ENV['DB_PORT'] ?? 3306;
        
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    
    private function connectSQLite() {
        $dbPath = __DIR__ . '/../blog.sqlite';
        
        // Create database file if it doesn't exist
        if (!file_exists($dbPath)) {
            $this->createSQLiteDatabase($dbPath);
        }
        
        $conn = new PDO("sqlite:" . $dbPath);
        $conn->exec("PRAGMA foreign_keys = ON");
        
        return $conn;
    }
    
    private function createSQLiteDatabase($dbPath) {
        // Create empty database file
        touch($dbPath);
        
        // Initialize with basic schema if needed
        $tempConn = new PDO("sqlite:" . $dbPath);
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
    
    public function getDatabaseType() {
        if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
            $url = parse_url($_ENV['DATABASE_URL']);
            return $url['scheme'] === 'postgres' ? 'postgresql' : 'mysql';
        }
        elseif (isset($_ENV['DB_HOST']) && !empty($_ENV['DB_HOST'])) {
            return 'mysql';
        }
        else {
            return 'sqlite';
        }
    }
}
?>