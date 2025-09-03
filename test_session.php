<?php
require_once 'config/config.php';
session_start();
require_once 'config/database.php';
require_once 'helpers/auth_helper.php';

echo "<h2>Session Debug Info</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
echo "Session Data:<br>";
print_r($_SESSION);

echo "<br><br><h3>Auth Helper Functions:</h3>";
echo "isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "<br>";
echo "isAdmin(): " . (isAdmin() ? 'true' : 'false') . "<br>";

if (isset($_SESSION['user_id'])) {
    echo "<br><h3>Current User Info:</h3>";
    $user = getCurrentUser();
    if ($user) {
        print_r($user);
    } else {
        echo "Failed to get user info";
    }
}

echo "<br><br><a href='/admin/dashboard'>Try Dashboard</a>";
?>