<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Educaster</title>
    <link rel="stylesheet" href="css/Payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
<?php include'header.php';?>

	<?php
    require 'config.php';

    $w1_sql = "SELECT Title, Due_date FROM course WHERE Course_Id = 1";
    $COV_W = $connection->query($w1_sql);

    $w1i_sql = "SELECT First_Name, Last_Name FROM registered_user WHERE Registered_USER_Id IN (SELECT Registered_USER_Id FROM course WHERE Course_Id = 1)";
    $Instructer_COV_W = $connection->query($w1i_sql);

    if($COV_W->num_rows > 0 && $Instructer_COV_W->num_rows > 0){
        
        $W_row = $COV_W->fetch_assoc();
        $W_Irow = $Instructer_COV_W->fetch_assoc();

        echo "<div class='sideform'>
        <h3>".$W_row['Title']."</h3>
        <p>Instructor: ".$W_Irow['First_Name']." ".$W_Irow['Last_Name']."</p>
        <p>Due date: ".$W_row['Due_date']."</p>
        <h4>Course content</h4>
        <a class='active' href='CprogressW1.html'>Week 01</a>
        <a href='#week2'>Week 02</a>
        <a href='#week3'>Week 03</a>
        <a href='#week4'>Week 04</a>
        <a href='#quiz'>QUIZ</a>
        </div>";
    }
    
    $connection->close();
?>


	<div class="pform">
    <form  method = "POST" action= "set_card_data.php">
	<center><h4>Payment Details</h4></center>
	Card Type<br/>
	<label for="cars">Choose the card type:</label>
        <select name="ctype">
            <option value=""></option>
            <option value="Mastercard">Mastercard</option>
            <option value="Visa">Visa</option>
        </select>
	
	<input type="text" placeholder="Card number" name="number" class="cn" required></br>
	
	
	<input type="text" name="expdate" placeholder="mm/yy" class="btn">
	
	
	<input type="year" name="CVC " placeholder="CVC CODE" class="btn">
     <br>
     <hr>
    <br><label> Do you want to save your card details :</label>
    <input type="checkbox" name="save" value="save"/>    
                 
	<p>Your order</p>
	
	<hr>
	
	<input type="number" name="amount" placeholder="Total amount"><br/>
	
    <br>
    <input type="submit" class = "Button">
	<input type="reset" class = "Button">
	
    </form>
	</div>
	
    <?php include'footer.php';?>

</body>

</html>