<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/S_dashbord.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src = "S_Dashbord.js"></script>
</head>

<body>
<?php include'header.php';?>
	
<br>
	<div class = "dbord">
	<ul class = "dbnavigater">
		<li><h1>STUDENT DASHBORD</h1></li>
		<div class = "li_right">
		<li>
			<form action="#">
			  <input type="text" placeholder="Search for Courses Title name" name="search" onkeyup="searchFunction()" id = "searchinput">
			  <button type="submit" onclick = "searchFunction()" id = "searchinput"><i class="fa fa-search"></i></button>
			</form>
		</li>
		</div>
		
	</ul>
	</div>
  <br>
  <!-- Dashboard table -->
  <br>

    
   <table id="stdash" >
   <thead>
		<tr>
			<th>STATUS</th>
			<th>TITLE</th>
			<th>DUE DATE</th>
			<th>COURSE ID</th>
			<th>MARKS</th>
			<th></th>

		</tr>
	<thead>
	<tbody>
	<?php
    require 'config.php';
    session_start();

    // Check if the user is logged in and if their ID is set in the session
    if(isset($_SESSION['user_ID'])) {
        
        // Read course table for the logged-in user
        $sql = "SELECT Title, Due_date, Course_Id FROM course WHERE Course_Id IN (SELECT Course_Id FROM enrolles WHERE Registered_User_Id = {$_SESSION['user_ID']})";
        $Dresult = $connection->query($sql);

        if($Dresult) {
            if($Dresult->num_rows > 0) {
                while($row = $Dresult->fetch_assoc()) {

                    // Get quiz id from database
                    $quizid_query = "SELECT Quiz_Id FROM quiz WHERE Course_Id = ".$row["Course_Id"];
                    $quizid_result = $connection->query($quizid_query);

                    if($quizid_result && $quizid_result->num_rows > 0) {
                        $quiz_row = $quizid_result->fetch_assoc();
                        $qid = $quiz_row["Quiz_Id"];

                        // Get quiz marks from database
                        $Qmarks = "SELECT Q_marks FROM takes WHERE Quiz_Id = ".$qid." AND Registered_User_Id = {$_SESSION['user_ID']}";
                        $Q_result = $connection->query($Qmarks);

                        if($Q_result && $Q_result->num_rows > 0) {
                            $quiz_marks = $Q_result->fetch_assoc();
                            $Q_marks = $quiz_marks["Q_marks"];
                        } else {
                            // If marks are not found, set default value
                            $Q_marks = "N/A";
                        }
                    } else {
                        // If quiz ID not found, set default value
                        $qid = "N/A";
                    }

                    // Due date read from database and check status
                    $getdate = "SELECT Due_date FROM course WHERE Course_Id IN (SELECT Course_Id FROM enrolles WHERE Registered_User_Id = {$_SESSION['user_ID']})";
                    $dateresult = $connection->query($getdate);
                    if ($dateresult && $dateresult->num_rows > 0) {
                        $date_row = $dateresult->fetch_assoc();
                        $d_ate = $date_row["Due_date"];
                    } else {
                        $d_ate = "N/A";
                    }

                    $due_date = strtotime($row["Due_date"]);
                    $current_date = strtotime(date('Y-m-d'));
                    if ($due_date < $current_date) {
                        $status = "EXPIRED";
                    } else {
                        $status = "ACTIVE";
                    }
                    // Output student dashboard
                    echo "<tr>
                            <td><p class='status-active'>$status</p></td>
                            <td>".$row["Title"]."</td>
                            <td>".$row["Due_date"]."</td>
                            <td>".$row["Course_Id"]."</td>
                            <td>".$Q_marks."</td>
                            <td><button type='button'>Continue</button></td>
                          </tr>";
                }
            } else {
                echo "<tr><td>No Results</td></tr>";
            }
        } else {
            // Error in executing the SQL query
            echo "Error: " . $connection->error;
        }
    } else {
        // User is not logged in or session variable is not set
        echo "User not logged in.";
    }

    $connection->close();
?>
	</tbody>
   </table>

    <br>

    <!-- Form of deiete -->
    <form method="POST" action="S_Dash_delete.php">
    <div class="delete">
        <h3 class="deleteHeader">Delete Courses</h3>
        <div class="deleteDetails">
            Course ID: <input type="text" placeholder="0001" name="courseId" id="courseId">
            <input type="submit" value="DELETE">
        </div>
    </div>
</form>



  <br>
  <br>
  <br>
  
    <!-- Footer -->
    <?php include'footer.php';?>

</body>

</html>