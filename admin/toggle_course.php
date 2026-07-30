<?php
// admin/toggle_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$id     = (int)($_GET['id'] ?? 0);
$status = (int)($_GET['status'] ?? 1);
$newStatus = $status ? 0 : 1;

$connection->query("UPDATE course SET Is_Active=$newStatus WHERE Course_Id=$id");
header("Location: admin_dashboard.php"); exit();
?>