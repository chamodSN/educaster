<?php
// common/providerHeader.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/loginFunctions.php';

// Count pending inquiries for notification badge
require_once __DIR__ . '/config.php';
$providerId = (int)($_SESSION['userData']['Registered_User_Id'] ?? 0);
$pendingInq = 0;
if ($providerId) {
    $res = $connection->query(
        "SELECT COUNT(*) AS t FROM inquiry i
         JOIN course c ON c.Course_Id = i.Course_Id
         WHERE c.Provider_Id = $providerId AND i.Reply IS NULL"
    );
    $pendingInq = $res ? (int)$res->fetch_assoc()['t'] : 0;
}
?>
<header class="site-header provider-header">
  <nav class="navbar">
    <a href="/educaster/provider/provider_dashboard.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER
      <span style="font-size:11px;background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:50px;margin-left:6px">PROVIDER</span>
    </a>
    <ul class="nav-links">
      <li><a href="/educaster/provider/provider_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="/educaster/provider/manage_courses.php"><i class="fas fa-book"></i> My Courses</a></li>
      <li><a href="/educaster/provider/create_course.php"><i class="fas fa-plus-circle"></i> New Course</a></li>
      <li>
        <a href="/educaster/provider/provider_inquiries.php" style="position:relative">
          <i class="fas fa-envelope"></i> Inquiries
          <?php if ($pendingInq > 0): ?>
            <span style="position:absolute;top:-4px;right:-8px;background:#e74c3c;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700"><?= $pendingInq ?></span>
          <?php endif; ?>
        </a>
      </li>
    </ul>
    <div class="nav-auth">
      <a href="/educaster/user/accountDetails.php" class="btn btn-outline btn-sm">
        <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['userData']['User_Name'] ?? 'Provider') ?>
      </a>
      <a href="/educaster/user/logout.php" class="btn btn-danger btn-sm">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu"><i class="fas fa-bars"></i></button>
  </nav>
</header>