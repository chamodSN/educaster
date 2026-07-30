<?php
// provider/delete_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$quizId     = (int)($_GET['id'] ?? 0);
$courseId   = (int)($_GET['course_id'] ?? 0);

$check = $connection->query(
    "SELECT q.Quiz_Id FROM quiz q JOIN course c ON c.Course_Id=q.Course_Id
     WHERE q.Quiz_Id=$quizId AND c.Provider_Id=$providerId"
)->num_rows;

if ($check) {
    $connection->query("DELETE FROM quiz WHERE Quiz_Id=$quizId");
}
header("Location: manage_quiz.php?course_id=$courseId"); exit();
?>