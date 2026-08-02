<?php
// provider/manage_courses.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();

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
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['updated'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Course updated!</div><?php endif; ?>
  <div class="list-toolbar">
    <h1 class="section-title">My Courses</h1>
    <a href="create_course.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Course</a>
  </div>

  <?php if ($courses->num_rows === 0): ?>
    <div class="empty-state">
      <i class="fas fa-book-open"></i>
      <h3>No courses yet</h3>
      <p>Create your first course to get started.</p>
      <a href="create_course.php" class="btn btn-primary">Create Course</a>
    </div>
  <?php else: ?>
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
          <td><?= (int) $c['enrolled'] ?></td>
          <td><span class="badge <?= $c['Is_Active'] ? 'badge-active' : 'badge-expired' ?>"><?= $c['Is_Active'] ? 'Active' : 'Inactive' ?></span></td>
          <td><?= $c['Due_Date'] ? format_date($c['Due_Date']) : '—' ?></td>
          <td class="action-btns">
            <a href="edit_course.php?id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
            <a href="add_week.php?course_id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-list"></i> Weeks</a>
            <a href="manage_quiz.php?course_id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-question-circle"></i> Quiz</a>
            <a href="course_stats.php?id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-chart-bar"></i> Stats</a>
            <form action="toggle_active.php" method="POST">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $c['Course_Id'] ?>">
              <input type="hidden" name="status" value="<?= (int) $c['Is_Active'] ?>">
              <button type="submit" class="btn btn-sm <?= $c['Is_Active'] ? 'btn-danger' : 'btn-primary' ?>"><?= $c['Is_Active'] ? 'Deactivate' : 'Activate' ?></button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>