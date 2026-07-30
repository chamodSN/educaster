<?php
// home.php
require_once 'common/config.php';
require_once 'common/loginFunctions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Educaster — Teacher Training Platform</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero">
  <div class="hero-content">
    <span class="hero-tag"><i class="fas fa-star"></i> #1 Teacher Training Platform</span>
    <h1>Transform Your <span class="highlight">Teaching Career</span></h1>
    <p>Empower yourself with world-class training programmes designed by expert educators. Start your journey today.</p>
    <div class="hero-cta">
      <a href="programs.php" class="btn btn-primary"><i class="fas fa-book-open"></i> Browse Programmes</a>
      <a href="user/signup.php" class="btn btn-outline" style="border-color:#fff;color:#fff"><i class="fas fa-user-plus"></i> Join Free</a>
    </div>
  </div>
  <div class="hero-visual">
    <div class="hero-card floating">
      <i class="fas fa-users"></i>
      <div><strong>1,000+</strong><span>Active Learners</span></div>
    </div>
    <div class="hero-card floating" style="animation-delay:0.5s">
      <i class="fas fa-layer-group"></i>
      <div><strong>50+</strong><span>Courses</span></div>
    </div>
  </div>
</section>

<!-- FEATURES STRIP -->
<section class="features-strip">
  <div class="features-inner">
    <div class="feature-item"><i class="fas fa-certificate"></i><span>Certified Programmes</span></div>
    <div class="feature-item"><i class="fas fa-video"></i><span>Video Learning</span></div>
    <div class="feature-item"><i class="fas fa-question-circle"></i><span>Interactive Quizzes</span></div>
    <div class="feature-item"><i class="fas fa-globe"></i><span>Learn Anywhere</span></div>
  </div>
</section>

<!-- INTRO CARDS -->
<section class="intro page-wrapper">
  <h2 class="section-title" style="text-align:center;margin-bottom:12px">Why Choose Educaster?</h2>
  <p class="section-subtitle" style="text-align:center">Everything you need to grow as an educator</p>
  <div class="intro-grid">
    <div class="intro-card"><i class="fas fa-chalkboard-teacher"></i><h4>Expert Instructors</h4><p>Learn from certified, experienced educators who bring real classroom insights.</p></div>
    <div class="intro-card"><i class="fas fa-graduation-cap"></i><h4>Structured Curriculum</h4><p>Week-by-week content designed for progressive, meaningful learning.</p></div>
    <div class="intro-card"><i class="fas fa-comments"></i><h4>Community Support</h4><p>Connect with fellow teachers, ask questions, and share experiences.</p></div>
    <div class="intro-card"><i class="fas fa-mobile-alt"></i><h4>Learn at Your Pace</h4><p>Access all course materials anytime, from any device.</p></div>
  </div>
</section>

<!-- STATS -->
<section class="stats-section">
  <div class="stats-inner page-wrapper">
    <div class="stat-card"><i class="fas fa-user-tie"></i><h2 id="s1">0</h2><p>Expert Instructors</p></div>
    <div class="stat-card"><i class="fas fa-users"></i><h2 id="s2">0</h2><p>Active Learners</p></div>
    <div class="stat-card"><i class="fas fa-book"></i><h2 id="s3">0</h2><p>Courses Available</p></div>
    <div class="stat-card"><i class="fas fa-globe"></i><h2>Global</h2><p>Learning Community</p></div>
  </div>
</section>

<!-- POPULAR COURSES -->
<section class="popular-section page-wrapper">
  <h2 class="section-title">Popular Courses</h2>
  <p class="section-subtitle">Explore top-rated teacher training programmes</p>
  <div class="courses-grid">
    <?php
    $q = "SELECT c.Course_Id, c.Title, c.Intro_Image, cat.Category_Name,
                 COALESCE(AVG(r.Rating),0) AS avg_rating, COUNT(DISTINCT e.Enrollment_Id) AS enrollments
          FROM course c
          LEFT JOIN course_category cat ON c.Category_Id = cat.Category_Id
          LEFT JOIN review r ON r.Course_Id = c.Course_Id
          LEFT JOIN enrollment e ON e.Course_Id = c.Course_Id
          WHERE c.Is_Active = 1
          GROUP BY c.Course_Id
          ORDER BY enrollments DESC LIMIT 6";
    $res = $connection->query($q);
    while ($row = $res->fetch_assoc()):
        $img = $row['Intro_Image'] ? '/educaster/uploads/'.$row['Intro_Image'] : '/educaster/images/course_default.png';
        $stars = round($row['avg_rating']);
    ?>
    <a href="courses/course_overview.php?id=<?= $row['Course_Id'] ?>" class="course-card">
      <div class="course-img"><img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['Title']) ?>"></div>
      <div class="course-body">
        <span class="course-tag"><?= htmlspecialchars($row['Category_Name'] ?? 'Teacher Training') ?></span>
        <h4><?= htmlspecialchars($row['Title']) ?></h4>
        <div class="course-meta">
          <span class="stars"><?= str_repeat('★',$stars).str_repeat('☆',5-$stars) ?></span>
          <span><?= (int)$row['enrollments'] ?> enrolled</span>
        </div>
      </div>
    </a>
    <?php endwhile; ?>
  </div>
  <div style="text-align:center;margin-top:32px">
    <a href="programs.php" class="btn btn-primary"><i class="fas fa-arrow-right"></i> View All Programmes</a>
  </div>
</section>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
<script>
// Animated counters
function animateCount(id, target, suffix='') {
  let c=0, step=Math.ceil(target/60);
  const el=document.getElementById(id);
  const t=setInterval(()=>{
    c=Math.min(c+step,target);
    el.textContent=c+suffix;
    if(c>=target) clearInterval(t);
  },25);
}
<?php
$tc = $connection->query("SELECT COUNT(*) as t FROM registered_user WHERE Registered_User_Type='INS' AND Is_Approved=1")->fetch_assoc()['t'];
$sc = $connection->query("SELECT COUNT(*) as t FROM registered_user WHERE Registered_User_Type='TCH'")->fetch_assoc()['t'];
$cc = $connection->query("SELECT COUNT(*) as t FROM course WHERE Is_Active=1")->fetch_assoc()['t'];
echo "animateCount('s1',$tc,'+');";
echo "animateCount('s2',$sc,'+');";
echo "animateCount('s3',$cc,'+');";
?>
// Scroll reveal
const observer = new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('visible'); });
},{threshold:0.15});
document.querySelectorAll('.course-card,.intro-card,.stat-card').forEach(el=>observer.observe(el));
</script>
</body>
</html>