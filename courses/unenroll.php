<?php
// courses/unenroll.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}
verify_csrf();

$courseId = (int) ($_POST['course_id'] ?? 0);
$userId   = currentUserId();

$stmt = $connection->prepare('DELETE FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
$stmt->bind_param('ii', $userId, $courseId);
$stmt->execute();

header("Location: " . BASE_PATH . "/courses/course_overview.php?id=$courseId&unenrolled=1");
exit();