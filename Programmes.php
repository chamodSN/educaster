<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php include_once'common/config.php'; ?>
</head>

<body>
<?php include'common/header.php';?>
<!-- <?php include'search.php';?> -->
    <br>
    <div class="category">
        <h3 class="categoryTitle">Pedagogy and Teaching Methods</h3>
        <hr>
        <br>
        <div class="course_Container">
        <?php

    $popular = "SELECT courseName, introImage, id
                FROM mainCourse
                WHERE courseCategory = 'Pedagogy and Teaching Methods'";

    $results = $connection->query($popular);

    while ($row = $results->fetch_assoc()) {
        echo '
        <div class="course">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
                <h4>' . $row['courseName'] . '</h4>
            </a>
        </div>';
    }
?>
</div>

        <br>
        <div class="category">
            <h3 class="categoryTitle">Subject-specific Teaching</h3>
            <hr>
            <br>
            <div class="course_Container">
        <?php
    $popular = "SELECT courseName, introImage, id
                FROM mainCourse
                WHERE courseCategory = 'Subject-specific Teaching'";

    $results = $connection->query($popular);

    while ($row = $results->fetch_assoc()) {
        echo '
        <div class="course">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
                <h4>' . $row['courseName'] . '</h4>
            </a>
        </div>';
    }
?>
</div>

            <br>

            <div class="category">
                <h3 class="categoryTitle">Educational Technology</h3>
                <hr>
                <br>
                <div class="course_Container">

                <?php
    $popular = "SELECT courseName, introImage, id
                FROM mainCourse
                WHERE courseCategory = 'Educational Technology'";

    $results = $connection->query($popular);

    while ($row = $results->fetch_assoc()) {
        echo '
        <div class="course">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
                <h4>' . $row['courseName'] . '</h4>
            </a>
        </div>';
    }
?>
</div>
                </div>

                <br>

                <div class="category">
                    <h3 class="categoryTitle">Special Education and Inclusive Teaching</h3>
                    <hr>
                    <br>
                    <div class="course_Container">

                    <?php
    $popular = "SELECT courseName, introImage, id
                FROM mainCourse
                WHERE courseCategory = 'Special Education and Inclusive Teaching'";

    $results = $connection->query($popular);

    while ($row = $results->fetch_assoc()) {
        echo '
        <div class="course">
        <img src="images/pcimg5.png">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
                <h4>' . $row['courseName'] . '</h4>
            </a>
        </div>';
    }
?>
                    </div>

                    <br>

                    <div class="category">
                        <h3 class="categoryTitle">Assessment and Evaluation</h3>
                        <hr>
                        <br>
                        <div class="course_Container">

                        <?php
    $popular = "SELECT courseName, introImage, id
                FROM mainCourse
                WHERE courseCategory = 'Assessment and Evaluation'";

    $results = $connection->query($popular);

    while ($row = $results->fetch_assoc()) {
        echo '
        <div class="course">
        <a href="courseOverview.php?course_id=' . $row['id'] . '" class="link">
                <h4>' . $row['courseName'] . '</h4>
            </a>
        </div>';
    }
?>
</div>

<br>

<?php include'common/footer.php';?>
</body>

</html>