<?php
// provider/edit_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$courseId   = (int) ($_GET['id'] ?? 0);
$categories = $connection->query('SELECT * FROM course_category ORDER BY Category_Name');

$stmt = $connection->prepare('SELECT * FROM course WHERE Course_Id=? AND Provider_Id=?');
$stmt->bind_param('ii', $courseId, $providerId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { header('Location: manage_courses.php'); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    verify_csrf();

    $title      = trim($_POST['title'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $dueDate    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $weeks      = max(1, (int) ($_POST['duration_weeks'] ?? 4));
    $imgName    = $course['Intro_Image'];

    $newImg = handle_upload('intro_image', 'allowed_image_extension', 'course');
    if ($newImg) {
        delete_upload($course['Intro_Image']);
        $imgName = $newImg;
    }

    $stmt = $connection->prepare(
        'UPDATE course SET Title=?, Description=?, Category_Id=?, Duration_Weeks=?, Intro_Image=?, Due_Date=?
         WHERE Course_Id=? AND Provider_Id=?'
    );
    $stmt->bind_param('ssiissii', $title, $desc, $categoryId, $weeks, $imgName, $dueDate, $courseId, $providerId);
    $stmt->execute();

    header('Location: manage_courses.php?updated=1');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Course — Educaster</title>
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
    <h1><i class="fas fa-edit"></i> Edit Course</h1>
    <form action="edit_course.php?id=<?= $courseId ?>" method="POST" enctype="multipart/form-data" class="create-form">
      <?= csrf_field() ?>
      <div class="form-group">
        <label>Course Title <span class="req">*</span></label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($course['Title']) ?>" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($course['Description']) ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Category</label>
          <select name="category_id" class="form-control">
            <option value="">Select category</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= (int) $cat['Category_Id'] ?>" <?= $cat['Category_Id'] == $course['Category_Id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['Category_Name']) ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Duration (weeks)</label>
          <input type="number" name="duration_weeks" class="form-control" value="<?= (int) $course['Duration_Weeks'] ?>" min="1">
        </div>
      </div>
      <div class="form-group">
        <label>Due / End Date</label>
        <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($course['Due_Date'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Cover Image <small style="color:var(--text-muted)">(leave blank to keep current)</small></label>
        <?php if ($course['Intro_Image']): ?>
          <img src="<?= BASE_PATH ?>/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" style="height:90px;border-radius:var(--radius-sm);display:block;margin-bottom:10px;object-fit:cover">
        <?php endif; ?>
        <input type="file" name="intro_image" class="form-control" accept="image/*">
      </div>
      <div style="display:flex;gap:12px">
        <button type="submit" name="update" class="btn btn-primary" style="flex:1">
          <i class="fas fa-save"></i> Save Changes
        </button>
        <a href="manage_courses.php" class="btn btn-outline" style="flex:1;justify-content:center">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>