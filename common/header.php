<?php
// common/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header class="site-header">
  <nav class="navbar">
    <a href="/educaster/home.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER
    </a>
    <ul class="nav-links">
      <li><a href="/educaster/home.php"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="/educaster/programs.php"><i class="fas fa-book-open"></i> Programmes</a></li>
      <li><a href="/educaster/aboutus.php"><i class="fas fa-info-circle"></i> About Us</a></li>
      <li><a href="/educaster/customerSupport/contactUs.php"><i class="fas fa-envelope"></i> Contact</a></li>
      <?php if (isLoggedIn()): ?>
        <li><a href="/educaster/dashboard/student_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <?php endif; ?>
    </ul>
    <div class="nav-auth">
      <?php if (isLoggedIn()): ?>
        <a href="/educaster/user/accountDetails.php" class="btn btn-outline btn-sm">
          <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['userData']['User_Name']) ?>
        </a>
        <a href="/educaster/user/logout.php" class="btn btn-primary btn-sm">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      <?php else: ?>
        <a href="/educaster/user/login.php" class="btn btn-primary btn-sm">
          <i class="fas fa-sign-in-alt"></i> Sign In
        </a>
        <a href="/educaster/user/signup.php" class="btn btn-outline btn-sm">
          <i class="fas fa-user-plus"></i> Sign Up
        </a>
      <?php endif; ?>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu">
      <i class="fas fa-bars"></i>
    </button>
  </nav>
</header>