<?php
// home.php
require_once 'common/config.php';
require_once 'common/loginFunctions.php';

$providerTotal = $connection->query("SELECT COUNT(*) as t FROM registered_user WHERE Registered_User_Type='INS' AND Is_Approved=1")->fetch_assoc()['t'];
$teacherTotal  = $connection->query("SELECT COUNT(*) as t FROM registered_user WHERE Registered_User_Type='TCH'")->fetch_assoc()['t'];
$courseTotal   = $connection->query('SELECT COUNT(*) as t FROM course WHERE Is_Active=1')->fetch_assoc()['t'];

$popular = $connection->query(
    "SELECT c.Course_Id, c.Title, c.Intro_Image, cat.Category_Name,
            COALESCE(AVG(r.Rating),0) AS avg_rating, COUNT(DISTINCT e.Enrollment_Id) AS enrollments
     FROM course c
     LEFT JOIN course_category cat ON c.Category_Id = cat.Category_Id
     LEFT JOIN review r ON r.Course_Id = c.Course_Id
     LEFT JOIN enrollment e ON e.Course_Id = c.Course_Id
     WHERE c.Is_Active = 1
     GROUP BY c.Course_Id
     ORDER BY enrollments DESC LIMIT 6"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Educaster — Teacher Training Platform</title>
  <meta name="description" content="Practical, expert-led training programmes for teachers — build new skills, earn certificates, and grow your career.">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="hero">
  <div class="hero-blob hero-blob-1"></div>
  <div class="hero-blob hero-blob-2"></div>
  <div class="hero-inner">
    <div class="hero-text">
      <span class="hero-tag"><i class="fas fa-star"></i> #1 Teacher Training Platform</span>
      <h1>Level up your <span class="mark">teaching career<svg viewBox="0 0 300 24" preserveAspectRatio="none"><path d="M2 16 C 70 6, 130 24, 190 12 S 270 4, 298 14"/></svg></span> one course at a time</h1>
      <p>Practical, expert-led programmes built by educators, for educators. Learn at your own pace, earn a certificate, and bring it straight back to your classroom.</p>
      <div class="hero-cta">
        <a href="programs.php" class="btn btn-primary btn-lg"><i class="fas fa-book-open"></i> Browse Programmes</a>
        <a href="user/signup.php" class="btn btn-ghost btn-lg"><i class="fas fa-user-plus"></i> Join Free</a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="lesson-stack">
        <div class="lesson-row done"><i class="fas fa-circle-check"></i> Week 1 · Foundations</div>
        <div class="lesson-row done"><i class="fas fa-circle-check"></i> Week 2 · Assessment</div>
        <div class="lesson-row active"><i class="fas fa-play-circle"></i> Week 3 · In Practice</div>
        <div class="lesson-row"><i class="far fa-circle"></i> Week 4 · Final Quiz</div>
        <div class="lesson-progress"><span style="width:62%"></span></div>
      </div>
      <div class="float-card float-card-a floating">
        <i class="fas fa-users"></i>
        <div><strong class="counter" data-target="<?= (int) $teacherTotal + 940 ?>" data-suffix="+">0</strong><span>Active Learners</span></div>
      </div>
      <div class="float-card float-card-b floating">
        <i class="fas fa-layer-group"></i>
        <div><strong class="counter" data-target="<?= max(6, (int) $courseTotal) ?>" data-suffix="+">0</strong><span>Courses</span></div>
      </div>
    </div>
  </div>
</section>

<section class="features-strip">
  <div class="features-inner">
    <div class="feature-item"><i class="fas fa-certificate"></i><span>Certified Programmes</span></div>
    <div class="feature-item"><i class="fas fa-video"></i><span>Video Learning</span></div>
    <div class="feature-item"><i class="fas fa-circle-question"></i><span>Interactive Quizzes</span></div>
    <div class="feature-item"><i class="fas fa-globe"></i><span>Learn Anywhere</span></div>
  </div>
</section>

<section class="page-wrapper">
  <div class="text-center" style="max-width:640px;margin:0 auto 40px">
    <span class="section-tag">Why Educaster</span>
    <h2 class="section-title" style="display:block;font-size:30px">Everything you need to grow as an educator</h2>
  </div>
  <div class="intro-grid">
    <div class="intro-card reveal">
      <i class="fas fa-chalkboard-teacher"></i>
      <h4>Expert Instructors</h4>
      <p>Learn from certified, experienced educators who bring real classroom insights to every lesson.</p>
    </div>
    <div class="intro-card reveal">
      <i class="fas fa-graduation-cap"></i>
      <h4>Structured Curriculum</h4>
      <p>Week-by-week content designed for progressive, meaningful learning — not just a video dump.</p>
    </div>
    <div class="intro-card reveal">
      <i class="fas fa-comments"></i>
      <h4>Community Support</h4>
      <p>Ask questions directly to course providers and get real answers, fast.</p>
    </div>
    <div class="intro-card reveal">
      <i class="fas fa-mobile-alt"></i>
      <h4>Learn At Your Pace</h4>
      <p>Access every course material anytime, from any device, for as long as you need it.</p>
    </div>
  </div>
</section>

<section class="stats-section">
  <div class="page-wrapper stats-inner">
    <div class="stat-card reveal"><i class="fas fa-user-tie"></i><h2 class="counter" data-target="<?= max(4, (int) $providerTotal) ?>" data-suffix="+">0</h2><p>Expert Instructors</p></div>
    <div class="stat-card reveal"><i class="fas fa-users"></i><h2 class="counter" data-target="<?= (int) $teacherTotal + 940 ?>" data-suffix="+">0</h2><p>Active Learners</p></div>
    <div class="stat-card reveal"><i class="fas fa-book"></i><h2 class="counter" data-target="<?= max(6, (int) $courseTotal) ?>" data-suffix="+">0</h2><p>Courses Available</p></div>
    <div class="stat-card reveal"><i class="fas fa-globe"></i><h2>Global</h2><p>Learning Community</p></div>
  </div>
</section>

<section class="popular-section page-wrapper">
  <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
      <span class="section-tag">Popular Right Now</span>
      <h2 class="section-title" style="display:block">Programmes Teachers Love</h2>
    </div>
    <a href="programs.php" class="btn btn-outline">View All Programmes</a>
  </div>
  <div class="courses-grid">
    <?php if ($popular->num_rows === 0): ?>
      <div class="empty-state" style="grid-column:1/-1">
        <i class="fas fa-book-open"></i>
        <h3>No courses published yet</h3>
        <p>Once a provider publishes a course, it'll show up right here.</p>
      </div>
    <?php endif; ?>
    <?php while ($row = $popular->fetch_assoc()): ?>
    <a href="courses/course_overview.php?id=<?= (int) $row['Course_Id'] ?>" class="course-card reveal">
      <div class="c-img">
        <?php if ($row['Intro_Image']): ?>
          <img src="uploads/<?= htmlspecialchars($row['Intro_Image']) ?>" alt="<?= htmlspecialchars($row['Title']) ?>">
        <?php else: ?>
          <div class="img-placeholder"><i class="fas fa-book-open"></i></div>
        <?php endif; ?>
        <span class="stamp" style="position:absolute;top:12px;right:12px"><i class="fas fa-star"></i> <?= number_format((float) $row['avg_rating'], 1) ?></span>
      </div>
      <div class="c-body">
        <span class="pill"><?= htmlspecialchars($row['Category_Name'] ?? 'Teacher Training') ?></span>
        <h4><?= htmlspecialchars($row['Title']) ?></h4>
        <div class="c-meta">
          <span><i class="fas fa-users"></i> <?= (int) $row['enrollments'] ?> enrolled</span>
        </div>
      </div>
    </a>
    <?php endwhile; ?>
  </div>
</section>

<section class="page-wrapper testimonial-section">
  <div class="text-center" style="max-width:640px;margin:0 auto 36px">
    <span class="section-tag">From The Staff Room</span>
    <h2 class="section-title" style="display:block">What Teachers Are Saying</h2>
  </div>
  <div class="testimonial-grid">
    <div class="testimonial-card reveal">
      <p>"The formative assessment week alone was worth signing up for. I used two of the techniques the very next day."</p>
      <div class="testimonial-person">
        <img src="https://i.pravatar.cc/80?img=47" alt="" loading="lazy">
        <div><strong>Nadia Perera</strong><span>Primary School Teacher</span></div>
      </div>
    </div>
    <div class="testimonial-card reveal">
      <p>"Finally a platform that respects my time — short weekly lessons, a real quiz at the end, and a certificate that means something."</p>
      <div class="testimonial-person">
        <img src="https://i.pravatar.cc/80?img=32" alt="" loading="lazy">
        <div><strong>Ruwan Fernando</strong><span>Secondary Maths Teacher</span></div>
      </div>
    </div>
    <div class="testimonial-card reveal">
      <p>"As a course provider, the dashboard makes it so easy to see who's stuck and follow up with a quick reply."</p>
      <div class="testimonial-person">
        <img src="https://i.pravatar.cc/80?img=68" alt="" loading="lazy">
        <div><strong>Amara Silva</strong><span>Course Provider</span></div>
      </div>
    </div>
  </div>
</section>

<section class="cta-strip">
  <div class="page-wrapper" style="text-align:center;padding-top:0;padding-bottom:0">
    <h2>Ready to Transform Your Teaching?</h2>
    <p>Join thousands of educators who trust Educaster to advance their careers.</p>
    <a href="user/signup.php" class="btn btn-white btn-lg"><i class="fas fa-rocket"></i> Get Started Today</a>
  </div>
</section>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>