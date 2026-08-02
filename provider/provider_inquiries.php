<?php
// provider/provider_inquiries.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text'])) {
    verify_csrf();
    $inqId = (int) $_POST['inquiry_id'];
    $reply = trim($_POST['reply_text']);
    if ($reply !== '') {
        $stmt = $connection->prepare(
            'UPDATE inquiry SET Reply=? WHERE Inquiry_Id=? AND Course_Id IN (SELECT Course_Id FROM course WHERE Provider_Id=?)'
        );
        $stmt->bind_param('sii', $reply, $inqId, $providerId);
        $stmt->execute();
    }
    header('Location: provider_inquiries.php?replied=1');
    exit();
}

$inquiries = $connection->query(
    "SELECT i.*, c.Title AS course_title, u.User_Name FROM inquiry i
     JOIN course c ON c.Course_Id=i.Course_Id
     LEFT JOIN registered_user u ON u.Registered_User_Id=i.Registered_User_Id
     WHERE c.Provider_Id=$providerId
     ORDER BY (i.Reply IS NOT NULL), i.Submitted_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Inquiries — Provider</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Course Inquiries</h1>
  <?php if (isset($_GET['replied'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Reply sent!</div><?php endif; ?>

  <?php if ($inquiries->num_rows === 0): ?>
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>No inquiries yet</h3>
      <p>Student questions about your courses will appear here.</p>
    </div>
  <?php else: ?>
  <div class="inquiry-list">
    <?php while ($inq = $inquiries->fetch_assoc()): ?>
    <div class="inquiry-card <?= $inq['Reply'] ? 'replied' : 'pending' ?>">
      <div class="inquiry-card-header">
        <div>
          <strong><?= htmlspecialchars($inq['Subject']) ?></strong>
          <span class="pill" style="margin-left:8px"><?= htmlspecialchars($inq['course_title']) ?></span>
        </div>
        <div class="inquiry-meta">
          <span class="badge <?= $inq['Reply'] ? 'badge-active' : 'badge-pending' ?>"><?= $inq['Reply'] ? 'Replied' : 'Pending' ?></span>
          <small>From: <?= htmlspecialchars($inq['User_Name'] ?? $inq['Email']) ?></small>
          <small><?= format_date($inq['Submitted_At']) ?></small>
        </div>
      </div>
      <p class="inquiry-msg"><?= nl2br(htmlspecialchars($inq['Message'])) ?></p>
      <?php if ($inq['Reply']): ?>
        <div class="inquiry-reply"><i class="fas fa-reply"></i> <strong>Your Reply:</strong> <?= nl2br(htmlspecialchars($inq['Reply'])) ?></div>
      <?php else: ?>
        <form action="provider_inquiries.php" method="POST" style="margin-top:12px">
          <?= csrf_field() ?>
          <input type="hidden" name="inquiry_id" value="<?= (int) $inq['Inquiry_Id'] ?>">
          <textarea name="reply_text" class="form-control" rows="3" placeholder="Reply to this inquiry..." required></textarea>
          <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px"><i class="fas fa-reply"></i> Send Reply</button>
        </form>
      <?php endif; ?>
    </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>