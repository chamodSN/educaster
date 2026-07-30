<?php
// courses/course_overview.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

$courseId = (int)($_GET['id'] ?? 0);
if (!$courseId) { header("Location: /educaster/programs.php"); exit(); }

$course = $connection->query("SELECT c.*, u.First_Name, u.Last_Name, cat.Category_Name,
  COALESCE(AVG(r.Rating),0) AS avg_rating, COUNT(DISTINCT r.Review_Id) AS review_count,
  COUNT(DISTINCT e.Enrollment_Id) AS enrollments
  FROM course c
  LEFT JOIN registered_user u ON u.Registered_User_Id = c.Provider_Id
  LEFT JOIN course_category cat ON cat.Category_Id = c.Category_Id
  LEFT JOIN review r ON r.Course_Id = c.Course_Id
  LEFT JOIN enrollment e ON e.Course_Id = c.Course_Id
  WHERE c.Course_Id = $courseId GROUP BY c.Course_Id")->fetch_assoc();

if (!$course) { header("Location: /educaster/programs.php"); exit(); }

$isEnrolled = false;
$userId = $_SESSION['userData']['Registered_User_Id'] ?? null;
if ($userId) {
    $chk = $connection->prepare("SELECT * FROM enrollment WHERE Registered_User_Id=? AND Course_Id=?");
    $chk->bind_param("ii", $userId, $courseId);
    $chk->execute();
    $isEnrolled = $chk->get_result()->num_rows > 0;
}

$weeks = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$reviews = $connection->query("SELECT rv.*, u.User_Name FROM review rv
  JOIN registered_user u ON u.Registered_User_Id = rv.Registered_User_Id
  WHERE rv.Course_Id = $courseId ORDER BY rv.Created_At DESC");

$quiz = $connection->query("SELECT * FROM quiz WHERE Course_Id=$courseId")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($course['Title']) ?> — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/course_overview.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">

  <!-- COURSE HEADER -->
  <div class="course-header-card">
    <div class="course-header-content">
      <span class="course-tag"><?= htmlspecialchars($course['Category_Name'] ?? 'Teacher Training') ?></span>
      <h1><?= htmlspecialchars($course['Title']) ?></h1>
      <p><?= htmlspecialchars(substr($course['Description'],0,200)) ?>...</p>
      <div class="course-meta-row">
        <span><i class="fas fa-user"></i> <?= htmlspecialchars($course['First_Name'].' '.$course['Last_Name']) ?></span>
        <span><i class="fas fa-users"></i> <?= $course['enrollments'] ?> students</span>
        <span><i class="fas fa-star"></i> <?= number_format($course['avg_rating'],1) ?> (<?= $course['review_count'] ?> reviews)</span>
        <?php if ($course['Due_Date']): ?>
          <span><i class="fas fa-calendar"></i> Until <?= htmlspecialchars($course['Due_Date']) ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div class="course-header-img">
      <?php if ($course['Intro_Image']): ?>
        <img src="/educaster/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" alt="Course">
      <?php endif; ?>
    </div>
  </div>

  <div class="course-layout">
    <!-- LEFT: Weeks & Description -->
    <div class="course-main">
      <div class="card" style="margin-bottom:24px">
        <h3><i class="fas fa-info-circle" style="color:var(--green)"></i> About This Course</h3>
        <p style="margin-top:12px;color:var(--text-muted);line-height:1.8"><?= nl2br(htmlspecialchars($course['Description'])) ?></p>
      </div>

      <div class="card" style="margin-bottom:24px">
        <h3><i class="fas fa-list" style="color:var(--green)"></i> Course Curriculum</h3>
        <div class="weeks-list" style="margin-top:16px">
          <?php $weeks->data_seek(0); while ($w = $weeks->fetch_assoc()): ?>
          <div class="week-item">
            <div class="week-num">W<?= $w['Week_Number'] ?></div>
            <div class="week-info">
              <strong><?= htmlspecialchars($w['Week_Title']) ?></strong>
              <?php if ($isEnrolled): ?>
                <a href="/educaster/courses/course_content.php?course_id=<?= $courseId ?>&week=<?= $w['Week_Number'] ?>" class="btn btn-sm btn-primary" style="margin-left:12px">
                  <i class="fas fa-play"></i> View
                </a>
              <?php endif; ?>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>

      <!-- Reviews -->
      <div class="card">
        <h3><i class="fas fa-star" style="color:var(--green)"></i> Reviews
          <span style="font-size:14px;color:var(--text-muted);font-weight:400;margin-left:8px"><?= number_format($course['avg_rating'],1) ?>/5 · <?= $course['review_count'] ?> reviews</span>
        </h3>
        <div style="margin-top:16px">
          <?php while ($rev = $reviews->fetch_assoc()): ?>
          <div class="review-card">
            <div class="review-header">
              <strong><?= htmlspecialchars($rev['User_Name']) ?></strong>
              <span class="review-stars"><?= str_repeat('★',$rev['Rating']).str_repeat('☆',5-$rev['Rating']) ?></span>
              <small><?= date('M j, Y', strtotime($rev['Created_At'])) ?></small>
            </div>
            <p><?= htmlspecialchars($rev['Review_Text']) ?></p>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

    <!-- RIGHT: Enroll Card -->
    <div class="course-sidebar">
      <div class="enroll-card">
        <?php if (!isLoggedIn()): ?>
          <div class="enroll-free-badge">Free Enrolment</div>
          <a href="/educaster/user/login.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:12px">
            <i class="fas fa-sign-in-alt"></i> Log In to Enroll
          </a>
        <?php elseif ($isEnrolled): ?>
          <div class="enroll-free-badge" style="background:#e8fdf3;color:var(--green-dark)">✓ You are enrolled</div>
          <a href="/educaster/courses/course_content.php?course_id=<?= $courseId ?>&week=1" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:12px">
            <i class="fas fa-play"></i> Continue Learning
          </a>
          <?php if ($quiz): ?>
          <a href="/educaster/quiz/start_quiz.php?course_id=<?= $courseId ?>" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px">
            <i class="fas fa-question-circle"></i> Take Quiz
          </a>
          <?php endif; ?>
          <form action="/educaster/courses/unenroll.php" method="POST" onsubmit="return confirm('Unenroll from this course?')">
            <input type="hidden" name="course_id" value="<?= $courseId ?>">
            <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;margin-top:10px">
              <i class="fas fa-times"></i> Unenroll
            </button>
          </form>
          <a href="/educaster/reviews/add_review.php?course_id=<?= $courseId ?>" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px">
            <i class="fas fa-star"></i> Leave a Review
          </a>
        <?php else: ?>
          <div class="enroll-free-badge">Free Enrolment</div>
          <form action="/educaster/courses/enroll.php" method="POST">
            <input type="hidden" name="course_id" value="<?= $courseId ?>">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:12px">
              <i class="fas fa-user-plus"></i> Enroll Now
            </button>
          </form>
        <?php endif; ?>
        <div class="enroll-features">
          <p><i class="fas fa-infinity"></i> Full lifetime access</p>
          <p><i class="fas fa-mobile-alt"></i> Access on any device</p>
          <p><i class="fas fa-certificate"></i> Certificate on completion</p>
        </div>
      </div>

      <!-- Course Inquiry -->
      <div class="card" style="margin-top:20px">
        <h4><i class="fas fa-question-circle" style="color:var(--green)"></i> Have a question?</h4>
        <?php if (isLoggedIn()): ?>
        <form action="/educaster/customerSupport/contactUs.php" method="POST" style="margin-top:12px">
          <input type="hidden" name="course_id" value="<?= $courseId ?>">
          <textarea name="message" class="form-control" rows="3" placeholder="Ask the course provider..."></textarea>
          <input type="hidden" name="subject" value="Course Inquiry: <?= htmlspecialchars($course['Title']) ?>">
          <button type="submit" name="submit_inquiry" class="btn btn-primary btn-sm" style="margin-top:10px">
            <i class="fas fa-paper-plane"></i> Send
          </button>
        </form>
        <?php else: ?>
          <p style="font-size:14px;color:var(--text-muted);margin-top:10px"><a href="/educaster/user/login.php">Log in</a> to ask a question.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>