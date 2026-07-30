<?php
// admin/manage_teachers.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    $connection->query("DELETE FROM registered_user WHERE Registered_User_Id=$uid AND Registered_User_Type='TCH'");
    header("Location: manage_teachers.php?deleted=1"); exit();
}

$search = trim($_GET['search'] ?? '');
$where  = "WHERE Registered_User_Type='TCH'";
if ($search) $where .= " AND (User_Name LIKE '%$search%' OR Email LIKE '%$search%')";

$teachers = $connection->query(
    "SELECT u.*, (SELECT COUNT(*) FROM enrollment e WHERE e.Registered_User_Id=u.Registered_User_Id) AS enrolCount
     FROM registered_user u $where ORDER BY u.Created_At DESC"
);
$total = $connection->query("SELECT COUNT(*) AS t FROM registered_user WHERE Registered_User_Type='TCH'")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Teachers — Admin</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/admin.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Teacher account deleted.</div><?php endif; ?>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h1 class="section-title">Manage Teachers</h1>
      <p class="section-subtitle"><?= $total ?> registered teachers</p>
    </div>
    <form action="manage_teachers.php" method="GET">
      <div style="display:flex;gap:8px">
        <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or email..." style="width:280px">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        <?php if ($search): ?><a href="manage_teachers.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
      </div>
    </form>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Courses Enrolled</th><th>Joined</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php while ($t = $teachers->fetch_assoc()): ?>
        <tr>
          <td>#<?= $t['Registered_User_Id'] ?></td>
          <td><strong><?= htmlspecialchars($t['User_Name']) ?></strong></td>
          <td><?= htmlspecialchars(trim($t['First_Name'].' '.$t['Last_Name'])) ?: '—' ?></td>
          <td><?= htmlspecialchars($t['Email']) ?></td>
          <td><?= htmlspecialchars($t['Phone_Number'] ?: '—') ?></td>
          <td><?= $t['enrolCount'] ?></td>
          <td><?= date('M j, Y', strtotime($t['Created_At'])) ?></td>
          <td>
            <a href="delete_user.php?id=<?= $t['Registered_User_Id'] ?>&type=TCH"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this teacher account and all their data?')">
              <i class="fas fa-trash"></i> Delete
            </a>
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