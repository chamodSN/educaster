<?php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();
$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$id = (int)($_GET['id'] ?? 0);
$status = (int)($_GET['status'] ?? 1);
$new = $status ? 0 : 1;
$connection->query("UPDATE course SET Is_Active=$new WHERE Course_Id=$id AND Provider_Id=$providerId");
header("Location: manage_courses.php"); exit();
?>