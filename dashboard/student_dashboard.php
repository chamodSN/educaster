<?php
// dashboard/student_dashboard.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if (isAdmin() || isProvider()) {
    redirectToDashboard();
}

$userId = currentUserId();

$enrolledCourses = $connection->query(
    "SELECT c.Course_Id, c.Title, c.Due_Date, c.Is_Active, cat.Category_Name,
            e.Enrolled_At, e.Is_Completed,
            (SELECT Q_Marks FROM takes t JOIN quiz q2 ON q2.Quiz_Id=t.Quiz_Id WHERE q2.Course_Id=c.Course_Id AND t.Registered_User_Id=$userId LIMIT 1) AS quiz_marks,
            (SELECT COUNT(*) FROM weekly_course WHERE Course_Id=c.Course_Id) AS total_weeks
     FROM enrollment e
     JOIN course c ON c.Course_Id=e.Course_Id
     LEFT JOIN course_category cat ON cat.Category_Id=c.Category_Id
     WHERE e.Registered_User_Id=$userId
     ORDER BY e.Enrolled_At DESC"
);

$totalEnrolled  = $enrolledCourses->num_rows;
$completedCount = $connection->query("SELECT COUNT(*) as t FROM enrollment WHERE Registered_User_Id=$userId AND Is_Completed=1")->fetch_assoc()['t'];
$inquiryCount   = $connection->query("SELECT COUNT(*) as t FROM inquiry WHERE Registered_User_Id=$userId")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">

  <div class="dash-welcome">
    <div>
      <h1>Welcome back, <?= htmlspecialchars($_SESSION['userData']['User_Name']) ?>! 👋</h1>
      <p>Continue your learning journey.</p>
    </div>
    <a href="<?= BASE_PATH ?>/programs.php" class="btn btn-primary"><i class="fas fa-plus"></i> Find More Courses</a>
  </div>

  <div class="dash-stats">
    <div class="dash-stat"><i class="fas fa-book"></i><div><strong><?= (int) $totalEnrolled ?></strong><span>Enrolled</span></div></div>
    <div class="dash-stat"><i class="fas fa-circle-check" style="color:var(--green)"></i><div><strong><?= (int) $completedCount ?></strong><span>Completed</span></div></div>
    <a href="<?= BASE_PATH ?>/quiz/quiz_results.php" class="dash-stat" style="text-decoration:none"><i class="fas fa-chart-bar" style="color:var(--blue)"></i><div><strong>View</strong><span>Quiz Results</span></div></a>
    <a href="<?= BASE_PATH ?>/customerSupport/myInquiries.php" class="dash-stat" style="text-decoration:none"><i class="fas fa-circle-question" style="color:var(--purple)"></i><div><strong><?= (int) $inquiryCount ?></strong><span>My Inquiries</span></div></a>
  </div>

  <div class="dash-search">
    <input type="text" id="dashSearch" class="form-control" placeholder="🔍 Search your enrolled courses...">
  </div>

  <h2 class="section-title" style="margin-top:24px">My Courses</h2>
  <?php if ($totalEnrolled === 0): ?>
    <div class="empty-state">
      <i class="fas fa-book-open"></i>
      <h3>No courses yet</h3>
      <p>Browse our catalogue and enroll in your first course.</p>
      <a href="<?= BASE_PATH ?>/programs.php" class="btn btn-primary">Browse Courses</a>
    </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table id="coursesTable">
      <thead>
        <tr><th>Course</th><th>Category</th><th>Status</th><th>Due Date</th><th>Quiz Score</th><th>Enrolled</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $enrolledCourses->fetch_assoc()):
            $expired     = $row['Due_Date'] && $row['Due_Date'] < date('Y-m-d');
            $statusClass = $row['Is_Completed'] ? 'badge-complete' : ($expired ? 'badge-expired' : 'badge-active');
            $statusText  = $row['Is_Completed'] ? 'Completed' : ($expired ? 'Expired' : 'Active');
        ?>
        <tr>
          <td><strong><?= htmlspecialchars($row['Title']) ?></strong></td>
          <td><?= htmlspecialchars($row['Category_Name'] ?? '—') ?></td>
          <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
          <td><?= $row['Due_Date'] ? format_date($row['Due_Date']) : '—' ?></td>
          <td><?= $row['quiz_marks'] !== null ? htmlspecialchars($row['quiz_marks']) . '%' : 'N/A' ?></td>
          <td><?= format_date($row['Enrolled_At']) ?></td>
          <td class="action-btns">
            <a href="<?= BASE_PATH ?>/courses/course_content.php?course_id=<?= (int) $row['Course_Id'] ?>&week=1" class="btn btn-sm btn-primary"><i class="fas fa-play"></i> Continue</a>
            <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= (int) $row['Course_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i> View</a>
            <form action="<?= BASE_PATH ?>/courses/unenroll.php" method="POST" style="display:inline" onsubmit="return confirm('Unenroll from this course?')">
              <?= csrf_field() ?>
              <input type="hidden" name="course_id" value="<?= (int) $row['Course_Id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Unenroll</button>
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
<script>
document.getElementById('dashSearch').addEventListener('input', function () {
  const val = this.value.toLowerCase();
  document.querySelectorAll('#coursesTable tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(val) ? '' : 'none';
  });
});
</script>
</body>
</html>