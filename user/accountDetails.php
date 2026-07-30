<?php
// user/accountDetails.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userData = $_SESSION['userData'];

// Re-fetch fresh data
$stmt = $connection->prepare("SELECT * FROM registered_user WHERE Registered_User_Id = ?");
$stmt->bind_param("i", $userData['Registered_User_Id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$enrollCount = $connection->query(
    "SELECT COUNT(*) AS t FROM enrollment WHERE Registered_User_Id = " . (int)$userData['Registered_User_Id']
)->fetch_assoc()['t'];

$completedCount = $connection->query(
    "SELECT COUNT(*) AS t FROM enrollment WHERE Registered_User_Id = " . (int)$userData['Registered_User_Id'] . " AND Is_Completed = 1"
)->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/accountDetails.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<div class="page-wrapper">
  <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
    <div class="alert alert-success"><i class="fas fa-check"></i> Account updated successfully!</div>
  <?php endif; ?>

  <div class="account-layout">
    <!-- Profile Card -->
    <div class="profile-card">
      <div class="avatar">
        <?= strtoupper(substr($user['User_Name'], 0, 1)) ?>
      </div>
      <h2><?= htmlspecialchars($user['User_Name']) ?></h2>
      <span class="role-badge">
        <?php
        $roles = ['TCH'=>'Teacher','INS'=>'Course Provider','SADMIN'=>'Super Admin','STD'=>'Student'];
        echo $roles[$user['Registered_User_Type']] ?? 'User';
        ?>
      </span>
      <p class="joined"><i class="fas fa-calendar"></i> Joined <?= date('F Y', strtotime($user['Created_At'])) ?></p>
      <div class="profile-stats">
        <div><strong><?= $enrollCount ?></strong><span>Enrolled</span></div>
        <div><strong><?= $completedCount ?></strong><span>Completed</span></div>
      </div>
    </div>

    <!-- Details Panel -->
    <div class="account-panel">
      <div class="panel-header">
        <h2><i class="fas fa-user-cog"></i> Account Details</h2>
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
          <span class="detail-value"><?= htmlspecialchars($user['First_Name'].' '.$user['Last_Name']) ?></span>
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
      </div>

      <div class="account-actions">
        <a href="/educaster/dashboard/student_dashboard.php" class="btn btn-primary">
          <i class="fas fa-tachometer-alt"></i> My Dashboard
        </a>
        <a href="/educaster/customerSupport/myInquiries.php" class="btn btn-outline">
          <i class="fas fa-envelope"></i> My Inquiries
        </a>
        <a href="updateAccountDetails.php" class="btn btn-outline">
          <i class="fas fa-lock"></i> Change Password
        </a>
        <button id="deleteBtn" class="btn btn-danger">
          <i class="fas fa-trash"></i> Delete Account
        </button>
        <a href="logout.php" class="btn btn-outline">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <h3><i class="fas fa-exclamation-triangle" style="color:#e74c3c"></i> Delete Account?</h3>
    <p style="color:var(--text-muted);margin:12px 0 24px">This will permanently delete your account, all enrollments, and quiz results. This action cannot be undone.</p>
    <div style="display:flex;gap:12px">
      <a href="deleteAccount.php" class="btn btn-danger" style="flex:1;justify-content:center">Yes, Delete</a>
      <button id="cancelDelete" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</button>
    </div>
  </div>
</div>

<?php include '../common/footer.php'; ?>
<script>
document.getElementById('deleteBtn').addEventListener('click', () => {
  document.getElementById('deleteModal').classList.add('active');
});
document.getElementById('cancelDelete').addEventListener('click', () => {
  document.getElementById('deleteModal').classList.remove('active');
});
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) this.classList.remove('active');
});
</script>
</body>
</html>