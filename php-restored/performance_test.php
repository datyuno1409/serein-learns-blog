<?php
$start_time = microtime(true);
$start_memory = memory_get_usage();

require_once 'config/config.php';
require_once 'config/database.php';

$end_time = microtime(true);
$end_memory = memory_get_usage();

$execution_time = ($end_time - $start_time) * 1000;
$memory_used = ($end_memory - $start_memory) / 1024;

header('Content-Type: application/json');
echo json_encode([
    'execution_time_ms' => round($execution_time, 2),
    'memory_used_kb' => round($memory_used, 2),
    'peak_memory_mb' => round(memory_get_peak_usage() / 1024 / 1024, 2),
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>