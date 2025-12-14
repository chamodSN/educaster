<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?php
    include_once'config.php';
    ?>

</head>


<body>
<?php include'adminHeader.php';?>
<?php include'adminStatusBox.php';?>
    
    <h3 class="tableName">MANAGE INSTRUCTORS</h3>
    <div class="table">
        <table>
            <tr>
                <th>User ID</th>
                <th>User Name</th>
                <th>E Mail</th>
                <th>Course Type</th>
            </tr>
            <?php

            $Instructors = "SELECT Registered_User_Id, first_Name, last_Name, Email,Course_Category
            FROM Registered_User
            WHERE Registered_User_Type = 'INS';";

            $results = $connection->query($Instructors);

            while($row = $results ->fetch_assoc()){
              echo"<tr>
                <td>" . $row["Registered_User_Id"] . "</td>
                <td>" . $row["first_Name"] . " " . $row["last_Name"] ."</td>
                <td>" . $row["Email"] . "</td>
                <td>" . $row["Course_Category"] . "</td>
                
            </tr>" ;
            }

            ?>
        </table>
    </div>

    <div class="crud">
<div class="add">
        <button id="popup">
            <i class="fa fa-plus"></i>Add Instructor
        </button>
    </div>
    <div class="update">
        <button id="updatePopupBtn">
            <i class="fa fa-pencil"></i>Update Instructor
        </button>
    </div>
    <div class="delete">
        <button id="deletePopupBtn">
            <i class="fa fa-trash"></i>Delete Instructor
        </button>
    </div>
</div>
    

    <div class="popup">
        <div class="popupContent">
            <h4>ADD A INSTRUCTOR<br></h4>
            <form method="post" action="addInstructorsByAdmin.php" onsubmit="return checkPassword()">
                <label>
                    <input type="text" name="fName" placeholder="First Name" required>
                    <input type="text" name="lName" placeholder="Last Name" required>
                    <input type="tel" name="pNum" placeholder="Phone Number +94"  pattern="[+]{1}[0-9]{11,14}"  required>
                    <input type="email" name="Email" placeholder="E Mail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required>
                    <input type="text" name="nic" placeholder="NIC" required>
                    <input type="password" name="pw" placeholder="Password" required>
                    <input type="password" name="rpw" placeholder="Re-Enter Password" required>
                    <h3>Select Gender</h3><br>
                    <select name="gender" id="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <h3>Course Category</h3><br>
<select name="category">
    <option value="PEDAGOGY_AND_TEACHING_METHODS">PEDAGOGY AND TEACHING METHODS</option>
    <option value="SUBJECT_SPECIFIC_TEACHING">SUBJECT-SPECIFIC TEACHING</option>
    <option value="EDUCATIONAL_TECHNOLOGY">EDUCATIONAL TECHNOLOGY</option>
    <option value="SPECIAL_EDUCATION_AND_INCLUSIVE_TEACHING">SPECIAL EDUCATION AND INCLUSIVE TEACHING</option>
</select>


                    <input type="submit" value="Add">
                </label>
            </form>
            <button class="close" id="close">Close</button>

        </div>

    </div>

    <div class="updatePopup" id="updatePopup">
        <div class="updatePopupContent">
            <h4>UPDATE INSTRUCTOR DETAILS<br></h4>
            <form method="post" action="updateUsersByAdmin.php">
                <label>User ID :
                    <input type="text" name="userId" required>
                    First Name :
                    <input type="text" name="fName" required>
                    Last Name :
                    <input type="text" name="lName" required>
                    Phone Number :
                    <input type="tel" name="pNum" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required>
                    NIC :
                    <input type="text" name="nic" required>
                    Email :
                    <input type="email" name="Email" pattern="[+]{1}[0-9]{11,14}" required>
                    Password :
                     <input type="password" name="pw" required>
                    <input type="submit" value="Update">
                    
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
                    <input type="text" name="userId" required>
                    <input type="submit" value="Delete">
                    
                </label>
            </form>
            <button class="close" id="deletePopupClose">Close</button>

        </div>
    </div>


    </div>

    <?php include'adminFooter.php'; ?>

    <script src="js/admin.js"></script>

</body>

</html>