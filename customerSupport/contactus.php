<?php
require '../common/config.php';
session_start();

$loggedIn = isset($_SESSION["userData"]);
$uid = $loggedIn ? $_SESSION["userData"]["Registered_User_Id"] : null;
$email = $loggedIn ? $_SESSION["userData"]["Email"] : "";
$inquiries = [];

if ($loggedIn) {
    $query = "SELECT * FROM enquiry WHERE Registered_User_Id = $uid";
    $result = $connection->query($query);
    $inquiries = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Educaster</title>
    <link rel="stylesheet" href="../css/contactus.css">
</head>
<body>

<?php include '../common/header.php'; ?>

<h2 class="intro">We are here to support you!</h2>

<div class="meetus-wrapper">
    <div class="meetus-container">
        <p class="topic">Meet Us</p><br>
        <i class="fa fa-phone"></i> +94 711212125<br>
        <i class="fa fa-map-marker"></i> No 167, New Kandy Road, Malabe.<br>
        <i class="fa fa-envelope"></i> info@educaster.com<br>
    </div>
</div>

<div class="wrapper">
    <div class="contact-container">
        <p class="topic">Share your thoughts or questions with us.</p><br>

        <?php if (!$loggedIn): ?>
            <p style="color:red;">Please log in to send inquiries.</p>
        <?php endif; ?>

        <form method="POST" action="sendInq.php">
            Email:<br>
            <input type="email" name="Email" value="<?= htmlspecialchars($email) ?>" <?= !$loggedIn ? 'disabled' : '' ?> required><br>

            Telephone:<br>
            <input type="tel" name="phone" maxlength="10" placeholder="+94XXXXXXXXX" <?= !$loggedIn ? 'disabled' : '' ?> required><br>

            Subject of your enquiry:<br>
            <input type="text" name="Enquiry" placeholder="What is this about?" <?= !$loggedIn ? 'disabled' : '' ?> required><br><br/>

            Enquiry:<br>
            <textarea name="Details" rows="8" placeholder="How can we assist you?" <?= !$loggedIn ? 'disabled' : '' ?> required></textarea><br>

            <input type="submit" value="Submit" <?= !$loggedIn ? 'disabled' : '' ?>>
        </form>
    </div>
</div>

<?php if ($loggedIn): ?>
    <div class="wrapper">
        <div class="contact-container">
            <h3 class="topic">Your Previous Inquiries</h3><br>
            <?php if (count($inquiries) > 0): ?>
                <table border="1" cellpadding="5">
                    <tr>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Phone</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($inquiries as $row): ?>
                        <tr>
                            <form action="updateInq.php" method="POST">
                                <input type="hidden" name="id" value="<?= $row['Enquiry_Id'] ?>">
                                <td><input type="text" name="Enquiry" value="<?= htmlspecialchars($row['Enq_Subject']) ?>" required></td>
                                <td><textarea name="Details" rows="3"><?= htmlspecialchars($row['Enquiry']) ?></textarea></td>
                                <td><input type="text" name="phone" value="<?= $row['phone_Number'] ?>" required></td>
                                <input type="hidden" name="Email" value="<?= htmlspecialchars($row['Email']) ?>">
                                <td><?= $row['Date'] ?></td>
                                <td>
                                    <input type="submit" value="Update">
                            </form>
                            <form action="deleteInq.php" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['Enquiry_Id'] ?>">
                                <input type="submit" value="Delete">
                            </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No previous inquiries found.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php include '../common/footer.php'; ?>
</body>
</html>
