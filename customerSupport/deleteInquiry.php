<?php
// customerSupport/deleteInquiry.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['inquiry_id'] ?? 0);
    $userId = (int)$_SESSION['userData']['Registered_User_Id'];
    $stmt = $connection->prepare("DELETE FROM inquiry WHERE Inquiry_Id=? AND Registered_User_Id=?");
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
}
header("Location: myInquiries.php");
exit();
?>