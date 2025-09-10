<?php
// Router for PHP built-in server
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Log the request
file_put_contents('router.log', "Router: $path at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Check if it's a static file that exists
if ($path !== '/' && file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    // For PHP files, let them execute normally
    if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
        return false;
    }
    // For other files (CSS, JS, images), serve them
    return false;
}

// For all other requests, route through index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/index.php';
?>