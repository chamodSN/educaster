<?php
// admin/delete_user.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$id   = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? '';

$allowed = ['TCH', 'INS', 'STD'];
if ($id && in_array($type, $allowed)) {
    $connection->query("DELETE FROM registered_user WHERE Registered_User_Id=$id AND Registered_User_Type='$type'");
}

$redirect = match($type) {
    'INS'   => 'manage_providers.php',
    default => 'manage_teachers.php',
};
header("Location: $redirect?deleted=1"); exit();
?>