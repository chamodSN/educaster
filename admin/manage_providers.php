<?php
// admin/manage_providers.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

// Handle approval / rejection
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $uid    = (int)$_GET['user_id'];
    if ($action === 'approve') {
        $connection->query("UPDATE registered_user SET Is_Approved=1 WHERE Registered_User_Id=$uid");
        $connection->query("UPDATE provider_request SET Status='approved' WHERE User_Id=$uid");
    } elseif ($action === 'reject') {
        $connection->query("UPDATE registered_user SET Is_Approved=0 WHERE Registered_User_Id=$uid");
        $connection->query("UPDATE provider_request SET Status='rejected' WHERE User_Id=$uid");
    }
    header("Location: manage_providers.php"); exit();
}

if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    $connection->query("DELETE FROM registered_user WHERE Registered_User_Id=$uid AND Registered_User_Type='INS'");
    header("Location: manage_providers.php"); exit();
}

$pending   = $connection->query("SELECT u.* FROM registered_user u JOIN provider_request pr ON pr.User_Id=u.Registered_User_Id WHERE pr.Status='pending' ORDER BY u.Created_At DESC");
$providers = $connection->query("SELECT u.*, (SELECT COUNT(*) FROM course c WHERE c.Provider_Id=u.Registered_User_Id) AS course_count FROM registered_user u WHERE u.Registered_User_Type='INS' AND u.Is_Approved=1 ORDER BY u.Created_At DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Providers — Admin</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <h1 class="section-title">Course Provider Management</h1>

  <?php if ($pending->num_rows > 0): ?>
  <div class="alert alert-info" style="margin-bottom:24px">
    <i class="fas fa-bell"></i> <strong><?= $pending->num_rows ?> pending approval<?= $pending->num_rows>1?'s':'' ?></strong> require your attention.
  </div>
  <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;color:#856404"><i class="fas fa-clock"></i> Pending Applications</h2>
  <div class="table-wrapper" style="margin-bottom:40px">
    <table>
      <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Applied</th><th>Action</th></tr></thead>
      <tbody>
        <?php while ($p = $pending->fetch_assoc()): ?>
        <tr>
          <td>#<?= $p['Registered_User_Id'] ?></td>
          <td><?= htmlspecialchars($p['User_Name']) ?></td>
          <td><?= htmlspecialchars($p['Email']) ?></td>
          <td><?= htmlspecialchars($p['Phone_Number'] ?: '—') ?></td>
          <td><?= date('M j, Y', strtotime($p['Created_At'])) ?></td>
          <td class="action-btns">
            <a href="?action=approve&user_id=<?= $p['Registered_User_Id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-check"></i> Approve</a>
            <a href="?action=reject&user_id=<?= $p['Registered_User_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this application?')"><i class="fas fa-times"></i> Reject</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <h2 style="font-size:20px;font-weight:700;margin-bottom:14px"><i class="fas fa-check-circle" style="color:var(--green)"></i> Approved Providers</h2>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Courses</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>
        <?php while ($p = $providers->fetch_assoc()): ?>
        <tr>
          <td>#<?= $p['Registered_User_Id'] ?></td>
          <td><?= htmlspecialchars($p['User_Name']) ?></td>
          <td><?= htmlspecialchars($p['Email']) ?></td>
          <td><?= $p['course_count'] ?></td>
          <td><?= date('M j, Y', strtotime($p['Created_At'])) ?></td>
          <td class="action-btns">
            <a href="?delete=<?= $p['Registered_User_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this provider and all their courses?')"><i class="fas fa-trash"></i> Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>