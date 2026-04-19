<?php
session_start();
require '../common/config.php';

$userId = $_SESSION['userData']['Registered_User_Id'] ?? null;

$courses = $connection->query("SELECT * FROM Course");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Courses - Educaster</title>
</head>
<body>
<?php include '../common/header.php'; ?>

<h1>Available Courses</h1>
<div class="course-list">
    <?php while ($course = $courses->fetch_assoc()): ?>
        <div class="course-card">
            <?php if ($course['Intro_Image']): ?>
                <img src="/educaster/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" alt="<?= htmlspecialchars($course['Title']) ?>">
            <?php endif; ?>
            <h2><?= htmlspecialchars($course['Title']) ?></h2>
            <p><?= nl2br(htmlspecialchars(substr($course['Description'], 0, 150))) ?>...</p>
            <a href="course_detail.php?id=<?= $course['Course_Id'] ?>">View Details</a>
        </div>
    <?php endwhile; ?>
</div>

<?php include '../common/footer.php'; ?>
</body>
</html>
