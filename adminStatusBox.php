<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/adminStatusBox.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php require'config.php'; ?>
</head>

<body>
<div class="statusContainer">
    <div class="statusBox">
        <div class="status">
            <div class="numbers">
                <?php 
                $insCountQuery = "SELECT COUNT(Registered_User_Id) AS total FROM Registered_User WHERE Registered_User_Type = 'TCH'";
                $result = $connection->query($insCountQuery);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                } else {
                    echo "0";
                }
                ?>
            </div>
            <div class="statusName">Total Teachers
                 <i class="fa fa-users"></i>
             </div>
        </div>
    
        <div class="status">
            <div class="numbers">
                <?php 
                $insCountQuery = "SELECT COUNT(Course_Id) AS total FROM course;";
                $result = $connection->query($insCountQuery);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                } else {
                    echo "0";
                }
                ?>
            </div>
            <div class="statusName">Total Courses
                 <i class="fa fa-bars"></i>
            </div>
        </div> 

        <div class="status">
            <div class="numbers">
            <?php 
                $insCountQuery = "SELECT COUNT(Registered_User_Id) AS total FROM Registered_User WHERE Registered_User_Type = 'INS'";
                $result = $connection->query($insCountQuery);

                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo $row['total'];
                } else {
                    echo "0";
                }
                ?>
            </div>
            <div class="statusName">Total Instructors
                <i class="fa fa-users"></i>
        </div>
    </div>
</div>

</body>

</html>
