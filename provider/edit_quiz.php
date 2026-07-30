<?php
// provider/edit_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$quizId     = (int)($_GET['id'] ?? 0);

$quiz = $connection->query(
    "SELECT qz.*, c.Provider_Id, c.Course_Id FROM quiz qz
     JOIN course c ON c.Course_Id=qz.Course_Id
     WHERE qz.Quiz_Id=$quizId AND c.Provider_Id=$providerId"
)->fetch_assoc();
if (!$quiz) { header("Location: manage_courses.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quiz'])) {
    $title = $connection->real_escape_string(trim($_POST['quiz_title'] ?? ''));
    $connection->query("UPDATE quiz SET Title='$title' WHERE Quiz_Id=$quizId");

    // Delete old questions & options, re-insert
    $connection->query("DELETE FROM quiz_question WHERE Quiz_Id=$quizId");

    foreach (($_POST['questions'] ?? []) as $q) {
        $qText = $connection->real_escape_string(trim($q['text'] ?? ''));
        $qExpl = $connection->real_escape_string(trim($q['explanation'] ?? ''));
        if (empty($qText)) continue;
        $connection->query("INSERT INTO quiz_question (Quiz_Id, Question_Text, Explanation) VALUES ($quizId, '$qText', '$qExpl')");
        $qId    = $connection->insert_id;
        $correct= (int)($q['correct'] ?? 0);
        foreach (($q['options'] ?? []) as $idx => $optText) {
            $optEsc = $connection->real_escape_string(trim($optText));
            $isCorr = ($idx === $correct) ? 1 : 0;
            $connection->query("INSERT INTO quiz_option (Question_Id, Option_Text, Is_Correct) VALUES ($qId, '$optEsc', $isCorr)");
        }
    }
    header("Location: manage_quiz.php?course_id=" . $quiz['Course_Id'] . "&updated=1"); exit();
}

$questions = $connection->query("SELECT * FROM quiz_question WHERE Quiz_Id=$quizId ORDER BY Question_Id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Quiz — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Edit Quiz</h1>
  <form action="edit_quiz.php?id=<?= $quizId ?>" method="POST">
    <div class="card" style="margin-bottom:24px">
      <div class="form-group">
        <label>Quiz Title</label>
        <input type="text" name="quiz_title" class="form-control" value="<?= htmlspecialchars($quiz['Title']) ?>" required>
      </div>
    </div>
    <div id="questionsContainer">
      <?php $qi = 0; while ($q = $questions->fetch_assoc()):
          $qi++;
          $options = $connection->query("SELECT * FROM quiz_option WHERE Question_Id=".$q['Question_Id']." ORDER BY Option_Id");
          $opts = []; $correctIdx = 0; $oi = 0;
          while ($o = $options->fetch_assoc()) {
              $opts[] = $o;
              if ($o['Is_Correct']) $correctIdx = $oi;
              $oi++;
          }
      ?>
      <div class="card question-builder" style="margin-bottom:20px;border-left:5px solid var(--green)">
        <p style="font-size:12px;color:var(--green);font-weight:700;margin-bottom:10px">QUESTION <?= $qi ?></p>
        <div class="form-group">
          <label>Question Text</label>
          <input type="text" name="questions[<?= $qi ?>][text]" class="form-control" value="<?= htmlspecialchars($q['Question_Text']) ?>" required>
        </div>
        <div class="form-group">
          <label>Explanation</label>
          <input type="text" name="questions[<?= $qi ?>][explanation]" class="form-control" value="<?= htmlspecialchars($q['Explanation'] ?? '') ?>">
        </div>
        <?php foreach ($opts as $oi => $opt): ?>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
          <input type="radio" name="questions[<?= $qi ?>][correct]" value="<?= $oi ?>" <?= $oi===$correctIdx ? 'checked':'' ?> style="accent-color:var(--green);width:18px;height:18px">
          <input type="text" name="questions[<?= $qi ?>][options][<?= $oi ?>]" class="form-control" value="<?= htmlspecialchars($opt['Option_Text']) ?>" required style="flex:1">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endwhile; ?>
    </div>
    <div style="margin-bottom:24px">
      <button type="button" onclick="addQ()" class="btn btn-outline"><i class="fas fa-plus"></i> Add Question</button>
    </div>
    <button type="submit" name="update_quiz" class="btn btn-primary" style="padding:14px 36px;font-size:16px">
      <i class="fas fa-save"></i> Save Quiz
    </button>
  </form>
</div>
<?php include '../common/footer.php'; ?>
<script>
let qc = <?= $qi ?>;
function addQ() {
  qc++;
  const c = document.getElementById('questionsContainer');
  const d = document.createElement('div');
  d.className = 'card question-builder';
  d.style.cssText = 'margin-bottom:20px;border-left:5px solid var(--green)';
  d.innerHTML = `<p style="font-size:12px;color:var(--green);font-weight:700;margin-bottom:10px">QUESTION ${qc}</p>
    <div class="form-group"><label>Question Text</label><input type="text" name="questions[${qc}][text]" class="form-control" required placeholder="Question..."></div>
    <div class="form-group"><label>Explanation</label><input type="text" name="questions[${qc}][explanation]" class="form-control" placeholder="Explanation..."></div>
    ${[0,1,2,3].map(i=>`<div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
      <input type="radio" name="questions[${qc}][correct]" value="${i}" ${i===0?'checked':''} style="accent-color:var(--green);width:18px;height:18px">
      <input type="text" name="questions[${qc}][options][${i}]" class="form-control" required placeholder="Option ${i+1}" style="flex:1">
    </div>`).join('')}
    <button type="button" onclick="this.closest('.question-builder').remove()" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Remove</button>`;
  c.appendChild(d);
}
</script>
</body>
</html>