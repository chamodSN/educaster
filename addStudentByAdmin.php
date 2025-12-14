<?php

require 'config.php';


$firstName = $_POST["fName"];
$lastName = $_POST["lName"];
$gender = $_POST["gender"];
$phoneNumber = $_POST["pNum"];
$email = $_POST["Email"];
$password = $_POST["pw"];

$studentDetails = "INSERT INTO Registered_User(Registered_User_Type, First_Name, Last_Name, Gender, Email, Phone_Number, Password) 
                   VALUES ('TCH', '$firstName', '$lastName', '$gender', '$email', '$phoneNumber', '$password')"; 

if(empty($firstName) || empty($lastName)||empty($gender)||empty($phoneNumber)||empty($email) ||empty($password))
{
    echo "<script>alert('ALL INPUTS REQUIRED!!');</script>";
}

else{
    if($connection->query($studentDetails)){
    echo "<script>alert('Insert Successfully');</script>";
    header("Location:Manage Students.php");

}

else{
    echo"Error: ".$connection->error;
}
}


$connection->close();

?>

