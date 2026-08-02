<?php
// user/login.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

if (isLoggedIn()) {
    redirectToDashboard();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    verify_csrf();

    $identifier = trim($_POST['userId'] ?? '');
    $password   = $_POST['userPwd'] ?? '';

    if (empty($identifier) || empty($password)) {
        header('Location: login.php?error=emptyfields');
        exit();
    }

    // Super admin check (this account lives in .env, not the database).
    if (($identifier === ADMIN_EMAIL || strcasecmp($identifier, 'superadmin') === 0)
        && ADMIN_PASSWORD_HASH !== ''
        && password_verify($password, ADMIN_PASSWORD_HASH)
    ) {
        session_regenerate_id(true);
        $_SESSION['userData'] = [
            'Registered_User_Id'   => 0,
            'User_Name'            => 'superadmin',
            'Email'                => ADMIN_EMAIL,
            'Registered_User_Type' => 'SADMIN',
        ];
        header('Location: ' . BASE_PATH . '/admin/admin_dashboard.php');
        exit();
    }

    $userData = uidExists($connection, $identifier);
    if (!$userData || !password_verify($password, $userData['Password'])) {
        header('Location: login.php?error=wronglogin');
        exit();
    }

    // Course providers need admin approval before they can log in.
    if ($userData['Registered_User_Type'] === 'INS' && (int) $userData['Is_Approved'] === 0) {
        $reqStmt = $connection->prepare('SELECT Status FROM provider_request WHERE User_Id = ? ORDER BY Request_Id DESC LIMIT 1');
        $reqStmt->bind_param('i', $userData['Registered_User_Id']);
        $reqStmt->execute();
        $status = $reqStmt->get_result()->fetch_assoc()['Status'] ?? 'pending';
        header('Location: login.php?error=' . ($status === 'rejected' ? 'rejected' : 'notapproved'));
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['userData'] = $userData;
    redirectToDashboard();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<main class="auth-page">
  <div class="auth-card">
    <div class="auth-icon"><i class="fas fa-graduation-cap"></i></div>
    <h1>Welcome Back</h1>
    <p class="auth-sub">Sign in to continue your learning journey</p>

    <?php
    if (isset($_GET['error'])) {
        $msgs = [
            'emptyfields' => 'Please fill in all fields.',
            'wronglogin'  => 'Invalid username/email or password.',
            'notapproved' => 'Your course provider application is still pending approval.',
            'rejected'    => 'Your course provider application was not approved. Contact support@educaster.com for details.',
        ];
        echo '<div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> ' . ($msgs[$_GET['error']] ?? 'An error occurred.') . '</div>';
    }
    if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
        echo '<div class="alert alert-success"><i class="fas fa-circle-check"></i> Account created! You can now log in.</div>';
    }
    if (isset($_GET['registered']) && $_GET['registered'] === 'provider') {
        echo '<div class="alert alert-info"><i class="fas fa-circle-info"></i> Application submitted! We will email you once an admin reviews it.</div>';
    }
    if (isset($_GET['loggedout'])) {
        echo '<div class="alert alert-success"><i class="fas fa-circle-check"></i> You have been logged out.</div>';
    }
    ?>

    <form action="login.php" method="POST">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="userId">Username or Email</label>
        <input type="text" id="userId" name="userId" class="form-control" placeholder="Enter username or email" required autofocus>
      </div>
      <div class="form-group">
        <label for="userPwd">Password</label>
        <div class="input-icon-wrap">
          <input type="password" id="userPwd" name="userPwd" class="form-control" placeholder="Enter password" required>
          <button type="button" class="toggle-pwd" onclick="togglePwd()"><i class="fas fa-eye" id="eyeIcon"></i></button>
        </div>
      </div>
      <button type="submit" name="login" class="btn btn-primary btn-block btn-lg">
        <i class="fas fa-sign-in-alt"></i> Log In
      </button>
    </form>

    <div class="auth-links">
      <p>Don't have an account? <a href="signup.php">Sign Up as Student/Teacher</a></p>
      <p>Want to offer courses? <a href="register_provider.php">Apply as Course Provider</a></p>
    </div>

    <div class="auth-demo">
      <strong>Demo logins</strong>
      <span>Admin — superadmin / Admin@2025</span>
      <span>Provider — demoprovider / Passw0rd!</span>
      <span>Teacher — demoteacher / Passw0rd!</span>
    </div>
  </div>
</main>

<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
<script>
function togglePwd() {
  const f = document.getElementById('userPwd');
  const i = document.getElementById('eyeIcon');
  if (f.type === 'password') { f.type = 'text'; i.className = 'fas fa-eye-slash'; }
  else { f.type = 'password'; i.className = 'fas fa-eye'; }
}
</script>
</body>
</html>