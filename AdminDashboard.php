<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
    <?php include_once'common/config.php'; ?>
</head>

<body>
    <?php include 'common/adminHeader.php';?>    
   
        <form method="post" action = "enrollByAdmin.php">
            <label>
                <div class="enroll">
                    <h3 class="enrollHeading">Enroll Courses</h3>
                    <div class="enrollDetails">
                        Course ID : <input type="text" placeholder="000132" name="courseId" required>
                        User Id : <input type="text" placeholder="00010" name="userId" required>
                        <input type="submit" value="Enroll">
                    </div>

                </div>
            </label>

        </form>
        <h3 class="tableName">COURSES OVERVIEW</h3>
        <div class="table">
            <table>
                <tr>
                    <th>Id</th>
                    <th>Status</th>
                    <th>Course Name</th>
                    <th>Due Date</th>
                    <th>View</th>
                </tr>
                <?php

            $Course = "SELECT id, courseName, endDate 
            FROM mainCourse;";

            $results = $connection->query($Course);

            while($row = $results ->fetch_assoc()){
                $due = $row["endDate"]; 
                $current = date("Y-m-d");
            
                if ($due < $current) {
                    $status = 'EXPIRED';
                } else {
                    $status = 'ACTIVE';
                }
                

                echo "<tr>" .
                    "<td>" . $row["id"] . "</td>" .
                    "<td>" . $status . "</td>" .
                    "<td>" . $row["courseName"] . "</td>" .
                    "<td>" . $row["endDate"] . "</td>" .
                    "<td><a href='adminCourseOverview.php?id=" . $row["id"] . "' class='view-button'>View</a></td>" .
                    "</tr>";

           
            }

            ?>
                
            </table>
        </div>

    <?php include'common/footer.php';?>
    

</body>

</html>