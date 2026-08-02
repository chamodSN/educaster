<?php
// provider/add_week.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$courseId   = (int) ($_GET['course_id'] ?? 0);

$stmt = $connection->prepare('SELECT * FROM course WHERE Course_Id=? AND Provider_Id=?');
$stmt->bind_param('ii', $courseId, $providerId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { header('Location: manage_courses.php'); exit(); }

$existingWeeks = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$nextWeekNum   = $existingWeeks->num_rows + 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_week'])) {
    verify_csrf();

    $weekNum = (int) ($_POST['week_number'] ?? $nextWeekNum);
    $title   = trim($_POST['week_title'] ?? '');
    $desc    = trim($_POST['description'] ?? '');
    $link    = sanitize_url($_POST['course_link'] ?? '');

    if (empty($title)) {
        header("Location: add_week.php?course_id=$courseId&error=emptytitle");
        exit();
    }

    // FIX: previously ANY file extension was accepted for resources
    // (a malicious .php upload here would have been executable, since
    // /uploads is under the public web root). Now validated + the
    // uploads folder also ships with a .htaccess that blocks script
    // execution as a second layer of defence.
    $vidName = handle_upload('video_file', 'allowed_video_extension', 'vid');
    $resName = handle_upload('resource_file', 'allowed_resource_extension', 'res');

    $stmt = $connection->prepare(
        'INSERT INTO weekly_course (Course_Id, Week_Number, Week_Title, Description, Video_File, Resource_File, Course_Link)
         VALUES (?,?,?,?,?,?,?)'
    );
    $stmt->bind_param('iisssss', $courseId, $weekNum, $title, $desc, $vidName, $resName, $link);

    if (!$stmt->execute()) {
        header("Location: add_week.php?course_id=$courseId&error=duplicate");
        exit();
    }

    header("Location: add_week.php?course_id=$courseId&added=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Week — <?= htmlspecialchars($course['Title']) ?></title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/global.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/header.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/footer.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['added'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Week added!</div><?php endif; ?>
  <?php if (isset($_GET['created'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Course created! Now add your weekly content.</div><?php endif; ?>
  <?php if (isset($_GET['updated'])): ?><div class="alert alert-success"><i class="fas fa-circle-check"></i> Week updated!</div><?php endif; ?>
  <?php if (isset($_GET['error']) && $_GET['error'] === 'emptytitle'): ?><div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> Week title is required.</div><?php endif; ?>
  <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?><div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> That week number already exists for this course.</div><?php endif; ?>

  <div class="weeks-layout">
    <div class="week-panel">
      <h3><i class="fas fa-list"></i> <?= htmlspecialchars($course['Title']) ?></h3>
      <?php $existingWeeks->data_seek(0); if ($existingWeeks->num_rows > 0): ?>
      <div style="margin-top:14px">
        <?php while ($w = $existingWeeks->fetch_assoc()): ?>
        <div class="week-list-item">
          <div class="wk-num">W<?= (int) $w['Week_Number'] ?></div>
          <div style="flex:1">
            <div class="wk-title"><?= htmlspecialchars($w['Week_Title']) ?></div>
            <a href="edit_week.php?id=<?= (int) $w['Week_Id'] ?>" class="btn btn-sm btn-outline" style="margin-top:6px"><i class="fas fa-edit"></i> Edit</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php else: ?>
        <p style="color:var(--text-muted);margin-top:12px;font-size:13.5px">No weeks added yet. Use the form to add your first lesson.</p>
      <?php endif; ?>
      <div style="margin-top:20px">
        <a href="manage_courses.php" class="btn btn-outline btn-sm btn-block"><i class="fas fa-arrow-left"></i> Back to My Courses</a>
      </div>
    </div>

    <div class="form-page" style="margin:0">
      <h3><i class="fas fa-plus-circle"></i> Add Week <?= $nextWeekNum ?></h3>
      <form action="add_week.php?course_id=<?= $courseId ?>" method="POST" enctype="multipart/form-data" style="margin-top:20px">
        <?= csrf_field() ?>
        <input type="hidden" name="week_number" value="<?= $nextWeekNum ?>">
        <div class="form-group">
          <label>Week Title <span class="req">*</span></label>
          <input type="text" name="week_title" class="form-control" required placeholder="e.g. Introduction to Pedagogy">
        </div>
        <div class="form-group">
          <label>Lesson Description / Content</label>
          <textarea name="description" class="form-control" rows="5" placeholder="Detailed lesson content..."></textarea>
        </div>
        <div class="form-group">
          <label>Video File <small>(mp4, mov, avi, webm)</small></label>
          <input type="file" name="video_file" class="form-control" accept="video/*">
        </div>
        <div class="form-group">
          <label>Resource File <small>(pdf, doc, ppt, xls, zip, image)</small></label>
          <input type="file" name="resource_file" class="form-control">
        </div>
        <div class="form-group">
          <label>External Link <small>(YouTube, article, etc.)</small></label>
          <input type="url" name="course_link" class="form-control" placeholder="https://...">
        </div>
        <button type="submit" name="add_week" class="btn btn-primary btn-block">
          <i class="fas fa-plus"></i> Add Week <?= $nextWeekNum ?>
        </button>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>