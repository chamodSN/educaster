<?php

require 'config.php';

$C_type = $_POST["ctype"];
$C_number = $_POST["number"];
$C_exp = $_POST["expdate"];
$C_cvc = $_POST["CVC"];
$C_D_save = $_POST["save"];

if($C_D_save == 'save') {
    $c_sql = "INSERT INTO card_details VALUES ('$C_type','$C_number','$C_exp','$C_cvc',2)";
    if($connection->query($c_sql)) {
        echo "Card Details Saved Successfully";
    } else {
        echo "Error: " . $connection->error;
    }
    header("Location:CProgressW1.php");
}
else{
    header("Location:CProgressW1.php");
}

$connection->close();

?>
