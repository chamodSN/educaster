<?php
// user/deleteAccount.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId = (int)$_SESSION['userData']['Registered_User_Id'];

// Delete dependent records first
$connection->query("DELETE FROM takes       WHERE Registered_User_Id = $userId");
$connection->query("DELETE FROM review      WHERE Registered_User_Id = $userId");
$connection->query("DELETE FROM inquiry     WHERE Registered_User_Id = $userId");
$connection->query("DELETE FROM enrollment  WHERE Registered_User_Id = $userId");
$connection->query("DELETE FROM registered_user WHERE Registered_User_Id = $userId");

session_unset();
session_destroy();
header("Location: /educaster/user/signup.php?message=accountdeleted");
exit();
?>