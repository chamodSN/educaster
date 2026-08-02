<?php
// provider/add_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$courseId   = (int) ($_GET['course_id'] ?? 0);

$stmt = $connection->prepare('SELECT * FROM course WHERE Course_Id=? AND Provider_Id=?');
$stmt->bind_param('ii', $courseId, $providerId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { header('Location: manage_courses.php'); exit(); }

$existsStmt = $connection->prepare('SELECT Quiz_Id FROM quiz WHERE Course_Id=?');
$existsStmt->bind_param('i', $courseId);
$existsStmt->execute();
if ($existsStmt->get_result()->num_rows > 0) { header("Location: manage_quiz.php?course_id=$courseId"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    verify_csrf();

    $quizTitle = trim($_POST['quiz_title'] ?? '');
    $questions = $_POST['questions'] ?? [];

    // Keep only questions that have text AND at least 2 non-empty options.
    $validQuestions = [];
    foreach ($questions as $q) {
        $qText = trim($q['text'] ?? '');
        $opts  = array_filter(array_map('trim', $q['options'] ?? []), fn($o) => $o !== '');
        if ($qText !== '' && count($opts) >= 2) {
            $validQuestions[] = $q;
        }
    }

    if ($quizTitle === '' || empty($validQuestions)) {
        header("Location: add_quiz.php?course_id=$courseId&error=incomplete");
        exit();
    }

    $connection->begin_transaction();
    try {
        $quizStmt = $connection->prepare('INSERT INTO quiz (Course_Id, Title) VALUES (?, ?)');
        $quizStmt->bind_param('is', $courseId, $quizTitle);
        $quizStmt->execute();
        $quizId = $connection->insert_id;

        $qStmt   = $connection->prepare('INSERT INTO quiz_question (Quiz_Id, Question_Text, Explanation) VALUES (?,?,?)');
        $optStmt = $connection->prepare('INSERT INTO quiz_option (Question_Id, Option_Text, Is_Correct) VALUES (?,?,?)');

        foreach ($validQuestions as $q) {
            $qText = trim($q['text']);
            $qExpl = trim($q['explanation'] ?? '');
            $qStmt->bind_param('iss', $quizId, $qText, $qExpl);
            $qStmt->execute();
            $qId = $connection->insert_id;

            $correct = (int) ($q['correct'] ?? 0);
            foreach (($q['options'] ?? []) as $optIdx => $optText) {
                $optText = trim($optText);
                if ($optText === '') continue;
                $isCorrect = ($optIdx == $correct) ? 1 : 0;
                $optStmt->bind_param('isi', $qId, $optText, $isCorrect);
                $optStmt->execute();
            }
        }
        $connection->commit();
    } catch (Throwable $e) {
        $connection->rollback();
        header("Location: add_quiz.php?course_id=$courseId&error=dbfail");
        exit();
    }

    header("Location: manage_quiz.php?course_id=$courseId&created=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Quiz — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Create Quiz</h1>
  <p class="section-subtitle">For: <?= htmlspecialchars($course['Title']) ?></p>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error">
      <i class="fas fa-triangle-exclamation"></i>
      <?= $_GET['error'] === 'incomplete' ? 'Give the quiz a title and at least one question with 2+ options.' : 'Something went wrong creating the quiz.' ?>
    </div>
  <?php endif; ?>

  <form action="add_quiz.php?course_id=<?= $courseId ?>" method="POST">
    <?= csrf_field() ?>
    <div class="card" style="margin-bottom:24px">
      <div class="form-group" style="margin-bottom:0">
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

    <button type="submit" name="create_quiz" class="btn btn-primary btn-lg">
      <i class="fas fa-save"></i> Save Quiz
    </button>
    <a href="manage_courses.php" class="btn btn-outline btn-lg" style="margin-left:12px">Cancel</a>
  </form>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
<script>
let qCount = 0;
function addQuestion() {
  qCount++;
  const c = document.getElementById('questionsContainer');
  const div = document.createElement('div');
  div.className = 'card q-builder';
  div.innerHTML = `
    <p class="q-num-label">QUESTION ${qCount}</p>
    <div class="form-group">
      <label>Question Text *</label>
      <input type="text" name="questions[${qCount}][text]" class="form-control" required placeholder="Enter the question">
    </div>
    <div class="form-group">
      <label>Explanation (shown after submission)</label>
      <input type="text" name="questions[${qCount}][explanation]" class="form-control" placeholder="Brief explanation of the correct answer">
    </div>
    <p style="font-weight:700;font-size:14px;margin-bottom:10px">Options <small style="color:var(--text-muted);font-weight:400">(select the correct one)</small></p>
    ${[0,1,2,3].map(i => `
      <div class="opt-row">
        <input type="radio" name="questions[${qCount}][correct]" value="${i}" ${i===0?'checked':''}>
        <input type="text" name="questions[${qCount}][options][${i}]" class="form-control" ${i<2?'required':''} placeholder="Option ${i+1}" style="flex:1">
      </div>`).join('')}
    <button type="button" onclick="this.closest('.q-builder').remove()" class="btn btn-danger btn-sm" style="margin-top:8px">
      <i class="fas fa-trash"></i> Remove Question
    </button>
  `;
  c.appendChild(div);
}
addQuestion();
</script>
</body>
</html>