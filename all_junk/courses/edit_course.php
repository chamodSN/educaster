<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}


$courseId = intval($_GET['id']);
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();

if (!$course) {
    die("Course not found.");
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $description = $connection->real_escape_string($_POST['description']);

    // Handle intro image update
    $introImage = $course['Intro_Image'];
    if (!empty($_FILES['intro_image']['name'])) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES['intro_image']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['intro_image']['tmp_name'], $targetFile)) {
                // Optionally delete old image file
                if ($introImage && file_exists($introImage)) unlink($introImage);
                $introImage = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files allowed.";
        }
    }

    if (empty($error)) {
        $sql = "UPDATE Course SET Title = '$title', Description = '$description', Intro_Image = '$introImage' WHERE Course_Id = $courseId";
        if ($connection->query($sql)) {
            header("Location: manage_courses.php");
            exit();
        } else {
            $error = "DB error: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Course</title>
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Edit Course</h1>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Course Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($course['Title']) ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required><?= htmlspecialchars($course['Description']) ?></textarea><br><br>

    <label>Current Intro Image:</label><br>
    <?php if ($course['Intro_Image']): ?>
        <img src="<?= $course['Intro_Image'] ?>" width="200"><br>
    <?php else: ?>
        <p>No image uploaded</p>
    <?php endif; ?>
    <label>Change Intro Image:</label><br>
    <input type="file" name="intro_image" accept="image/*"><br><br>

    <button type="submit">Update Course</button>
</form>

<?php include '../common/footer.php'; ?>
</body>
</html>
