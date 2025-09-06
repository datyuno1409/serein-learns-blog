<?php
try {
    $pdo = new PDO('sqlite:blog.sqlite');
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='projects';");
    if($result->fetch()) {
        echo 'Table projects exists';
    } else {
        echo 'Table projects does not exist';
    }
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>