<?php
// user/signup.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username     = trim($_POST['userName'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $firstName    = trim($_POST['firstName'] ?? '');
    $lastName     = trim($_POST['lastName'] ?? '');
    $password     = $_POST['password'] ?? '';
    $passwordRep  = $_POST['passwordRepeat'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($passwordRep)) {
        header("Location: signup.php?error=emptyfields"); exit();
    }
    if (invalidUserName($username)) {
        header("Location: signup.php?error=invalidusername"); exit();
    }
    if (invalidEmail($email)) {
        header("Location: signup.php?error=invalidemail"); exit();
    }
    if ($password !== $passwordRep) {
        header("Location: signup.php?error=passwordsdontmatch"); exit();
    }
    if (uidExists($connection, $username) || uidExists($connection, $email)) {
        header("Location: signup.php?error=userexists"); exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare(
        "INSERT INTO registered_user (User_Name, First_Name, Last_Name, Email, Password, Registered_User_Type) VALUES (?,?,?,?,?,'TCH')"
    );
    $stmt->bind_param("sssss", $username, $firstName, $lastName, $email, $hashed);
    if ($stmt->execute()) {
        header("Location: login.php?signup=success"); exit();
    }
    header("Location: signup.php?error=queryfailed"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/login.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<main class="auth-wrapper">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
    <h1>Create Account</h1>
    <p class="auth-sub">Join Educaster as a student or teacher</p>

    <?php
    if (isset($_GET['error'])) {
        $msgs = [
            'emptyfields'       => 'Please fill in all required fields.',
            'invalidusername'   => 'Username must be 3–50 alphanumeric characters.',
            'invalidemail'      => 'Please enter a valid email address.',
            'passwordsdontmatch'=> 'Passwords do not match.',
            'userexists'        => 'Username or email is already taken.',
            'queryfailed'       => 'Something went wrong. Please try again.',
        ];
        echo '<div class="alert alert-error">' . ($msgs[$_GET['error']] ?? 'Error.') . '</div>';
    }
    ?>

    <form action="signup.php" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" name="firstName" class="form-control" placeholder="John">
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" name="lastName" class="form-control" placeholder="Doe">
        </div>
      </div>
      <div class="form-group">
        <label>Username <span class="req">*</span></label>
        <input type="text" name="userName" class="form-control" placeholder="johnteacher" required>
      </div>
      <div class="form-group">
        <label>Email Address <span class="req">*</span></label>
        <input type="email" name="email" class="form-control" placeholder="john@email.com" required>
      </div>
      <div class="form-group">
        <label>Password <span class="req">*</span></label>
        <input type="password" id="pwd" name="password" class="form-control" placeholder="Min 6 characters" required>
        <div id="strengthBar"><div id="strengthFill"></div></div>
        <small id="strengthText"></small>
      </div>
      <div class="form-group">
        <label>Confirm Password <span class="req">*</span></label>
        <input type="password" name="passwordRepeat" class="form-control" placeholder="Repeat password" required>
      </div>
      <button type="submit" name="signup" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;">
        <i class="fas fa-user-plus"></i> Create Account
      </button>
    </form>
    <div class="auth-links">
      <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>
</main>
<?php include '../common/footer.php'; ?>
<script>
const pwd = document.getElementById('pwd');
const fill = document.getElementById('strengthFill');
const txt  = document.getElementById('strengthText');
pwd.addEventListener('input', () => {
  const v = pwd.value;
  let pct = 0, label = '', color = '';
  if (v.length >= 6)  { pct = 33; label = 'Weak';   color = '#e74c3c'; }
  if (v.length >= 8 && /[A-Z]/.test(v)) { pct = 66; label = 'Medium'; color = '#f39c12'; }
  if (v.length >= 10 && /[A-Z]/.test(v) && /\d/.test(v) && /[^a-zA-Z0-9]/.test(v)) {
    pct = 100; label = 'Strong'; color = '#04AA6D';
  }
  fill.style.width = pct + '%';
  fill.style.background = color;
  txt.textContent = label;
  txt.style.color = color;
});
</script>
</body>
</html>