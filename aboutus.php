<?php
// aboutus.php
require_once 'common/config.php';
require_once 'common/loginFunctions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us — Educaster</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/aboutus.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="about-hero">
  <div class="page-wrapper" style="padding-top:0;padding-bottom:0">
    <h1>About <span class="mark">Educaster<svg viewBox="0 0 260 24" preserveAspectRatio="none"><path d="M2 16 C 60 6, 120 22, 180 10 S 240 4, 258 14"/></svg></span></h1>
    <p>Building the next generation of exceptional teachers — one course at a time.</p>
    <div class="hero-cta">
      <a href="programs.php" class="btn btn-primary"><i class="fas fa-book-open"></i> Explore Programmes</a>
      <a href="user/signup.php" class="btn btn-ghost"><i class="fas fa-user-plus"></i> Join Today</a>
    </div>
  </div>
</section>

<section class="about-mission page-wrapper">
  <div class="mission-grid">
    <div class="mission-text reveal">
      <span class="section-tag">Our Mission</span>
      <h2>Empowering Educators Worldwide</h2>
      <p>Educaster is a dynamic platform designed to empower educators with the latest pedagogical tools and methodologies. We cater to both seasoned professionals and aspiring teachers, offering resources tailored to the diverse needs of today's classrooms.</p>
      <p style="margin-top:14px">From interactive lesson plans and instructional videos to insightful quizzes and provider Q&amp;A, we foster a space where educators can grow, ask questions, and put new ideas into practice immediately.</p>
      <div class="mission-badges">
        <span><i class="fas fa-circle-check"></i> Expert-led courses</span>
        <span><i class="fas fa-circle-check"></i> Flexible learning</span>
        <span><i class="fas fa-circle-check"></i> Certified programmes</span>
      </div>
    </div>
    <div class="mission-visual reveal">
      <div class="about-stat-grid">
        <div class="about-stat"><span>50+</span><p>Expert Instructors</p></div>
        <div class="about-stat"><span>1000+</span><p>Graduates</p></div>
        <div class="about-stat"><span>20+</span><p>Course Categories</p></div>
        <div class="about-stat"><span>5★</span><p>Average Rating</p></div>
      </div>
    </div>
  </div>
</section>

<section class="values-section">
  <div class="page-wrapper">
    <div class="text-center" style="margin-bottom:8px">
      <span class="section-tag">What We Stand For</span>
      <h2 class="section-title" style="display:block">Our Core Values</h2>
    </div>
    <div class="values-grid">
      <div class="value-card reveal"><i class="fas fa-heart"></i><h4>Passion</h4><p>We believe great teaching starts with a genuine love for learning and sharing knowledge.</p></div>
      <div class="value-card reveal"><i class="fas fa-shield-halved"></i><h4>Integrity</h4><p>We uphold the highest standards of academic honesty and professional ethics.</p></div>
      <div class="value-card reveal"><i class="fas fa-people-group"></i><h4>Community</h4><p>We build supportive networks where educators thrive together.</p></div>
      <div class="value-card reveal"><i class="fas fa-lightbulb"></i><h4>Innovation</h4><p>We constantly evolve our platform to meet the changing needs of modern education.</p></div>
    </div>
  </div>
</section>

<section class="page-wrapper team-section">
  <div class="text-center" style="margin-bottom:36px">
    <span class="section-tag">The People Behind It</span>
    <h2 class="section-title" style="display:block">Meet a Few of Our Course Providers</h2>
  </div>
  <div class="team-grid">
    <div class="team-card reveal">
      <img src="https://i.pravatar.cc/160?img=47" alt="">
      <h4>Amara Silva</h4>
      <span>Educational Technology</span>
    </div>
    <div class="team-card reveal">
      <img src="https://i.pravatar.cc/160?img=12" alt="">
      <h4>Nadia Perera</h4>
      <span>Pedagogy &amp; Teaching Methods</span>
    </div>
    <div class="team-card reveal">
      <img src="https://i.pravatar.cc/160?img=33" alt="">
      <h4>Ruwan Fernando</h4>
      <span>Classroom Management</span>
    </div>
    <div class="team-card reveal">
      <img src="https://i.pravatar.cc/160?img=5" alt="">
      <h4>Dilani Jayasuriya</h4>
      <span>Special &amp; Inclusive Education</span>
    </div>
  </div>
</section>

<section class="about-cta">
  <div class="page-wrapper" style="text-align:center;padding-top:0;padding-bottom:0">
    <h2>Ready to Transform Your Teaching?</h2>
    <p>Join thousands of educators who trust Educaster to advance their careers.</p>
    <a href="user/signup.php" class="btn btn-white btn-lg" style="margin-top:20px">
      <i class="fas fa-rocket"></i> Get Started Today
    </a>
  </div>
</section>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>