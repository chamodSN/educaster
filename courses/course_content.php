<?php
// courses/course_content.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId  = (int)($_GET['course_id'] ?? 0);
$weekNum   = (int)($_GET['week'] ?? 1);
$userId    = (int)$_SESSION['userData']['Registered_User_Id'];

// Verify enrollment
$chk = $connection->prepare("SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?");
$chk->bind_param("ii", $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
    header("Location: /educaster/courses/course_overview.php?id=$courseId"); exit();
}

$course  = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId")->fetch_assoc();
$allWeeks= $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$week    = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId AND Week_Number=$weekNum")->fetch_assoc();
$quiz    = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();

$totalWeeks  = $allWeeks->num_rows;
$prevWeek    = $weekNum > 1 ? $weekNum - 1 : null;
$nextWeek    = $weekNum < $totalWeeks ? $weekNum + 1 : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($course['Title']) ?> — Week <?= $weekNum ?></title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/course_content.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="content-layout">

  <!-- Sidebar -->
  <aside class="content-sidebar">
    <div class="sidebar-course-title">
      <i class="fas fa-book"></i>
      <h3><?= htmlspecialchars($course['Title']) ?></h3>
    </div>
    <div class="sidebar-nav">
      <p class="sidebar-label">Course Content</p>
      <?php $allWeeks->data_seek(0); while ($w = $allWeeks->fetch_assoc()): ?>
      <a href="course_content.php?course_id=<?= $courseId ?>&week=<?= $w['Week_Number'] ?>"
         class="sidebar-link <?= $w['Week_Number'] == $weekNum ? 'active' : '' ?>">
        <span class="sidebar-week-num">W<?= $w['Week_Number'] ?></span>
        <?= htmlspecialchars($w['Week_Title']) ?>
      </a>
      <?php endwhile; ?>
      <?php if ($quiz): ?>
      <a href="/educaster/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="sidebar-link quiz-link">
        <i class="fas fa-question-circle"></i> Take Quiz
      </a>
      <?php endif; ?>
    </div>
    <a href="/educaster/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline btn-sm" style="margin:16px">
      <i class="fas fa-arrow-left"></i> Course Overview
    </a>
  </aside>

  <!-- Main content -->
  <main class="content-main">
    <?php if (!$week): ?>
      <div class="empty-state"><i class="fas fa-book-open"></i><h3>Week not found</h3><p>This week's content has not been added yet.</p></div>
    <?php else: ?>
    <div class="content-header">
      <span class="content-week-badge">Week <?= $weekNum ?></span>
      <h1><?= htmlspecialchars($week['Week_Title']) ?></h1>
    </div>

    <?php if ($week['Video_File']): ?>
    <div class="content-video">
      <video controls>
        <source src="/educaster/uploads/<?= htmlspecialchars($week['Video_File']) ?>" type="video/mp4">
        Your browser does not support the video element.
      </video>
    </div>
    <?php endif; ?>

    <?php if ($week['Description']): ?>
    <div class="content-text card">
      <h3><i class="fas fa-book-open"></i> Lesson Content</h3>
      <div class="prose"><?= nl2br(htmlspecialchars($week['Description'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="content-resources">
      <?php if ($week['Resource_File']): ?>
      <a href="/educaster/uploads/<?= htmlspecialchars($week['Resource_File']) ?>" class="resource-link" download>
        <i class="fas fa-file-download"></i> Download Resource File
      </a>
      <?php endif; ?>
      <?php if ($week['Course_Link']): ?>
      <a href="<?= htmlspecialchars($week['Course_Link']) ?>" class="resource-link" target="_blank" rel="noopener">
        <i class="fas fa-external-link-alt"></i> External Learning Resource
      </a>
      <?php endif; ?>
    </div>

    <!-- Navigation -->
    <div class="content-nav-btns">
      <?php if ($prevWeek): ?>
        <a href="course_content.php?course_id=<?= $courseId ?>&week=<?= $prevWeek ?>" class="btn btn-outline">
          <i class="fas fa-chevron-left"></i> Previous Week
        </a>
      <?php else: ?>
        <span></span>
      <?php endif; ?>

      <?php if ($nextWeek): ?>
        <a href="course_content.php?course_id=<?= $courseId ?>&week=<?= $nextWeek ?>" class="btn btn-primary">
          Next Week <i class="fas fa-chevron-right"></i>
        </a>
      <?php elseif ($quiz): ?>
        <a href="/educaster/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="btn btn-primary">
          <i class="fas fa-question-circle"></i> Take Quiz
        </a>
      <?php else: ?>
        <a href="/educaster/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-primary">
          <i class="fas fa-check"></i> Complete Course
        </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </main>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>