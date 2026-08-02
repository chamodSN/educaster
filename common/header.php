<?php
// common/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<header class="site-header">
  <nav class="navbar">
    <a href="<?= BASE_PATH ?>/home.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER
    </a>
    <ul class="nav-links">
      <li><a href="<?= BASE_PATH ?>/home.php" class="<?= $currentPage === 'home.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="<?= BASE_PATH ?>/programs.php" class="<?= $currentPage === 'programs.php' ? 'active' : '' ?>"><i class="fas fa-book-open"></i> Programmes</a></li>
      <li><a href="<?= BASE_PATH ?>/aboutus.php" class="<?= $currentPage === 'aboutus.php' ? 'active' : '' ?>"><i class="fas fa-info-circle"></i> About Us</a></li>
      <li><a href="<?= BASE_PATH ?>/customerSupport/contactUs.php" class="<?= $currentPage === 'contactUs.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Contact</a></li>
      <?php if (isLoggedIn() && !isAdmin() && !isProvider()): ?>
        <li><a href="<?= BASE_PATH ?>/dashboard/student_dashboard.php" class="<?= $currentPage === 'student_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <?php endif; ?>
    </ul>
    <div class="nav-auth">
      <?php if (isLoggedIn()): ?>
        <?php if (isAdmin()): ?>
          <a href="<?= BASE_PATH ?>/admin/admin_dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-user-shield"></i> Admin Panel</a>
        <?php elseif (isProvider()): ?>
          <a href="<?= BASE_PATH ?>/provider/provider_dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-chalkboard-teacher"></i> Provider Panel</a>
        <?php else: ?>
          <a href="<?= BASE_PATH ?>/user/accountDetails.php" class="btn btn-outline btn-sm">
            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['userData']['User_Name']) ?>
          </a>
        <?php endif; ?>
        <form action="<?= BASE_PATH ?>/user/logout.php" method="POST" style="display:inline">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
      <?php else: ?>
        <a href="<?= BASE_PATH ?>/user/login.php" class="btn btn-primary btn-sm"><i class="fas fa-sign-in-alt"></i> Sign In</a>
        <a href="<?= BASE_PATH ?>/user/signup.php" class="btn btn-outline btn-sm"><i class="fas fa-user-plus"></i> Sign Up</a>
      <?php endif; ?>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
      <i class="fas fa-bars"></i>
    </button>
  </nav>
</header>