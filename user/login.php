<?php
// user/login.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

if (isLoggedIn()) {
    if (isAdmin())    { header("Location: /educaster/admin/admin_dashboard.php"); exit(); }
    if (isProvider()) { header("Location: /educaster/provider/provider_dashboard.php"); exit(); }
    header("Location: /educaster/home.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $identifier = trim($_POST['userId'] ?? '');
    $password   = $_POST['userPwd'] ?? '';

    if (empty($identifier) || empty($password)) {
        header("Location: login.php?error=emptyfields"); exit();
    }

    // Super admin check
    if (($identifier === ADMIN_EMAIL || $identifier === 'superadmin') && $password === ADMIN_PASSWORD) {
        $_SESSION['userData'] = [
            'Registered_User_Id'   => 0,
            'User_Name'            => 'superadmin',
            'Email'                => ADMIN_EMAIL,
            'Registered_User_Type' => 'SADMIN'
        ];
        header("Location: /educaster/admin/admin_dashboard.php"); exit();
    }

    $userData = uidExists($connection, $identifier);
    if (!$userData || !password_verify($password, $userData['Password'])) {
        header("Location: login.php?error=wronglogin"); exit();
    }

    // Check if provider is approved
    if ($userData['Registered_User_Type'] === 'INS' && $userData['Is_Approved'] == 0) {
        header("Location: login.php?error=notapproved"); exit();
    }

    $_SESSION['userData'] = $userData;

    if (isProvider()) { header("Location: /educaster/provider/provider_dashboard.php"); exit(); }
    header("Location: /educaster/home.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/login.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<main class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-icon"><i class="fas fa-graduation-cap"></i></div>
    <h1>Welcome Back</h1>
    <p class="auth-sub">Sign in to continue your learning journey</p>

    <?php
    if (isset($_GET['error'])) {
        $msgs = [
            'emptyfields'  => 'Please fill in all fields.',
            'wronglogin'   => 'Invalid username/email or password.',
            'notapproved'  => 'Your course provider account is pending approval.',
        ];
        echo '<div class="alert alert-error">' . ($msgs[$_GET['error']] ?? 'An error occurred.') . '</div>';
    }
    if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
        echo '<div class="alert alert-success">Account created! You can now log in.</div>';
    }
    ?>

    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="userId">Username or Email</label>
        <input type="text" id="userId" name="userId" class="form-control" placeholder="Enter username or email" required>
      </div>
      <div class="form-group">
        <label for="userPwd">Password</label>
        <div class="input-icon-wrap">
          <input type="password" id="userPwd" name="userPwd" class="form-control" placeholder="Enter password" required>
          <button type="button" class="toggle-pwd" onclick="togglePwd()"><i class="fas fa-eye" id="eyeIcon"></i></button>
        </div>
      </div>
      <button type="submit" name="login" class="btn btn-primary" style="width:100%; justify-content:center; padding:13px;">
        <i class="fas fa-sign-in-alt"></i> Log In
      </button>
    </form>

    <div class="auth-links">
      <p>Don't have an account? <a href="signup.php">Sign Up as Student/Teacher</a></p>
      <p>Want to offer courses? <a href="register_provider.php">Apply as Course Provider</a></p>
    </div>
  </div>
</main>

<?php include '../common/footer.php'; ?>
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