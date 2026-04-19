<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle intro image upload
    $introImage = null;
    if (!empty($_FILES['intro_image']['name'])) {
        $targetDir = "../uploads/";
        $fileName = basename($_FILES['intro_image']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['intro_image']['tmp_name'], $targetFile)) {
                $introImage = $targetFile;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files allowed.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO Course (Title, Description, Intro_Image) VALUES ('$title', '$description', '$introImage')";
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
    <title>Add Course</title>
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Add New Course</h1>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Course Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required></textarea><br><br>

    <label>Intro Image:</label><br>
    <input type="file" name="intro_image" accept="image/*"><br><br>

    <button type="submit">Add Course</button>
</form>

<?php include '../common/footer.php'; ?>
</body>
</html>
