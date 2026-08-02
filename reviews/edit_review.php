<?php
// reviews/edit_review.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId   = currentUserId();
$courseId = (int) ($_GET['course_id'] ?? 0);

$stmt = $connection->prepare('SELECT * FROM review WHERE Course_Id=? AND Registered_User_Id=?');
$stmt->bind_param('ii', $courseId, $userId);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();
if (!$review) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$course = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    verify_csrf();

    $rating = (int) ($_POST['rating'] ?? 0);
    $text   = trim($_POST['review_text'] ?? '');
    if ($rating >= 1 && $rating <= 5 && $text !== '') {
        $upd = $connection->prepare('UPDATE review SET Rating=?, Review_Text=? WHERE Course_Id=? AND Registered_User_Id=?');
        $upd->bind_param('isii', $rating, $text, $courseId, $userId);
        $upd->execute();
        header("Location: " . BASE_PATH . "/courses/course_overview.php?id=$courseId&reviewed=1");
        exit();
    }
    header("Location: edit_review.php?course_id=$courseId&error=invalid");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Review — Educaster</title>
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
      <h2>Edit Your Review</h2>
      <p class="auth-sub"><?= htmlspecialchars($course['Title']) ?></p>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> Please choose a rating and write a short review.</div>
      <?php endif; ?>

      <form action="edit_review.php?course_id=<?= $courseId ?>" method="POST" style="text-align:left;margin-top:24px">
        <?= csrf_field() ?>
        <div class="form-group">
          <label>Rating</label>
          <div class="star-rating" id="starRating">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <i class="fas fa-star <?= $i <= $review['Rating'] ? 'active' : '' ?>" data-val="<?= $i ?>"></i>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="ratingInput" value="<?= (int) $review['Rating'] ?>" required>
        </div>
        <div class="form-group">
          <label>Your Review</label>
          <textarea name="review_text" class="form-control" rows="5" required><?= htmlspecialchars($review['Review_Text']) ?></textarea>
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" name="update_review" class="btn btn-primary" style="flex:1">
            <i class="fas fa-save"></i> Update Review
          </button>
          <a href="<?= BASE_PATH ?>/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</a>
        </div>
      </form>
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