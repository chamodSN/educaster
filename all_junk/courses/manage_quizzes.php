<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$courseId = intval($_GET['course_id']);

$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();
if (!$course) {
    die("Course not found.");
}

$quizzes = $connection->query("SELECT * FROM Quiz WHERE Course_Id = $courseId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Quizzes for <?= htmlspecialchars($course['Title']) ?></title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Manage Quizzes for: <?= htmlspecialchars($course['Title']) ?></h1>

<a href="add_quiz.php?course_id=<?= $courseId ?>">Add New Quiz</a><br><br>

<table border="1" cellpadding="8">
    <tr><th>ID</th><th>Title</th><th>Actions</th></tr>
    <?php while ($quiz = $quizzes->fetch_assoc()): ?>
    <tr>
        <td><?= $quiz['Quiz_Id'] ?></td>
        <td><?= htmlspecialchars($quiz['Title']) ?></td>
        <td>
            <a href="edit_quiz.php?id=<?= $quiz['Quiz_Id'] ?>">Edit</a> |
            <a href="delete_quiz.php?id=<?= $quiz['Quiz_Id'] ?>&course_id=<?= $courseId ?>" onclick="return confirm('Delete this quiz?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<a href="manage_courses.php">Back to Courses</a>

<?php include '../common/footer.php'; ?>
</body>
</html>
