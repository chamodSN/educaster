<?php
// provider/manage_courses.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];

$courses = $connection->query(
    "SELECT c.*, cat.Category_Name,
            COUNT(DISTINCT e.Enrollment_Id) AS enrolled
     FROM course c
     LEFT JOIN course_category cat ON cat.Category_Id=c.Category_Id
     LEFT JOIN enrollment e ON e.Course_Id=c.Course_Id
     WHERE c.Provider_Id=$providerId
     GROUP BY c.Course_Id ORDER BY c.Created_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses — Provider</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['updated'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Course updated!</div><?php endif; ?>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div><h1 class="section-title">My Courses</h1></div>
    <a href="create_course.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Course</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Title</th><th>Category</th><th>Enrolled</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($c = $courses->fetch_assoc()): ?>
        <tr>
          <td><strong><?= htmlspecialchars($c['Title']) ?></strong></td>
          <td><?= htmlspecialchars($c['Category_Name'] ?? '—') ?></td>
          <td><?= $c['enrolled'] ?></td>
          <td><span class="badge <?= $c['Is_Active'] ? 'badge-active' : 'badge-expired' ?>"><?= $c['Is_Active'] ? 'Active' : 'Inactive' ?></span></td>
          <td><?= $c['Due_Date'] ?: '—' ?></td>
          <td class="action-btns">
            <a href="edit_course.php?id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
            <a href="add_week.php?course_id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-list"></i> Weeks</a>
            <a href="manage_quiz.php?course_id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-question-circle"></i> Quiz</a>
            <a href="toggle_active.php?id=<?= $c['Course_Id'] ?>&status=<?= $c['Is_Active'] ?>" class="btn btn-sm <?= $c['Is_Active'] ? 'btn-danger':'btn-primary' ?>">
              <?= $c['Is_Active'] ? 'Deactivate':'Activate' ?>
            </a>
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