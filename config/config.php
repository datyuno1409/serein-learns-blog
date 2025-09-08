<?php
// Application configuration
define('APP_NAME', 'Serein Blog');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost:8000');

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'blogdb');

// Upload configuration
define('UPLOAD_PATH', __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Session configuration
define('SESSION_LIFETIME', 7200); // 2 hours

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session settings (must be before session_start())
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
$__sessionDir = __DIR__ . '/../storage/sessions';
if (!is_dir($__sessionDir)) {
    @mkdir($__sessionDir, 0777, true);
}
ini_set('session.save_path', $__sessionDir);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);
?>