<?php
echo "Current REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Current SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Current PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "Current PATH_INFO: " . (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : 'Not set') . "\n";
?>