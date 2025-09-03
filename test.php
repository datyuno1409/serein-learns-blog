<?php
echo "Server is working!";
echo "<br>REQUEST_URI: " . $_SERVER['REQUEST_URI'];
echo "<br>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'];
$base_path = dirname($_SERVER['SCRIPT_NAME']);
echo "<br>Base path: " . $base_path;
$route = str_replace($base_path, '', $_SERVER['REQUEST_URI']);
$route = strtok($route, '?');
echo "<br>Route: " . $route;
?>