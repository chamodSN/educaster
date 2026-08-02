<?php
// provider/create_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = currentUserId();
$categories = $connection->query('SELECT * FROM course_category ORDER BY Category_Name');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    verify_csrf();

    $title      = trim($_POST['title'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $dueDate    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $weeks      = max(1, (int) ($_POST['duration_weeks'] ?? 4));

    if (empty($title)) {
        header('Location: create_course.php?error=emptytitle');
        exit();
    }

    $imgName = handle_upload('intro_image', 'allowed_image_extension', 'course');

    $stmt = $connection->prepare(
        'INSERT INTO course (Title, Description, Category_Id, Provider_Id, Intro_Image, Duration_Weeks, Due_Date)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('ssiisis', $title, $desc, $categoryId, $providerId, $imgName, $weeks, $dueDate);

    if ($stmt->execute()) {
        $newId = $connection->insert_id;
        header("Location: add_week.php?course_id=$newId&created=1");
    } else {
        header('Location: create_course.php?error=dbfail');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Course — Educaster</title>
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
    <h1><i class="fas fa-plus-circle"></i> Create New Course</h1>
    <p class="form-sub">Fill in the details below. You can add weekly content right after creating the course.</p>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-error">
        <i class="fas fa-triangle-exclamation"></i>
        <?= $_GET['error'] === 'emptytitle' ? 'Course title is required.' : 'Something went wrong. Please try again.' ?>
      </div>
    <?php endif; ?>

    <form action="create_course.php" method="POST" enctype="multipart/form-data" class="create-form">
      <?= csrf_field() ?>
      <div class="form-group">
        <label for="title">Course Title <span class="req">*</span></label>
        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Modern Classroom Management" required>
      </div>

      <div class="form-group">
        <label for="desc">Description</label>
        <textarea id="desc" name="description" class="form-control" rows="5" placeholder="What will students learn? What topics are covered?"></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="cat">Category</label>
          <select id="cat" name="category_id" class="form-control">
            <option value="0">— Select category —</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= (int) $cat['Category_Id'] ?>"><?= htmlspecialchars($cat['Category_Name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="weeks">Duration (weeks)</label>
          <input type="number" id="weeks" name="duration_weeks" class="form-control" value="4" min="1" max="52">
        </div>
      </div>

      <div class="form-group">
        <label for="due">End / Due Date</label>
        <input type="date" id="due" name="due_date" class="form-control" min="<?= date('Y-m-d') ?>">
      </div>

      <div class="form-group">
        <label for="img">Cover Image <span class="hint">(jpg, png, webp — optional)</span></label>
        <input type="file" id="img" name="intro_image" class="form-control" accept="image/*">
      </div>

      <button type="submit" name="create" class="btn btn-primary btn-lg">
        <i class="fas fa-arrow-right"></i> Create Course &amp; Add Content
      </button>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>