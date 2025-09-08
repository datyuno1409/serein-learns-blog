<?php
require_once 'helpers/auth_helper.php';
requireAdmin();

$page_title = 'Sao lưu';
$current_page = 'backup';

$message = '';
$error = '';

// Handle backup actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_db_backup':
            try {
                $backupDir = 'backups/database';
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
                
                $filename = 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
                $filepath = $backupDir . '/' . $filename;
                
                // Create database backup using mysqldump
                $command = sprintf(
                    'mysqldump --host=%s --user=%s --password=%s %s > %s',
                    DB_HOST,
                    DB_USER,
                    DB_PASS,
                    DB_NAME,
                    $filepath
                );
                
                exec($command, $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($filepath)) {
                    // Log backup in database
                    $stmt = $pdo->prepare(
                        "INSERT INTO backups (type, filename, filepath, size, created_by, created_at) 
                         VALUES ('database', ?, ?, ?, ?, NOW())"
                    );
                    $stmt->execute([
                        $filename,
                        $filepath,
                        filesize($filepath),
                        $_SESSION['user_id']
                    ]);
                    
                    $message = 'Sao lưu cơ sở dữ liệu thành công!';
                } else {
                    $error = 'Không thể tạo sao lưu cơ sở dữ liệu.';
                }
            } catch (Exception $e) {
                $error = 'Lỗi: ' . $e->getMessage();
            }
            break;
            
        case 'create_files_backup':
            try {
                $backupDir = 'backups/files';
                if (!is_dir($backupDir)) {
                    mkdir($backupDir, 0755, true);
                }
                
                $filename = 'files_backup_' . date('Y-m-d_H-i-s') . '.zip';
                $filepath = $backupDir . '/' . $filename;
                
                $zip = new ZipArchive();
                if ($zip->open($filepath, ZipArchive::CREATE) === TRUE) {
                    // Add uploads directory
                    $uploadsDir = 'uploads';
                    if (is_dir($uploadsDir)) {
                        $iterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($uploadsDir)
                        );
                        
                        foreach ($iterator as $file) {
                            if ($file->isFile()) {
                                $relativePath = str_replace('\\', '/', $iterator->getSubPathName());
                                $zip->addFile($file->getRealPath(), $uploadsDir . '/' . $relativePath);
                            }
                        }
                    }
                    
                    // Add config files
                    $configFiles = ['config.php', '.htaccess', 'router.php'];
                    foreach ($configFiles as $configFile) {
                        if (file_exists($configFile)) {
                            $zip->addFile($configFile);
                        }
                    }
                    
                    $zip->close();
                    
                    // Log backup in database
                    $stmt = $pdo->prepare(
                        "INSERT INTO backups (type, filename, filepath, size, created_by, created_at) 
                         VALUES ('files', ?, ?, ?, ?, NOW())"
                    );
                    $stmt->execute([
                        $filename,
                        $filepath,
                        filesize($filepath),
                        $_SESSION['user_id']
                    ]);
                    
                    $message = 'Sao lưu tệp tin thành công!';
                } else {
                    $error = 'Không thể tạo file zip.';
                }
            } catch (Exception $e) {
                $error = 'Lỗi: ' . $e->getMessage();
            }
            break;
            
        case 'delete_backup':
            $backupId = $_POST['backup_id'] ?? 0;
            try {
                $stmt = $pdo->prepare("SELECT * FROM backups WHERE id = ?");
                $stmt->execute([$backupId]);
                $backup = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($backup) {
                    // Delete file
                    if (file_exists($backup['filepath'])) {
                        unlink($backup['filepath']);
                    }
                    
                    // Delete record
                    $stmt = $pdo->prepare("DELETE FROM backups WHERE id = ?");
                    $stmt->execute([$backupId]);
                    
                    $message = 'Xóa sao lưu thành công!';
                } else {
                    $error = 'Không tìm thấy sao lưu.';
                }
            } catch (Exception $e) {
                $error = 'Lỗi: ' . $e->getMessage();
            }
            break;
    }
}

// Create backups table if not exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS backups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('database', 'files', 'full') NOT NULL,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(500) NOT NULL,
            size BIGINT NOT NULL,
            created_by INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
} catch (PDOException $e) {
    // Table might already exist
}

// Get backup list
try {
    $stmt = $pdo->query("
        SELECT b.*, u.username 
        FROM backups b 
        LEFT JOIN users u ON b.created_by = u.id 
        ORDER BY b.created_at DESC
    ");
    $backups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $backups = [];
    $error = 'Không thể tải danh sách sao lưu: ' . $e->getMessage();
}

// Calculate total backup size
$totalSize = 0;
foreach ($backups as $backup) {
    $totalSize += $backup['size'];
}

function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    return round($size, $precision) . ' ' . $units[$i];
}

$content = __DIR__ . '/../views/admin/backup/index.php';
require_once 'views/layouts/admin.php';
?>
