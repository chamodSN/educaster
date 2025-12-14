<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$quizId = intval($_GET['id']);
$courseId = intval($_GET['course_id']);

// Delete quiz questions first
$connection->query("DELETE FROM Question WHERE Quiz_Id = $quizId");

// Delete quiz
$connection->query("DELETE FROM Quiz WHERE Quiz_Id = $quizId");

header("Location: manage_quizzes.php?course_id=$courseId");
exit();
