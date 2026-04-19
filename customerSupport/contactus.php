<?php
require '../common/config.php';
session_start();

$loggedIn = isset($_SESSION["userData"]);
$uid = $loggedIn ? $_SESSION["userData"]["Registered_User_Id"] : null;
$email = $loggedIn ? $_SESSION["userData"]["Email"] : "";
$inquiries = [];

if ($loggedIn) {
    $query = "SELECT * FROM enquiry WHERE Registered_User_Id = $uid ORDER BY Date DESC";
    $result = $connection->query($query);
    $inquiries = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | Educaster</title>
    <link rel="stylesheet" href="../css/contactus.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>

<?php include '../common/header.php'; ?>

<!-- ================= MEET US SECTION ================= -->
<section class="meet-us-section">
    <h2 class="section-title">Meet Us</h2>
    <p class="section-subtitle">We’d love to hear from you</p>

    <div class="meet-us-cards">
        <div class="intro-card">
            <i class="fas fa-phone-alt"></i>
            <h4>Call Us</h4>
            <p>+94 71 121 2125</p>
        </div>

        <div class="intro-card">
            <i class="fas fa-envelope"></i>
            <h4>Email Us</h4>
            <p>info@educaster.com</p>
        </div>

        <div class="intro-card">
            <i class="fas fa-map-marker-alt"></i>
            <h4>Visit Us</h4>
            <p>No 167, New Kandy Road, Malabe</p>
        </div>

        <div class="intro-card">
            <i class="fas fa-clock"></i>
            <h4>Office Hours</h4>
            <p>Mon – Fri<br>9:00 AM – 5:00 PM</p>
        </div>
    </div>
</section>

<!-- ================= CONTACT FORM & INQUIRY LIST ================= -->
<section class="contact-section">
    <div class="contact-box">
        <!-- Lottie Animation -->
        <div class="contact-image">
            <lottie-player
                src="https://assets7.lottiefiles.com/packages/lf20_w51pcehl.json"
                background="transparent"
                speed="1"
                loop
                autoplay>
            </lottie-player>
        </div>

        <!-- Form -->
        <div class="contact-form" id="contactFormContainer">
            
            <div class="contact-actions">
                <button id="showFormBtn" class="action-btn">Send Us an Inquiry</button>
                <?php if($loggedIn): ?>
                <button id="showInquiriesBtn" class="action-btn">View Your Inquiries</button>
                <?php endif; ?>
            </div>

            <?php if (!$loggedIn): ?>
                <p class="warning">Please log in to send inquiries.</p>
            <?php endif; ?>

            <form method="POST" action="sendInq.php" id="contactForm">
                <input type="email" name="Email" value="<?= htmlspecialchars($email) ?>" placeholder="Your Email" <?= !$loggedIn ? 'disabled' : '' ?> required>
                <input type="tel" name="phone" placeholder="Phone Number" maxlength="10" <?= !$loggedIn ? 'disabled' : '' ?> required>
                <input type="text" name="Enquiry" placeholder="Subject" <?= !$loggedIn ? 'disabled' : '' ?> required>
                <textarea name="Details" rows="5" placeholder="Your Message" <?= !$loggedIn ? 'disabled' : '' ?> required></textarea>
                <button type="submit" <?= !$loggedIn ? 'disabled' : '' ?>>Send Message</button>
            </form>
        </div>

        <!-- Inquiry List -->
        <?php if($loggedIn): ?>
        <div class="contact-inquiries" id="inquiriesContainer" style="display:none;">
            <h3>Your Inquiries</h3>
            <?php if(count($inquiries)>0): ?>
            <ul id="inquiryList">
                <?php foreach($inquiries as $inq): ?>
                <li class="inq-item" data-id="<?= $inq['Enquiry_Id'] ?>">
                    <?= htmlspecialchars($inq['Enq_Subject']) ?>
                    <?php if(!empty($inq['Reply'])): ?>
                        <span class="replied">✓ Replied</span>
                    <?php else: ?>
                        <span class="pending">Pending</span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>

            <div id="inqDetails" style="display:none;">
                <form id="inqUpdateForm" method="POST">
                    <input type="hidden" name="id" id="inqId">
                    <label>Subject:</label>
                    <input type="text" name="Enquiry" id="inqSubject" required>
                    <label>Message:</label>
                    <textarea name="Details" id="inqMessage" rows="5" required></textarea>
                    <label>Phone:</label>
                    <input type="text" name="phone" id="inqPhone" required>
                    <label>Email:</label>
                    <input type="email" name="Email" id="inqEmail" required>
                    <label>Reply:</label>
                    <textarea id="inqReply" rows="3" disabled></textarea>
                    <div class="inq-actions">
                        <button type="submit" id="updateBtn">Update Inquiry</button>
                        <button type="button" id="deleteBtn">Delete Inquiry</button>
                    </div>
                </form>
            </div>

            <?php else: ?>
            <p>No inquiries sent yet.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../common/footer.php'; ?>

<script>
// Toggle Form / Inquiries
const showFormBtn = document.getElementById('showFormBtn');
const showInquiriesBtn = document.getElementById('showInquiriesBtn');
const contactFormContainer = document.getElementById('contactFormContainer');
const inquiriesContainer = document.getElementById('inquiriesContainer');
const inqItems = document.querySelectorAll('.inq-item');
const inqDetailsDiv = document.getElementById('inqDetails');
const updateForm = document.getElementById('inqUpdateForm');
const updateBtn = document.getElementById('updateBtn');
const deleteBtn = document.getElementById('deleteBtn');

showFormBtn?.addEventListener('click',()=>{
    contactFormContainer.style.display='block';
    inquiriesContainer.style.display='none';
});
showInquiriesBtn?.addEventListener('click',()=>{
    contactFormContainer.style.display='none';
    inquiriesContainer.style.display='block';
    inqDetailsDiv.style.display='none';
});

// Load inquiry details
inqItems.forEach(item=>{
    item.addEventListener('click',()=>{
        const id=item.dataset.id;
        fetch(`getInquiry.php?id=${id}`).then(res=>res.json()).then(data=>{
            document.getElementById('inqId').value=data.Enquiry_Id;
            document.getElementById('inqSubject').value=data.Enq_Subject;
            document.getElementById('inqMessage').value=data.Enquiry;
            document.getElementById('inqPhone').value=data.phone_Number;
            document.getElementById('inqEmail').value=data.Email;
            document.getElementById('inqReply').value=data.Reply || 'No reply yet';
            updateBtn.disabled=!!data.Reply;
            inqDetailsDiv.style.display='block';
        });
    });
});

// Delete inquiry
deleteBtn?.addEventListener('click',()=>{
    if(confirm('Are you sure to delete this inquiry?')){
        updateForm.action='deleteInquery.php';
        updateForm.submit();
    }
});

// Update inquiry
updateForm?.addEventListener('submit',e=>{
    e.preventDefault();
    if(confirm('Update this inquiry?')){
        updateForm.action='updateInquery.php';
        updateForm.submit();
    }
});
</script>
</body>
</html>
