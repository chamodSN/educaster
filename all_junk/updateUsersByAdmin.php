<?php

require 'config.php';

$userId = $_POST["userId"];
$firstName = $_POST["fName"];
$lastName = $_POST["lName"];
$phoneNumber = $_POST["pNum"];
$email = $_POST["Email"];
$password = $_POST["pw"];

$updateStudentDetails = "UPDATE Registered_User SET First_Name = '$firstName', Last_Name = '$lastName', Email = '$email', Phone_Number = '$phoneNumber', Password = '$password'
                   WHERE Registered_User_Id = $userId"; 


if($connection->query($updateStudentDetails)){
    echo"Update Sucessfully";
    header("Location:Manage Students.php");
}

else {
    echo "<script>console.log('Error: " . $connection->error . "');</script>";
    header("Location: ManageStudents.php");
}


$connection->close();

?>
