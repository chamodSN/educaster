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
                <li><a href="/educaster/home.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/educaster/courses/courses.php"><i class="fas fa-book"></i> Programmes</a></li>
                <li><a href="/educaster/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="/educaster/aboutUs.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                <li><a href="/educaster/customerSupport/contactUs.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
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
