<?php
// user/updateAccountDetails.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userData = $_SESSION['userData'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $newEmail    = trim($_POST['email'] ?? '');
    $firstName   = trim($_POST['firstName'] ?? '');
    $lastName    = trim($_POST['lastName'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $newPwd      = $_POST['password'] ?? '';
    $newPwdRepeat= $_POST['passwordRepeat'] ?? '';

    if (empty($newEmail)) { header("Location: updateAccountDetails.php?error=emptyemail"); exit(); }
    if (invalidEmail($newEmail)) { header("Location: updateAccountDetails.php?error=invalidemail"); exit(); }
    if (!empty($newPwd) && $newPwd !== $newPwdRepeat) {
        header("Location: updateAccountDetails.php?error=passwordsdontmatch"); exit();
    }

    if (!empty($newPwd)) {
        $hashed = password_hash($newPwd, PASSWORD_DEFAULT);
        $stmt = $connection->prepare(
            "UPDATE registered_user SET Email=?, First_Name=?, Last_Name=?, Phone_Number=?, Password=? WHERE Registered_User_Id=?"
        );
        $stmt->bind_param("sssssi", $newEmail, $firstName, $lastName, $phone, $hashed, $userData['Registered_User_Id']);
    } else {
        $stmt = $connection->prepare(
            "UPDATE registered_user SET Email=?, First_Name=?, Last_Name=?, Phone_Number=? WHERE Registered_User_Id=?"
        );
        $stmt->bind_param("ssssi", $newEmail, $firstName, $lastName, $phone, $userData['Registered_User_Id']);
    }
    $stmt->execute();
    $_SESSION['userData']['Email'] = $newEmail;
    header("Location: accountDetails.php?update=success"); exit();
}

// Re-fetch current data
$stmt = $connection->prepare("SELECT * FROM registered_user WHERE Registered_User_Id=?");
$stmt->bind_param("i", $userData['Registered_User_Id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Account — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/accountDetails.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div style="max-width:600px;margin:0 auto">
    <div class="auth-card" style="max-width:100%;padding:40px">
      <div class="auth-icon"><i class="fas fa-user-edit"></i></div>
      <h2>Update Account Details</h2>

      <?php
      if (isset($_GET['error'])) {
          $msgs = ['emptyemail'=>'Email is required.','invalidemail'=>'Invalid email format.','passwordsdontmatch'=>'Passwords do not match.'];
          echo '<div class="alert alert-error">' . ($msgs[$_GET['error']] ?? 'Error.') . '</div>';
      }
      ?>

      <form action="updateAccountDetails.php" method="POST" style="text-align:left;margin-top:24px">
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
        <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:14px">Leave password fields blank to keep current password.</p>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="password" class="form-control" placeholder="New password (optional)">
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="passwordRepeat" class="form-control" placeholder="Repeat new password">
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" name="update" class="btn btn-primary" style="flex:1;justify-content:center;padding:13px">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <a href="accountDetails.php" class="btn btn-outline" style="flex:1;justify-content:center;padding:13px">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>