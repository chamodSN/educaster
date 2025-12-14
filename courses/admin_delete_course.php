<?php
session_start();
require 'config.php';

// TODO: admin check

$courseId = intval($_GET['id'] ?? 0);
if ($courseId) {
    // Delete course, cascade deletes quizzes and questions as needed in DB or here manually
    $connection->query("DELETE FROM Course WHERE Course_Id = $courseId");
    // Optionally delete related quizzes and questions here manually or rely on FK cascade
}

header('Location: admin_courses.php');
exit();
