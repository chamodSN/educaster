<?php
// reviews/add_review.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$courseId = (int)($_GET['course_id'] ?? 0);
$userId   = (int)$_SESSION['userData']['Registered_User_Id'];

// Verify enrollment
$chk = $connection->prepare("SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?");
$chk->bind_param("ii", $userId, $courseId);
$chk->execute();
if ($chk->get_result()->num_rows === 0) { header("Location: /educaster/programs.php"); exit(); }

$course = $connection->query("SELECT Title FROM course WHERE Course_Id=$courseId")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = (int)($_POST['rating'] ?? 0);
    $text   = trim($_POST['review_text'] ?? '');
    if ($rating >= 1 && $rating <= 5 && !empty($text)) {
        $stmt = $connection->prepare("INSERT INTO review (Course_Id, Registered_User_Id, Rating, Review_Text) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE Rating=?, Review_Text=?, Created_At=NOW()");
        $stmt->bind_param("iisis is", $courseId, $userId, $rating, $text, $rating, $text);
        // Clean approach:
        $stmt2 = $connection->prepare("INSERT INTO review (Course_Id, Registered_User_Id, Rating, Review_Text) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE Rating=VALUES(Rating), Review_Text=VALUES(Review_Text)");
        $stmt2->bind_param("iiis", $courseId, $userId, $rating, $text);
        $stmt2->execute();
        header("Location: /educaster/courses/course_overview.php?id=$courseId&reviewed=1"); exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave a Review — Educaster</title>
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
      <h2>Review: <?= htmlspecialchars($course['Title']) ?></h2>
      <form action="add_review.php?course_id=<?= $courseId ?>" method="POST" style="text-align:left;margin-top:24px">
        <div class="form-group">
          <label>Your Rating <span class="req">*</span></label>
          <div class="star-rating" id="starRating">
            <?php for ($i=1;$i<=5;$i++): ?>
              <i class="fas fa-star" data-val="<?= $i ?>"></i>
            <?php endfor; ?>
          </div>
          <input type="hidden" name="rating" id="ratingInput" required>
        </div>
        <div class="form-group">
          <label>Your Review <span class="req">*</span></label>
          <textarea name="review_text" class="form-control" rows="5" required placeholder="Share your experience..."></textarea>
        </div>
        <button type="submit" name="submit_review" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px">
          <i class="fas fa-paper-plane"></i> Submit Review
        </button>
      </form>
      <div style="margin-top:16px;text-align:center">
        <a href="/educaster/courses/course_overview.php?id=<?= $courseId ?>">← Back to Course</a>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<style>
.star-rating { font-size: 36px; cursor: pointer; color: var(--border); margin-bottom: 8px; }
.star-rating .fas { transition: color 0.2s; }
.star-rating .fas.active, .star-rating .fas:hover { color: #f39c12; }
</style>
<script>
const stars = document.querySelectorAll('#starRating .fas');
const inp   = document.getElementById('ratingInput');
stars.forEach((s,i) => {
  s.addEventListener('click', () => {
    inp.value = i+1;
    stars.forEach((s2,j) => s2.classList.toggle('active', j <= i));
  });
  s.addEventListener('mouseenter', () => stars.forEach((s2,j) => s2.style.color = j<=i ? '#f39c12' : ''));
  s.addEventListener('mouseleave', () => stars.forEach((s2,j) => s2.style.color = j < (inp.value||0) ? '#f39c12' : ''));
});
</script>
</body>
</html>