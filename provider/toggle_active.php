<?php
// provider/toggle_active.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_courses.php');
    exit();
}
verify_csrf();

$providerId = currentUserId();
$id     = (int) ($_POST['id'] ?? 0);
$status = (int) ($_POST['status'] ?? 1);
$new    = $status ? 0 : 1;

$stmt = $connection->prepare('UPDATE course SET Is_Active = ? WHERE Course_Id = ? AND Provider_Id = ?');
$stmt->bind_param('iii', $new, $id, $providerId);
$stmt->execute();

header('Location: manage_courses.php');
exit();