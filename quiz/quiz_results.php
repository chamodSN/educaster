<?php
// quiz/quiz_results.php — view past results
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId    = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId  = (int)($_GET['course_id'] ?? 0);

$results = $connection->query(
    "SELECT t.*, q.Title AS quiz_title, c.Title AS course_title, c.Course_Id
     FROM takes t
     JOIN quiz q ON q.Quiz_Id = t.Quiz_Id
     JOIN course c ON c.Course_Id = q.Course_Id
     WHERE t.Registered_User_Id = $userId
     " . ($courseId ? "AND c.Course_Id = $courseId" : "") . "
     ORDER BY t.Taken_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Results — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/quiz.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">My Quiz Results</h1>
  <p class="section-subtitle">Track your performance across all courses</p>

  <?php if ($results->num_rows === 0): ?>
  <div class="empty-state">
    <i class="fas fa-chart-bar"></i>
    <h3>No quiz results yet</h3>
    <p>Complete a course quiz to see your results here.</p>
    <a href="/educaster/programs.php" class="btn btn-primary">Browse Courses</a>
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Course</th><th>Quiz</th><th>Score</th><th>Grade</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php while ($r = $results->fetch_assoc()):
            $grade = $r['Q_Marks'] >= 80 ? 'A' : ($r['Q_Marks'] >= 60 ? 'B' : ($r['Q_Marks'] >= 40 ? 'C' : 'F'));
            $gradeClass = $r['Q_Marks'] >= 60 ? 'badge-active' : 'badge-expired';
        ?>
        <tr>
          <td><?= htmlspecialchars($r['course_title']) ?></td>
          <td><?= htmlspecialchars($r['quiz_title']) ?></td>
          <td><strong><?= $r['Q_Marks'] ?>%</strong></td>
          <td><span class="badge <?= $gradeClass ?>"><?= $grade ?></span></td>
          <td><?= date('M j, Y', strtotime($r['Taken_At'])) ?></td>
          <td>
            <a href="/educaster/quiz/start_quiz.php?course_id=<?= $r['Course_Id'] ?>" class="btn btn-sm btn-outline">
              <i class="fas fa-redo"></i> Retake
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>