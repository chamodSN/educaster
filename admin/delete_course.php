<?php
// admin/delete_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $connection->query("DELETE FROM course WHERE Course_Id=$id");
}
header("Location: admin_dashboard.php"); exit();
?>