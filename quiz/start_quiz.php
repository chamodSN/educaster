<?php
session_start();
require 'config.php';

$userId = $_SESSION['userData']['Registered_User_Id'] ?? null;
$quizId = intval($_GET['id'] ?? 0);

if (!$userId) {
    header("Location: login.php");
    exit();
}

// Fetch quiz
$quiz = $connection->query("SELECT * FROM Quiz WHERE Quiz_Id = $quizId")->fetch_assoc();
if (!$quiz) {
    die("Quiz not found.");
}

// Check if user enrolled in course
$courseId = $quiz['Course_Id'];
$res = $connection->query("SELECT * FROM Enrollment WHERE Registered_User_Id = $userId AND Course_Id = $courseId");
if ($res->num_rows === 0) {
    die("You are not enrolled in the course for this quiz.");
}

// Fetch questions
$questions = $connection->query("SELECT * FROM Quiz_Question WHERE Quiz_Id = $quizId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Quiz: <?= htmlspecialchars($quiz['Title']) ?></title>
    <link rel="stylesheet" href="css/quiz.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Quiz: <?= htmlspecialchars($quiz['Title']) ?></h1>

<form method="POST" action="quiz_submit.php">
    <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
    <?php
    $qNum = 1;
    while ($q = $questions->fetch_assoc()): ?>
        <div class="question-block">
            <p><strong>Q<?= $qNum ?>. <?= htmlspecialchars($q['Question_Text']) ?></strong></p>
            <?php for ($i = 1; $i <= 4; $i++): 
                $opt = $q["Option$i"];
                $inputName = "answers[" . $q['Question_Id'] . "]";
            ?>
                <label>
                    <input type="radio" name="<?= $inputName ?>" value="<?= $i ?>" required>
                    <?= htmlspecialchars($opt) ?>
                </label><br>
            <?php endfor; ?>
        </div>
    <?php
    $qNum++;
    endwhile;
    ?>
    <button type="submit">Submit Quiz</button>
</form>

<?php include 'footer.php'; ?>
</body>
</html>
