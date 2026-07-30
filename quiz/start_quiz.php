<?php
// quiz/start_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int)($_GET['course_id'] ?? 0);
$userId   = (int)$_SESSION['userData']['Registered_User_Id'];

$chk = $connection->prepare("SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?");
$chk->bind_param("ii", $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) { header("Location: /educaster/programs.php"); exit(); }

$quiz    = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
$course  = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();
if (!$quiz) { header("Location: /educaster/courses/course_overview.php?id=$courseId"); exit(); }

$qCount  = $connection->query("SELECT COUNT(*) AS t FROM quiz_question WHERE Quiz_Id=".$quiz['Quiz_Id'])->fetch_assoc()['t'];
$prevTake= $connection->query("SELECT * FROM takes WHERE Quiz_Id=".$quiz['Quiz_Id']." AND Registered_User_Id=$userId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Start Quiz — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/quiz.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="quiz-wrapper" style="max-width:600px;margin:0 auto">
    <div class="quiz-start-card">
      <div class="quiz-start-icon"><i class="fas fa-question-circle"></i></div>
      <h1><?= htmlspecialchars($quiz['Title']) ?></h1>
      <p class="quiz-course-name"><?= htmlspecialchars($course['Title']) ?></p>

      <?php if ($prevTake): ?>
      <div class="alert alert-info" style="text-align:left">
        <i class="fas fa-info-circle"></i>
        You previously scored <strong><?= $prevTake['Q_Marks'] ?>%</strong> on this quiz.
        Taking it again will overwrite your previous score.
      </div>
      <?php endif; ?>

      <div class="quiz-info-grid">
        <div class="qi"><i class="fas fa-list-ol"></i><span><?= $qCount ?> Questions</span></div>
        <div class="qi"><i class="fas fa-clock"></i><span>30 Minutes</span></div>
        <div class="qi"><i class="fas fa-check-circle"></i><span>Instant Results</span></div>
        <div class="qi"><i class="fas fa-lightbulb"></i><span>Explanations Shown</span></div>
      </div>

      <div class="quiz-rules">
        <h4><i class="fas fa-exclamation-circle"></i> Instructions</h4>
        <ul>
          <li>Answer all questions before submitting.</li>
          <li>You have 30 minutes. The quiz auto-submits when time expires.</li>
          <li>All questions are multiple choice with one correct answer.</li>
          <li>You can retake the quiz but only your latest score is saved.</li>
        </ul>
      </div>

      <div style="display:flex;gap:12px;justify-content:center">
        <a href="take_quiz.php?course_id=<?= $courseId ?>" class="btn btn-primary" style="font-size:16px;padding:14px 36px">
          <i class="fas fa-play"></i> Start Quiz
        </a>
        <a href="/educaster/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline" style="padding:14px 24px">
          Back to Course
        </a>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>