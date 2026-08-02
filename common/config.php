<?php
/**
 * common/config.php
 * Bootstraps the session, loads .env, opens the DB connection, and
 * pulls in the shared helper functions. Every page requires this file
 * first, so anything added here is available everywhere.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Harden the session cookie a little (kept simple on purpose so it
    // still works over plain http://localhost during local XAMPP dev).
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/helpers.php';

$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    http_response_code(500);
    die('Configuration error: .env file not found. Copy .env.example to .env and fill in your database details.');
}

$env = parse_ini_file($envPath);
if ($env === false) {
    http_response_code(500);
    die('Configuration error: .env file could not be parsed.');
}

$dbHost = $env['DB_HOST'] ?? 'localhost';
$dbPort = (int) ($env['DB_PORT'] ?? 3306);
$dbUser = $env['DB_USER'] ?? 'root';
$dbPass = $env['DB_PASS'] ?? '';
$dbName = $env['DB_NAME'] ?? 'educaster';

mysqli_report(MYSQLI_REPORT_OFF);
$connection = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if ($connection->connect_error) {
    http_response_code(500);
    die(
        'Database connection failed. Make sure MySQL is running in XAMPP and that the "educaster" ' .
        'database has been imported (see database/educaster.sql). Original error: ' .
        htmlspecialchars($connection->connect_error)
    );
}

$connection->set_charset('utf8mb4');

// Super-admin credentials (this account is not stored in the database).
define('ADMIN_EMAIL', $env['ADMIN_EMAIL'] ?? 'admin@educaster.com');
define('ADMIN_PASSWORD_HASH', $env['ADMIN_PASSWORD_HASH'] ?? '');

// Base path of the app relative to the web root, e.g. /educaster
// Change this constant in ONE place if you rename the project folder.
define('BASE_PATH', '/educaster');