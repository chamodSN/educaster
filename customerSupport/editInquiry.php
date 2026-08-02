<?php
// customerSupport/editInquiry.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId = currentUserId();
$id     = (int) ($_GET['id'] ?? 0);

$stmt = $connection->prepare('SELECT * FROM inquiry WHERE Inquiry_Id=? AND Registered_User_Id=?');
$stmt->bind_param('ii', $id, $userId);
$stmt->execute();
$inquiry = $stmt->get_result()->fetch_assoc();

if (!$inquiry) { header('Location: myInquiries.php'); exit(); }

if ($inquiry['Reply']) {
    header('Location: myInquiries.php?error=alreadyreplied'); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_inquiry'])) {
    verify_csrf();
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject !== '' && $message !== '') {
        $upd = $connection->prepare('UPDATE inquiry SET Subject=?, Message=? WHERE Inquiry_Id=? AND Registered_User_Id=?');
        $upd->bind_param('ssii', $subject, $message, $id, $userId);
        $upd->execute();
        header('Location: myInquiries.php?updated=1'); exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Inquiry — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div class="narrow-wrapper">
    <div class="contact-form-box">
      <h2><i class="fas fa-edit" style="color:var(--green)"></i> Edit Inquiry</h2>
      <form action="editInquiry.php?id=<?= $id ?>" method="POST" style="margin-top:24px">
        <?= csrf_field() ?>
        <div class="form-group">
          <label>Subject <span class="req">*</span></label>
          <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($inquiry['Subject']) ?>" required>
        </div>
        <div class="form-group">
          <label>Message <span class="req">*</span></label>
          <textarea name="message" class="form-control" rows="6" required><?= htmlspecialchars($inquiry['Message']) ?></textarea>
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" name="update_inquiry" class="btn btn-primary" style="flex:1">
            <i class="fas fa-save"></i> Update Inquiry
          </button>
          <a href="myInquiries.php" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>