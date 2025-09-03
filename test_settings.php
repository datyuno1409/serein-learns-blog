<?php
// Test Settings page with proper admin session
session_start();

// Set admin session
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = 'admin';

echo "Testing Settings page with admin session...\n";

// Include the AdminController to test directly
require_once 'config/database.php';
require_once 'controllers/AdminController.php';

try {
    $controller = new AdminController();
    
    // Capture output
    ob_start();
    $controller->settings();
    $output = ob_get_clean();
    
    echo "Settings page loaded successfully\n";
    echo "Output length: " . strlen($output) . "\n";
    
    // Check for key elements
    if (strpos($output, 'Site Configuration') !== false) {
        echo "✓ Site Configuration section found\n";
    } else {
        echo "✗ Site Configuration section NOT found\n";
    }
    
    if (strpos($output, 'SEO Configuration') !== false) {
        echo "✓ SEO Configuration section found\n";
    } else {
        echo "✗ SEO Configuration section NOT found\n";
    }
    
    if (strpos($output, 'Social Media') !== false) {
        echo "✓ Social Media section found\n";
    } else {
        echo "✗ Social Media section NOT found\n";
    }
    
    if (strpos($output, 'Features') !== false) {
        echo "✓ Features section found\n";
    } else {
        echo "✗ Features section NOT found\n";
    }
    
    if (strpos($output, 'form') !== false && strpos($output, 'action="/admin/settings/save"') !== false) {
        echo "✓ Settings form with correct action found\n";
    } else {
        echo "✗ Settings form NOT found or incorrect action\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nSettings page test completed.\n";
?>