<?php
// customerSupport/myInquiries.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireLogin();

$userId = (int)$_SESSION['userData']['Registered_User_Id'];
$inquiries = $connection->query(
    "SELECT i.*, c.Title AS course_title FROM inquiry i
     LEFT JOIN course c ON c.Course_Id = i.Course_Id
     WHERE i.Registered_User_Id = $userId
     ORDER BY i.Submitted_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Inquiries — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/contact.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/header.php'; ?>
<div class="page-wrapper">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 class="section-title">My Inquiries</h1>
      <p class="section-subtitle">Track all your messages and replies</p>
    </div>
    <a href="contactUs.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Inquiry</a>
  </div>

  <?php if ($inquiries->num_rows === 0): ?>
    <div class="empty-state" style="text-align:center;padding:60px;background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)">
      <i class="fas fa-inbox" style="font-size:48px;color:var(--border);display:block;margin-bottom:16px"></i>
      <h3>No inquiries yet</h3>
      <p style="color:var(--text-muted);margin-bottom:20px">Have a question? Send us a message.</p>
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
              <span class="course-tag" style="margin-left:10px"><?= htmlspecialchars($inq['course_title']) ?></span>
            <?php endif; ?>
          </div>
          <div class="inquiry-meta">
            <span class="badge <?= $inq['Reply'] ? 'badge-active' : 'badge-pending' ?>"><?= $inq['Reply'] ? 'Replied' : 'Pending' ?></span>
            <small><?= date('M j, Y', strtotime($inq['Submitted_At'])) ?></small>
          </div>
        </div>
        <p class="inquiry-msg"><?= htmlspecialchars($inq['Message']) ?></p>
        <?php if ($inq['Reply']): ?>
          <div class="inquiry-reply"><i class="fas fa-reply"></i> <strong>Admin Reply:</strong> <?= htmlspecialchars($inq['Reply']) ?></div>
        <?php endif; ?>
        <div class="inquiry-actions">
          <a href="editInquiry.php?id=<?= $inq['Inquiry_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
          <form action="deleteInquiry.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this inquiry?')">
            <input type="hidden" name="inquiry_id" value="<?= $inq['Inquiry_Id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
          </form>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>