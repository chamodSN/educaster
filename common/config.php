<?php
// common/config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    die('Configuration error: .env file not found.');
}

$env = parse_ini_file($envPath);

$serverName = $env['DB_HOST'];
$userName   = $env['DB_USER'];
$password   = $env['DB_PASS'];
$dbName     = $env['DB_NAME'];

$connection = new mysqli($serverName, $userName, $password, $dbName);

if ($connection->connect_error) {
    die("Database connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// Make admin credentials available
define('ADMIN_EMAIL', $env['ADMIN_EMAIL'] ?? 'admin@educaster.com');
define('ADMIN_PASSWORD', $env['ADMIN_PASSWORD'] ?? 'Admin@2025');
?>