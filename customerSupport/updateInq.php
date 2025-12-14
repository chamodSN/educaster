<?php
require '../common/config.php';
session_start();

if (!isset($_SESSION["userData"])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION["userData"]["Registered_User_Id"];
$enqid = $_POST["id"];
$email = $_POST["Email"];
$phoneNumber = $_POST["phone"];
$subject = $_POST["Enquiry"];
$enquiry = $_POST["Details"];
$currentDate = date("Y-m-d");

$sql = "UPDATE enquiry SET Email = ?, Enq_Subject = ?, Date = ?, Enquiry = ?, phone_Number = ?
        WHERE Enquiry_Id = ? AND Registered_User_Id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ssssssi", $email, $subject, $currentDate, $enquiry, $phoneNumber, $enqid, $uid);

if ($stmt->execute()) {
    header("Location: contactus.php");
} else {
    echo "Error: " . $connection->error;
}

$stmt->close();
$connection->close();
?>
