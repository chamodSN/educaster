<?php
session_start();
require '../common/config.php';

if (!isset($_SESSION['userData']) || $_SESSION['userData']['User_Name'] !== 'admin') {
    header("Location: /educaster/user/login.php");
    exit();
}

$courseId = intval($_GET['course_id']);
$course = $connection->query("SELECT * FROM Course WHERE Course_Id = $courseId")->fetch_assoc();
if (!$course) die("Course not found.");

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    
    // Insert quiz
    $sql = "INSERT INTO Quiz (Course_Id, Title) VALUES ($courseId, '$title')";
    if ($connection->query($sql)) {
        $quizId = $connection->insert_id;

        // Insert questions
        foreach ($_POST['questions'] as $index => $q) {
            $question = $connection->real_escape_string($q['question']);
            $option1 = $connection->real_escape_string($q['option1']);
            $option2 = $connection->real_escape_string($q['option2']);
            $option3 = $connection->real_escape_string($q['option3']);
            $option4 = $connection->real_escape_string($q['option4']);
            $correct = intval($q['correct']);

        $connection->query("INSERT INTO Question (Quiz_Id, Question_Text, Option_A, Option_B, Option_C, Option_D, Correct_Option) 
            VALUES ($quizId, '$question', '$option1', '$option2', '$option3', '$option4', $correct)");
        }

        header("Location: manage_quizzes.php?course_id=$courseId");
        exit();
    } else {
        $error = "DB error: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Quiz to <?= htmlspecialchars($course['Title']) ?></title>
    <link rel="stylesheet" href="../css/admin.css">
    <script>
    function addQuestion() {
        const container = document.getElementById('questions-container');
        const count = container.children.length;
        const html = `
        <div class="question-block">
            <label>Question ${count + 1}:</label><br>
            <textarea name="questions[${count}][question]" required></textarea><br>
            <label>Option 1:</label><input type="text" name="questions[${count}][option1]" required><br>
            <label>Option 2:</label><input type="text" name="questions[${count}][option2]" required><br>
            <label>Option 3:</label><input type="text" name="questions[${count}][option3]" required><br>
            <label>Option 4:</label><input type="text" name="questions[${count}][option4]" required><br>
            <label>Correct Option (1-4):</label>
            <input type="number" min="1" max="4" name="questions[${count}][correct]" required><br><br>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
    </script>
</head>
<body>
<?php include '../common/adminHeader.php'; ?>
<h1>Add Quiz to Course: <?= htmlspecialchars($course['Title']) ?></h1>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
    <label>Quiz Title:</label><br>
    <input type="text" name="title" required><br><br>

    <div id="questions-container">
        <!-- Questions will be added here -->
    </div>
    <button type="button" onclick="addQuestion()">Add Question</button><br><br>

    <button type="submit">Add Quiz</button>
</form>

<?php include '../common/footer.php'; ?>
</body>
</html>
