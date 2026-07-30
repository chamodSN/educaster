<?php
// courses/enroll.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = (int)($_POST['course_id'] ?? 0);
    $userId   = (int)$_SESSION['userData']['Registered_User_Id'];

    $stmt = $connection->prepare("INSERT IGNORE INTO enrollment (Registered_User_Id, Course_Id) VALUES (?,?)");
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();

    header("Location: /educaster/courses/course_overview.php?id=$courseId&enrolled=1");
    exit();
}
header("Location: /educaster/programs.php");
exit();
?>