<?php
session_start();
require '../common/config.php';

$userId = $_SESSION['userData']['Registered_User_Id'] ?? null;
$courseId = intval($_GET['id'] ?? 0);

if (!$userId) {
    header("Location: /educaster/user/login.php");
    exit();
}

// Check enrollment
$res = $connection->query("SELECT * FROM Enrollment WHERE Registered_User_Id = $userId AND Course_Id = $courseId");
if ($res->num_rows === 0) {
    die("You are not enrolled in this course.");
}

// Fetch course
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();
if (!$course) {
    die("Course not found.");
}

// TODO: fetch course materials (videos, slides, docs) from DB when implemented
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($course['Title']) ?> - Content</title>
    <link rel="stylesheet" href="css/course_content.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<h1>Course Content: <?= htmlspecialchars($course['Title']) ?></h1>

<p>This is where videos, slides, and other materials will be listed.</p>

<?php include '../common/footer.php'; ?>
</body>
</html>
