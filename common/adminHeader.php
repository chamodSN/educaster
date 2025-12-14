<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/educaster/css/header.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <p class="site-name">EDUCASTER</p>
            <ul class="navLinks">
                <li><a href="AdminDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/educaster/courses/manage_courses.php"><i class="fas fa-book"></i> Manage Course</a></li>
                <li><a href="Manage Students.php"><i class="fas fa-users"></i> Manage Students</a></li>
                <li><a href="Manage Instructors.php"><i class="fas fa-users"></i> Manage Instructors</a></li>
                <li><a href="Messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
            </ul>

            </ul>
            <?php
                if (isset($_SESSION["userData"]) && !empty($_SESSION["userData"]["User_Name"])) {
                    echo '<a class="signin-btn" href="/educaster/user/accountDetails.php">
                            <i class="fas fa-user"></i> ' . $_SESSION["userData"]["User_Name"] . '
                        </a>';
                } else {
                    echo '<a class="signin-btn" href="/educaster/user/login.php">
                            <i class="fas fa-sign-in-alt"></i> SIGN IN
                        </a>';
                }
            ?>
        </nav>
    </header>
</body>

</html>
