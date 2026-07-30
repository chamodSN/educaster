<?php
// reviews/edit_review.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId   = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId = (int)($_GET['course_id'] ?? 0);

$stmt = $connection->prepare("SELECT * FROM review WHERE Course_Id=? AND Registered_User_Id=?");
$stmt->bind_param("ii", $courseId, $userId);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();
if (!$review) { header("Location: /educaster/programs.php"); exit(); }

$course = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_review'])) {
    $rating = (int)($_POST['rating'] ?? 0);
    $text   = trim($_POST['review_text'] ?? '');
    if ($rating >= 1 && $rating <= 5 && !empty($text)) {
        $upd = $connection->prepare("UPDATE review SET Rating=?, Review_Text=? WHERE Course_Id=? AND Registered_User_Id=?");
        $upd->bind_param("isii", $rating, $text, $courseId, $userId);
        $upd->execute();
        header("Location: /educaster/courses/course_overview.php?id=$courseId&reviewed=1"); exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Review — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/contact.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div style="max-width:600px;margin:0 auto">
    <div class="auth-card" style="max-width:100%">
      <div class="auth-icon"><i class="fas fa-star"></i></div>
      <h2>Edit Your Review</h2>
      <p class="auth-sub"><?= htmlspecialchars($course['Title']) ?></p>
      <form action="edit_review.php?course_id=<?= $courseId ?>" method="POST" style="text-align:left;margin-top:24px">
        <div class="form-group">
          <label>Rating</label>
          <div class="star-rating" id="starRating">
            <?php for ($i=1; $i<=5; $i++): ?>
              <i class="fas fa-star <?= $i <= $review['Rating'] ? 'active' : '' ?>" data-val="<?= $i ?>"></i>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="ratingInput" value="<?= $review['Rating'] ?>" required>
        </div>
        <div class="form-group">
          <label>Your Review</label>
          <textarea name="review_text" class="form-control" rows="5" required><?= htmlspecialchars($review['Review_Text']) ?></textarea>
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" name="update_review" class="btn btn-primary" style="flex:1;justify-content:center;padding:13px">
            <i class="fas fa-save"></i> Update Review
          </button>
          <a href="/educaster/courses/course_overview.php?id=<?= $courseId ?>" class="btn btn-outline" style="flex:1;justify-content:center;padding:13px">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<style>
.star-rating { font-size: 36px; cursor: pointer; color: var(--border); margin-bottom: 8px; }
.star-rating .fas.active { color: #f39c12; }
</style>
<script>
const stars = document.querySelectorAll('#starRating .fas');
const inp   = document.getElementById('ratingInput');
stars.forEach((s,i) => {
  s.addEventListener('click', () => {
    inp.value = i+1;
    stars.forEach((s2,j) => s2.classList.toggle('active', j <= i));
  });
});
</script>
</body>
</html>