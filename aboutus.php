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
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="about-hero">
  <div class="page-wrapper">
    <h1>About <span class="highlight">Educaster</span></h1>
    <p>Building the next generation of exceptional teachers — one course at a time.</p>
    <div class="hero-cta">
      <a href="programs.php" class="btn btn-primary"><i class="fas fa-book-open"></i> Explore Programmes</a>
      <a href="user/signup.php" class="btn btn-outline" style="border-color:#fff;color:#fff"><i class="fas fa-user-plus"></i> Join Today</a>
    </div>
  </div>
</section>

<section class="about-mission page-wrapper">
  <div class="mission-grid">
    <div class="mission-text">
      <span class="section-tag">Our Mission</span>
      <h2>Empowering Educators Worldwide</h2>
      <p>Educaster is a dynamic platform designed to empower educators with the latest pedagogical tools and methodologies. We cater to both seasoned professionals and aspiring teachers, offering comprehensive resources tailored to the diverse needs of today's classrooms.</p>
      <p style="margin-top:14px">From interactive lesson plans and instructional videos to insightful articles and community forums, we foster a collaborative learning environment where educators can grow, exchange ideas, and inspire each other.</p>
      <div class="mission-badges">
        <span><i class="fas fa-check-circle"></i> Expert-led courses</span>
        <span><i class="fas fa-check-circle"></i> Flexible learning</span>
        <span><i class="fas fa-check-circle"></i> Certified programmes</span>
      </div>
    </div>
    <div class="mission-visual">
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
    <h2 class="section-title" style="text-align:center">Our Core Values</h2>
    <p class="section-subtitle" style="text-align:center">Principles that guide everything we do</p>
    <div class="values-grid">
      <div class="value-card"><i class="fas fa-heart"></i><h4>Passion</h4><p>We believe great teaching starts with a genuine love for learning and sharing knowledge.</p></div>
      <div class="value-card"><i class="fas fa-shield-alt"></i><h4>Integrity</h4><p>We uphold the highest standards of academic honesty and professional ethics.</p></div>
      <div class="value-card"><i class="fas fa-users"></i><h4>Community</h4><p>We build supportive networks where educators thrive together.</p></div>
      <div class="value-card"><i class="fas fa-lightbulb"></i><h4>Innovation</h4><p>We constantly evolve our platform to meet the changing needs of modern education.</p></div>
    </div>
  </div>
</section>

<section class="about-cta">
  <div class="page-wrapper" style="text-align:center">
    <h2>Ready to Transform Your Teaching?</h2>
    <p>Join thousands of educators who trust Educaster to advance their careers.</p>
    <a href="user/signup.php" class="btn btn-primary" style="font-size:17px;padding:14px 36px;margin-top:20px">
      <i class="fas fa-rocket"></i> Get Started Today
    </a>
  </div>
</section>

<?php include 'common/footer.php'; ?>
</body>
</html>