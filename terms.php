<?php
// terms.php
require_once 'common/config.php';
require_once 'common/loginFunctions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms &amp; Privacy — Educaster</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/terms.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="terms-hero">
  <div class="page-wrapper" style="padding-top:0;padding-bottom:0">
    <h1>Legal &amp; Policies</h1>
    <p>Understand your rights and our responsibilities</p>
  </div>
</section>

<div class="page-wrapper terms-page">
  <div class="terms-tabs">
    <button class="terms-tab active" data-target="terms" type="button"><i class="fas fa-file-contract"></i> Terms &amp; Conditions</button>
    <button class="terms-tab" data-target="privacy" type="button"><i class="fas fa-shield-halved"></i> Privacy Policy</button>
    <button class="terms-tab" data-target="cookies" type="button"><i class="fas fa-cookie-bite"></i> Cookie Policy</button>
  </div>

  <div class="terms-panel active" id="terms">
    <h2><i class="fas fa-file-contract"></i> Terms &amp; Conditions</h2>
    <p class="t-updated">Last updated: January 2026</p>

    <div class="t-section">
      <h3>1. User Accounts</h3>
      <p>Users must create an account to access certain features of Educaster. You agree to provide accurate, complete, and current information during registration, and are responsible for maintaining the confidentiality of your credentials.</p>
      <p>Each person may hold only one account. Creating multiple accounts or impersonating others is prohibited.</p>
    </div>
    <div class="t-section">
      <h3>2. Use of Content</h3>
      <p>All content on Educaster — including course videos, quizzes, text, and graphics — is the property of Educaster or its content providers and is protected by copyright. Users may access content solely for personal, non-commercial educational purposes.</p>
    </div>
    <div class="t-section">
      <h3>3. Course Enrolment</h3>
      <p>All courses on Educaster are currently offered free of charge. No payment information is required during enrolment. Educaster reserves the right to introduce paid tiers in the future with clear advance notice.</p>
    </div>
    <div class="t-section">
      <h3>4. User Conduct</h3>
      <p>Users agree to use Educaster in compliance with all applicable laws. You may not disrupt or interfere with the platform or its users, and are solely responsible for content you submit.</p>
    </div>
    <div class="t-section">
      <h3>5. Disclaimer &amp; Liability</h3>
      <p>Educaster is provided on an "as is" basis without warranties of any kind. In no event shall Educaster be liable for indirect, incidental, or consequential damages arising from use of the platform.</p>
    </div>
    <div class="t-section">
      <h3>6. Changes to Terms</h3>
      <p>Educaster may update these Terms at any time. Continued use of the platform after changes are posted constitutes acceptance of the revised Terms.</p>
    </div>
    <div class="t-section">
      <h3>7. Contact</h3>
      <p>Questions about these Terms? Email <a href="mailto:legal@educaster.com">legal@educaster.com</a></p>
    </div>
  </div>

  <div class="terms-panel" id="privacy">
    <h2><i class="fas fa-shield-halved"></i> Privacy Policy</h2>
    <p class="t-updated">Last updated: January 2026</p>

    <div class="t-section">
      <h3>1. Information We Collect</h3>
      <p>Educaster collects personal information including name, email address, username, and optional phone number during registration, plus usage data such as courses enrolled, quiz scores, and interaction timestamps.</p>
    </div>
    <div class="t-section">
      <h3>2. How We Use Your Information</h3>
      <p>Your data is used to provide access to courses, manage your account, send account-related communications, and display your learning progress.</p>
    </div>
    <div class="t-section">
      <h3>3. Information Sharing</h3>
      <p>We do not sell or rent your personal information for marketing. Course providers can see aggregate enrolment statistics, but not your individual personal details.</p>
    </div>
    <div class="t-section">
      <h3>4. Data Security</h3>
      <p>We employ industry-standard security measures including bcrypt password hashing, prepared SQL statements, and CSRF-protected forms. No method of electronic storage is 100% secure.</p>
    </div>
    <div class="t-section">
      <h3>5. Your Rights</h3>
      <p>You may access, update, or delete your account at any time from your Account Details page. To request complete data deletion, contact <a href="mailto:privacy@educaster.com">privacy@educaster.com</a>.</p>
    </div>
    <div class="t-section">
      <h3>6. Retention</h3>
      <p>We retain your data for as long as your account is active. Upon deletion, your personal data is removed from our systems, except where retention is required by law.</p>
    </div>
  </div>

  <div class="terms-panel" id="cookies">
    <h2><i class="fas fa-cookie-bite"></i> Cookie Policy</h2>
    <p class="t-updated">Last updated: January 2026</p>

    <div class="t-section">
      <h3>1. What Are Cookies</h3>
      <p>Cookies are small text files stored on your device when you visit Educaster. They let the platform remember your session and preferences.</p>
    </div>
    <div class="t-section">
      <h3>2. Cookies We Use</h3>
      <p><strong>Session cookies:</strong> required for login and keeping you authenticated; these expire when you close your browser.<br>
      <strong>Preference cookies:</strong> remember settings such as display preferences.</p>
    </div>
    <div class="t-section">
      <h3>3. Managing Cookies</h3>
      <p>You can disable cookies through your browser settings, though disabling session cookies will prevent you from logging in to Educaster.</p>
    </div>
    <div class="t-section">
      <h3>4. Contact</h3>
      <p>Questions about our cookie use? Email <a href="mailto:privacy@educaster.com">privacy@educaster.com</a></p>
    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>