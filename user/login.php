<?php
session_start();

if (isset($_SESSION['userData'])) {
    if ($_SESSION['userData']['User_Name'] === 'admin') {
        header("Location: AdminDashboard.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $usersNameOrEmail = trim($_POST["userId"]);
    $typePassword = $_POST["userPwd"];

    if (empty($usersNameOrEmail) || empty($typePassword)) {
        header("location:login.php?error=emptyfields");
        exit();
    }

    require_once '../common/config.php';
    require_once '../common/loginFunctions.php';

    if (($usersNameOrEmail === 'admin' || $usersNameOrEmail === 'admin@gmail.com') && $typePassword === 'admin123') {
        $_SESSION["userData"] = [
            "User_Name" => "admin",
            "Email" => "admin@gmail.com"
        ];
        header("location:../AdminDashboard.php");
        exit();
    }

    $userData = uidExists($connection, $usersNameOrEmail);

     echo "Entered: $typePassword<br>";
    echo "Stored : " . $userData["Password"] . "<br>";

    if (!$userData || !password_verify($typePassword, $userData["Password"])) {
        
        header("location:login.php?error=wronglogin");
        exit();
    }
   

    $_SESSION["userData"] = $userData;
    header("location:../home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<div class="wrapper">
    <div class="login-container">
        <p>Login</p>
        <form action="login.php" method="POST">
            <input type="text" name="userId" placeholder="Username or Email" required><br>
            <input type="password" name="userPwd" placeholder="Password" required><br>
            <input type="submit" name="login" value="LOG IN">
        </form>

        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>

        <?php
        if (isset($_GET["error"])) {
            $errorMessages = [
                "emptyfields" => "Please fill in all fields.",
                "wronglogin" => "Invalid username, email, or password.",
                "queryfailed" => "Internal Server Error."
            ];
            echo '<div class="error">' . $errorMessages[$_GET["error"]] . '</div>';
        }
        ?>
    </div>
</div>

<?php include '../common/footer.php'; ?>
</body>
</html>
