<?php
// provider/manage_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId   = (int)($_GET['course_id'] ?? 0);

$course = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId AND Provider_Id=$providerId")->fetch_assoc();
if (!$course) { header("Location: manage_courses.php"); exit(); }

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Quiz — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Quiz Management</h1>
  <p class="section-subtitle">Course: <?= htmlspecialchars($course['Title']) ?></p>

  <?php if (!$quiz): ?>
  <div class="quiz-empty-card">
    <i class="fas fa-question-circle"></i>
    <h3>No quiz yet</h3>
    <p>Create a quiz for this course. Each course can have one quiz with multiple questions.</p>
    <a href="add_quiz.php?course_id=<?= $courseId ?>" class="btn btn-primary">
      <i class="fas fa-plus"></i> Create Quiz
    </a>
  </div>
  <?php else: ?>
  <div class="card" style="margin-bottom:24px">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div>
        <h3><?= htmlspecialchars($quiz['Title']) ?></h3>
        <?php
        $qCount = $connection->query("SELECT COUNT(*) AS t FROM quiz_question WHERE Quiz_Id=".$quiz['Quiz_Id'])->fetch_assoc()['t'];
        echo "<p style='color:var(--text-muted);margin-top:4px'>$qCount questions</p>";
        ?>
      </div>
      <div class="action-btns">
        <a href="edit_quiz.php?id=<?= $quiz['Quiz_Id'] ?>" class="btn btn-outline"><i class="fas fa-edit"></i> Edit Quiz</a>
        <a href="delete_quiz.php?id=<?= $quiz['Quiz_Id'] ?>&course_id=<?= $courseId ?>" class="btn btn-danger" onclick="return confirm('Delete this quiz and all questions?')">
          <i class="fas fa-trash"></i> Delete Quiz
        </a>
      </div>
    </div>
  </div>

  <!-- Questions list -->
  <?php
  $questions = $connection->query("SELECT q.*, (SELECT COUNT(*) FROM quiz_option o WHERE o.Question_Id=q.Question_Id) AS opt_count FROM quiz_question q WHERE q.Quiz_Id=".$quiz['Quiz_Id']." ORDER BY q.Question_Id ASC");
  $qNum = 0;
  while ($q = $questions->fetch_assoc()):
      $qNum++;
  ?>
  <div class="card" style="margin-bottom:16px;border-left:4px solid var(--green)">
    <p style="font-size:12px;color:var(--green);font-weight:700;margin-bottom:6px">Q<?= $qNum ?></p>
    <strong><?= htmlspecialchars($q['Question_Text']) ?></strong>
    <p style="font-size:13px;color:var(--text-muted);margin-top:4px"><?= $q['opt_count'] ?> options</p>
    <?php if ($q['Explanation']): ?>
    <p style="font-size:13px;color:#1a5276;margin-top:6px"><i class="fas fa-info-circle"></i> <?= htmlspecialchars($q['Explanation']) ?></p>
    <?php endif; ?>
  </div>
  <?php endwhile; ?>
  <?php endif; ?>

  <div style="margin-top:20px">
    <a href="manage_courses.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to My Courses</a>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>