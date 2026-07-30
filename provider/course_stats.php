<?php
// provider/course_stats.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId   = (int)($_GET['id'] ?? 0);

$course = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId AND Provider_Id=$providerId")->fetch_assoc();
if (!$course) { header("Location: manage_courses.php"); exit(); }

$enrollCount   = $connection->query("SELECT COUNT(*) AS t FROM enrollment WHERE Course_Id=$courseId")->fetch_assoc()['t'];
$completedCount= $connection->query("SELECT COUNT(*) AS t FROM enrollment WHERE Course_Id=$courseId AND Is_Completed=1")->fetch_assoc()['t'];
$avgRating     = $connection->query("SELECT COALESCE(AVG(Rating),0) AS a FROM review WHERE Course_Id=$courseId")->fetch_assoc()['a'];
$reviewCount   = $connection->query("SELECT COUNT(*) AS t FROM review WHERE Course_Id=$courseId")->fetch_assoc()['t'];

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
$avgScore = 0; $attemptCount = 0;
if ($quiz) {
    $res = $connection->query("SELECT AVG(Q_Marks) AS a, COUNT(*) AS c FROM takes WHERE Quiz_Id=".$quiz['Quiz_Id'])->fetch_assoc();
    $avgScore    = round($res['a'] ?? 0, 1);
    $attemptCount= (int)($res['c'] ?? 0);
}

$enrollees = $connection->query(
    "SELECT u.User_Name, u.Email, e.Enrolled_At, e.Is_Completed,
            (SELECT Q_Marks FROM takes t JOIN quiz q2 ON q2.Quiz_Id=t.Quiz_Id WHERE q2.Course_Id=$courseId AND t.Registered_User_Id=u.Registered_User_Id LIMIT 1) AS quiz_score
     FROM enrollment e JOIN registered_user u ON u.Registered_User_Id=e.Registered_User_Id
     WHERE e.Course_Id=$courseId ORDER BY e.Enrolled_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Stats — <?= htmlspecialchars($course['Title']) ?></title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title"><?= htmlspecialchars($course['Title']) ?></h1>
  <p class="section-subtitle">Course Statistics & Learner Overview</p>

  <div class="admin-stats" style="margin-bottom:32px">
    <div class="admin-stat-card"><i class="fas fa-users"></i><div><strong><?= $enrollCount ?></strong><span>Enrolled</span></div></div>
    <div class="admin-stat-card"><i class="fas fa-check-circle" style="color:#27ae60"></i><div><strong><?= $completedCount ?></strong><span>Completed</span></div></div>
    <div class="admin-stat-card"><i class="fas fa-star" style="color:#f39c12"></i><div><strong><?= number_format($avgRating,1) ?></strong><span>Avg Rating (<?= $reviewCount ?> reviews)</span></div></div>
    <?php if ($quiz): ?>
    <div class="admin-stat-card"><i class="fas fa-percent" style="color:#3498db"></i><div><strong><?= $avgScore ?>%</strong><span>Avg Quiz Score (<?= $attemptCount ?> attempts)</span></div></div>
    <?php endif; ?>
  </div>

  <h2 style="font-size:20px;font-weight:700;margin-bottom:16px">Enrolled Learners</h2>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Username</th><th>Email</th><th>Enrolled</th><th>Status</th><th>Quiz Score</th></tr></thead>
      <tbody>
        <?php while ($e = $enrollees->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($e['User_Name']) ?></td>
          <td><?= htmlspecialchars($e['Email']) ?></td>
          <td><?= date('M j, Y', strtotime($e['Enrolled_At'])) ?></td>
          <td><span class="badge <?= $e['Is_Completed'] ? 'badge-complete' : 'badge-pending' ?>"><?= $e['Is_Completed'] ? 'Completed' : 'In Progress' ?></span></td>
          <td><?= $e['quiz_score'] !== null ? $e['quiz_score'].'%' : '—' ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top:20px">
    <a href="manage_courses.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>