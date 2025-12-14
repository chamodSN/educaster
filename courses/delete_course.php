<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$courseId = intval($_GET['id']);

// Optional: delete associated quizzes/content before course delete

$connection->query("DELETE FROM Course WHERE Course_Id = $courseId");
header("Location: manage_courses.php");
exit();
