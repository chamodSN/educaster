<?php
// user/register_provider.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username    = trim($_POST['userName'] ?? '');
    $firstName   = trim($_POST['firstName'] ?? '');
    $lastName    = trim($_POST['lastName'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $password    = $_POST['password'] ?? '';
    $passwordRep = $_POST['passwordRepeat'] ?? '';
    $category    = $_POST['category'] ?? '';

    if (empty($username)||empty($firstName)||empty($lastName)||empty($email)||empty($password)||empty($passwordRep)) {
        header("Location: register_provider.php?error=emptyfields"); exit();
    }
    if (invalidUserName($username)) { header("Location: register_provider.php?error=invalidusername"); exit(); }
    if (invalidEmail($email))       { header("Location: register_provider.php?error=invalidemail"); exit(); }
    if ($password !== $passwordRep) { header("Location: register_provider.php?error=passwordsdontmatch"); exit(); }
    if (uidExists($connection, $username)||uidExists($connection, $email)) {
        header("Location: register_provider.php?error=userexists"); exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare(
        "INSERT INTO registered_user (User_Name, First_Name, Last_Name, Email, Phone_Number, Password, Registered_User_Type, Is_Approved) VALUES (?,?,?,?,?,?,'INS',0)"
    );
    $stmt->bind_param("ssssss", $username, $firstName, $lastName, $email, $phone, $hashed);
    if ($stmt->execute()) {
        $userId = $connection->insert_id;
        $req = $connection->prepare("INSERT INTO provider_request (User_Id) VALUES (?)");
        $req->bind_param("i", $userId);
        $req->execute();
        header("Location: login.php?registered=provider"); exit();
    }
    header("Location: register_provider.php?error=queryfailed"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply as Course Provider — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/login.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<main class="auth-wrapper">
  <div class="auth-card" style="max-width:560px">
    <div class="auth-icon"><i class="fas fa-chalkboard-teacher"></i></div>
    <h1>Apply as Course Provider</h1>
    <p class="auth-sub">Your account will be reviewed and approved by the admin</p>

    <?php
    if (isset($_GET['error'])) {
        $msgs = ['emptyfields'=>'Fill all required fields.','invalidusername'=>'Invalid username.','invalidemail'=>'Invalid email.','passwordsdontmatch'=>'Passwords do not match.','userexists'=>'Username or email taken.','queryfailed'=>'Error. Try again.'];
        echo '<div class="alert alert-error">' . ($msgs[$_GET['error']] ?? 'Error.') . '</div>';
    }
    ?>

    <form action="register_provider.php" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>First Name <span class="req">*</span></label>
          <input type="text" name="firstName" class="form-control" required placeholder="Jane">
        </div>
        <div class="form-group">
          <label>Last Name <span class="req">*</span></label>
          <input type="text" name="lastName" class="form-control" required placeholder="Smith">
        </div>
      </div>
      <div class="form-group">
        <label>Username <span class="req">*</span></label>
        <input type="text" name="userName" class="form-control" required placeholder="janesmith">
      </div>
      <div class="form-group">
        <label>Email <span class="req">*</span></label>
        <input type="email" name="email" class="form-control" required placeholder="jane@email.com">
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone" class="form-control" placeholder="+94 77 123 4567">
      </div>
      <div class="form-group">
        <label>Expertise / Category</label>
        <select name="category" class="form-control">
          <option value="">Select a category</option>
          <option>Pedagogy and Teaching Methods</option>
          <option>Subject-Specific Teaching</option>
          <option>Educational Technology</option>
          <option>Special Education and Inclusive Teaching</option>
          <option>Classroom Management</option>
          <option>Online Teaching Tools</option>
        </select>
      </div>
      <div class="form-group">
        <label>Password <span class="req">*</span></label>
        <input type="password" name="password" class="form-control" required placeholder="Min 6 characters">
      </div>
      <div class="form-group">
        <label>Confirm Password <span class="req">*</span></label>
        <input type="password" name="passwordRepeat" class="form-control" required placeholder="Repeat password">
      </div>
      <button type="submit" name="register" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;">
        <i class="fas fa-paper-plane"></i> Submit Application
      </button>
    </form>
    <div class="auth-links">
      <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>
</main>
<?php include '../common/footer.php'; ?>
</body>
</html>