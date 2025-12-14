<?php
session_start();
require 'config.php';

$courseId = intval($_GET['course_id'] ?? 0);
if (!$courseId) {
    die("Course not specified.");
}

// TODO: admin check

// Get course title for header
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();

$quizzes = $connection->query("SELECT * FROM Quiz WHERE Course_Id = $courseId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Manage Quizzes for <?= htmlspecialchars($course['Title']) ?></title>
    <link rel="stylesheet" href="css/admin_quizzes.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Manage Quizzes for <?= htmlspecialchars($course['Title']) ?></h1>
<a href="admin_quiz_form.php?course_id=<?= $courseId ?>" class="btn add-btn">Add New Quiz</a>
<a href="admin_courses.php" class="btn back-btn">Back to Courses</a>

<table>
    <thead>
        <tr>
            <th>Quiz ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($quiz = $quizzes->fetch_assoc()): ?>
        <tr>
            <td><?= $quiz['Quiz_Id'] ?></td>
            <td><?= htmlspecialchars($quiz['Title']) ?></td>
            <td>
                <a href="admin_quiz_form.php?id=<?= $quiz['Quiz_Id'] ?>&course_id=<?= $courseId ?>">Edit</a> | 
                <a href="admin_delete_quiz.php?id=<?= $quiz['Quiz_Id'] ?>&course_id=<?= $courseId ?>" onclick="return confirm('Are you sure?')">Delete</a> | 
                <a href="admin_quiz_questions.php?quiz_id=<?= $quiz['Quiz_Id'] ?>">Manage Questions</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
</body>
</html>
