<?php
// user/deleteAccount.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if (isAdmin()) {
    header('Location: ' . BASE_PATH . '/admin/admin_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: accountDetails.php');
    exit();
}
verify_csrf();

$userId = currentUserId();

// Foreign keys with ON DELETE CASCADE take care of enrollment, review,
// inquiry (SET NULL), takes and — for providers — their courses.
$stmt = $connection->prepare('DELETE FROM registered_user WHERE Registered_User_Id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();

session_unset();
session_destroy();
header('Location: ' . BASE_PATH . '/user/signup.php?message=accountdeleted');
exit();