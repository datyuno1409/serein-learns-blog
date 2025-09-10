<?php
// InfinityFree Production Configuration

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value, '"\' ');
    }
}

// Application configuration
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Serein Blog');
define('APP_URL', $_ENV['APP_URL'] ?? 'https://serein.lovestoblog.com');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');

// Database configuration - Auto detect environment
if (isset($_ENV['DB_HOST']) && $_ENV['DB_HOST'] !== 'localhost') {
    // Production MySQL configuration
    define('DB_TYPE', 'mysql');
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
} else {
    // Development SQLite configuration
    define('DB_TYPE', 'sqlite');
    define('DB_PATH', __DIR__ . '/../blog.sqlite');
}

// Upload configuration
define('UPLOAD_PATH', $_ENV['UPLOAD_PATH'] ?? __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', $_ENV['UPLOAD_MAX_SIZE'] ?? 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Session configuration
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 7200); // 2 hours

// Error reporting
if (APP_ENV === 'production') {
    error_reporting($_ENV['ERROR_REPORTING'] ?? 0);
    ini_set('display_errors', $_ENV['DISPLAY_ERRORS'] ?? 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Security settings
if (APP_ENV === 'production') {
    ini_set('session.cookie_secure', $_ENV['SECURE_COOKIES'] ?? 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
}

// Session settings
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
$sessionDir = __DIR__ . '/../storage/sessions';
if (!is_dir($sessionDir)) {
    @mkdir($sessionDir, 0755, true);
}
ini_set('session.save_path', $sessionDir);

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => APP_ENV === 'production' ? parse_url(APP_URL, PHP_URL_HOST) : '',
    'secure' => APP_ENV === 'production' && ($_ENV['HTTPS_ONLY'] ?? true),
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Cache configuration
define('CACHE_ENABLED', $_ENV['CACHE_ENABLED'] ?? (APP_ENV === 'production'));
define('CACHE_LIFETIME', $_ENV['CACHE_LIFETIME'] ?? 3600);

// Create necessary directories
$directories = [
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../storage/sessions',
    __DIR__ . '/../storage/cache',
    __DIR__ . '/../uploads/media',
    __DIR__ . '/../backups'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Helper function to get database connection
function getDatabaseConnection() {
    static $connection = null;
    
    if ($connection === null) {
        if (DB_TYPE === 'mysql') {
            require_once __DIR__ . '/database_mysql.php';
            $db = new DatabaseMySQL();
            $connection = $db->connect();
        } else {
            require_once __DIR__ . '/database.php';
            $db = new Database();
            $connection = $db->connect();
        }
    }
    
    return $connection;
}

// Helper function for logging
function logError($message, $context = []) {
    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;
    
    @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}
?>