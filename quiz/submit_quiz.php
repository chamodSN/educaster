<?php
// quiz/submit_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}
verify_csrf();

$quizId   = (int) ($_POST['quiz_id'] ?? 0);
$courseId = (int) ($_POST['course_id'] ?? 0);
$answers  = $_POST['answers'] ?? [];
$userId   = currentUserId();

// FIX: originally nothing verified that quiz_id actually belongs to
// course_id, or that the learner was enrolled in that course — a
// crafted request could mark an unrelated course "completed".
$verify = $connection->prepare(
    'SELECT q.Quiz_Id FROM quiz q
     JOIN enrollment e ON e.Course_Id = q.Course_Id
     WHERE q.Quiz_Id = ? AND q.Course_Id = ? AND e.Registered_User_Id = ?'
);
$verify->bind_param('iii', $quizId, $courseId, $userId);
$verify->execute();
if ($verify->get_result()->num_rows === 0) {
    header('Location: ' . BASE_PATH . '/programs.php');
    exit();
}

$questions = $connection->query('SELECT * FROM quiz_question WHERE Quiz_Id=' . $quizId);
$total = $correct = 0;
$results = [];

$correctStmt   = $connection->prepare('SELECT * FROM quiz_option WHERE Question_Id=? AND Is_Correct=1');
$selectedStmt  = $connection->prepare('SELECT * FROM quiz_option WHERE Option_Id=? AND Question_Id=?');

while ($q = $questions->fetch_assoc()) {
    $qId = (int) $q['Question_Id'];
    $total++;
    $selectedOptId = (int) ($answers[$qId] ?? 0);

    $correctStmt->bind_param('i', $qId);
    $correctStmt->execute();
    $correctOpt = $correctStmt->get_result()->fetch_assoc();

    $selectedOpt = null;
    if ($selectedOptId) {
        $selectedStmt->bind_param('ii', $selectedOptId, $qId);
        $selectedStmt->execute();
        $selectedOpt = $selectedStmt->get_result()->fetch_assoc();
    }

    $isCorrect = $correctOpt && $selectedOptId === (int) $correctOpt['Option_Id'];
    if ($isCorrect) $correct++;

    $results[] = [
        'question'      => $q['Question_Text'],
        'explanation'   => $q['Explanation'],
        'correct_text'  => $correctOpt['Option_Text'] ?? '',
        'selected_text' => $selectedOpt['Option_Text'] ?? 'No answer',
        'is_correct'    => $isCorrect,
    ];
}

$score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

$stmt = $connection->prepare('INSERT INTO takes (Quiz_Id, Registered_User_Id, Q_Marks) VALUES (?,?,?) ON DUPLICATE KEY UPDATE Q_Marks=?, Taken_At=NOW()');
$stmt->bind_param('iidd', $quizId, $userId, $score, $score);
$stmt->execute();

$upd = $connection->prepare('UPDATE enrollment SET Is_Completed=1 WHERE Registered_User_Id=? AND Course_Id=?');
$upd->bind_param('ii', $userId, $courseId);
$upd->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Results — Educaster</title>
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
    <div class="results-top">
      <div class="score-ring <?= $score >= 60 ? 'pass' : 'fail' ?>">
        <span><?= $score ?>%</span>
      </div>
      <h2><?= $score >= 60 ? '🎉 Congratulations!' : '📚 Keep Practicing' ?></h2>
      <p>You scored <strong><?= $correct ?>/<?= $total ?></strong> correct answers.</p>
      <div class="results-btns">
        <a href="<?= BASE_PATH ?>/dashboard/student_dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="<?= BASE_PATH ?>/reviews/add_review.php?course_id=<?= $courseId ?>" class="btn btn-outline"><i class="fas fa-star"></i> Leave Review</a>
      </div>
    </div>

    <h3 style="margin-bottom:20px">Answer Explanations</h3>
    <?php foreach ($results as $i => $res): ?>
    <div class="ans-card <?= $res['is_correct'] ? 'correct' : 'wrong' ?>">
      <div class="ans-top">
        <span class="ans-icon"><?= $res['is_correct'] ? '✓' : '✗' ?></span>
        <strong>Q<?= $i + 1 ?>. <?= htmlspecialchars($res['question']) ?></strong>
      </div>
      <p><strong>Your answer:</strong> <?= htmlspecialchars($res['selected_text']) ?></p>
      <?php if (!$res['is_correct']): ?>
        <p><strong>Correct answer:</strong> <?= htmlspecialchars($res['correct_text']) ?></p>
      <?php endif; ?>
      <?php if ($res['explanation']): ?>
        <div class="ans-expl"><i class="fas fa-circle-info"></i> <?= htmlspecialchars($res['explanation']) ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>