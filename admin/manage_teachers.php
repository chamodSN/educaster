<?php
// admin/manage_teachers.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireAdmin();

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    // FIX: original code interpolated $search directly into the SQL
    // string ("... LIKE '%$search%' ..."), which was a SQL injection
    // vulnerability. This now uses a prepared statement.
    $like = '%' . $search . '%';
    $stmt = $connection->prepare(
        "SELECT u.*, (SELECT COUNT(*) FROM enrollment e WHERE e.Registered_User_Id=u.Registered_User_Id) AS enrolCount
         FROM registered_user u
         WHERE Registered_User_Type='TCH' AND (User_Name LIKE ? OR Email LIKE ?)
         ORDER BY u.Created_At DESC"
    );
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $teachers = $stmt->get_result();
} else {
    $teachers = $connection->query(
        "SELECT u.*, (SELECT COUNT(*) FROM enrollment e WHERE e.Registered_User_Id=u.Registered_User_Id) AS enrolCount
         FROM registered_user u WHERE Registered_User_Type='TCH' ORDER BY u.Created_At DESC"
    );
}
$total = $connection->query("SELECT COUNT(*) AS t FROM registered_user WHERE Registered_User_Type='TCH'")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Teachers — Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Teacher account deleted.</div><?php endif; ?>
  <div class="list-toolbar">
    <div>
      <h1 class="section-title">Manage Teachers</h1>
      <p class="section-subtitle" style="margin-bottom:0"><?= (int) $total ?> registered teachers</p>
    </div>
    <form action="manage_teachers.php" method="GET" class="search-form">
      <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or email...">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
      <?php if ($search): ?><a href="manage_teachers.php" class="btn btn-outline btn-sm">Clear</a><?php endif; ?>
    </form>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Courses Enrolled</th><th>Joined</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php if ($teachers->num_rows === 0): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:28px">No teachers found.</td></tr>
        <?php endif; ?>
        <?php while ($t = $teachers->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int) $t['Registered_User_Id'] ?></td>
          <td><strong><?= htmlspecialchars($t['User_Name']) ?></strong></td>
          <td><?= htmlspecialchars(trim($t['First_Name'] . ' ' . $t['Last_Name'])) ?: '—' ?></td>
          <td><?= htmlspecialchars($t['Email']) ?></td>
          <td><?= htmlspecialchars($t['Phone_Number'] ?: '—') ?></td>
          <td><?= (int) $t['enrolCount'] ?></td>
          <td><?= format_date($t['Created_At']) ?></td>
          <td>
            <form action="delete_user.php" method="POST" onsubmit="return confirm('Delete this teacher account and all their data?')">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int) $t['Registered_User_Id'] ?>">
              <input type="hidden" name="type" value="TCH">
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