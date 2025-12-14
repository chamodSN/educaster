<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData'])) {
    header("Location: /educaster/user/login.php");
    exit();
}

$userId = $_SESSION['userData']['Registered_User_Id'];
$courseId = intval($_POST['course_id'] ?? 0);

if (!$courseId) {
    die("Invalid course.");
}

// Check if already enrolled
$res = $connection->query("SELECT * FROM Enrollment WHERE Registered_User_Id = $userId AND Course_Id = $courseId");
if ($res->num_rows === 0) {
    $connection->query("INSERT INTO Enrollment (Registered_User_Id, Course_Id) VALUES ($userId, $courseId)");
}

header("Location: course_detail.php?id=$courseId");
exit();
