<?php

require 'config.php';


$id = $_POST["EnquiryNo"];
$reply = $_POST["reply"];

if(empty($id) ||empty($reply))
{
    echo "<script>alert('ALL REQUIRED!!');</script>";
}

else{
    $replyfromAdmin = "UPDATE enquiry 
SET Reply = '$reply' 
WHERE Enquiry_Id = $id";

    if($connection->query($replyfromAdmin)){
    echo "<script>alert('Reply sent');</script>";
    // header("Location:Messages.php");

}

else{
    echo"Error: ".$connection->error;
}
}


$connection->close();

?>

