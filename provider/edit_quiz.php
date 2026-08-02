<?php
// provider/edit_quiz.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$quizId     = (int) ($_GET['id'] ?? 0);

$stmt = $connection->prepare(
    'SELECT qz.*, c.Provider_Id, c.Course_Id FROM quiz qz
     JOIN course c ON c.Course_Id=qz.Course_Id
     WHERE qz.Quiz_Id=? AND c.Provider_Id=?'
);
$stmt->bind_param('ii', $quizId, $providerId);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
if (!$quiz) { header('Location: manage_courses.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quiz'])) {
    verify_csrf();

    $title     = trim($_POST['quiz_title'] ?? '');
    $questions = $_POST['questions'] ?? [];

    if ($title === '') {
        header('Location: edit_quiz.php?id=' . $quizId . '&error=incomplete');
        exit();
    }

    $connection->begin_transaction();
    try {
        $upd = $connection->prepare('UPDATE quiz SET Title=? WHERE Quiz_Id=?');
        $upd->bind_param('si', $title, $quizId);
        $upd->execute();

        // Cascading FK removes the old quiz_option rows automatically
        // when quiz_question rows are deleted.
        $del = $connection->prepare('DELETE FROM quiz_question WHERE Quiz_Id=?');
        $del->bind_param('i', $quizId);
        $del->execute();

        $qStmt   = $connection->prepare('INSERT INTO quiz_question (Quiz_Id, Question_Text, Explanation) VALUES (?,?,?)');
        $optStmt = $connection->prepare('INSERT INTO quiz_option (Question_Id, Option_Text, Is_Correct) VALUES (?,?,?)');

        foreach ($questions as $q) {
            $qText = trim($q['text'] ?? '');
            if ($qText === '') continue;
            $qExpl = trim($q['explanation'] ?? '');
            $qStmt->bind_param('iss', $quizId, $qText, $qExpl);
            $qStmt->execute();
            $qId = $connection->insert_id;

            $correct = (int) ($q['correct'] ?? 0);
            foreach (($q['options'] ?? []) as $idx => $optText) {
                $optText = trim($optText);
                if ($optText === '') continue;
                $isCorrect = ($idx == $correct) ? 1 : 0;
                $optStmt->bind_param('isi', $qId, $optText, $isCorrect);
                $optStmt->execute();
            }
        }
        $connection->commit();
    } catch (Throwable $e) {
        $connection->rollback();
        header('Location: edit_quiz.php?id=' . $quizId . '&error=dbfail');
        exit();
    }

    header('Location: manage_quiz.php?course_id=' . $quiz['Course_Id'] . '&updated=1');
    exit();
}

$questions = $connection->query("SELECT * FROM quiz_question WHERE Quiz_Id=$quizId ORDER BY Question_Id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Quiz — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Edit Quiz</h1>
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> Quiz title is required.</div>
  <?php endif; ?>
  <form action="edit_quiz.php?id=<?= $quizId ?>" method="POST">
    <?= csrf_field() ?>
    <div class="card" style="margin-bottom:24px">
      <div class="form-group" style="margin-bottom:0">
        <label>Quiz Title</label>
        <input type="text" name="quiz_title" class="form-control" value="<?= htmlspecialchars($quiz['Title']) ?>" required>
      </div>
    </div>
    <div id="questionsContainer">
      <?php $qi = 0; while ($q = $questions->fetch_assoc()):
          $qi++;
          $options = $connection->query('SELECT * FROM quiz_option WHERE Question_Id=' . (int) $q['Question_Id'] . ' ORDER BY Option_Id');
          $opts = []; $correctIdx = 0; $oi = 0;
          while ($o = $options->fetch_assoc()) {
              $opts[] = $o;
              if ($o['Is_Correct']) $correctIdx = $oi;
              $oi++;
          }
      ?>
      <div class="card q-builder">
        <p class="q-num-label">QUESTION <?= $qi ?></p>
        <div class="form-group">
          <label>Question Text</label>
          <input type="text" name="questions[<?= $qi ?>][text]" class="form-control" value="<?= htmlspecialchars($q['Question_Text']) ?>" required>
        </div>
        <div class="form-group">
          <label>Explanation</label>
          <input type="text" name="questions[<?= $qi ?>][explanation]" class="form-control" value="<?= htmlspecialchars($q['Explanation'] ?? '') ?>">
        </div>
        <?php foreach ($opts as $oi => $opt): ?>
        <div class="opt-row">
          <input type="radio" name="questions[<?= $qi ?>][correct]" value="<?= $oi ?>" <?= $oi === $correctIdx ? 'checked' : '' ?>>
          <input type="text" name="questions[<?= $qi ?>][options][<?= $oi ?>]" class="form-control" value="<?= htmlspecialchars($opt['Option_Text']) ?>" required style="flex:1">
        </div>
        <?php endforeach; ?>
        <button type="button" onclick="this.closest('.q-builder').remove()" class="btn btn-danger btn-sm" style="margin-top:8px"><i class="fas fa-trash"></i> Remove Question</button>
      </div>
      <?php endwhile; ?>
    </div>
    <div style="margin-bottom:24px">
      <button type="button" onclick="addQ()" class="btn btn-outline"><i class="fas fa-plus"></i> Add Question</button>
    </div>
    <button type="submit" name="update_quiz" class="btn btn-primary btn-lg">
      <i class="fas fa-save"></i> Save Quiz
    </button>
  </form>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
<script>
let qc = <?= $qi ?>;
function addQ() {
  qc++;
  const c = document.getElementById('questionsContainer');
  const d = document.createElement('div');
  d.className = 'card q-builder';
  d.innerHTML = `<p class="q-num-label">QUESTION ${qc}</p>
    <div class="form-group"><label>Question Text</label><input type="text" name="questions[${qc}][text]" class="form-control" required placeholder="Question..."></div>
    <div class="form-group"><label>Explanation</label><input type="text" name="questions[${qc}][explanation]" class="form-control" placeholder="Explanation..."></div>
    ${[0,1,2,3].map(i=>`<div class="opt-row">
      <input type="radio" name="questions[${qc}][correct]" value="${i}" ${i===0?'checked':''}>
      <input type="text" name="questions[${qc}][options][${i}]" class="form-control" ${i<2?'required':''} placeholder="Option ${i+1}" style="flex:1">
    </div>`).join('')}
    <button type="button" onclick="this.closest('.q-builder').remove()" class="btn btn-danger btn-sm" style="margin-top:8px"><i class="fas fa-trash"></i> Remove</button>`;
  c.appendChild(d);
}
</script>
</body>
</html>