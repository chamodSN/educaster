<?php
// provider/provider_dashboard.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];

$courseCount = $connection->query(
    "SELECT COUNT(*) AS t FROM course WHERE Provider_Id=$providerId"
)->fetch_assoc()['t'];

$enrollCount = $connection->query(
    "SELECT COUNT(*) AS t FROM enrollment e
     JOIN course c ON c.Course_Id=e.Course_Id
     WHERE c.Provider_Id=$providerId"
)->fetch_assoc()['t'];

$pendingInq = $connection->query(
    "SELECT COUNT(*) AS t FROM inquiry i
     JOIN course c ON c.Course_Id=i.Course_Id
     WHERE c.Provider_Id=$providerId AND i.Reply IS NULL"
)->fetch_assoc()['t'];

$courses = $connection->query(
    "SELECT c.*, cat.Category_Name,
            COUNT(DISTINCT e.Enrollment_Id) AS enrolled,
            COALESCE(AVG(r.Rating), 0)      AS avg_rating
     FROM course c
     LEFT JOIN course_category cat ON cat.Category_Id = c.Category_Id
     LEFT JOIN enrollment e        ON e.Course_Id = c.Course_Id
     LEFT JOIN review r            ON r.Course_Id = c.Course_Id
     WHERE c.Provider_Id = $providerId
     GROUP BY c.Course_Id
     ORDER BY c.Created_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Provider Dashboard — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrap">

  <!-- Welcome -->
  <div class="admin-welcome">
    <div>
      <h1>Welcome, <?= htmlspecialchars($_SESSION['userData']['User_Name']) ?>!</h1>
      <p>Manage your courses and track learner progress.</p>
    </div>
    <a href="create_course.php" class="btn-green">
      <i class="fas fa-plus"></i> New Course
    </a>
  </div>

  <!-- Stats -->
  <div class="admin-stats">
    <div class="a-stat">
      <i class="fas fa-book"></i>
      <div><strong><?= $courseCount ?></strong><span>My Courses</span></div>
    </div>
    <div class="a-stat">
      <i class="fas fa-users"></i>
      <div><strong><?= $enrollCount ?></strong><span>Total Enrolled</span></div>
    </div>
    <div class="a-stat warn">
      <i class="fas fa-envelope"></i>
      <div><strong><?= $pendingInq ?></strong><span>Pending Inquiries</span></div>
    </div>
    <a href="provider_inquiries.php" class="a-stat info" style="text-decoration:none;cursor:pointer">
      <i class="fas fa-reply"></i>
      <div><strong>View</strong><span>Inquiries</span></div>
    </a>
  </div>

  <!-- Courses -->
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <h2 class="section-title">My Courses</h2>
    <a href="create_course.php" class="btn-outline btn-sm"><i class="fas fa-plus"></i> Add Course</a>
  </div>

  <?php if ($courseCount === 0): ?>
    <div class="empty-box">
      <i class="fas fa-book-open"></i>
      <h3>No courses yet</h3>
      <p>Create your first course to start teaching.</p>
      <a href="create_course.php" class="btn-green">Create Course</a>
    </div>
  <?php else: ?>
  <div class="prov-grid">
    <?php while ($c = $courses->fetch_assoc()):
        $stars = round($c['avg_rating']);
    ?>
    <div class="prov-card">
      <div class="prov-card-top">
        <div>
          <span class="pill"><?= htmlspecialchars($c['Category_Name'] ?? 'General') ?></span>
          <h4><?= htmlspecialchars($c['Title']) ?></h4>
        </div>
        <span class="badge <?= $c['Is_Active'] ? 'badge-active' : 'badge-expired' ?>">
          <?= $c['Is_Active'] ? 'Active' : 'Off' ?>
        </span>
      </div>

      <div class="prov-card-meta">
        <span><i class="fas fa-users"></i> <?= $c['enrolled'] ?> enrolled</span>
        <span><i class="fas fa-star" style="color:#f59e0b"></i> <?= number_format($c['avg_rating'],1) ?></span>
        <span><i class="fas fa-calendar"></i> <?= $c['Due_Date'] ?: 'No deadline' ?></span>
      </div>

      <div class="prov-card-actions">
        <a href="edit_course.php?id=<?= $c['Course_Id'] ?>" class="btn-outline btn-sm">
          <i class="fas fa-edit"></i> Edit
        </a>
        <a href="add_week.php?course_id=<?= $c['Course_Id'] ?>" class="btn-outline btn-sm">
          <i class="fas fa-list"></i> Weeks
        </a>
        <a href="manage_quiz.php?course_id=<?= $c['Course_Id'] ?>" class="btn-outline btn-sm">
          <i class="fas fa-question-circle"></i> Quiz
        </a>
        <a href="course_stats.php?id=<?= $c['Course_Id'] ?>" class="btn-green btn-sm">
          <i class="fas fa-chart-bar"></i> Stats
        </a>
        <a href="toggle_active.php?id=<?= $c['Course_Id'] ?>&status=<?= $c['Is_Active'] ?>"
           class="<?= $c['Is_Active'] ? 'btn-red' : 'btn-green' ?> btn-sm">
          <i class="fas fa-<?= $c['Is_Active'] ? 'pause' : 'play' ?>"></i>
          <?= $c['Is_Active'] ? 'Deactivate' : 'Activate' ?>
        </a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>

</div>
<?php include '../common/footer.php'; ?>
<script src="/educaster/js/main.js"></script>
</body>
</html>