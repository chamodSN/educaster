<?php
// admin/admin_dashboard.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$teacherCount  = $connection->query("SELECT COUNT(*) AS t FROM registered_user WHERE Registered_User_Type='TCH'")->fetch_assoc()['t'];
$providerCount = $connection->query("SELECT COUNT(*) AS t FROM registered_user WHERE Registered_User_Type='INS' AND Is_Approved=1")->fetch_assoc()['t'];
$courseCount   = $connection->query("SELECT COUNT(*) AS t FROM course")->fetch_assoc()['t'];
$enrollCount   = $connection->query("SELECT COUNT(*) AS t FROM enrollment")->fetch_assoc()['t'];
$pendingCount  = $connection->query("SELECT COUNT(*) AS t FROM provider_request WHERE Status='pending'")->fetch_assoc()['t'];
$unanswered    = $connection->query("SELECT COUNT(*) AS t FROM inquiry WHERE Reply IS NULL")->fetch_assoc()['t'];

$courses = $connection->query("SELECT c.*, u.User_Name AS provider,
  (SELECT COUNT(*) FROM enrollment e WHERE e.Course_Id=c.Course_Id) AS enrolled
  FROM course c
  JOIN registered_user u ON u.Registered_User_Id=c.Provider_Id
  ORDER BY c.Created_At DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <div class="dash-welcome">
    <div><h1>Admin Dashboard</h1><p>Full system overview and control.</p></div>
    <?php if ($pendingCount > 0): ?>
      <a href="manage_providers.php" class="btn btn-primary"><i class="fas fa-bell"></i> <?= $pendingCount ?> Pending Approval<?= $pendingCount>1?'s':'' ?></a>
    <?php endif; ?>
  </div>

  <!-- Stats Grid -->
  <div class="admin-stats">
    <div class="admin-stat-card"><i class="fas fa-users"></i><div><strong><?= $teacherCount ?></strong><span>Teachers</span></div></div>
    <div class="admin-stat-card"><i class="fas fa-chalkboard-teacher"></i><div><strong><?= $providerCount ?></strong><span>Course Providers</span></div></div>
    <div class="admin-stat-card"><i class="fas fa-book"></i><div><strong><?= $courseCount ?></strong><span>Total Courses</span></div></div>
    <div class="admin-stat-card"><i class="fas fa-user-check"></i><div><strong><?= $enrollCount ?></strong><span>Enrollments</span></div></div>
    <div class="admin-stat-card" style="border-left-color:#f39c12"><i class="fas fa-clock" style="color:#f39c12"></i><div><strong><?= $pendingCount ?></strong><span>Pending Providers</span></div></div>
    <div class="admin-stat-card" style="border-left-color:#3498db"><i class="fas fa-envelope" style="color:#3498db"></i><div><strong><?= $unanswered ?></strong><span>Unanswered Inquiries</span></div></div>
  </div>

  <!-- Quick Nav -->
  <div class="admin-quick-nav">
    <a href="manage_teachers.php" class="quick-link"><i class="fas fa-users"></i> Manage Teachers</a>
    <a href="manage_providers.php" class="quick-link"><i class="fas fa-chalkboard-teacher"></i> Manage Providers</a>
    <a href="manage_courses.php" class="quick-link"><i class="fas fa-book"></i> Manage Courses</a>
    <a href="manage_inquiries.php" class="quick-link"><i class="fas fa-envelope"></i> Inquiries</a>
  </div>

  <!-- Courses Table -->
  <h2 class="section-title" style="margin-top:40px">All Courses</h2>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>ID</th><th>Title</th><th>Provider</th><th>Enrolled</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($c = $courses->fetch_assoc()):
            $exp = $c['Due_Date'] && $c['Due_Date'] < date('Y-m-d');
        ?>
        <tr>
          <td>#<?= $c['Course_Id'] ?></td>
          <td><strong><?= htmlspecialchars($c['Title']) ?></strong></td>
          <td><?= htmlspecialchars($c['provider']) ?></td>
          <td><?= $c['enrolled'] ?></td>
          <td><span class="badge <?= $c['Is_Active'] ? 'badge-active' : 'badge-expired' ?>"><?= $c['Is_Active'] ? 'Active' : 'Inactive' ?></span></td>
          <td><?= $c['Due_Date'] ?: '—' ?></td>
          <td class="action-btns">
            <a href="toggle_course.php?id=<?= $c['Course_Id'] ?>&status=<?= $c['Is_Active'] ?>" class="btn btn-sm btn-outline">
              <i class="fas fa-<?= $c['Is_Active'] ? 'pause' : 'play' ?>"></i> <?= $c['Is_Active'] ? 'Deactivate' : 'Activate' ?>
            </a>
            <a href="/educaster/courses/course_overview.php?id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> View</a>
            <a href="delete_course.php?id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course and all its content?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>