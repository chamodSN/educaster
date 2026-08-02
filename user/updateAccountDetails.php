<?php
// user/updateAccountDetails.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if (isAdmin()) {
    header('Location: ' . BASE_PATH . '/admin/admin_dashboard.php');
    exit();
}

$userData = $_SESSION['userData'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    verify_csrf();

    $newEmail     = trim($_POST['email'] ?? '');
    $firstName    = trim($_POST['firstName'] ?? '');
    $lastName     = trim($_POST['lastName'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $gender       = $_POST['gender'] ?? '';
    $newPwd       = $_POST['password'] ?? '';
    $newPwdRepeat = $_POST['passwordRepeat'] ?? '';

    $allowedGenders = ['Male', 'Female', 'Other', 'Prefer not to say'];
    $gender = in_array($gender, $allowedGenders, true) ? $gender : null;

    if (empty($newEmail)) { header('Location: updateAccountDetails.php?error=emptyemail'); exit(); }
    if (invalidEmail($newEmail)) { header('Location: updateAccountDetails.php?error=invalidemail'); exit(); }
    if (!empty($newPwd) && invalidPassword($newPwd)) { header('Location: updateAccountDetails.php?error=weakpassword'); exit(); }
    if (!empty($newPwd) && $newPwd !== $newPwdRepeat) {
        header('Location: updateAccountDetails.php?error=passwordsdontmatch'); exit();
    }

    // Email must stay unique across the table (excluding the current user).
    $dupCheck = $connection->prepare('SELECT Registered_User_Id FROM registered_user WHERE Email = ? AND Registered_User_Id != ?');
    $dupCheck->bind_param('si', $newEmail, $userData['Registered_User_Id']);
    $dupCheck->execute();
    if ($dupCheck->get_result()->num_rows > 0) {
        header('Location: updateAccountDetails.php?error=emailtaken'); exit();
    }

    if (!empty($newPwd)) {
        $hashed = password_hash($newPwd, PASSWORD_DEFAULT);
        $stmt = $connection->prepare(
            'UPDATE registered_user SET Email=?, First_Name=?, Last_Name=?, Phone_Number=?, Gender=?, Password=? WHERE Registered_User_Id=?'
        );
        $stmt->bind_param('ssssssi', $newEmail, $firstName, $lastName, $phone, $gender, $hashed, $userData['Registered_User_Id']);
    } else {
        $stmt = $connection->prepare(
            'UPDATE registered_user SET Email=?, First_Name=?, Last_Name=?, Phone_Number=?, Gender=? WHERE Registered_User_Id=?'
        );
        $stmt->bind_param('sssssi', $newEmail, $firstName, $lastName, $phone, $gender, $userData['Registered_User_Id']);
    }
    $stmt->execute();
    $_SESSION['userData']['Email'] = $newEmail;
    header('Location: accountDetails.php?update=success'); exit();
}

$stmt = $connection->prepare('SELECT * FROM registered_user WHERE Registered_User_Id=?');
$stmt->bind_param('i', $userData['Registered_User_Id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Account — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/accountDetails.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="narrow-wrapper">
    <div class="auth-card auth-card-wide">
      <div class="auth-icon"><i class="fas fa-user-pen"></i></div>
      <h2>Update Account Details</h2>

      <?php
      if (isset($_GET['error'])) {
          $msgs = [
              'emptyemail'         => 'Email is required.',
              'invalidemail'       => 'Invalid email format.',
              'weakpassword'       => 'New password must be at least 6 characters.',
              'passwordsdontmatch' => 'Passwords do not match.',
              'emailtaken'         => 'That email is already used by another account.',
          ];
          echo '<div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> ' . ($msgs[$_GET['error']] ?? 'Error.') . '</div>';
      }
      ?>

      <form action="updateAccountDetails.php" method="POST" style="text-align:left;margin-top:24px">
        <?= csrf_field() ?>
        <div class="form-row">
          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="firstName" class="form-control" value="<?= htmlspecialchars($user['First_Name'] ?? '') ?>" placeholder="First name">
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="lastName" class="form-control" value="<?= htmlspecialchars($user['Last_Name'] ?? '') ?>" placeholder="Last name">
          </div>
        </div>
        <div class="form-group">
          <label>Email Address <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email']) ?>" required>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['Phone_Number'] ?? '') ?>" placeholder="+94 77 123 4567">
        </div>
        <div class="form-group">
          <label>Gender</label>
          <select name="gender" class="form-control">
            <option value="">Prefer not to say</option>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
              <option <?= ($user['Gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px">Leave password fields blank to keep your current password.</p>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="password" class="form-control" placeholder="New password (optional)" minlength="6">
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="passwordRepeat" class="form-control" placeholder="Repeat new password">
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" name="update" class="btn btn-primary" style="flex:1;justify-content:center">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <a href="accountDetails.php" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>