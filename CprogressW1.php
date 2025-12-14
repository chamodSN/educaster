<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/Cprogress.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- Font Awesome Icon Library -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
<?php include'header.php';?>
    <br>
    <br>
    
	<?php
    require 'config.php';
    $C_ID = $_GET['course_id']; 
    $w1_sql = "SELECT Title, Due_date FROM course WHERE Course_Id = $C_ID ;";
    $COV_W = $connection->query($w1_sql);

    $w1i_sql = "SELECT First_Name, Last_Name FROM registered_user WHERE Registered_USER_Id IN (SELECT Registered_USER_Id FROM course WHERE Course_Id = $C_ID );";
    $Instructer_COV_W = $connection->query($w1i_sql);

    $week1_sql = "SELECT week_title,video_file,image_file,course_description,resource_file,course_link FROM weekly_courses WHERE main_course_id = $C_ID";
    $week = $connection->query($week1_sql);

    if($COV_W->num_rows > 0 && $Instructer_COV_W->num_rows > 0 && $week->num_rows > 0){

        $W_row = $COV_W->fetch_assoc();
        $W_Irow = $Instructer_COV_W->fetch_assoc();
        $Week_details = $week->fetch_assoc();

        echo "<div class='sidecolom'>
        <h3>".$W_row['Title']."</h3>
        <p>Instructor: ".$W_Irow['First_Name']." ".$W_Irow['Last_Name']."</p>
        <p>Due date: ".$W_row['Due_date']."</p>
        <h4>Course content</h4>
        <a class='active' href='CprogressW1.html'>Week 01</a>
        <a href='#week2'>Week 02</a>
        <a href='#week3'>Week 03</a>
        <a href='#week4'>Week 04</a>
        <a href='#quiz'>QUIZ</a>
        </div>
        
        <div class='main'>
        <h1>".$Week_details['week_title']."</h1>
        <hr>
        <br>
        <video controls>
          <source src=".$Week_details['video_file']." type='video/mp4'>
          <source src=".$Week_details['video_file']." type='video/ogg'>
        </video>
        <br>
        
        <p>".$Week_details['course_description']."</p>
        <hr>
        <br>
        <a href=".$Week_details['resource_file'].">Resource</a>
        <hr>
        <br>
        <a href=".$Week_details['course_link'].">Lesson 01</a>
        <hr>
        <br>
        <button class='quiz' type='button'>Quiz</button>
        <hr>
        <br>
        <button type='button'>PREVIOUS</button>
        <button class='next' type='button'>NEXT</button>
        </div>";
    }

    

    $connection->close();
?>

	
	
    <br>
    <?php include'footer.php';?> 
</body>

</html>