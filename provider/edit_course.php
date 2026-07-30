<?php
// provider/edit_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$courseId   = (int)($_GET['id'] ?? 0);
$categories = $connection->query("SELECT * FROM course_category ORDER BY Category_Name");

$course = $connection->query(
    "SELECT * FROM course WHERE Course_Id=$courseId AND Provider_Id=$providerId"
)->fetch_assoc();
if (!$course) { header("Location: manage_courses.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title      = trim($_POST['title'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0) ?: 'NULL';
    $dueDate    = $_POST['due_date'] ?: null;
    $weeks      = (int)($_POST['duration_weeks'] ?? 4);
    $imgName    = $course['Intro_Image'];

    if (!empty($_FILES['intro_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['intro_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $imgName = uniqid('course_') . '.' . $ext;
            move_uploaded_file($_FILES['intro_image']['tmp_name'], '../uploads/' . $imgName);
        }
    }

    $connection->query(
        "UPDATE course SET
            Title='" . $connection->real_escape_string($title) . "',
            Description='" . $connection->real_escape_string($desc) . "',
            Category_Id=" . ($categoryId ?: 'NULL') . ",
            Duration_Weeks=$weeks,
            Intro_Image=" . ($imgName ? "'" . $connection->real_escape_string($imgName) . "'" : 'NULL') . ",
            Due_Date=" . ($dueDate ? "'" . $connection->real_escape_string($dueDate) . "'" : 'NULL') . "
         WHERE Course_Id=$courseId AND Provider_Id=$providerId"
    );
    header("Location: manage_courses.php?updated=1"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Course — Educaster</title>
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
      <h1><i class="fas fa-edit" style="color:var(--green)"></i> Edit Course</h1>
      <form action="edit_course.php?id=<?= $courseId ?>" method="POST" enctype="multipart/form-data" style="margin-top:24px">
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
              <option value="<?= $cat['Category_Id'] ?>" <?= $cat['Category_Id'] == $course['Category_Id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['Category_Name']) ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Duration (weeks)</label>
            <input type="number" name="duration_weeks" class="form-control" value="<?= $course['Duration_Weeks'] ?>" min="1">
          </div>
        </div>
        <div class="form-group">
          <label>Due / End Date</label>
          <input type="date" name="due_date" class="form-control" value="<?= $course['Due_Date'] ?>">
        </div>
        <div class="form-group">
          <label>Cover Image <small style="color:var(--text-muted)">(leave blank to keep current)</small></label>
          <?php if ($course['Intro_Image']): ?>
            <img src="/educaster/uploads/<?= htmlspecialchars($course['Intro_Image']) ?>" style="height:80px;border-radius:8px;display:block;margin-bottom:10px">
          <?php endif; ?>
          <input type="file" name="intro_image" class="form-control" accept="image/*">
        </div>
        <div style="display:flex;gap:12px">
          <button type="submit" name="update" class="btn btn-primary" style="flex:1;justify-content:center;padding:13px">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <a href="manage_courses.php" class="btn btn-outline" style="flex:1;justify-content:center;padding:13px">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>