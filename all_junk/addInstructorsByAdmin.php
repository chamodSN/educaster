<?php

require 'config.php';


$firstName = $_POST["fName"];
$lastName = $_POST["lName"];
$nic = $_POST["nic"];
$gender = $_POST["gender"];
$email = $_POST["Email"];
$phoneNumber = $_POST["pNum"];
$password = $_POST["pw"];
$category = $_POST["category"];

$studentDetails = "INSERT INTO Registered_User(Registered_User_Type, First_Name, Last_Name, NIC, Gender, Email, Phone_Number, Password,Course_Category) 
                   VALUES ('INS', '$firstName', '$lastName', '$gender', '$email', '$phoneNumber', '$password', '$nic', '$category')"; 

if(empty($firstName) || empty($lastName)||empty($gender)||empty($phoneNumber)||empty($email) ||empty($password) ||empty($nic) ||empty($category))
{
    echo "<script>alert('ALL REQUIRED!!');</script>";
}

else{
    if($connection->query($studentDetails)){
    echo "<script>alert('Insert Successfully');</script>";
    header("Location:Manage Instructors.php");

}

else{
    echo"Error: ".$connection->error;
}
}


$connection->close();

?>

