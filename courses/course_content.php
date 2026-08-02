<?php
// courses/course_content.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int) ($_GET['course_id'] ?? 0);
$weekNum  = (int) ($_GET['week'] ?? 1);
$userId   = currentUserId();

$chk = $connection->prepare('SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
$chk->bind_param('ii', $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
    header('Location: ' . BASE_PATH . "/courses/course_overview.php?id=$courseId");
    exit();
}

$course   = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId")->fetch_assoc();
if (!$course) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$allWeeks = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$week     = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId AND Week_Number=$weekNum")->fetch_assoc();
$quiz     = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();

$totalWeeks = $allWeeks->num_rows;
$prevWeek   = $weekNum > 1 ? $weekNum - 1 : null;
$nextWeek   = $weekNum < $totalWeeks ? $weekNum + 1 : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($course['Title']) ?> — Week <?= $weekNum ?></title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/course_content.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="content-layout">

  <aside class="c-sidebar">
    <div class="c-sidebar-title">
      <i class="fas fa-book"></i>
      <h3><?= htmlspecialchars($course['Title']) ?></h3>
    </div>
    <div>
      <p class="c-nav-label">Course Content</p>
      <?php $allWeeks->data_seek(0); while ($w = $allWeeks->fetch_assoc()): ?>
      <a href="course_content.php?course_id=<?= $courseId ?>&week=<?= (int) $w['Week_Number'] ?>"
         class="c-nav-link <?= $w['Week_Number'] == $weekNum ? 'active' : '' ?>">
        <span class="c-nav-num">W<?= (int) $w['Week_Number'] ?></span>
        <?= htmlspecialchars($w['Week_Title']) ?>
      </a>
      <?php endwhile; ?>
      <?php if ($quiz): ?>
      <a href="<?= BASE_PATH ?>/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="c-nav-link c-quiz-link">
        <i class="fas fa-circle-question"></i> Take Quiz
      </a>
      <?php endif; ?>
    </div>
    <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline btn-sm c-back-link">
      <i class="fas fa-arrow-left"></i> Course Overview
    </a>
  </aside>

  <main class="c-main">
    <?php if (!$week): ?>
      <div class="empty-state"><i class="fas fa-book-open"></i><h3>Week not found</h3><p>This week's content hasn't been added yet.</p></div>
    <?php else: ?>
    <span class="c-week-tag">Week <?= $weekNum ?></span>
    <h1><?= htmlspecialchars($week['Week_Title']) ?></h1>

    <?php if ($week['Video_File']): ?>
    <div class="c-video">
      <video controls>
        <source src="<?= BASE_PATH ?>/uploads/<?= htmlspecialchars($week['Video_File']) ?>" type="video/mp4">
        Your browser does not support the video element.
      </video>
    </div>
    <?php endif; ?>

    <?php if ($week['Description']): ?>
    <div class="c-lesson card" style="margin-bottom:20px">
      <h3><i class="fas fa-book-open"></i> Lesson Content</h3>
      <div class="prose"><?= nl2br(htmlspecialchars($week['Description'])) ?></div>
    </div>
    <?php endif; ?>

    <div class="c-resources">
      <?php if ($week['Resource_File']): ?>
      <a href="<?= BASE_PATH ?>/uploads/<?= htmlspecialchars($week['Resource_File']) ?>" class="res-link" download>
        <i class="fas fa-file-arrow-down"></i> Download Resource File
      </a>
      <?php endif; ?>
      <?php $safeLink = sanitize_url($week['Course_Link'] ?? ''); if ($safeLink): ?>
      <a href="<?= htmlspecialchars($safeLink) ?>" class="res-link" target="_blank" rel="noopener noreferrer">
        <i class="fas fa-arrow-up-right-from-square"></i> External Learning Resource
      </a>
      <?php endif; ?>
    </div>

    <div class="c-nav-btns">
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
        <a href="<?= BASE_PATH ?>/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="btn btn-primary">
          <i class="fas fa-circle-question"></i> Take Quiz
        </a>
      <?php else: ?>
        <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-primary">
          <i class="fas fa-check"></i> Back to Overview
        </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </main>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>