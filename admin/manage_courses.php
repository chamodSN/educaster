<?php
// admin/manage_courses.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$search = trim($_GET['search'] ?? '');
$where  = "WHERE 1=1";
if ($search) $where .= " AND (c.Title LIKE '%" . $connection->real_escape_string($search) . "%' OR u.User_Name LIKE '%" . $connection->real_escape_string($search) . "%')";

$courses = $connection->query(
    "SELECT c.*, u.User_Name AS provider, cat.Category_Name,
            COUNT(DISTINCT e.Enrollment_Id) AS enrolled,
            COALESCE(AVG(r.Rating),0) AS avg_rating
     FROM course c
     JOIN registered_user u ON u.Registered_User_Id=c.Provider_Id
     LEFT JOIN course_category cat ON cat.Category_Id=c.Category_Id
     LEFT JOIN enrollment e ON e.Course_Id=c.Course_Id
     LEFT JOIN review r ON r.Course_Id=c.Course_Id
     $where
     GROUP BY c.Course_Id
     ORDER BY c.Created_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses — Admin</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Course deleted.</div><?php endif; ?>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <h1 class="section-title">All Courses</h1>
    <form action="manage_courses.php" method="GET">
      <div style="display:flex;gap:8px">
        <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search title or provider..." style="width:280px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        <?php if ($search): ?><a href="manage_courses.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
      </div>
    </form>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>ID</th><th>Title</th><th>Provider</th><th>Category</th><th>Enrolled</th><th>Rating</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($c = $courses->fetch_assoc()): ?>
        <tr>
          <td>#<?= $c['Course_Id'] ?></td>
          <td><strong><?= htmlspecialchars($c['Title']) ?></strong></td>
          <td><?= htmlspecialchars($c['provider']) ?></td>
          <td><?= htmlspecialchars($c['Category_Name'] ?? '—') ?></td>
          <td><?= $c['enrolled'] ?></td>
          <td>
            <span style="color:#f39c12"><?= str_repeat('★', round($c['avg_rating'])) ?></span>
            <?= number_format($c['avg_rating'],1) ?>
          </td>
          <td><span class="badge <?= $c['Is_Active'] ? 'badge-active':'badge-expired' ?>"><?= $c['Is_Active'] ? 'Active':'Inactive' ?></span></td>
          <td><?= $c['Due_Date'] ?: '—' ?></td>
          <td class="action-btns">
            <a href="/educaster/courses/course_overview.php?id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i> View</a>
            <a href="toggle_course.php?id=<?= $c['Course_Id'] ?>&status=<?= $c['Is_Active'] ?>" class="btn btn-sm btn-outline">
              <i class="fas fa-<?= $c['Is_Active'] ? 'pause':'play' ?>"></i>
            </a>
            <a href="delete_course.php?id=<?= $c['Course_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course permanently?')"><i class="fas fa-trash"></i></a>
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