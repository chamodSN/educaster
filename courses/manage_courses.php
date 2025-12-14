<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$courses = $connection->query("SELECT * FROM Course");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Courses</title>
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Manage Courses</h1>

<a href="add_course.php">Add New Course</a><br><br>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>Title</th><th>Actions</th>
    </tr>
    <?php while ($course = $courses->fetch_assoc()): ?>
    <tr>
        <td><?= $course['Course_Id'] ?></td>
        <td><?= htmlspecialchars($course['Title']) ?></td>
        <td>
            <a href="edit_course.php?id=<?= $course['Course_Id'] ?>">Edit</a> |
            <a href="delete_course.php?id=<?= $course['Course_Id'] ?>" onclick="return confirm('Delete this course?')">Delete</a> |
            <a href="manage_quizzes.php?course_id=<?= $course['Course_Id'] ?>">Manage Quizzes</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<?php include '../common/footer.php'; ?>
</body>
</html>
