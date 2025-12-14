<?php
session_start();
require 'config.php';

// TODO: Add admin check here to restrict access

$courses = $connection->query("SELECT * FROM Course");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Manage Courses</title>
    <link rel="stylesheet" href="css/admin_courses.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Manage Courses</h1>
<a href="admin_course_form.php" class="btn add-btn">Add New Course</a>

<table>
    <thead>
        <tr>
            <th>Course ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($course = $courses->fetch_assoc()): ?>
        <tr>
            <td><?= $course['Course_Id'] ?></td>
            <td><?= htmlspecialchars($course['Title']) ?></td>
            <td><?= htmlspecialchars(substr($course['Description'], 0, 50)) ?>...</td>
            <td>
                <a href="admin_course_form.php?id=<?= $course['Course_Id'] ?>">Edit</a> | 
                <a href="admin_delete_course.php?id=<?= $course['Course_Id'] ?>" onclick="return confirm('Are you sure?')">Delete</a> | 
                <a href="admin_quizzes.php?course_id=<?= $course['Course_Id'] ?>">Manage Quizzes</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
</body>
</html>
