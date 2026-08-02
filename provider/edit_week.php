<?php
// provider/edit_week.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$weekId     = (int) ($_GET['id'] ?? 0);

$stmt = $connection->prepare(
    'SELECT wc.* FROM weekly_course wc
     JOIN course c ON c.Course_Id=wc.Course_Id
     WHERE wc.Week_Id=? AND c.Provider_Id=?'
);
$stmt->bind_param('ii', $weekId, $providerId);
$stmt->execute();
$week = $stmt->get_result()->fetch_assoc();
if (!$week) { header('Location: manage_courses.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_week'])) {
    verify_csrf();

    $title = trim($_POST['week_title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $link  = sanitize_url($_POST['course_link'] ?? '');

    $vidName = $week['Video_File'];
    $resName = $week['Resource_File'];

    $newVid = handle_upload('video_file', 'allowed_video_extension', 'vid');
    if ($newVid) {
        delete_upload($week['Video_File']);
        $vidName = $newVid;
    }
    $newRes = handle_upload('resource_file', 'allowed_resource_extension', 'res');
    if ($newRes) {
        delete_upload($week['Resource_File']);
        $resName = $newRes;
    }

    $stmt = $connection->prepare(
        'UPDATE weekly_course SET Week_Title=?, Description=?, Video_File=?, Resource_File=?, Course_Link=? WHERE Week_Id=?'
    );
    $stmt->bind_param('sssssi', $title, $desc, $vidName, $resName, $link, $weekId);
    $stmt->execute();

    header('Location: add_week.php?course_id=' . $week['Course_Id'] . '&updated=1');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Week — Educaster</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <div class="form-page">
    <h1><i class="fas fa-edit"></i> Edit Week <?= (int) $week['Week_Number'] ?></h1>
    <form action="edit_week.php?id=<?= $weekId ?>" method="POST" enctype="multipart/form-data" style="margin-top:24px">
      <?= csrf_field() ?>
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
        <button type="submit" name="update_week" class="btn btn-primary" style="flex:1">
          <i class="fas fa-save"></i> Save Changes
        </button>
        <a href="add_week.php?course_id=<?= (int) $week['Course_Id'] ?>" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>