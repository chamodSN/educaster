<?php
// provider/create_course.php
require_once '../common/config.php';
require_once '../common/loginFunctions.php';
requireProvider();

$providerId = (int)$_SESSION['userData']['Registered_User_Id'];
$categories = $connection->query("SELECT * FROM course_category ORDER BY Category_Name");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title      = trim($_POST['title'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $dueDate    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $weeks      = max(1, (int)($_POST['duration_weeks'] ?? 4));
    $imgName    = null;

    if (empty($title)) {
        header("Location: create_course.php?error=emptytitle");
        exit();
    }

    // Handle image upload
    if (!empty($_FILES['intro_image']['name']) && $_FILES['intro_image']['error'] === 0) {
        $ext     = strtolower(pathinfo($_FILES['intro_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $imgName = uniqid('course_') . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            move_uploaded_file($_FILES['intro_image']['tmp_name'], $uploadDir . $imgName);
        }
    }

    // Single clean prepared statement — 7 values, 7 bind vars
    $stmt = $connection->prepare(
        "INSERT INTO course (Title, Description, Category_Id, Provider_Id, Intro_Image, Duration_Weeks, Due_Date)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    // s=Title, s=Description, i=Category_Id, i=Provider_Id, s=Intro_Image, i=Duration_Weeks, s=Due_Date
    $stmt->bind_param(
        "ssiisis",
        $title,
        $desc,
        $categoryId,
        $providerId,
        $imgName,
        $weeks,
        $dueDate
    );

    if ($stmt->execute()) {
        $newId = $connection->insert_id;
        header("Location: add_week.php?course_id=$newId&created=1");
    } else {
        header("Location: create_course.php?error=dbfail");
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
  <link rel="stylesheet" href="/educaster/css/global.css">
  <link rel="stylesheet" href="/educaster/css/header.css">
  <link rel="stylesheet" href="/educaster/css/footer.css">
  <link rel="stylesheet" href="/educaster/css/provider.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include '../common/providerHeader.php'; ?>
<div class="page-wrap">
  <div class="form-page">
    <h1><i class="fas fa-plus-circle"></i> Create New Course</h1>
    <p class="form-sub">Fill in the details below. You can add weekly content after creating the course.</p>

    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-error">
        <?= $_GET['error'] === 'emptytitle' ? 'Course title is required.' : 'Something went wrong. Please try again.' ?>
      </div>
    <?php endif; ?>

    <form action="create_course.php" method="POST" enctype="multipart/form-data" class="create-form">
      <div class="field">
        <label for="title">Course Title <span class="req">*</span></label>
        <input type="text" id="title" name="title" placeholder="e.g. Modern Classroom Management" required>
      </div>

      <div class="field">
        <label for="desc">Description</label>
        <textarea id="desc" name="description" rows="5" placeholder="What will students learn? What topics are covered?"></textarea>
      </div>

      <div class="field-row">
        <div class="field">
          <label for="cat">Category</label>
          <select id="cat" name="category_id">
            <option value="0">— Select category —</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['Category_Id'] ?>"><?= htmlspecialchars($cat['Category_Name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="field">
          <label for="weeks">Duration (weeks)</label>
          <input type="number" id="weeks" name="duration_weeks" value="4" min="1" max="52">
        </div>
      </div>

      <div class="field">
        <label for="due">End / Due Date</label>
        <input type="date" id="due" name="due_date" min="<?= date('Y-m-d') ?>">
      </div>

      <div class="field">
        <label for="img">Cover Image <span class="hint">(jpg, png, webp — optional)</span></label>
        <input type="file" id="img" name="intro_image" accept="image/*">
      </div>

      <button type="submit" name="create" class="btn-green">
        <i class="fas fa-arrow-right"></i> Create Course &amp; Add Content
      </button>
    </form>
  </div>
</div>
<?php include '../common/footer.php'; ?>
</body>
</html>