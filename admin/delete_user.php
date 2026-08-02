<?php
// admin/delete_user.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_teachers.php');
    exit();
}
verify_csrf();

$id   = (int) ($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';

$allowed = ['TCH', 'INS', 'STD'];
if ($id && in_array($type, $allowed, true)) {
    $stmt = $connection->prepare('DELETE FROM registered_user WHERE Registered_User_Id = ? AND Registered_User_Type = ?');
    $stmt->bind_param('is', $id, $type);
    $stmt->execute();
}

$redirect = $type === 'INS' ? 'manage_providers.php' : 'manage_teachers.php';
header("Location: $redirect?deleted=1");
exit();