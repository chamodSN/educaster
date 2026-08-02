<?php
// admin/toggle_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_dashboard.php');
    exit();
}
verify_csrf();

$id        = (int) ($_POST['id'] ?? 0);
$status    = (int) ($_POST['status'] ?? 1);
$newStatus = $status ? 0 : 1;
$from      = ($_POST['from'] ?? '') === 'dashboard' ? 'admin_dashboard.php' : 'manage_courses.php';

$stmt = $connection->prepare('UPDATE course SET Is_Active = ? WHERE Course_Id = ?');
$stmt->bind_param('ii', $newStatus, $id);
$stmt->execute();

header('Location: ' . $from);
exit();