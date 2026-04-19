<?php
require 'config.php';

$courseId = $_POST["courseId"];
$userId = $_POST["userId"];


    $enrollQuery = "INSERT INTO enrolles (Registered_User_Id, Course_Id ) VALUES ('$courseId', '$userId')"; 

    if($connection->query($enrollQuery)) {
        echo "<script>alert('Enroll Successfully');</script>";
        header("Location: AdminDashboard.php");
        exit();
    } else {
        echo "Error: " . $connection->error;
    }

$connection->close();
?>
