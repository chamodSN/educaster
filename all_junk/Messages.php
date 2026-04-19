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
    
    <h3 class="tableName">USER INQUIRIES</h3>
    <div class="table">
        <table>
            <tr>
            <th>ID</th>
                <th>E Mail</th>
                <th>Date</th>
                <th>Subject</th>

            </tr>
            <?php

            $Messages = "SELECT Enquiry_Id, Email, Date, Enq_Subject,Enquiry,Reply
            FROM enquiry
            WHERE Reply IS NULL;";

            $results = $connection->query($Messages);

            while($row = $results ->fetch_assoc()){
              echo"<tr>
              <td>" . $row["Enquiry_Id"] . "</td>
                <td>" . $row["Email"] . "</td>
                <td>" . $row["Date"] . "</td>
                <td>" . $row["Enq_Subject"] . "</td>
            </tr>" ;
            echo "<tr>
            <td>" . $row["Enquiry"] . "</td>;
            </tr>" ;
            }
            ?>
        </table>
    </div>
    
    <div class="crud">

    <div class="reply">
        <button id="replyPopupBtn">
            <i class="fa fa-send"></i>Send Reply
        </button>
    </div>
</div>

<div class="replyPopup" id="replyPopup">
        <div class="replyPopupContent">
            <h4>REPLY FOR USER ENQUIRIES<br></h4>
            <form method="post" action="reply.php">
                <label>User ID :
                <input type="text" placeholder="Enquiry Number" name="EnquiryNo">
            <textarea name="reply" rows="8" cols="50" placeholder="Enter your reply here"></textarea>
            <input type="submit" value="SEND">
                    
                </label>
            </form>
            <button class="close" id="replyPopupClose">Close</button>

        </div>
    </div>

    <?php include'adminFooter.php'; ?>
    <script src="js/reply.js"></script>

</body>

</html>