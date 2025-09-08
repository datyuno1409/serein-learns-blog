<?php
// Test direct admin login access
session_start();

// Include necessary files
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

echo "Testing direct admin login access...\n";

// Create AuthController instance
try {
    $controller = new AuthController($pdo);
    echo "AuthController created successfully\n";
    
    // Call adminLogin method directly
    echo "Calling adminLogin method...\n";
    $controller->adminLogin();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>