<?php
session_start();
require 'common/config.php';

if (!isset($_SESSION['userData'])) {
    header('Location: userlogin.php');
    exit();
}

$courseId = intval($_GET['id'] ?? 0);
if (!$courseId) {
    die('Invalid course ID');
}

$userId = $_SESSION['userData']['Registered_User_Id'];

// Get course info
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();

if (!$course) {
    die('Course not found.');
}

// Check enrollment
$stmt = $connection->prepare("SELECT * FROM Enrollment WHERE Registered_User_Id = ? AND Course_Id = ?");
$stmt->bind_param("ii", $userId, $courseId);
$stmt->execute();
$enrolled = $stmt->get_result()->num_rows > 0;

// Handle enrollment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$enrolled) {
    $stmtInsert = $connection->prepare("INSERT INTO Enrollment (Registered_User_Id, Course_Id, Enroll_Date) VALUES (?, ?, CURDATE())");
    $stmtInsert->bind_param("ii", $userId, $courseId);
    if ($stmtInsert->execute()) {
        $enrolled = true;
    }
    $stmtInsert->close();
}

// Get course materials
$stmtMat = $connection->prepare("SELECT * FROM Course_Material WHERE Course_Id = ?");
$stmtMat->bind_param("i", $courseId);
$stmtMat->execute();
$materials = $stmtMat->get_result();

// Get quizzes count
$stmtQuiz = $connection->prepare("SELECT COUNT(*) as quiz_count FROM Quiz WHERE Course_Id = ?");
$stmtQuiz->bind_param("i", $courseId);
$stmtQuiz->execute();
$quizCount = $stmtQuiz->get_result()->fetch_assoc()['quiz_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= htmlspecialchars($course['Title']) ?></title>
    <link rel="stylesheet" href="css/course_description.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<h1><?= htmlspecialchars($course['Title']) ?></h1>
<p><?= nl2br(htmlspecialchars($course['Description'])) ?></p>
<?php if ($course['Intro_Image']): ?>
    <img src="uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" alt="Intro Image" style="max-width: 300px;">
<?php endif; ?>

<?php if (!$enrolled): ?>
    <form method="POST">
        <button type="submit">Enroll in this course</button>
    </form>
<?php else: ?>
    <h2>Course Materials</h2>
    <?php if ($materials->num_rows === 0): ?>
        <p>No materials available yet.</p>
    <?php else: ?>
        <ul>
        <?php while ($mat = $materials->fetch_assoc()): ?>
            <li>
                <?= htmlspecialchars($mat['Title']) ?> (<?= htmlspecialchars($mat['Type']) ?>)
                <?php if ($mat['File_Path']): ?>
                    - <a href="uploads/<?= htmlspecialchars($mat['File_Path']) ?>" target="_blank">Download/View</a>
                <?php elseif ($mat['Content']): ?>
                    - Content Available
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <?php if ($quizCount > 0): ?>
        <a href="dashboard.php">Start Quiz / View Scores</a>
    <?php else: ?>
        <p>No quizzes available for this course yet.</p>
    <?php endif; ?>
<?php endif; ?>

<?php include '../common/footer.php'; ?>
</body>
</html>
