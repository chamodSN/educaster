<?php
session_start();
if (!isset($_SESSION["userData"])) {
    header("location: login.php");
    exit();
}

require_once '../common/config.php';
require_once '../common/loginFunctions.php';

$userData = $_SESSION["userData"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $newEmail = trim($_POST["email"]);
    $newPwd = $_POST["password"];
    $newPwdRepeat = $_POST["passwordRepeat"];

    if (empty($newEmail)) {
        header("location: updateAccountDetails.php?error=emptyemail");
        exit();
    }

    if (invalidEmail($newEmail)) {
        header("location: updateAccountDetails.php?error=invalidemail");
        exit();
    }

    if (!empty($newPwd)) {
        if ($newPwd !== $newPwdRepeat) {
            header("location: updateAccountDetails.php?error=passwordsdontmatch");
            exit();
        }
        $hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);
        $sql = "UPDATE Registered_User SET Email = ?, Password = ? WHERE User_Name = ?";
    } else {
        $sql = "UPDATE Registered_User SET Email = ? WHERE User_Name = ?";
    }

    $stmt = mysqli_stmt_init($connection);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: updateAccountDetails.php?error=queryfailed");
        exit();
    }

    if (!empty($newPwd)) {
        mysqli_stmt_bind_param($stmt, "sss", $newEmail, $hashedPwd, $userData["User_Name"]);
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $newEmail, $userData["User_Name"]);
    }

    mysqli_stmt_execute($stmt);

    // Update session data
    $_SESSION["userData"]["Email"] = $newEmail;
    header("location: accountDetails.php?update=success");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Account</title>
    <link rel="stylesheet" href="../css/updateAccountDetails.css">
</head>
<body>
<?php include '../common/header.php'; ?>

<div class="update-account-wrapper">
    <div class="update-account-container">
        <h1>Update Account Details</h1>
        <form action="updateAccountDetails.php" method="POST">
            <input type="email" name="email" value="<?= htmlspecialchars($userData["Email"]) ?>" required><br>
            <input type="password" name="password" placeholder="New Password (leave blank to keep current)"><br>
            <input type="password" name="passwordRepeat" placeholder="Repeat New Password"><br>
            <input type="submit" name="update" value="Update">
        </form>

    <?php
    if (isset($_GET["error"])) {
        $messages = [
            "emptyemail" => "Email cannot be empty.",
            "invalidemail" => "Invalid email address.",
            "passwordsdontmatch" => "Passwords do not match.",
            "queryfailed" => "Something went wrong. Please try again."
        ];
        echo '<div class="error">' . $messages[$_GET["error"]] . '</div>';
    }

    if (isset($_GET["update"]) && $_GET["update"] === "success") {
        echo '<div class="success">Account updated successfully!</div>';
    }
    ?>
    </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>
