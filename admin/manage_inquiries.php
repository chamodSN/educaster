<?php
// admin/manage_inquiries.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $id    = (int)$_POST['inquiry_id'];
    $reply = trim($_POST['reply_text']);
    if (!empty($reply)) {
        $stmt = $connection->prepare("UPDATE inquiry SET Reply=? WHERE Inquiry_Id=?");
        $stmt->bind_param("si", $reply, $id);
        $stmt->execute();
    }
    header("Location: manage_inquiries.php?replied=1"); exit();
}

$inquiries = $connection->query(
    "SELECT i.*, u.User_Name, c.Title AS course_title
     FROM inquiry i
     LEFT JOIN registered_user u ON u.Registered_User_Id=i.Registered_User_Id
     LEFT JOIN course c ON c.Course_Id=i.Course_Id
     ORDER BY i.Reply IS NOT NULL, i.Submitted_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Inquiries — Admin</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="/educaster/css/contact.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">User Inquiries</h1>
  <?php if (isset($_GET['replied'])): ?>
    <div class="alert alert-success"><i class="fas fa-check"></i> Reply sent successfully!</div>
  <?php endif; ?>

  <div class="inquiry-list" style="margin-top:24px">
    <?php while ($inq = $inquiries->fetch_assoc()): ?>
    <div class="inquiry-card <?= $inq['Reply'] ? 'replied' : 'pending' ?>">
      <div class="inquiry-card-header">
        <div>
          <strong><?= htmlspecialchars($inq['Subject']) ?></strong>
          <?php if ($inq['course_title']): ?>
            <span class="course-tag" style="margin-left:8px"><?= htmlspecialchars($inq['course_title']) ?></span>
          <?php endif; ?>
        </div>
        <div class="inquiry-meta">
          <span class="badge <?= $inq['Reply'] ? 'badge-active' : 'badge-pending' ?>"><?= $inq['Reply'] ? 'Replied' : 'Pending' ?></span>
          <small><?= date('M j, Y g:i A', strtotime($inq['Submitted_At'])) ?></small>
          <small><strong>From:</strong> <?= htmlspecialchars($inq['User_Name'] ?? $inq['Email']) ?></small>
        </div>
      </div>
      <p class="inquiry-msg"><?= htmlspecialchars($inq['Message']) ?></p>
      <?php if ($inq['Reply']): ?>
        <div class="inquiry-reply"><i class="fas fa-reply"></i> <strong>Your Reply:</strong> <?= htmlspecialchars($inq['Reply']) ?></div>
      <?php else: ?>
        <form action="manage_inquiries.php" method="POST" style="margin-top:12px">
          <input type="hidden" name="inquiry_id" value="<?= $inq['Inquiry_Id'] ?>">
          <div class="form-group">
            <textarea name="reply_text" class="form-control" rows="3" placeholder="Type your reply..." required></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-reply"></i> Send Reply</button>
        </form>
      <?php endif; ?>
    </div>
    <?php endwhile; ?>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>