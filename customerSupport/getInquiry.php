<?php
require '../common/config.php';
session_start();

// Only proceed if user is logged in and id is provided
if (!isset($_SESSION["userData"]) || !isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$uid = $_SESSION["userData"]["Registered_User_Id"];
$inqId = intval($_GET['id']);

// Prepare and fetch the inquiry
$sql = "SELECT * FROM enquiry WHERE Enquiry_Id = ? AND Registered_User_Id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $inqId, $uid);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();
echo json_encode($row ? $row : []); // Output only JSON

$stmt->close();
$connection->close();
exit; // Make sure nothing else is output
