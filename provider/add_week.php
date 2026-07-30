<?php
// provider/add_week.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId   = (int)($_GET['course_id'] ?? 0);

$course = $connection->query("SELECT * FROM course WHERE Course_Id=$courseId AND Provider_Id=$providerId")->fetch_assoc();
if (!$course) { header("Location: manage_courses.php"); exit(); }

$existingWeeks = $connection->query("SELECT * FROM weekly_course WHERE Course_Id=$courseId ORDER BY Week_Number ASC");
$nextWeekNum   = $existingWeeks->num_rows + 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_week'])) {
    $weekNum  = (int)($_POST['week_number'] ?? $nextWeekNum);
    $title    = trim($_POST['week_title'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $link     = trim($_POST['course_link'] ?? '');
    $vidName  = null;
    $resName  = null;

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

    $esc_title = $connection->real_escape_string($title);
    $esc_desc  = $connection->real_escape_string($desc);
    $esc_link  = $connection->real_escape_string($link);
    $connection->query(
        "INSERT INTO weekly_course (Course_Id, Week_Number, Week_Title, Description, Video_File, Resource_File, Course_Link)
         VALUES ($courseId, $weekNum, '$esc_title', '$esc_desc',
                 " . ($vidName ? "'$vidName'" : 'NULL') . ",
                 " . ($resName ? "'$resName'" : 'NULL') . ",
                 " . ($link ? "'$esc_link'" : 'NULL') . ")"
    );
    header("Location: add_week.php?course_id=$courseId&added=1"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Week — <?= htmlspecialchars($course['Title']) ?></title>
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrapper">
  <?php if (isset($_GET['added'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Week added!</div><?php endif; ?>
  <?php if (isset($_GET['created'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Course created! Now add your weekly content.</div><?php endif; ?>

  <div class="weeks-manager">
    <!-- Existing weeks -->
    <div class="existing-weeks">
      <h3><i class="fas fa-list" style="color:var(--green)"></i> <?= htmlspecialchars($course['Title']) ?></h3>
      <?php $existingWeeks->data_seek(0); if ($existingWeeks->num_rows > 0): ?>
      <div class="weeks-list" style="margin-top:16px">
        <?php while ($w = $existingWeeks->fetch_assoc()): ?>
        <div class="week-item">
          <div class="week-num">W<?= $w['Week_Number'] ?></div>
          <div class="week-info">
            <strong><?= htmlspecialchars($w['Week_Title']) ?></strong>
            <div style="display:flex;gap:8px;margin-top:6px">
              <a href="edit_week.php?id=<?= $w['Week_Id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i> Edit</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php else: ?>
        <p style="color:var(--text-muted);margin-top:12px">No weeks added yet. Use the form to add content.</p>
      <?php endif; ?>
      <div style="margin-top:20px">
        <a href="manage_courses.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to My Courses</a>
      </div>
    </div>

    <!-- Add week form -->
    <div class="form-page-card" style="flex:1">
      <h3><i class="fas fa-plus-circle" style="color:var(--green)"></i> Add Week <?= $nextWeekNum ?></h3>
      <form action="add_week.php?course_id=<?= $courseId ?>" method="POST" enctype="multipart/form-data" style="margin-top:20px">
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
          <label>Video File <small>(mp4, mov, webm)</small></label>
          <input type="file" name="video_file" class="form-control" accept="video/*">
        </div>
        <div class="form-group">
          <label>Resource File <small>(pdf, doc, ppt, etc.)</small></label>
          <input type="file" name="resource_file" class="form-control">
        </div>
        <div class="form-group">
          <label>External Link <small>(YouTube, article, etc.)</small></label>
          <input type="url" name="course_link" class="form-control" placeholder="https://...">
        </div>
        <button type="submit" name="add_week" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px">
          <i class="fas fa-plus"></i> Add Week <?= $nextWeekNum ?>
        </button>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>