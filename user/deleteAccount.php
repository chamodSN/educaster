<?php
session_start();

if (!isset($_SESSION["userData"])) {
    header("location: login.php");
    exit();
}

require_once '../common/config.php';

$userName = $_SESSION["userData"]["User_Name"];

$sql = "DELETE FROM Registered_User WHERE User_Name = ?";
$stmt = mysqli_stmt_init($connection);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: accountDetails.php?error=queryfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);

session_unset();
session_destroy();

header("location: login.php?message=accountdeleted");
exit();
