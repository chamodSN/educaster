<?php
session_start();
require 'common/config.php';

if (!isset($_SESSION['userData'])) {
    header('Location: /user/login.php');
    exit();
}

$userId = $_SESSION['userData']['Registered_User_Id'];

$sql = "
    SELECT c.Course_Id, c.Title, e.Enrolled_At,
           q.Quiz_Id, q.Title as Quiz_Title,
           (SELECT MAX(r.Score) 
            FROM quiz_result r 
            WHERE r.Registered_User_Id = ? AND r.Quiz_Id = q.Quiz_Id) AS Best_Score
    FROM Enrollment e
    JOIN Course c ON e.Course_Id = c.Course_Id
    LEFT JOIN Quiz q ON q.Course_Id = c.Course_Id
    WHERE e.Registered_User_Id = ?
    ORDER BY c.Title
";


$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[$row['Course_Id']]['Title'] = $row['Title'];
    $courses[$row['Course_Id']]['Enrolled_At'] = $row['Enrolled_At'];
    if ($row['Quiz_Id']) {
        $courses[$row['Course_Id']]['Quizzes'][$row['Quiz_Id']] = [
            'Title' => $row['Quiz_Title'],
            'Best_Score' => $row['Best_Score']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['userData']['User_Name']) ?></h1>

<h2>Your Enrolled Courses & Quiz Scores</h2>

<?php if (empty($courses)): ?>
    <p>You are not enrolled in any courses yet.</p>
<?php else: ?>
    <?php foreach ($courses as $courseId => $course): ?>
        <div class="course-block">
            <h3><a href="course_description.php?id=<?= $courseId ?>"><?= htmlspecialchars($course['Title']) ?></a></h3>
            <p>Enrolled on: <?= htmlspecialchars($course['Enrolled_At']) ?></p>
            <?php if (!empty($course['Quizzes'])): ?>
                <table>
                    <thead>
                        <tr><th>Quiz</th><th>Best Score</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($course['Quizzes'] as $quizId => $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz['Title']) ?></td>
                            <td><?= $quiz['Best_Score'] ?? 'Not taken' ?></td>
                            <td><a href="start_quiz.php?id=<?= $quizId ?>">Start / Retake</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No quizzes for this course yet.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'common/footer.php'; ?>
</body>
</html>
