<?php
// customerSupport/myInquiries.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

if (isAdmin()) { header('Location: ' . BASE_PATH . '/admin/manage_inquiries.php'); exit(); }

$userId = currentUserId();
$stmt = $connection->prepare(
    "SELECT i.*, c.Title AS course_title FROM inquiry i
     LEFT JOIN course c ON c.Course_Id = i.Course_Id
     WHERE i.Registered_User_Id = ?
     ORDER BY i.Submitted_At DESC"
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$inquiries = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Inquiries — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/contact.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['updated'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Inquiry updated.</div><?php endif; ?>
  <?php if (isset($_GET['error']) && $_GET['error'] === 'alreadyreplied'): ?><div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> This inquiry already has a reply and can no longer be edited.</div><?php endif; ?>

  <div class="list-toolbar">
    <div>
      <h1 class="section-title">My Inquiries</h1>
      <p class="section-subtitle" style="margin-bottom:0">Track all your messages and replies</p>
    </div>
    <a href="contactUs.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Inquiry</a>
  </div>

  <?php if ($inquiries->num_rows === 0): ?>
    <div class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>No inquiries yet</h3>
      <p>Have a question? Send us a message.</p>
      <a href="contactUs.php" class="btn btn-primary">Send Inquiry</a>
    </div>
  <?php else: ?>
    <div class="inquiry-list">
      <?php while ($inq = $inquiries->fetch_assoc()): ?>
      <div class="inquiry-card <?= $inq['Reply'] ? 'replied' : 'pending' ?>">
        <div class="inquiry-card-header">
          <div>
            <strong><?= htmlspecialchars($inq['Subject']) ?></strong>
            <?php if ($inq['course_title']): ?>
              <span class="pill" style="margin-left:10px"><?= htmlspecialchars($inq['course_title']) ?></span>
            <?php endif; ?>
          </div>
          <div class="inquiry-meta">
            <span class="badge <?= $inq['Reply'] ? 'badge-active' : 'badge-pending' ?>"><?= $inq['Reply'] ? 'Replied' : 'Pending' ?></span>
            <small><?= format_date($inq['Submitted_At']) ?></small>
          </div>
        </div>
        <p class="inquiry-msg"><?= nl2br(htmlspecialchars($inq['Message'])) ?></p>
        <?php if ($inq['Reply']): ?>
          <div class="inquiry-reply"><i class="fas fa-reply"></i> <strong>Admin Reply:</strong> <?= nl2br(htmlspecialchars($inq['Reply'])) ?></div>
        <?php endif; ?>
        <div class="inquiry-actions">
          <?php if (!$inq['Reply']): ?>
            <a href="editInquiry.php?id=<?= (int) $inq['Inquiry_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
          <?php endif; ?>
          <form action="deleteInquiry.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this inquiry?')">
            <?= csrf_field() ?>
            <input type="hidden" name="inquiry_id" value="<?= (int) $inq['Inquiry_Id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
          </form>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>