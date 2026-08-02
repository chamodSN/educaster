<?php
// courses/enroll.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}
verify_csrf();

if (isAdmin() || isProvider()) {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}

$courseId = (int) ($_POST['course_id'] ?? 0);
$userId   = currentUserId();

$courseStmt = $connection->prepare('SELECT Due_Date FROM course WHERE Course_Id = ? AND Is_Active = 1');
$courseStmt->bind_param('i', $courseId);
$courseStmt->execute();
$course = $courseStmt->get_result()->fetch_assoc();

if (!$course) {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}

// FIX: previously a learner could enroll in a course even after its
// due date had passed (the "Expired" badge was purely cosmetic).
if ($course['Due_Date'] && $course['Due_Date'] < date('Y-m-d')) {
    header("Location: " . BASE_PATH . "/courses/course_overview.php?id=$courseId&expired=1");
    exit();
}

$stmt = $connection->prepare('INSERT IGNORE INTO enrollment (Registered_User_Id, Course_Id) VALUES (?,?)');
$stmt->bind_param('ii', $userId, $courseId);
$stmt->execute();

header("Location: " . BASE_PATH . "/courses/course_overview.php?id=$courseId&enrolled=1");
exit();