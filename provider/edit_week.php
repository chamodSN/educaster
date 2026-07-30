<?php
// provider/edit_week.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$weekId     = (int)($_GET['id'] ?? 0);

$week = $connection->query(
    "SELECT wc.* FROM weekly_course wc
     JOIN course c ON c.Course_Id=wc.Course_Id
     WHERE wc.Week_Id=$weekId AND c.Provider_Id=$providerId"
)->fetch_assoc();
if (!$week) { header("Location: manage_courses.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_week'])) {
    $title   = $connection->real_escape_string(trim($_POST['week_title'] ?? ''));
    $desc    = $connection->real_escape_string(trim($_POST['description'] ?? ''));
    $link    = $connection->real_escape_string(trim($_POST['course_link'] ?? ''));
    $vidName = $week['Video_File'];
    $resName = $week['Resource_File'];

    if (!empty($_FILES['video_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4','mov','avi','webm'])) {
            $vidName = uniqid('vid_') . '.' . $ext;
            move_uploaded_file($_FILES['video_file']['tmp_name'], '../uploads/' . $vidName);
        }
    }
    if (!empty($_FILES['resource_file']['name'])) {
        $resName = uniqid('res_') . '_' . basename($_FILES['resource_file']['name']);
        move_uploaded_file($_FILES['resource_file']['tmp_name'], '../uploads/' . $resName);
    }

    $connection->query(
        "UPDATE weekly_course SET
            Week_Title='$title', Description='$desc',
            Video_File=" . ($vidName ? "'$vidName'" : 'NULL') . ",
            Resource_File=" . ($resName ? "'$resName'" : 'NULL') . ",
            Course_Link=" . ($link ? "'$link'" : 'NULL') . "
         WHERE Week_Id=$weekId"
    );
    header("Location: add_week.php?course_id=" . $week['Course_Id'] . "&updated=1"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Week — Educaster</title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <div class="form-page-wrapper">
    <div class="form-page-card">
      <h1><i class="fas fa-edit" style="color:var(--green)"></i> Edit Week <?= $week['Week_Number'] ?></h1>
      <form action="edit_week.php?id=<?= $weekId ?>" method="POST" enctype="multipart/form-data" style="margin-top:24px">
        <div class="form-group">
          <label>Week Title <span class="req">*</span></label>
          <input type="text" name="week_title" class="form-control" value="<?= htmlspecialchars($week['Week_Title']) ?>" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($week['Description']) ?></textarea>
        </div>
        <div class="form-group">
          <label>Video File <small>(leave blank to keep current)</small></label>
          <?php if ($week['Video_File']): ?><p style="font-size:13px;color:var(--green);margin-bottom:6px"><i class="fas fa-video"></i> Current: <?= htmlspecialchars($week['Video_File']) ?></p><?php endif; ?>
          <input type="file" name="video_file" class="form-control" accept="video/*">
        </div>
        <div class="form-group">
          <label>Resource File <small>(leave blank to keep current)</small></label>
          <?php if ($week['Resource_File']): ?><p style="font-size:13px;color:var(--green);margin-bottom:6px"><i class="fas fa-file"></i> Current: <?= htmlspecialchars($week['Resource_File']) ?></p><?php endif; ?>
          <input type="file" name="resource_file" class="form-control">
        </div>
        <div class="form-group">
          <label>External Link</label>
          <input type="url" name="course_link" class="form-control" value="<?= htmlspecialchars($week['Course_Link'] ?? '') ?>" placeholder="https://...">
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" name="update_week" class="btn btn-primary" style="flex:1;justify-content:center;padding:13px">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <a href="add_week.php?course_id=<?= $week['Course_Id'] ?>" class="btn btn-outline" style="flex:1;justify-content:center;padding:13px">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>