<?php

require 'config.php';

$id = $_POST["courseId"];//get course id from s_dashbord

$D_sql = "DELETE FROM enrolles WHERE Course_Id = '$id'"; //delete data abot relevent course id

if ($connection->query($D_sql)) {
    echo "<script> alert('Record Deleted Successfully !!');</script>";
    header("Location:Dashbord.php");//reloate student dash bord
} 
else {
    echo "Error: " . $connection->error;
}

$connection->close();

?>