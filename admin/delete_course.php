<?php
// admin/delete_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_dashboard.php');
    exit();
}
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id) {
    $stmt = $connection->prepare('DELETE FROM course WHERE Course_Id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: manage_courses.php?deleted=1');
exit();