<?php
// reviews/add_review.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int) ($_GET['course_id'] ?? 0);
$userId   = currentUserId();

$chk = $connection->prepare('SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
$chk->bind_param('ii', $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$course = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();
if (!$course) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    verify_csrf();

    $rating = (int) ($_POST['rating'] ?? 0);
    $text   = trim($_POST['review_text'] ?? '');
    if ($rating >= 1 && $rating <= 5 && $text !== '') {
        // FIX: the original code here prepared a statement with a
        // malformed bind_param type string ("iisis is" — wrong length
        // AND a stray space) right before preparing a second, correct
        // statement and never executing the first. On modern PHP that
        // mismatched bind_param call throws immediately. Only the
        // correct statement is kept below.
        $stmt = $connection->prepare(
            'INSERT INTO review (Course_Id, Registered_User_Id, Rating, Review_Text) VALUES (?,?,?,?)
             ON DUPLICATE KEY UPDATE Rating=VALUES(Rating), Review_Text=VALUES(Review_Text), Created_At=NOW()'
        );
        $stmt->bind_param('iiis', $courseId, $userId, $rating, $text);
        $stmt->execute();
        header("Location: " . BASE_PATH . "/courses/course_overview.php?id=$courseId&reviewed=1");
        exit();
    }
    header("Location: add_review.php?course_id=$courseId&error=invalid");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave a Review — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/login.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="narrow-wrapper">
    <div class="auth-card">
      <div class="auth-icon"><i class="fas fa-star"></i></div>
      <h2>Review: <?= htmlspecialchars($course['Title']) ?></h2>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> Please choose a rating and write a short review.</div>
      <?php endif; ?>

      <form action="add_review.php?course_id=<?= $courseId ?>" method="POST" style="text-align:left;margin-top:24px">
        <?= csrf_field() ?>
        <div class="form-group">
          <label>Your Rating <span class="req">*</span></label>
          <div class="star-rating" id="starRating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <i class="fas fa-star" data-val="<?= $i ?>"></i>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="ratingInput" required>
        </div>
        <div class="form-group">
          <label>Your Review <span class="req">*</span></label>
          <textarea name="review_text" class="form-control" rows="5" required placeholder="Share your experience..."></textarea>
        </div>
        <button type="submit" name="submit_review" class="btn btn-primary btn-block">
          <i class="fas fa-paper-plane"></i> Submit Review
        </button>
      </form>
      <div style="margin-top:16px;text-align:center">
        <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= $courseId ?>" style="color:var(--green);font-weight:700">← Back to Course</a>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
<script>
const starRating = document.getElementById('starRating');
const stars = starRating.querySelectorAll('.fas');
const inp   = document.getElementById('ratingInput');
function paintStars(count) {
  stars.forEach((s, j) => s.classList.toggle('active', j < count));
}
stars.forEach((s, i) => {
  s.addEventListener('click', () => { inp.value = i + 1; paintStars(i + 1); });
  s.addEventListener('mouseenter', () => paintStars(i + 1));
});
starRating.addEventListener('mouseleave', () => paintStars(parseInt(inp.value || '0', 10)));
</script>
</body>
</html>