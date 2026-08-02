<?php
// quiz/take_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int) ($_GET['course_id'] ?? 0);
$userId   = currentUserId();

$enrolCheck = $connection->prepare('SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
$enrolCheck->bind_param('ii', $userId, $courseId);
$enrolCheck->execute();
if ($enrolCheck->get_result()->num_rows === 0) {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
if (!$quiz) { header('Location: ' . BASE_PATH . "/courses/course_overview.php?id=$courseId"); exit(); }

$questions = $connection->query('SELECT * FROM quiz_question WHERE Quiz_Id=' . (int) $quiz['Quiz_Id'] . ' ORDER BY Question_Id ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz — <?= htmlspecialchars($quiz['Title']) ?></title>
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
    <div class="quiz-header">
      <h1><i class="fas fa-circle-question"></i> <?= htmlspecialchars($quiz['Title']) ?></h1>
      <div class="quiz-timer" id="timer"><i class="fas fa-clock"></i> <span id="time">30:00</span></div>
    </div>

    <form action="submit_quiz.php" method="POST" id="quizForm">
      <?= csrf_field() ?>
      <input type="hidden" name="quiz_id" value="<?= (int) $quiz['Quiz_Id'] ?>">
      <input type="hidden" name="course_id" value="<?= $courseId ?>">

      <?php $qNum = 0; while ($q = $questions->fetch_assoc()):
          $qNum++;
          $options = $connection->query('SELECT * FROM quiz_option WHERE Question_Id=' . (int) $q['Question_Id'] . ' ORDER BY Option_Id ASC');
      ?>
      <div class="q-card" id="q<?= $qNum ?>">
        <div class="q-label">Question <?= $qNum ?></div>
        <p class="q-text"><?= htmlspecialchars($q['Question_Text']) ?></p>
        <div class="opts">
          <?php while ($opt = $options->fetch_assoc()): ?>
          <label class="opt-label">
            <input type="radio" name="answers[<?= (int) $q['Question_Id'] ?>]" value="<?= (int) $opt['Option_Id'] ?>" required>
            <span><?= htmlspecialchars($opt['Option_Text']) ?></span>
          </label>
          <?php endwhile; ?>
        </div>
      </div>
      <?php endwhile; ?>

      <button type="submit" class="btn btn-primary btn-lg" style="margin-top:24px" onclick="return confirm('Submit quiz? You cannot change your answers after submission.')">
        <i class="fas fa-paper-plane"></i> Submit Quiz
      </button>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/quiz.js"></script>
</body>
</html>