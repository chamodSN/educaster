<?php
// provider/delete_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_courses.php');
    exit();
}
verify_csrf();

$providerId = currentUserId();
$quizId     = (int) ($_POST['id'] ?? 0);
$courseId   = (int) ($_POST['course_id'] ?? 0);

$check = $connection->prepare(
    'SELECT q.Quiz_Id FROM quiz q JOIN course c ON c.Course_Id=q.Course_Id
     WHERE q.Quiz_Id=? AND c.Provider_Id=?'
);
$check->bind_param('ii', $quizId, $providerId);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    $del = $connection->prepare('DELETE FROM quiz WHERE Quiz_Id=?');
    $del->bind_param('i', $quizId);
    $del->execute();
}
header("Location: manage_quiz.php?course_id=$courseId");
exit();