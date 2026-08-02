<?php
/**
 * common/loginFunctions.php
 * Authentication / authorization helper functions.
 */

function invalidUserName(string $username): bool
{
    return !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
}

function invalidEmail(string $email): bool
{
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

function invalidPassword(string $password): bool
{
    return strlen($password) < 6;
}

function uidExists(mysqli $conn, string $identifier)
{
    $stmt = $conn->prepare('SELECT * FROM registered_user WHERE User_Name = ? OR Email = ? LIMIT 1');
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['userData']) && !empty($_SESSION['userData']);
}

function requireLogin(string $redirect = null): void
{
    if (!isLoggedIn()) {
        header('Location: ' . ($redirect ?? BASE_PATH . '/user/login.php'));
        exit();
    }
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['userData']['Registered_User_Type'] ?? '') === 'SADMIN';
}

function isProvider(): bool
{
    return isLoggedIn() && ($_SESSION['userData']['Registered_User_Type'] ?? '') === 'INS';
}

function currentUserId(): int
{
    return (int) ($_SESSION['userData']['Registered_User_Id'] ?? 0);
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ' . BASE_PATH . '/user/login.php');
        exit();
    }
}

function requireProvider(): void
{
    if (!isProvider()) {
        header('Location: ' . BASE_PATH . '/user/login.php');
        exit();
    }
}

/**
 * Sends a logged-in user to the dashboard that matches their role.
 * Used right after login, and to keep admins/providers away from
 * pages that only make sense for regular learners.
 */
function redirectToDashboard(): void
{
    if (isAdmin()) {
        header('Location: ' . BASE_PATH . '/admin/admin_dashboard.php');
    } elseif (isProvider()) {
        header('Location: ' . BASE_PATH . '/provider/provider_dashboard.php');
    } else {
        header('Location: ' . BASE_PATH . '/dashboard/student_dashboard.php');
    }
    exit();
}