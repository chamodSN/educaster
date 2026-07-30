<?php
// quiz/submit_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: /educaster/programs.php"); exit(); }

$quizId   = (int)($_POST['quiz_id'] ?? 0);
$courseId = (int)($_POST['course_id'] ?? 0);
$answers  = $_POST['answers'] ?? [];
$userId   = (int)$_SESSION['userData']['Registered_User_Id'];

$questions = $connection->query("SELECT * FROM quiz_question WHERE Quiz_Id=$quizId");
$total = $correct = 0;
$results = [];

while ($q = $questions->fetch_assoc()) {
    $qId = $q['Question_Id'];
    $total++;
    $selectedOptId = (int)($answers[$qId] ?? 0);
    $correctOpt = $connection->query("SELECT * FROM quiz_option WHERE Question_Id=$qId AND Is_Correct=1")->fetch_assoc();
    $selectedOpt = $selectedOptId ? $connection->query("SELECT * FROM quiz_option WHERE Option_Id=$selectedOptId")->fetch_assoc() : null;

    $isCorrect = $correctOpt && $selectedOptId === (int)$correctOpt['Option_Id'];
    if ($isCorrect) $correct++;

    $results[] = [
        'question'    => $q['Question_Text'],
        'explanation' => $q['Explanation'],
        'correct_text'=> $correctOpt['Option_Text'] ?? '',
        'selected_text'=> $selectedOpt['Option_Text'] ?? 'No answer',
        'is_correct'  => $isCorrect,
    ];
}

$score = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

// Save result
$stmt = $connection->prepare("INSERT INTO takes (Quiz_Id, Registered_User_Id, Q_Marks) VALUES (?,?,?) ON DUPLICATE KEY UPDATE Q_Marks=?, Taken_At=NOW()");
$stmt->bind_param("iidd", $quizId, $userId, $score, $score);
$stmt->execute();

// Mark course as completed
$connection->prepare("UPDATE enrollment SET Is_Completed=1 WHERE Registered_User_Id=? AND Course_Id=?")->bind_param("ii",$userId,$courseId) && null;
$upd = $connection->prepare("UPDATE enrollment SET Is_Completed=1 WHERE Registered_User_Id=? AND Course_Id=?");
$upd->bind_param("ii", $userId, $courseId);
$upd->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quiz Results — Educaster</title>
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
    <div class="results-header">
      <div class="score-circle <?= $score >= 60 ? 'pass' : 'fail' ?>">
        <span><?= $score ?>%</span>
      </div>
      <h2><?= $score >= 60 ? '🎉 Congratulations!' : '📚 Keep Practicing' ?></h2>
      <p>You scored <strong><?= $correct ?>/<?= $total ?></strong> correct answers.</p>
      <div class="results-actions">
        <a href="/educaster/dashboard/student_dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="/educaster/reviews/add_review.php?course_id=<?= $courseId ?>" class="btn btn-outline"><i class="fas fa-star"></i> Leave Review</a>
      </div>
    </div>

    <h3 style="margin-bottom:20px">Answer Explanations</h3>
    <?php foreach ($results as $i => $res): ?>
    <div class="result-card <?= $res['is_correct'] ? 'correct' : 'wrong' ?>">
      <div class="result-q-header">
        <span class="q-icon"><?= $res['is_correct'] ? '✓' : '✗' ?></span>
        <strong>Q<?= $i+1 ?>. <?= htmlspecialchars($res['question']) ?></strong>
      </div>
      <p><strong>Your answer:</strong> <?= htmlspecialchars($res['selected_text']) ?></p>
      <?php if (!$res['is_correct']): ?>
        <p><strong>Correct answer:</strong> <?= htmlspecialchars($res['correct_text']) ?></p>
      <?php endif; ?>
      <?php if ($res['explanation']): ?>
        <div class="explanation"><i class="fas fa-info-circle"></i> <?= htmlspecialchars($res['explanation']) ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>