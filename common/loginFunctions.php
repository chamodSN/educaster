<?php

function invalidUserName($userName) {
    return !preg_match("/^[a-zA-Z0-9_]*$/", $userName);
}

function invalidEmail($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

function uidExists($connection, $userInput) {
    $sql = "SELECT * FROM Registered_User WHERE User_Name = ? OR Email = ?";
    $stmt = mysqli_stmt_init($connection);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location:login.php?error=queryfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $userInput, $userInput);
    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($resultData) ?: false;
}


