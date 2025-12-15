<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    require_once '../common/config.php';
    require_once '../common/loginFunctions.php';

    $username = trim($_POST["userName"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $passwordRepeat = $_POST["passwordRepeat"];

    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        header("location: signup.php?error=emptyfields");
        exit();
    }

    if (invalidUserName($username)) {
        header("location: signup.php?error=invalidusername");
        exit();
    }

    if (invalidEmail($email)) {
        header("location: signup.php?error=invalidemail");
        exit();
    }

    if ($password !== $passwordRepeat) {
        header("location: signup.php?error=passwordsdontmatch");
        exit();
    }

    if (uidExists($connection, $username) || uidExists($connection, $email)) {
        header("location: signup.php?error=userexists");
        exit();
    }

    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Registered_User (User_Name, Email, Password) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($connection);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: signup.php?error=queryfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPwd);
    mysqli_stmt_execute($stmt);

    header("location: login.php?signup=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/user_common.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<div class="common-wrapper">
    <div class="common-container">
        <h1 class="topic">Sign Up</h1>
        <form action="signup.php" method="POST">
            <input type="text" name="userName" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div id="strength"></div>
            <input type="password" name="passwordRepeat" placeholder="Repeat Password" required><br>
            <input type="submit" name="signup" value="Sign Up">
        </form>

        <p>Already have an account? <a href="login.php">Log in</a></p>

        <?php
        if (isset($_GET["error"])) {
            $messages = [
                "emptyfields" => "Please fill in all fields.",
                "invalidusername" => "Invalid username format.",
                "invalidemail" => "Invalid email address.",
                "passwordsdontmatch" => "Passwords do not match.",
                "userexists" => "Username or email already taken.",
                "queryfailed" => "Something went wrong. Please try again."
            ];
            echo '<div class="error">' . $messages[$_GET["error"]] . '</div>';
        }

        if (isset($_GET["signup"]) && $_GET["signup"] === "success") {
            echo '<div class="success" style="color: green; background-color: #e6ffe6; border: 1px solid green; padding: 10px; border-radius: 5px;">Signup successful! You can now log in.</div>';
        }
        ?>
    </div>
</div>


<?php include '../common/footer.php'; ?>
</body>
</html>

<script>
const pwd = document.getElementById("password");
const strength = document.getElementById("strength");

pwd.addEventListener("input", function() {
    let val = pwd.value;
    let textClass = "";
    let borderClass = "";

    if (val.length < 6) {
        strength.textContent = "Weak";
        textClass = "strength-weak";
        borderClass = "weak";
    } else if (val.match(/[A-Z]/) && val.match(/\d/)) {
        strength.textContent = "Strong";
        textClass = "strength-strong";
        borderClass = "strong";
    } else {
        strength.textContent = "Medium";
        textClass = "strength-medium";
        borderClass = "medium";
    }

    // Remove previous classes
    strength.className = "";
    pwd.className = "";
    
    // Add current classes
    strength.classList.add(textClass);
    pwd.classList.add(borderClass);
});

const errorMsg = document.querySelector(".error");
const successMsg = document.querySelector(".success");

if(errorMsg) setTimeout(() => errorMsg.style.display = 'none', 5000);
if(successMsg) setTimeout(() => successMsg.style.display = 'none', 5000);

</script>