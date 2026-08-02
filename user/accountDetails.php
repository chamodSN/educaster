<?php
// user/accountDetails.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

// The super-admin isn't a database row, so this page doesn't apply to it.
if (isAdmin()) {
    header('Location: ' . BASE_PATH . '/admin/admin_dashboard.php');
    exit();
}

$userData = $_SESSION['userData'];

$stmt = $connection->prepare('SELECT * FROM registered_user WHERE Registered_User_Id = ?');
$stmt->bind_param('i', $userData['Registered_User_Id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    // Account no longer exists (e.g. deleted elsewhere) — clear the stale session.
    session_destroy();
    header('Location: ' . BASE_PATH . '/user/login.php');
    exit();
}

$enrollCount = $connection->query(
    'SELECT COUNT(*) AS t FROM enrollment WHERE Registered_User_Id = ' . (int) $userData['Registered_User_Id']
)->fetch_assoc()['t'];

$completedCount = $connection->query(
    'SELECT COUNT(*) AS t FROM enrollment WHERE Registered_User_Id = ' . (int) $userData['Registered_User_Id'] . ' AND Is_Completed = 1'
)->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/accountDetails.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<div class="page-wrapper">
  <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
    <div class="alert alert-success"><i class="fas fa-circle-check"></i> Account updated successfully!</div>
  <?php endif; ?>

  <div class="account-layout">
    <div class="profile-card">
      <div class="avatar-initial"><?= strtoupper(substr($user['User_Name'], 0, 1)) ?></div>
      <h2><?= htmlspecialchars($user['User_Name']) ?></h2>
      <span class="role-badge"><?= role_label($user['Registered_User_Type']) ?></span>
      <p class="joined"><i class="fas fa-calendar"></i> Joined <?= date('F Y', strtotime($user['Created_At'])) ?></p>
      <div class="profile-stats">
        <div><strong><?= $enrollCount ?></strong><span>Enrolled</span></div>
        <div><strong><?= $completedCount ?></strong><span>Completed</span></div>
      </div>
    </div>

    <div class="account-panel">
      <div class="panel-header">
        <h2><i class="fas fa-user-gear"></i> Account Details</h2>
        <a href="updateAccountDetails.php" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
      </div>

      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-user"></i> Username</span>
          <span class="detail-value"><?= htmlspecialchars($user['User_Name']) ?></span>
        </div>
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-envelope"></i> Email</span>
          <span class="detail-value"><?= htmlspecialchars($user['Email']) ?></span>
        </div>
        <?php if ($user['First_Name']): ?>
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-id-card"></i> Full Name</span>
          <span class="detail-value"><?= htmlspecialchars(trim($user['First_Name'] . ' ' . $user['Last_Name'])) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($user['Phone_Number']): ?>
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-phone"></i> Phone</span>
          <span class="detail-value"><?= htmlspecialchars($user['Phone_Number']) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($user['Gender']): ?>
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-venus-mars"></i> Gender</span>
          <span class="detail-value"><?= htmlspecialchars($user['Gender']) ?></span>
        </div>
        <?php endif; ?>
        <?php if ($user['Registered_User_Type'] === 'INS' && $user['Expertise']): ?>
        <div class="detail-item">
          <span class="detail-label"><i class="fas fa-briefcase"></i> Expertise</span>
          <span class="detail-value"><?= htmlspecialchars($user['Expertise']) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <div class="account-actions">
        <?php if ($user['Registered_User_Type'] === 'INS'): ?>
          <a href="<?= BASE_PATH ?>/provider/provider_dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Provider Dashboard</a>
        <?php else: ?>
          <a href="<?= BASE_PATH ?>/dashboard/student_dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
        <?php endif; ?>
        <a href="<?= BASE_PATH ?>/customerSupport/myInquiries.php" class="btn btn-outline"><i class="fas fa-envelope"></i> My Inquiries</a>
        <a href="updateAccountDetails.php" class="btn btn-outline"><i class="fas fa-lock"></i> Change Password</a>
        <button type="button" id="deleteBtn" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Account</button>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <h3><i class="fas fa-triangle-exclamation" style="color:var(--red)"></i> Delete Account?</h3>
    <p style="color:var(--text-muted);margin:12px 0 24px">This will permanently delete your account, all enrollments, and quiz results. This action cannot be undone.</p>
    <div style="display:flex;gap:12px">
      <form action="deleteAccount.php" method="POST" style="flex:1">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-block">Yes, Delete</button>
      </form>
      <button type="button" id="cancelDelete" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</button>
    </div>
  </div>
</div>

<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
<script>
document.getElementById('deleteBtn').addEventListener('click', () => {
  document.getElementById('deleteModal').classList.add('active');
});
document.getElementById('cancelDelete').addEventListener('click', () => {
  document.getElementById('deleteModal').classList.remove('active');
});
document.getElementById('deleteModal').addEventListener('click', function (e) {
  if (e.target === this) this.classList.remove('active');
});
</script>
</body>
</html>