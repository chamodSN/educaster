<?php
// common/adminHeader.php
if (session_status() === PHP_SESSION_NONE) session_start();
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<header class="site-header admin-header">
  <nav class="navbar">
    <a href="<?= BASE_PATH ?>/admin/admin_dashboard.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER <span class="role-tag">ADMIN</span>
    </a>
    <ul class="nav-links">
      <li><a href="<?= BASE_PATH ?>/admin/admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="<?= BASE_PATH ?>/admin/manage_teachers.php" class="<?= $currentPage === 'manage_teachers.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Teachers</a></li>
      <li><a href="<?= BASE_PATH ?>/admin/manage_providers.php" class="<?= $currentPage === 'manage_providers.php' ? 'active' : '' ?>"><i class="fas fa-chalkboard-teacher"></i> Providers</a></li>
      <li><a href="<?= BASE_PATH ?>/admin/manage_courses.php" class="<?= $currentPage === 'manage_courses.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> Courses</a></li>
      <li><a href="<?= BASE_PATH ?>/admin/course_stats.php" class="<?= $currentPage === 'course_stats.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Statistics</a></li>
      <li><a href="<?= BASE_PATH ?>/admin/manage_inquiries.php" class="<?= $currentPage === 'manage_inquiries.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Inquiries</a></li>
    </ul>
    <div class="nav-auth">
      <span style="color:rgba(255,255,255,.65);font-size:13px;white-space:nowrap"><i class="fas fa-user-shield"></i> Super Admin</span>
      <form action="<?= BASE_PATH ?>/user/logout.php" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
      </form>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu"><i class="fas fa-bars"></i></button>
  </nav>
</header>