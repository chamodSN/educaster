<?php
// courses/course_overview.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

$courseId = (int) ($_GET['id'] ?? 0);
if (!$courseId) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$stmt = $connection->prepare(
    "SELECT c.*, u.First_Name, u.Last_Name, u.User_Name AS provider_username, cat.Category_Name,
       COALESCE(AVG(r.Rating),0) AS avg_rating, COUNT(DISTINCT r.Review_Id) AS review_count,
       COUNT(DISTINCT e.Enrollment_Id) AS enrollments
     FROM course c
     LEFT JOIN registered_user u ON u.Registered_User_Id = c.Provider_Id
     LEFT JOIN course_category cat ON cat.Category_Id = c.Category_Id
     LEFT JOIN review r ON r.Course_Id = c.Course_Id
     LEFT JOIN enrollment e ON e.Course_Id = c.Course_Id
     WHERE c.Course_Id = ? GROUP BY c.Course_Id"
);
$stmt->bind_param('i', $courseId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) { header('Location: ' . BASE_PATH . '/programs.php'); exit(); }

$isExpired  = $course['Due_Date'] && $course['Due_Date'] < date('Y-m-d');
$isEnrolled = false;
$userId     = $_SESSION['userData']['Registered_User_Id'] ?? null;
if ($userId) {
    $chk = $connection->prepare('SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?');
    $chk->bind_param('ii', $userId, $courseId);
    $chk->execute();
    $isEnrolled = $chk->get_result()->num_rows > 0;
}

$weeks = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$reviews = $connection->query(
    "SELECT rv.*, u.User_Name FROM review rv
     JOIN registered_user u ON u.Registered_User_Id = rv.Registered_User_Id
     WHERE rv.Course_Id = $courseId ORDER BY rv.Created_At DESC"
);
$myReview = null;
if ($userId) {
    $mr = $connection->prepare('SELECT * FROM review WHERE Course_Id=? AND Registered_User_Id=?');
    $mr->bind_param('ii', $courseId, $userId);
    $mr->execute();
    $myReview = $mr->get_result()->fetch_assoc();
}

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($course['Title']) ?> — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/course_overview.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">

  <?php if (isset($_GET['enrolled'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> You're enrolled! Happy learning.</div><?php endif; ?>
  <?php if (isset($_GET['unenrolled'])): ?><div class="alert alert-info"><i class="fas fa-circle-info"></i> You've been unenrolled from this course.</div><?php endif; ?>
  <?php if (isset($_GET['reviewed'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Thanks for your review!</div><?php endif; ?>
  <?php if (isset($_GET['expired'])): ?><div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> Enrolment for this course has closed.</div><?php endif; ?>

  <div class="c-banner">
    <div class="c-banner-inner">
      <div class="c-banner-text">
        <span class="pill"><?= htmlspecialchars($course['Category_Name'] ?? 'Teacher Training') ?></span>
        <h1><?= htmlspecialchars($course['Title']) ?></h1>
        <p><?= htmlspecialchars(truncate_text($course['Description'] ?? '', 200)) ?></p>
        <div class="c-banner-meta">
          <span><i class="fas fa-user"></i> <?= htmlspecialchars(trim($course['First_Name'] . ' ' . $course['Last_Name']) ?: $course['provider_username']) ?></span>
          <span><i class="fas fa-users"></i> <?= (int) $course['enrollments'] ?> students</span>
          <span><i class="fas fa-star"></i> <?= number_format((float) $course['avg_rating'], 1) ?> (<?= (int) $course['review_count'] ?> reviews)</span>
          <?php if ($course['Due_Date']): ?>
            <span><i class="fas fa-calendar"></i> <?= $isExpired ? 'Closed on' : 'Until' ?> <?= format_date($course['Due_Date']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="c-banner-img">
        <?php if ($course['Intro_Image']): ?>
          <img src="<?= BASE_PATH ?>/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" alt="<?= htmlspecialchars($course['Title']) ?>">
        <?php else: ?>
          <div class="img-placeholder"><i class="fas fa-book-open"></i></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="c-layout">
    <div class="c-main">
      <div class="card" style="margin-bottom:24px">
        <h3><i class="fas fa-circle-info" style="color:var(--green)"></i> About This Course</h3>
        <p style="margin-top:12px;color:var(--text-muted);line-height:1.8"><?= nl2br(htmlspecialchars($course['Description'])) ?></p>
      </div>

      <div class="card" style="margin-bottom:24px">
        <h3><i class="fas fa-list" style="color:var(--green)"></i> Course Curriculum</h3>
        <div class="weeks-list" style="margin-top:16px">
          <?php if ($weeks->num_rows === 0): ?>
            <p style="color:var(--text-muted);font-size:14px">Curriculum content is being finalized by the provider.</p>
          <?php endif; ?>
          <?php $weeks->data_seek(0); while ($w = $weeks->fetch_assoc()): ?>
          <div class="week-row">
            <div class="week-num">W<?= (int) $w['Week_Number'] ?></div>
            <div class="week-info">
              <?= htmlspecialchars($w['Week_Title']) ?>
              <?php if ($isEnrolled): ?>
                <a href="<?= BASE_PATH ?>/courses/course_content.php?course_id=<?= $courseId ?>&week=<?= (int) $w['Week_Number'] ?>" class="btn btn-sm btn-primary" style="margin-left:12px"><i class="fas fa-play"></i> View</a>
              <?php endif; ?>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>

      <div class="card">
        <h3><i class="fas fa-star" style="color:var(--green)"></i> Reviews
          <span style="font-size:14px;color:var(--text-muted);font-weight:400;margin-left:8px"><?= number_format((float) $course['avg_rating'], 1) ?>/5 · <?= (int) $course['review_count'] ?> reviews</span>
        </h3>
        <div style="margin-top:16px">
          <?php if ($reviews->num_rows === 0): ?>
            <p style="color:var(--text-muted);font-size:14px">No reviews yet — be the first to share your experience.</p>
          <?php endif; ?>
          <?php while ($rev = $reviews->fetch_assoc()): ?>
          <div class="review-item">
            <div class="review-top">
              <span class="review-user"><?= htmlspecialchars($rev['User_Name']) ?></span>
              <?= render_stars((float) $rev['Rating']) ?>
              <span class="review-date"><?= format_date($rev['Created_At']) ?></span>
            </div>
            <p class="review-text"><?= nl2br(htmlspecialchars($rev['Review_Text'])) ?></p>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

    <div class="c-sidebar-col">
      <div class="enroll-card">
        <?php if ($isExpired): ?>
          <div class="enroll-free" style="background:#fbe0df;color:#9c2b25">Enrolment Closed</div>
          <?php if ($isEnrolled): ?>
            <a href="<?= BASE_PATH ?>/courses/course_content.php?course_id=<?= $courseId ?>&week=1" class="btn btn-primary btn-block" style="margin-top:12px"><i class="fas fa-play"></i> Continue Learning</a>
          <?php endif; ?>
        <?php elseif (!isLoggedIn()): ?>
          <div class="enroll-free">Free Enrolment</div>
          <a href="<?= BASE_PATH ?>/user/login.php" class="btn btn-primary btn-block" style="margin-top:12px"><i class="fas fa-sign-in-alt"></i> Log In to Enroll</a>
        <?php elseif (isAdmin() || isProvider()): ?>
          <div class="enroll-free">Free Enrolment</div>
          <p style="font-size:13px;color:var(--text-muted);margin-top:10px;text-align:center">Admin and provider accounts can't enroll in courses.</p>
        <?php elseif ($isEnrolled): ?>
          <div class="enroll-enrolled">✓ You are enrolled</div>
          <a href="<?= BASE_PATH ?>/courses/course_content.php?course_id=<?= $courseId ?>&week=1" class="btn btn-primary btn-block" style="margin-top:12px"><i class="fas fa-play"></i> Continue Learning</a>
          <?php if ($quiz): ?>
          <a href="<?= BASE_PATH ?>/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="btn btn-outline btn-block" style="margin-top:10px"><i class="fas fa-circle-question"></i> Take Quiz</a>
          <?php endif; ?>
          <form action="<?= BASE_PATH ?>/courses/unenroll.php" method="POST" onsubmit="return confirm('Unenroll from this course?')">
            <?= csrf_field() ?>
            <input type="hidden" name="course_id" value="<?= $courseId ?>">
            <button type="submit" class="btn btn-danger btn-block" style="margin-top:10px"><i class="fas fa-times"></i> Unenroll</button>
          </form>
          <a href="<?= BASE_PATH ?>/reviews/<?= $myReview ? 'edit_review.php' : 'add_review.php' ?>?course_id=<?= $courseId ?>" class="btn btn-outline btn-block" style="margin-top:10px"><i class="fas fa-star"></i> <?= $myReview ? 'Edit Your Review' : 'Leave a Review' ?></a>
        <?php else: ?>
          <div class="enroll-free">Free Enrolment</div>
          <form action="<?= BASE_PATH ?>/courses/enroll.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="course_id" value="<?= $courseId ?>">
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px"><i class="fas fa-user-plus"></i> Enroll Now</button>
          </form>
        <?php endif; ?>
        <div class="enroll-features">
          <p><i class="fas fa-infinity"></i> Full lifetime access</p>
          <p><i class="fas fa-mobile-alt"></i> Access on any device</p>
          <p><i class="fas fa-certificate"></i> Certificate on completion</p>
        </div>
      </div>

      <div class="card sidebar-inq" style="margin-top:20px">
        <h4><i class="fas fa-circle-question" style="color:var(--green)"></i> Have a question?</h4>
        <?php if (isLoggedIn()): ?>
        <form action="<?= BASE_PATH ?>/customerSupport/contactUs.php" method="POST" style="margin-top:12px">
          <?= csrf_field() ?>
          <input type="hidden" name="course_id" value="<?= $courseId ?>">
          <input type="hidden" name="subject" value="Course Inquiry: <?= htmlspecialchars($course['Title']) ?>">
          <textarea name="message" class="form-control" rows="3" placeholder="Ask the course provider..." required></textarea>
          <button type="submit" name="submit_inquiry" class="btn btn-primary btn-sm" style="margin-top:10px"><i class="fas fa-paper-plane"></i> Send</button>
        </form>
        <?php else: ?>
          <p style="font-size:14px;color:var(--text-muted);margin-top:10px"><a href="<?= BASE_PATH ?>/user/login.php" style="color:var(--green);font-weight:700">Log in</a> to ask a question.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>