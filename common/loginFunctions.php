<?php
// common/loginFunctions.php

function invalidUserName(string $username): bool {
    return !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username);
}

function invalidEmail(string $email): bool {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

function uidExists(mysqli $conn, string $identifier): array|false {
    $stmt = $conn->prepare(
        "SELECT * FROM registered_user WHERE User_Name = ? OR Email = ? LIMIT 1"
    );
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : false;
}

function isLoggedIn(): bool {
    return isset($_SESSION['userData']) && !empty($_SESSION['userData']);
}

function requireLogin(string $redirect = '/educaster/user/login.php'): void {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit();
    }
}

function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['userData']['Registered_User_Type'] === 'SADMIN');
}

function isProvider(): bool {
    return isLoggedIn() && ($_SESSION['userData']['Registered_User_Type'] === 'INS');
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header("Location: /educaster/user/login.php");
        exit();
    }
}

function requireProvider(): void {
    if (!isProvider()) {
        header("Location: /educaster/user/login.php");
        exit();
    }
}
?>