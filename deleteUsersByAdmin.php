<?php

    require'config.php';

    $Id = $_POST["userId"];

    $delete = "DELETE FROM Registered_User WHERE Registered_User_Id = '$Id'";
    
    if($connection->query($delete)){
        echo'<script>record delete sucessfully</script>';
        header("Location:Manage Students.php");
    }
?>