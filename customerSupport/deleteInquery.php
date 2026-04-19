<?php
require 'config.php';
session_start();

if (!isset($_SESSION["userData"])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION["userData"]["Registered_User_Id"];
$Id = $_POST["id"];

$sql = "DELETE FROM enquiry WHERE Enquiry_Id = ? AND Registered_User_Id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $Id, $uid);

if ($stmt->execute()) {
    header("Location: contactus.php");
} else {
    echo "Error: " . $connection->error;
}

$stmt->close();
$connection->close();
?>
