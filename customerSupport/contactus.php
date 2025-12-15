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
    <title>Contact Us | Educaster</title>
    <link rel="stylesheet" href="../css/contactus.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>

<?php include '../common/header.php'; ?>

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

<section class="contact-section">
    <div class="contact-box">

        <div class="contact-image">
            <lottie-player
                src="https://assets7.lottiefiles.com/packages/lf20_w51pcehl.json"
                background="transparent"
                speed="1"
                loop
                autoplay>
            </lottie-player>
        </div>


        <div class="contact-form">
            <h3>Send Us an Inquiry</h3>

            <?php if (!$loggedIn): ?>
                <p class="warning">Please log in to send inquiries.</p>
            <?php endif; ?>

            <form method="POST" action="sendInq.php" id="contactForm">
                <input type="email" name="Email"
                       value="<?= htmlspecialchars($email) ?>"
                       placeholder="Your Email"
                       <?= !$loggedIn ? 'disabled' : '' ?> required>

                <input type="tel" name="phone"
                       placeholder="Phone Number"
                       maxlength="10"
                       <?= !$loggedIn ? 'disabled' : '' ?> required>

                <input type="text" name="Enquiry"
                       placeholder="Subject"
                       <?= !$loggedIn ? 'disabled' : '' ?> required>

                <textarea name="Details" rows="5"
                          placeholder="Your Message"
                          <?= !$loggedIn ? 'disabled' : '' ?> required></textarea>

                <button type="submit" <?= !$loggedIn ? 'disabled' : '' ?>>
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php include '../common/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const lottie = document.querySelector("lottie-player");
    lottie.style.opacity = 0;
    lottie.style.transform = "translateY(30px)";

    setTimeout(() => {
        lottie.style.transition = "all 0.8s ease";
        lottie.style.opacity = 1;
        lottie.style.transform = "translateY(0)";
    }, 200);
});

// simple form UX
const form = document.getElementById("contactForm");

if (form) {
    form.addEventListener("submit", () => {
        alert("Thank you! Your inquiry has been sent.");
    });
}

// animate cards on scroll
const cards = document.querySelectorAll(".intro-card");

window.addEventListener("scroll", () => {
    cards.forEach(card => {
        const pos = card.getBoundingClientRect().top;
        if (pos < window.innerHeight - 100) {
            card.style.opacity = 1;
            card.style.transform = "translateY(0)";
        }
    });
});

</script>

</body>
</html>
