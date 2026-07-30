<?php
// quiz/take_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int)($_GET['course_id'] ?? 0);
$userId   = (int)$_SESSION['userData']['Registered_User_Id'];

// Verify enrollment
$enrolCheck = $connection->prepare("SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?");
$enrolCheck->bind_param("ii", $userId, $courseId);
$enrolCheck->execute();
if ($enrolCheck->get_result()->num_rows === 0) {
    header("Location: /educaster/programs.php"); exit();
}

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
if (!$quiz) { header("Location: /educaster/courses/course_overview.php?id=$courseId"); exit(); }

$questions = $connection->query("SELECT * FROM quiz_question WHERE Quiz_Id=".$quiz['Quiz_Id']." ORDER BY Question_Id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz — <?= htmlspecialchars($quiz['Title']) ?></title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/quiz.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="quiz-wrapper">
    <div class="quiz-header">
      <h1><i class="fas fa-question-circle"></i> <?= htmlspecialchars($quiz['Title']) ?></h1>
      <div class="quiz-timer" id="timer"><i class="fas fa-clock"></i> <span id="time">30:00</span></div>
    </div>

    <form action="submit_quiz.php" method="POST" id="quizForm">
      <input type="hidden" name="quiz_id" value="<?= $quiz['Quiz_Id'] ?>">
      <input type="hidden" name="course_id" value="<?= $courseId ?>">

      <?php $qNum = 0; while ($q = $questions->fetch_assoc()):
          $qNum++;
          $options = $connection->query("SELECT * FROM quiz_option WHERE Question_Id=".$q['Question_Id']." ORDER BY Option_Id ASC");
      ?>
      <div class="question-card" id="q<?= $qNum ?>">
        <div class="q-number">Question <?= $qNum ?></div>
        <p class="q-text"><?= htmlspecialchars($q['Question_Text']) ?></p>
        <div class="options-list">
          <?php while ($opt = $options->fetch_assoc()): ?>
          <label class="option-label">
            <input type="radio" name="answers[<?= $q['Question_Id'] ?>]" value="<?= $opt['Option_Id'] ?>" required>
            <span class="option-text"><?= htmlspecialchars($opt['Option_Text']) ?></span>
          </label>
          <?php endwhile; ?>
        </div>
      </div>
      <?php endwhile; ?>

      <button type="submit" class="btn btn-primary" style="margin-top:24px;font-size:16px;padding:14px 36px" onclick="return confirm('Submit quiz? You cannot change answers after submission.')">
        <i class="fas fa-paper-plane"></i> Submit Quiz
      </button>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="/educaster/js/quiz.js"></script>
</body>
</html>