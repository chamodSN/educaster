<?php

// Load .env file
$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
    die('.env file not found');
}

$env = parse_ini_file($envPath);

// Declare variables from .env
$serverName = $env['DB_HOST'] . ':' . $env['DB_PORT'];
$userName   = $env['DB_USER'];
$password   = $env['DB_PASS'];
$dbName     = $env['DB_NAME'];

// Create the connection
$connection = new mysqli($serverName, $userName, $password, $dbName);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
} else {
    echo "<script>console.log('Connection Successful')</script>";
}

?>
