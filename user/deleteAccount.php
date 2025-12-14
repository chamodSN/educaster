<?php
session_start();

if (!isset($_SESSION["userData"])) {
    header("location: login.php");
    exit();
}

require_once '../common/config.php';

$userId   = $_SESSION["userData"]["Registered_User_Id"];
$userName = $_SESSION["userData"]["User_Name"];

$deleteEnrollments = $connection->prepare(
    "DELETE FROM enrollment WHERE Registered_User_Id = ?"
);

$deleteEnrollments->bind_param("i", $userId);
$deleteEnrollments->execute();
$deleteEnrollments->close();

$deleteUser = $connection->prepare(
    "DELETE FROM registered_user WHERE Registered_User_Id = ?"
);

$deleteUser->bind_param("i", $userId);
$deleteUser->execute();
$deleteUser->close();

session_unset();
session_destroy();

header("location: signup.php?message=accountdeleted");
exit();
