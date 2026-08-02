<?php
// admin/manage_courses.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$search = trim($_GET['search'] ?? '');

$sql = "SELECT c.*, u.User_Name AS provider, cat.Category_Name,
               COUNT(DISTINCT e.Enrollment_Id) AS enrolled,
               COALESCE(AVG(r.Rating),0) AS avg_rating
        FROM course c
        JOIN registered_user u ON u.Registered_User_Id=c.Provider_Id
        LEFT JOIN course_category cat ON cat.Category_Id=c.Category_Id
        LEFT JOIN enrollment e ON e.Course_Id=c.Course_Id
        LEFT JOIN review r ON r.Course_Id=c.Course_Id";

if ($search !== '') {
    $sql .= ' WHERE (c.Title LIKE ? OR u.User_Name LIKE ?) GROUP BY c.Course_Id ORDER BY c.Created_At DESC';
    $like = '%' . $search . '%';
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $courses = $stmt->get_result();
} else {
    $sql .= ' GROUP BY c.Course_Id ORDER BY c.Created_At DESC';
    $courses = $connection->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses — Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Course deleted.</div><?php endif; ?>
  <div class="list-toolbar">
    <h1 class="section-title">All Courses</h1>
    <form action="manage_courses.php" method="GET" class="search-form">
      <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search title or provider...">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
      <?php if ($search): ?><a href="manage_courses.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>ID</th><th>Title</th><th>Provider</th><th>Category</th><th>Enrolled</th><th>Rating</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($courses->num_rows === 0): ?>
          <tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:28px">No courses found.</td></tr>
        <?php endif; ?>
        <?php while ($c = $courses->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int) $c['Course_Id'] ?></td>
          <td><strong><?= htmlspecialchars($c['Title']) ?></strong></td>
          <td><?= htmlspecialchars($c['provider']) ?></td>
          <td><?= htmlspecialchars($c['Category_Name'] ?? '—') ?></td>
          <td><?= (int) $c['enrolled'] ?></td>
          <td><?= render_stars((float) $c['avg_rating']) ?> <?= number_format((float) $c['avg_rating'], 1) ?></td>
          <td><span class="badge <?= $c['Is_Active'] ? 'badge-active' : 'badge-expired' ?>"><?= $c['Is_Active'] ? 'Active' : 'Inactive' ?></span></td>
          <td><?= $c['Due_Date'] ? format_date($c['Due_Date']) : '—' ?></td>
          <td class="action-btns">
            <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= (int) $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
            <form action="toggle_course.php" method="POST">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $c['Course_Id'] ?>">
              <input type="hidden" name="status" value="<?= (int) $c['Is_Active'] ?>">
              <input type="hidden" name="from" value="courses">
              <button type="submit" class="btn btn-sm btn-outline"><i class="fas fa-<?= $c['Is_Active'] ? 'pause' : 'play' ?>"></i></button>
            </form>
            <form action="delete_course.php" method="POST" onsubmit="return confirm('Delete this course permanently?')">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $c['Course_Id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
          </td>
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