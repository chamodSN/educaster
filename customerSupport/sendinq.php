<?php
require '../common/config.php';
session_start();

if (!isset($_SESSION["userData"])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION["userData"]["Registered_User_Id"];
$email = $_POST["Email"];
$phoneNumber = $_POST["phone"];
$subject = $_POST["Enquiry"];
$enquiry = $_POST["Details"];
$currentDate = date("Y-m-d");

if (empty($email) || empty($subject) || empty($enquiry) || empty($phoneNumber)) {
    echo "<script>alert('All fields are required!'); window.location.href='contactus.php';</script>";
    exit();
}

$sql = "INSERT INTO enquiry (Registered_User_Id, Email, Enq_Subject, Date, Enquiry, phone_Number)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $connection->prepare($sql);
$stmt->bind_param("isssss", $uid, $email, $subject, $currentDate, $enquiry, $phoneNumber);

if ($stmt->execute()) {
    header("Location: contactus.php");
} else {
    echo "Error: " . $connection->error;
}

$stmt->close();
$connection->close();
?>
