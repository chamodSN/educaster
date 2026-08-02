<?php
// quiz/start_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int) ($_GET['course_id'] ?? 0);
$userId   = currentUserId();

$chk = $connection->prepare('SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
$chk->bind_param('ii', $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$quiz   = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
$course = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();
if (!$quiz) { header('Location: ' . BASE_PATH . "/courses/course_overview.php?id=$courseId"); exit(); }

$qCount   = $connection->query('SELECT COUNT(*) AS t FROM quiz_question WHERE Quiz_Id=' . (int) $quiz['Quiz_Id'])->fetch_assoc()['t'];
$prevTake = $connection->query('SELECT * FROM takes WHERE Quiz_Id=' . (int) $quiz['Quiz_Id'] . " AND Registered_User_Id=$userId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Start Quiz — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/quiz.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="quiz-wrap">
    <div class="quiz-start">
      <div class="quiz-start-icon"><i class="fas fa-circle-question"></i></div>
      <h1><?= htmlspecialchars($quiz['Title']) ?></h1>
      <p class="quiz-course-label"><?= htmlspecialchars($course['Title']) ?></p>

      <?php if ($prevTake): ?>
      <div class="alert alert-info" style="text-align:left">
        <i class="fas fa-circle-info"></i>
        You previously scored <strong><?= $prevTake['Q_Marks'] ?>%</strong> on this quiz.
        Taking it again will overwrite your previous score.
      </div>
      <?php endif; ?>

      <div class="quiz-info-grid">
        <div class="qi-item"><i class="fas fa-list-ol"></i><span><?= (int) $qCount ?> Questions</span></div>
        <div class="qi-item"><i class="fas fa-clock"></i><span>30 Minutes</span></div>
        <div class="qi-item"><i class="fas fa-circle-check"></i><span>Instant Results</span></div>
        <div class="qi-item"><i class="fas fa-lightbulb"></i><span>Explanations Shown</span></div>
      </div>

      <div class="quiz-rules">
        <h4><i class="fas fa-triangle-exclamation"></i> Instructions</h4>
        <ul>
          <li>Answer all questions before submitting.</li>
          <li>You have 30 minutes. The quiz auto-submits when time expires.</li>
          <li>All questions are multiple choice with one correct answer.</li>
          <li>You can retake the quiz, but only your latest score is saved.</li>
        </ul>
      </div>

      <div class="quiz-start-btns">
        <a href="take_quiz.php?course_id=<?= $courseId ?>" class="btn btn-primary btn-lg">
          <i class="fas fa-play"></i> Start Quiz
        </a>
        <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline btn-lg">
          Back to Course
        </a>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>