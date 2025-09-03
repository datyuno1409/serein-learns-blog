<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = new Database();
    $conn = $db->connect();

    // Ensure remember_token column exists
    $check = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'remember_token'");
    $check->execute();
    $rememberColExists = $check->fetchColumn() > 0;
    if (!$rememberColExists) {
        $conn->exec("ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) NULL");
        $rememberColExists = true;
    }

    $username = isset($_GET['username']) ? trim($_GET['username']) : 'admin';
    $newPassword = isset($_GET['password']) ? (string)$_GET['password'] : 'admin123';

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE (username = ? OR role = 'admin') LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo 'No admin user found';
        exit;
    }

    $hash = password_hash($newPassword, PASSWORD_BCRYPT);

    if ($rememberColExists) {
        $upd = $conn->prepare("UPDATE users SET password = ?, is_active = 1, remember_token = NULL WHERE id = ?");
        $upd->execute([$hash, $user['id']]);
    } else {
        $upd = $conn->prepare("UPDATE users SET password = ?, is_active = 1 WHERE id = ?");
        $upd->execute([$hash, $user['id']]);
    }

    echo 'OK: user=' . htmlspecialchars($user['username']) . ' id=' . (int)$user['id'];
} catch (Throwable $e) {
    http_response_code(500);
    echo 'ERR: ' . $e->getMessage();
}