<?php
session_start();
require '../common/config.php';

$userId = $_SESSION['userData']['Registered_User_Id'] ?? null;
$courseId = intval($_GET['id'] ?? 0);

if (!$courseId) {
    die("Course not found.");
}

// Fetch course
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();
if (!$course) {
    die("Course not found.");
}

// Check enrollment
$enrolled = false;
if ($userId) {
    $res = $connection->query("SELECT * FROM Enrollment WHERE Registered_User_Id = $userId AND Course_Id = $courseId");
    $enrolled = $res->num_rows > 0;
}

// Fetch quiz for this course (assuming one quiz per course)
$quiz = $connection->query("SELECT * FROM Quiz WHERE Course_Id = $courseId")->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($course['Title']) ?> - Educaster</title>
    <link rel="stylesheet" href="css/course_detail.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<h1><?= htmlspecialchars($course['Title']) ?></h1>
<?php if ($course['Intro_Image']): ?>
    <img src="/educaster/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" alt="<?= htmlspecialchars($course['Title']) ?>" class="intro-img">
<?php endif; ?>

<p><?= nl2br(htmlspecialchars($course['Description'])) ?></p>

<?php if (!$userId): ?>
    <p><a href="/educaster/user/login.php">Login</a> to enroll in this course.</p>
<?php elseif ($enrolled): ?>
    <p>You are enrolled in this course.</p>
    <a href="course_content.php?id=<?= $courseId ?>" class="btn">Go to Course Content</a>
    <?php if ($quiz): ?>
        <a href="quiz_start.php?id=<?= $quiz['Quiz_Id'] ?>" class="btn quiz-btn">Start Quiz</a>
    <?php endif; ?>
<?php else: ?>
    <form method="POST" action="enroll.php">
        <input type="hidden" name="course_id" value="<?= $courseId ?>">
        <button type="submit" class="btn enroll-btn">Enroll in this Course</button>
    </form>
<?php endif; ?>

<?php include '../common/footer.php'; ?>
</body>
</html>
