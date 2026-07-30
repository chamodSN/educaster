<?php
// customerSupport/contactUs.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';

$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $email    = trim($_POST['email'] ?? ($_SESSION['userData']['Email'] ?? ''));
    $subject  = trim($_POST['subject'] ?? '');
    $message  = trim($_POST['message'] ?? '');
    $courseId = (int)($_POST['course_id'] ?? 0) ?: null;
    $userId   = $_SESSION['userData']['Registered_User_Id'] ?? null;

    if (!empty($email) && !empty($subject) && !empty($message)) {
        $stmt = $connection->prepare("INSERT INTO inquiry (Registered_User_Id, Email, Subject, Message, Course_Id) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isssi", $userId, $email, $subject, $message, $courseId);
        $stmt->execute();
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/contact.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<section class="contact-hero"><div class="page-wrapper"><h1>Contact Us</h1><p>We'd love to hear from you. Reach out anytime.</p></div></section>

<div class="page-wrapper">
  <div class="contact-grid">
    <!-- Info Cards -->
    <div class="contact-info">
      <div class="info-card"><i class="fas fa-envelope"></i><h4>Email Us</h4><p>support@educaster.com</p></div>
      <div class="info-card"><i class="fas fa-phone"></i><h4>Call Us</h4><p>+94 11 234 5678</p></div>
      <div class="info-card"><i class="fas fa-map-marker-alt"></i><h4>Location</h4><p>Colombo, Sri Lanka</p></div>
      <?php if (isLoggedIn()): ?>
      <a href="myInquiries.php" class="info-card" style="text-decoration:none;color:inherit;border-color:var(--green)">
        <i class="fas fa-list" style="color:var(--green)"></i><h4>My Inquiries</h4><p>View & track your messages</p>
      </a>
      <?php endif; ?>
    </div>

    <!-- Form -->
    <div class="contact-form-box">
      <?php if ($submitted): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> Your inquiry has been sent! We'll get back to you soon.</div>
      <?php endif; ?>
      <h2>Send a Message</h2>
      <form action="contactUs.php" method="POST">
        <div class="form-group">
          <label>Your Email <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['userData']['Email'] ?? '') ?>" required placeholder="you@email.com">
        </div>
        <div class="form-group">
          <label>Subject <span class="req">*</span></label>
          <input type="text" name="subject" class="form-control" required placeholder="How can we help?" value="<?= htmlspecialchars($_GET['subject'] ?? '') ?>">
        </div>
        <?php if (isset($_GET['course_id'])): ?>
          <input type="hidden" name="course_id" value="<?= (int)$_GET['course_id'] ?>">
        <?php endif; ?>
        <div class="form-group">
          <label>Message <span class="req">*</span></label>
          <textarea name="message" class="form-control" rows="6" required placeholder="Tell us more..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="submit_inquiry" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px">
          <i class="fas fa-paper-plane"></i> Send Message
        </button>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>