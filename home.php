<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/home.css"> 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
    <?php include_once'common/config.php'; ?>
</head>

<body>
<?php include 'common/header.php';?>
    <br>
    <div class="homeImage">
        <img src="images/home1.png" alt="homeImage" class="image">
        
    </div>

    </div>

    <br>

    <div class="intro">
        <p class="para">
            Welcome to <b>EDUCASTER</b>, where passion for teaching meets the power of knowledge. Aspiring educators,
            seasoned
            professionals, and lifelong learners alike, embark on a transformative journey with us.
        </p><br>
        <p class="para">
            At <b>EDUCASTER</b>, we believe that education is the cornerstone of progress, and teachers are the
            architects
            of tomorrow. Whether you're just starting your teaching career or looking to enhance your skills, our
            comprehensive platform offers the tools, resources, and guidance you need to succeed.
        </p><br>
        <p class="para">
            Led by some of the industry's best instructors, our team is committed to providing you with the highest
            quality education. Benefit from their expertise, insights, and personalized support as you navigate through
            your learning journey.
        </p><br>
        <p class="para">
            <b><a href="">Join us</a></b> today and unlock your full potential as an educator. Your journey to success
            starts here.
        </p><br>
    </div>
    <br>

    <div class="status">

        <div class="pStatus" data-name="s-1">
            <a href="" class="link"><img src="images/simage1.png">
            </a>
        </div>

        <div class="pStatus" data-name="s-2">
            <a href="" class="link"><img src="images/simage2.png">
            </a>
        </div>

        <div class="pStatus" data-name="s-3">
            <a href="" class="link"><img src="images/simage3.png">
            </a>
        </div>

        <div class="pStatus" data-name="s-4">
            <a href="" class="link"><img src="images/simage4.png">
            </a>
        </div>


    </div>
    <!-- coure section start-->
    <div class="category">
    <h3 class="categoryTitle">Popular Courses</h3>
    <hr>
    <br>
    <div class="course_Container">

    <?php

$popular = "SELECT courseName, introImage, id
            FROM mainCourse
            LIMIT 5";

$results = $connection->query($popular);

while ($row = $results->fetch_assoc()) {
    
    echo '
    <div class="course">    
    <img src="images/courseImages/' . $row['introImage'] . '">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
        
            <h4>' . $row['courseName'] . '</h4>
        </a>
    </div>';
}
?>


        </div>

    </div>
    <br>

    <!-- course section end -->
    <?php include 'common/footer.php';?>

</body>

</html>