<?php
require_once 'config/config.php';
session_start();
require_once 'config/database.php';
require_once 'helpers/auth_helper.php';

echo "<h2>Session Debug Test</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session Save Path: " . session_save_path() . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h3>Login Attempt</h3>";
    echo "<p>Username: $username</p>";
    
    try {
        $db = new Database();
        $conn = $db->connect();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password']) && $user['is_active']) {
            echo "<p style='color: green;'>User found and password verified!</p>";
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_admin'] = ($user['role'] === 'admin');
            
            echo "<p>Session data set:</p>";
            echo "<pre>" . print_r($_SESSION, true) . "</pre>";
            
            echo "<p><a href='/admin/articles'>Test Articles Page</a></p>";
            echo "<p><a href='/admin/dashboard'>Test Dashboard</a></p>";
        } else {
            echo "<p style='color: red;'>Login failed!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h3>Current Session Data</h3>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    
    echo "<h3>Auth Helper Functions</h3>";
    echo "<p>isLoggedIn(): " . (isLoggedIn() ? 'true' : 'false') . "</p>";
    echo "<p>isAdmin(): " . (isAdmin() ? 'true' : 'false') . "</p>";
}
?>

<form method='POST'>
    <h3>Test Login</h3>
    <p>
        <label>Username:</label><br>
        <input type='text' name='username' value='admin' required>
    </p>
    <p>
        <label>Password:</label><br>
        <input type='password' name='password' value='admin123' required>
    </p>
    <p>
        <button type='submit'>Login</button>
    </p>
</form>

<p><a href='?clear=1'>Clear Session</a></p>

<?php
if (isset($_GET['clear'])) {
    session_destroy();
    echo "<p style='color: orange;'>Session cleared! <a href='?'>Refresh</a></p>";
}
?>