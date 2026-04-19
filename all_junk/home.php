<?php require_once 'common/config.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/home.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
<?php include 'common/header.php';?>
    <div class="homeImage">
        <img src="images/home1.png" alt="homeImage" class="image">
    </div>

    <div class="intro">
        <div class="intro-card">
            <i class="fas fa-chalkboard-teacher"></i>
            <p class="para">
                Welcome to <b>EDUCASTER</b>, where passion for teaching meets the power of knowledge.
            </p>
        </div>

        <div class="intro-card">
            <i class="fas fa-graduation-cap"></i>
            <p class="para">
                We empower aspiring and professional educators with structured, high-quality training programs.
            </p>
        </div>

        <div class="intro-card">
            <i class="fas fa-users"></i>
            <p class="para">
                Learn from industry-leading instructors who guide you with real-world teaching expertise.
            </p>
        </div>

        <div class="intro-card">
            <i class="fas fa-rocket"></i>
            <p class="para">
                <b><a href="">Join us</a></b> today and unlock your full potential as an educator.
            </p>
        </div>

        <div class="intro-card">
            <i class="fas fa-globe-americas"></i>
            <p class="para">
                Become part of a <b>global community</b> of educators, sharing knowledge and inspiring learners worldwide.
            </p>
        </div>

    </div>

    <div class="status-section">
    <h3 class="status-title">Our Learning Impact</h3>
    <p class="status-subtitle">Building a global ecosystem for modern educators</p>

    <div class="status">
        <div class="status-card">
            <i class="fas fa-user-tie"></i>
            <h2>50+</h2>
            <p>Expert Instructors</p>
        </div>

        <div class="status-card">
            <i class="fas fa-users"></i>
            <h2>1000+</h2>
            <p>Active Learners</p>
        </div>

        <div class="status-card">
            <i class="fas fa-layer-group"></i>
            <h2>50+</h2>
            <p>Course Categories</p>
        </div>

        <div class="status-card">
            <i class="fas fa-globe"></i>
            <h2>Global</h2>
            <p>Learning Community</p>
        </div>
    </div>
</div>

    <!-- coure section start-->
    <div class="category">
    <h3 class="categoryTitle">Popular Courses</h3>
    <hr>
    <br>
    <div class="course_Container">

    <?php

$popular = "SELECT Title, Intro_Image, Course_Id
            FROM Course
            LIMIT 5";

$results = $connection->query($popular);

while ($row = $results->fetch_assoc()) {
    
    echo '
<div class="course-card">
    <a href="courseOverview.php?course_id=' . $row['Course_Id'] . '" class="course-link">
        <div class="course-image">
            <img src="/educaster/uploads/' . $row['Intro_Image'] . '" alt="Course Image">
        </div>
        <div class="course-content">
            <h4>' . $row['Title'] . '</h4>
            <span class="course-tag">Teacher Training</span>
        </div>
    </a>
</div>';

}
?>


        </div>

    </div>
    <br>

    <!-- course section end -->
    <?php include 'common/footer.php';?>

    <script>
const cards = document.querySelectorAll('.course-card');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = 1;
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, { threshold: 0.3 });

cards.forEach(card => {
    card.style.opacity = 0;
    card.style.transform = 'translateY(30px)';
    observer.observe(card);
});
</script>


</body>

</html>