<?php
require '../config.php';
session_start();

$courseId = $_GET['id'];
$userId = $_SESSION["userData"]["Registered_User_Id"] ?? null;

// Fetch course
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();

// Fetch content
$content = $connection->query("SELECT * FROM Course_Content WHERE Course_Id = $courseId");

$isEnrolled = false;
if ($userId) {
    $checkEnroll = $connection->query("SELECT * FROM Enrollment WHERE Registered_User_Id = $userId AND Course_Id = $courseId");
    $isEnrolled = $checkEnroll->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/course.css">
</head>
<body>
    <h1><?= htmlspecialchars($course['Title']) ?></h1>
    <img src="<?= $course['Intro_Image'] ?>" alt="Course Image" width="300">
    <p><?= nl2br(htmlspecialchars($course['Description'])) ?></p>

    <?php if ($userId && !$isEnrolled): ?>
        <form action="enroll.php" method="POST">
            <input type="hidden" name="Course_Id" value="<?= $courseId ?>">
            <button type="submit">Enroll in Course</button>
        </form>
    <?php elseif ($isEnrolled): ?>
        <h3>Course Content</h3>
        <ul>
            <?php while ($row = $content->fetch_assoc()): ?>
                <li><?= htmlspecialchars($row['Title']) ?> - 
                    <?php if ($row['Content_Type'] == 'text'): ?>
                        <?= nl2br(htmlspecialchars($row['Content_Text'])) ?>
                    <?php else: ?>
                        <a href="../uploads/<?= $row['File_Path'] ?>" target="_blank">View File</a>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <a href="start_quiz.php?course_id=<?= $courseId ?>">Start Quiz</a>
    <?php else: ?>
        <p>Please <a href="../login.php">log in</a> to enroll.</p>
    <?php endif; ?>
</body>
</html>
