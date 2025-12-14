<?php
session_start();
if (!isset($_SESSION["userData"])) {
    header("location: login.php");
    exit();
}

$userData = $_SESSION["userData"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Details</title>
    <link rel="stylesheet" href="../css/accountDetails.css">
</head>
<body>
<?php 
    include '../common/header.php'; 

?>

<div class="wrapper">
    <h1>Account Details</h1>
    <div class="details">
        <p><strong>Username:</strong> <?= htmlspecialchars($userData["User_Name"]) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($userData["Email"]) ?></p>
    </div>
    <div class="actions">
        <a href="updateAccountDetails.php">Update Account</a> |
        <a href="deleteAccount.php" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete Account</a>
    </div>
    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

<?php include '../common/footer.php'; ?>
</body>
</html>
