<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <?php include_once'config.php'; ?>
</head>


<body>
<?php include'adminHeader.php';?>
<?php include'adminStatusBox.php';?>

    <h3 class="tableName">MANAGE STUDENTS</h3>
    <div class="table">
        <table>
            <tr>
                <th>User ID</th>
                <th>User Name</th>
                <th>E Mail</th>
                <th>Phone Number</th>

            </tr>
            <?php

            $Teachers = "SELECT Registered_User_Id, first_Name, last_Name, Email,Phone_Number 
            FROM Registered_User
            WHERE Registered_User_Type = 'TCH';";

            $results = $connection->query($Teachers);

            while($row = $results ->fetch_assoc()){
              echo"<tr>
                <td>" . $row["Registered_User_Id"] . "</td>
                <td>" . $row["first_Name"] . " " . $row["last_Name"] ."</td>
                <td>" . $row["Email"] . "</td>
                <td>" . $row["Phone_Number"] . "</td>
            </tr>" ;
            }

            ?>
            
        </table>
    </div>
<div class="crud">
<div class="add">
        <button id="popup">
            <i class="fa fa-plus"></i>Add Student
        </button>
    </div>
    <div class="update">
        <button id="updatePopupBtn">
            <i class="fa fa-pencil"></i>Update Student
        </button>
    </div>
    <div class="delete">
        <button id="deletePopupBtn">
            <i class="fa fa-trash"></i>Delete Student
        </button>
    </div>
</div>
    

    <div class="popup">
        <div class="popupContent">
            <h4>ADD A STUDENT<br></h4>
            <form method="post" action="addStudentByAdmin.php" onsubmit="return checkPassword()">
                <label>
                    <input type="text" name="fName" placeholder="First Name" required>
                    <input type="text" name="lName" placeholder="Last Name" required>
                    <input type="tel" name="pNum" placeholder="Phone Number +94" required  pattern="[+]{1}[0-9]{11,14}" >
                    <input type="email" name="Email" placeholder="E Mail"pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required>
                    Select Gender
                    <select name="gender" id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="password" name="pw" id = "pw" placeholder="Password">
                    <input type="password" name="rpw" id = "rpw" placeholder="Re-Enter Password">
                    <input type="submit" value="Add" >
                    

                </label>
            </form>
            <button class="close" id="close">Close</button>

        </div>

    </div>

    <div class="updatePopup" id="updatePopup">
        <div class="updatePopupContent">
            <h4>UPDATE STUDENTS DETAILS<br></h4>
            <form method="post" action="updateUsersByAdmin.php">
                <label>User ID :
                    <input type="text" name="userId" required>
                    First Name :
                    <input type="text" name="fName" required>
                    Last Name :
                    <input type="text" name="lName" required>
                    Phone Number :
                    <input type="tel" name="pNum" placeholder = "+94" pattern="[+]{1}[0-9]{11,14}" required>
                    Email :
                    <input type="email" name="Email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required>
                    Password :
                     <input type="password" name="pw" required>
                    <input type="submit" value="Update" required>
                    
                </label>
            </form>
            <button class="close" id="updatePopupClose">Close</button>

        </div>
    </div>


    <div class="deletePopup" id="deletePopup">
        <div class="deletePopupContent">
            <h4>DELETE STUDENTS DETAILS<br></h4>
            <form method="post" action="deleteUsersByAdmin.php">
                <label>User ID :
                    <input type="text" name="userId">
                    <input type="submit" value="Delete">
                    
                </label>
            </form>
            <button class="close" id="deletePopupClose">Close</button>

        </div>
    </div>

    
    <?php include'adminFooter.php'; ?>
    <script src="js/admin.js"></script>
</body>

</html>