<?php
// admin/manage_providers.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (isset($_POST['action'], $_POST['user_id'])) {
        $action = $_POST['action'];
        $uid    = (int) $_POST['user_id'];

        if ($action === 'approve') {
            $stmt = $connection->prepare('UPDATE registered_user SET Is_Approved=1 WHERE Registered_User_Id=?');
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $stmt = $connection->prepare("UPDATE provider_request SET Status='approved' WHERE User_Id=?");
            $stmt->bind_param('i', $uid);
            $stmt->execute();
        } elseif ($action === 'reject') {
            $stmt = $connection->prepare('UPDATE registered_user SET Is_Approved=0 WHERE Registered_User_Id=?');
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $stmt = $connection->prepare("UPDATE provider_request SET Status='rejected' WHERE User_Id=?");
            $stmt->bind_param('i', $uid);
            $stmt->execute();
        }
        header('Location: manage_providers.php');
        exit();
    }

    if (isset($_POST['delete'])) {
        $uid = (int) $_POST['delete'];
        $stmt = $connection->prepare("DELETE FROM registered_user WHERE Registered_User_Id=? AND Registered_User_Type='INS'");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        header('Location: manage_providers.php?deleted=1');
        exit();
    }
}

$pending = $connection->query(
    "SELECT u.* FROM registered_user u
     JOIN provider_request pr ON pr.User_Id=u.Registered_User_Id
     WHERE pr.Status='pending' ORDER BY u.Created_At DESC"
);
$providers = $connection->query(
    "SELECT u.*, (SELECT COUNT(*) FROM course c WHERE c.Provider_Id=u.Registered_User_Id) AS course_count
     FROM registered_user u
     WHERE u.Registered_User_Type='INS' AND u.Is_Approved=1
     ORDER BY u.Created_At DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Providers — Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Provider account deleted.</div><?php endif; ?>
  <h1 class="section-title">Course Provider Management</h1>
  <p class="section-subtitle">Approve applications and manage active providers</p>

  <?php if ($pending->num_rows > 0): ?>
  <div class="alert alert-info" style="margin-top:20px">
    <i class="fas fa-bell"></i> <strong><?= $pending->num_rows ?> pending application<?= $pending->num_rows > 1 ? 's' : '' ?></strong> require your attention.
  </div>
  <h2 class="subhead"><i class="fas fa-clock" style="color:var(--chalk-dark)"></i> Pending Applications</h2>
  <div class="table-wrapper" style="margin-bottom:40px">
    <table>
      <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Expertise</th><th>Applied</th><th>Action</th></tr></thead>
      <tbody>
        <?php while ($p = $pending->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int) $p['Registered_User_Id'] ?></td>
          <td><?= htmlspecialchars($p['User_Name']) ?></td>
          <td><?= htmlspecialchars($p['Email']) ?></td>
          <td><?= htmlspecialchars($p['Phone_Number'] ?: '—') ?></td>
          <td><?= htmlspecialchars($p['Expertise'] ?: '—') ?></td>
          <td><?= format_date($p['Created_At']) ?></td>
          <td class="action-btns">
            <form action="manage_providers.php" method="POST">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="user_id" value="<?= (int) $p['Registered_User_Id'] ?>">
              <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i> Approve</button>
            </form>
            <form action="manage_providers.php" method="POST" onsubmit="return confirm('Reject this application?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="user_id" value="<?= (int) $p['Registered_User_Id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <h2 class="subhead"><i class="fas fa-circle-check" style="color:var(--green)"></i> Approved Providers</h2>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Courses</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>
        <?php if ($providers->num_rows === 0): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:28px">No approved providers yet.</td></tr>
        <?php endif; ?>
        <?php while ($p = $providers->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int) $p['Registered_User_Id'] ?></td>
          <td><?= htmlspecialchars($p['User_Name']) ?></td>
          <td><?= htmlspecialchars($p['Email']) ?></td>
          <td><?= (int) $p['course_count'] ?></td>
          <td><?= format_date($p['Created_At']) ?></td>
          <td class="action-btns">
            <form action="manage_providers.php" method="POST" onsubmit="return confirm('Delete this provider and all their courses?')">
              <?= csrf_field() ?>
              <input type="hidden" name="delete" value="<?= (int) $p['Registered_User_Id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>