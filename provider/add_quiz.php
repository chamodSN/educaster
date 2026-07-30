<?php
// provider/add_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId   = (int)($_GET['course_id'] ?? 0);

$course = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId AND Provider_Id=$providerId")->fetch_assoc();
if (!$course) { header("Location: manage_courses.php"); exit(); }

// Check no existing quiz
$exists = $connection->query("SELECT Quiz_Id FROM quiz WHERE Course_Id=$courseId")->num_rows;
if ($exists) { header("Location: manage_quiz.php?course_id=$courseId"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $quizTitle = $connection->real_escape_string(trim($_POST['quiz_title'] ?? ''));
    $connection->query("INSERT INTO quiz (Course_Id, Title) VALUES ($courseId, '$quizTitle')");
    $quizId = $connection->insert_id;

    $questions = $_POST['questions'] ?? [];
    foreach ($questions as $q) {
        $qText  = $connection->real_escape_string(trim($q['text'] ?? ''));
        $qExpl  = $connection->real_escape_string(trim($q['explanation'] ?? ''));
        if (empty($qText)) continue;
        $connection->query("INSERT INTO quiz_question (Quiz_Id, Question_Text, Explanation) VALUES ($quizId, '$qText', '$qExpl')");
        $qId = $connection->insert_id;
        $correct = (int)($q['correct'] ?? 0);
        foreach (($q['options'] ?? []) as $optIdx => $optText) {
            $optEsc = $connection->real_escape_string(trim($optText));
            $isCorr = ($optIdx === $correct) ? 1 : 0;
            $connection->query("INSERT INTO quiz_option (Question_Id, Option_Text, Is_Correct) VALUES ($qId, '$optEsc', $isCorr)");
        }
    }
    header("Location: manage_quiz.php?course_id=$courseId&created=1"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Quiz — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Create Quiz</h1>
  <p class="section-subtitle">For: <?= htmlspecialchars($course['Title']) ?></p>

  <form action="add_quiz.php?course_id=<?= $courseId ?>" method="POST">
    <div class="card" style="margin-bottom:24px">
      <div class="form-group">
        <label>Quiz Title <span class="req">*</span></label>
        <input type="text" name="quiz_title" class="form-control" required placeholder="e.g. Final Assessment">
      </div>
    </div>

    <div id="questionsContainer"></div>

    <div style="display:flex;gap:12px;margin-bottom:24px">
      <button type="button" onclick="addQuestion()" class="btn btn-outline">
        <i class="fas fa-plus"></i> Add Question
      </button>
    </div>

    <button type="submit" name="create_quiz" class="btn btn-primary" style="font-size:16px;padding:14px 36px">
      <i class="fas fa-save"></i> Save Quiz
    </button>
    <a href="manage_courses.php" class="btn btn-outline" style="margin-left:12px;padding:14px 24px">Cancel</a>
  </form>
</div>
<?php include '../common/footer.php'; ?>
<script>
let qCount = 0;
function addQuestion() {
  qCount++;
  const c = document.getElementById('questionsContainer');
  const div = document.createElement('div');
  div.className = 'card question-builder';
  div.style.marginBottom = '20px';
  div.style.borderLeft   = '5px solid var(--green)';
  div.innerHTML = `
    <p style="font-size:12px;color:var(--green);font-weight:700;margin-bottom:10px">QUESTION ${qCount}</p>
    <div class="form-group">
      <label>Question Text *</label>
      <input type="text" name="questions[${qCount}][text]" class="form-control" required placeholder="Enter the question">
    </div>
    <div class="form-group">
      <label>Explanation (shown after submission)</label>
      <input type="text" name="questions[${qCount}][explanation]" class="form-control" placeholder="Brief explanation of correct answer">
    </div>
    <p style="font-weight:600;font-size:14px;margin-bottom:10px">Options <small style="color:var(--text-muted)">(select the correct one)</small></p>
    ${[0,1,2,3].map(i => `
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <input type="radio" name="questions[${qCount}][correct]" value="${i}" ${i===0?'checked':''} style="accent-color:var(--green);width:18px;height:18px">
        <input type="text" name="questions[${qCount}][options][${i}]" class="form-control" required placeholder="Option ${i+1}" style="flex:1">
      </div>`).join('')}
    <button type="button" onclick="this.closest('.question-builder').remove()" class="btn btn-danger btn-sm" style="margin-top:8px">
      <i class="fas fa-trash"></i> Remove Question
    </button>
  `;
  c.appendChild(div);
}
// Auto-add first question
addQuestion();
</script>
</body>
</html>