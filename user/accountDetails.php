<?php
session_start();
if (!isset($_SESSION["userData"])) {
    header("location: login.php");
    exit();
}

$userData = $_SESSION["userData"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Details</title>
    <link rel="stylesheet" href="../css/accountDetails.css">
    
</head>
<body>
<?php include '../common/header.php'; ?>

        <div class="account-wrapper">
            <div class="account-container">
                <h1 class="topic">Account Details</h1>
                <p class="detail-row">
            <strong class="label">Username:</strong>
            <span class="value"><?= htmlspecialchars($userData["User_Name"]) ?></span>
        </p>

        <p class="detail-row">
            <strong class="label">Email:</strong>
            <span class="value"><?= htmlspecialchars($userData["Email"]) ?></span>
        </p>

        <div class="actions">
            <a href="updateAccountDetails.php">Update Account</a> |
            <a href="#" id="deleteAccountLink">Delete Account</a>
        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="confirmModal">
    <div class="modal-content">
        <p>Are you sure you want to delete your account? This action cannot be undone.</p>
        <button id="yesDelete">Yes</button>
        <button id="noDelete">Cancel</button>
    </div>
</div>

<?php include '../common/footer.php'; ?>
</body>
</html>

<script>
const deleteLink = document.getElementById("deleteAccountLink");
const modal = document.getElementById("confirmModal");
const yesBtn = document.getElementById("yesDelete");
const noBtn = document.getElementById("noDelete");

deleteLink.addEventListener("click", function(e) {
    e.preventDefault();
    modal.style.display = "flex";
});

noBtn.addEventListener("click", function() {
    modal.style.display = "none";
});

yesBtn.addEventListener("click", function() {
    window.location.href = "deleteAccount.php";
});

// Close modal if clicked outside content
window.addEventListener("click", function(e) {
    if (e.target === modal) {
        modal.style.display = "none";
    }
});
</script>
