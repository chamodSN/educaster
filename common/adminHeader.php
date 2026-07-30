<?php
// common/adminHeader.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/loginFunctions.php';
?>
<header class="site-header admin-header">
  <nav class="navbar">
    <a href="/educaster/admin/admin_dashboard.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER <span style="font-size:11px;background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:50px;margin-left:6px">ADMIN</span>
    </a>
    <ul class="nav-links">
      <li><a href="/educaster/admin/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="/educaster/admin/manage_teachers.php"><i class="fas fa-users"></i> Teachers</a></li>
      <li><a href="/educaster/admin/manage_providers.php"><i class="fas fa-chalkboard-teacher"></i> Providers</a></li>
      <li><a href="/educaster/admin/manage_courses.php"><i class="fas fa-book"></i> Courses</a></li>
      <li><a href="/educaster/admin/manage_inquiries.php"><i class="fas fa-envelope"></i> Inquiries</a></li>
    </ul>
    <div class="nav-auth">
      <span style="color:rgba(255,255,255,0.7);font-size:13px"><i class="fas fa-user-shield"></i> Super Admin</span>
      <a href="/educaster/user/logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>
</header>