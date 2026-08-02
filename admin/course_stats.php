<?php
// admin/course_stats.php — platform-wide stats for super admin
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$topCourses = $connection->query(
    "SELECT c.Course_Id, c.Title, u.User_Name AS provider,
            COUNT(DISTINCT e.Enrollment_Id) AS enrolled,
            COALESCE(AVG(r.Rating),0) AS avg_rating,
            (SELECT AVG(t.Q_Marks) FROM takes t JOIN quiz q ON q.Quiz_Id=t.Quiz_Id WHERE q.Course_Id=c.Course_Id) AS avg_quiz
     FROM course c
     JOIN registered_user u ON u.Registered_User_Id=c.Provider_Id
     LEFT JOIN enrollment e ON e.Course_Id=c.Course_Id
     LEFT JOIN review r ON r.Course_Id=c.Course_Id
     GROUP BY c.Course_Id ORDER BY enrolled DESC LIMIT 20"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Statistics — Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Platform Statistics</h1>
  <p class="section-subtitle">Top courses ranked by enrolment</p>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Rank</th><th>Course</th><th>Provider</th><th>Enrolled</th><th>Avg Rating</th><th>Avg Quiz Score</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php if ($topCourses->num_rows === 0): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:28px">No course data yet.</td></tr>
        <?php endif; ?>
        <?php $rank = 0; while ($c = $topCourses->fetch_assoc()): $rank++; ?>
        <tr>
          <td><strong>#<?= $rank ?></strong></td>
          <td><?= htmlspecialchars($c['Title']) ?></td>
          <td><?= htmlspecialchars($c['provider']) ?></td>
          <td><?= (int) $c['enrolled'] ?></td>
          <td><?= render_stars((float) $c['avg_rating']) ?> <?= number_format((float) $c['avg_rating'], 1) ?></td>
          <td><?= $c['avg_quiz'] !== null ? number_format((float) $c['avg_quiz'], 1) . '%' : '—' ?></td>
          <td><a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i> View</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>