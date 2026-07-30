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
  <title>Terms & Privacy — Educaster</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/terms.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include 'common/header.php'; ?>

<section class="terms-hero">
  <div class="page-wrapper">
    <h1>Legal & Policies</h1>
    <p>Understand your rights and our responsibilities</p>
  </div>
</section>

<div class="page-wrapper terms-layout">
  <!-- Tab Nav -->
  <div class="terms-tabs">
    <button class="terms-tab active" data-target="terms">
      <i class="fas fa-file-contract"></i> Terms & Conditions
    </button>
    <button class="terms-tab" data-target="privacy">
      <i class="fas fa-shield-alt"></i> Privacy Policy
    </button>
    <button class="terms-tab" data-target="cookies">
      <i class="fas fa-cookie-bite"></i> Cookie Policy
    </button>
  </div>

  <!-- Terms & Conditions -->
  <div class="terms-panel active" id="terms">
    <h2><i class="fas fa-file-contract"></i> Terms & Conditions</h2>
    <p class="terms-updated">Last updated: January 2025</p>

    <div class="terms-section">
      <h3>1. User Accounts</h3>
      <p>Users must create an account to access certain features of Educaster. You agree to provide accurate, complete, and current information during registration. You are responsible for maintaining the confidentiality of your account credentials and all activities under your account.</p>
      <p>Users may only possess one account on Educaster. Creating multiple accounts or impersonating others is prohibited.</p>
    </div>
    <div class="terms-section">
      <h3>2. Use of Content</h3>
      <p>All content provided on Educaster — including course videos, quizzes, text, graphics, and logos — is the property of Educaster or its content providers and is protected by copyright and intellectual property laws. Users may not reproduce, distribute, modify, or create derivative works without prior written consent.</p>
      <p>Users may access content solely for personal, non-commercial educational purposes.</p>
    </div>
    <div class="terms-section">
      <h3>3. Course Enrolment</h3>
      <p>All courses on Educaster are currently offered free of charge. No payment information is required during enrolment. Educaster reserves the right to introduce paid tiers in the future with clear advance notice.</p>
    </div>
    <div class="terms-section">
      <h3>4. User Conduct</h3>
      <p>Users agree to use Educaster in compliance with all applicable laws and regulations. You may not engage in activity that disrupts or interferes with the platform or its users. Users are solely responsible for content they submit or upload.</p>
    </div>
    <div class="terms-section">
      <h3>5. Disclaimer of Warranties</h3>
      <p>Educaster is provided on an "as is" and "as available" basis without warranties of any kind. We do not guarantee that the platform will be uninterrupted, error-free, or free of harmful components.</p>
    </div>
    <div class="terms-section">
      <h3>6. Limitation of Liability</h3>
      <p>In no event shall Educaster or its affiliates be liable for any direct, indirect, incidental, special, or consequential damages arising out of or connected with the use or inability to use the platform.</p>
    </div>
    <div class="terms-section">
      <h3>7. Changes to Terms</h3>
      <p>Educaster reserves the right to update or modify these Terms at any time without prior notice. Continued use of the platform after posting changes constitutes acceptance of the revised Terms.</p>
    </div>
    <div class="terms-section">
      <h3>8. Contact</h3>
      <p>For questions about these Terms, contact us at <a href="mailto:legal@educaster.com">legal@educaster.com</a></p>
    </div>
  </div>

  <!-- Privacy Policy -->
  <div class="terms-panel" id="privacy">
    <h2><i class="fas fa-shield-alt"></i> Privacy Policy</h2>
    <p class="terms-updated">Last updated: January 2025</p>

    <div class="terms-section">
      <h3>1. Information We Collect</h3>
      <p>Educaster collects personal information including name, email address, username, and optional phone number during registration. We also collect usage data such as courses enrolled, quiz scores, and interaction timestamps.</p>
    </div>
    <div class="terms-section">
      <h3>2. How We Use Your Information</h3>
      <p>Your data is used to: provide access to courses and learning materials, manage user accounts, send account-related communications, improve platform experience, and display your learning progress.</p>
    </div>
    <div class="terms-section">
      <h3>3. Information Sharing</h3>
      <p>We do not sell or rent your personal information to third parties for marketing. We may share data with service providers (hosting, analytics) solely to operate the platform. Course providers can see aggregate enrolment statistics but not individual personal details.</p>
    </div>
    <div class="terms-section">
      <h3>4. Data Security</h3>
      <p>We employ industry-standard security measures including password hashing (bcrypt), prepared SQL statements to prevent injection, and HTTPS transmission. However, no method of electronic storage is 100% secure.</p>
    </div>
    <div class="terms-section">
      <h3>5. Your Rights</h3>
      <p>You have the right to access, update, or delete your account at any time through your Account Details page. To request complete data deletion, contact us at <a href="mailto:privacy@educaster.com">privacy@educaster.com</a>.</p>
    </div>
    <div class="terms-section">
      <h3>6. Retention</h3>
      <p>We retain your data for as long as your account is active. Upon account deletion, your personal data is removed from our systems within 30 days, except where retention is required by law.</p>
    </div>
  </div>

  <!-- Cookie Policy -->
  <div class="terms-panel" id="cookies">
    <h2><i class="fas fa-cookie-bite"></i> Cookie Policy</h2>
    <p class="terms-updated">Last updated: January 2025</p>

    <div class="terms-section">
      <h3>1. What Are Cookies</h3>
      <p>Cookies are small text files stored on your device when you visit Educaster. They enable the platform to remember your session and preferences.</p>
    </div>
    <div class="terms-section">
      <h3>2. Cookies We Use</h3>
      <p><strong>Session cookies:</strong> Required for login and keeping you authenticated while browsing. These expire when you close your browser.<br>
      <strong>Preference cookies:</strong> Remember your settings such as language and display preferences.<br>
      <strong>Analytics cookies:</strong> Help us understand how users interact with the platform to improve the experience.</p>
    </div>
    <div class="terms-section">
      <h3>3. Managing Cookies</h3>
      <p>You can disable cookies through your browser settings. Note that disabling session cookies will prevent you from logging in to Educaster.</p>
    </div>
    <div class="terms-section">
      <h3>4. Contact</h3>
      <p>Questions about our cookie use? Email <a href="mailto:privacy@educaster.com">privacy@educaster.com</a></p>
    </div>
  </div>
</div>

<?php include 'common/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>