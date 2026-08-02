<?php
// common/providerHeader.php
if (session_status() === PHP_SESSION_NONE) session_start();

$providerId = currentUserId();
$pendingInq = 0;
if ($providerId && isset($connection)) {
    $res = $connection->prepare(
        "SELECT COUNT(*) AS t FROM inquiry i
         JOIN course c ON c.Course_Id = i.Course_Id
         WHERE c.Provider_Id = ? AND i.Reply IS NULL"
    );
    $res->bind_param('i', $providerId);
    $res->execute();
    $pendingInq = (int) $res->get_result()->fetch_assoc()['t'];
}
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<header class="site-header provider-header">
  <nav class="navbar">
    <a href="<?= BASE_PATH ?>/provider/provider_dashboard.php" class="brand">
      <i class="fas fa-graduation-cap"></i> EDUCASTER <span class="role-tag">PROVIDER</span>
    </a>
    <ul class="nav-links">
      <li><a href="<?= BASE_PATH ?>/provider/provider_dashboard.php" class="<?= $currentPage === 'provider_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="<?= BASE_PATH ?>/provider/manage_courses.php" class="<?= $currentPage === 'manage_courses.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> My Courses</a></li>
      <li><a href="<?= BASE_PATH ?>/provider/create_course.php" class="<?= $currentPage === 'create_course.php' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> New Course</a></li>
      <li>
        <a href="<?= BASE_PATH ?>/provider/provider_inquiries.php" class="<?= $currentPage === 'provider_inquiries.php' ? 'active' : '' ?>" style="position:relative">
          <i class="fas fa-envelope"></i> Inquiries
          <?php if ($pendingInq > 0): ?><span class="notif-dot"></span><?php endif; ?>
        </a>
      </li>
    </ul>
    <div class="nav-auth">
      <a href="<?= BASE_PATH ?>/user/accountDetails.php" class="btn btn-outline btn-sm">
        <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['userData']['User_Name'] ?? 'Provider') ?>
      </a>
      <form action="<?= BASE_PATH ?>/user/logout.php" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
      </form>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Menu"><i class="fas fa-bars"></i></button>
  </nav>
</header>