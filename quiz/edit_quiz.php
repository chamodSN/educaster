<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$quizId = intval($_GET['id']);
$quiz = $connection->query("SELECT * FROM Quiz WHERE Quiz_Id = $quizId")->fetch_assoc();

if (!$quiz) die("Quiz not found.");

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    if ($connection->query("UPDATE Quiz SET Title='$title' WHERE Quiz_Id=$quizId")) {
        header("Location: manage_quizzes.php?course_id=" . $quiz['Course_Id']);
        exit();
    } else {
        $error = "DB error: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Edit Quiz</h1>

<?php if ($error) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <label>Quiz Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($quiz['Title']) ?>" required><br><br>

    <button type="submit">Update Quiz</button>
</form>

<?php include '../common/footer.php'; ?>
</body>
</html>
