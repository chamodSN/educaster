<?php
session_start();
require 'config.php';

if (!isset($_SESSION['userData'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['userData']['Registered_User_Id'];
$quizId = intval($_GET['id'] ?? 0);
if (!$quizId) {
    die('Invalid quiz ID');
}

// Get quiz info
$quiz = $connection->query("SELECT * FROM Quiz WHERE Quiz_Id = $quizId")->fetch_assoc();
if (!$quiz) {
    die('Quiz not found');
}

// Get questions and options
$questionsRes = $connection->query("SELECT * FROM Quiz_Question WHERE Quiz_Id = $quizId");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process submitted answers
    $answers = $_POST['answers'] ?? [];
    $score = 0;

    // Calculate score
    foreach ($answers as $questionId => $optionId) {
        $optionId = intval($optionId);
        $questionId = intval($questionId);

        $optRes = $connection->query("SELECT Is_Correct FROM Quiz_Option WHERE Option_Id = $optionId AND Question_Id = $questionId");
        if ($optRes && $optRes->num_rows) {
            $optRow = $optRes->fetch_assoc();
            if ($optRow['Is_Correct']) {
                $score++;
            }
        }
    }

    // Insert attempt record
    $stmt = $connection->prepare("INSERT INTO Quiz_Attempt (Registered_User_Id, Quiz_Id, Attempt_Date, Score) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("iii", $userId, $quizId, $score);
    $stmt->execute();
    $attemptId = $stmt->insert_id;
    $stmt->close();

    // Insert answers
    $stmtAns = $connection->prepare("INSERT INTO Quiz_Attempt_Answer (Attempt_Id, Question_Id, Option_Id) VALUES (?, ?, ?)");
    foreach ($answers as $questionId => $optionId) {
        $questionId = intval($questionId);
        $optionId = intval($optionId);
        $stmtAns->bind_param("iii", $attemptId, $questionId, $optionId);
        $stmtAns->execute();
    }
    $stmtAns->close();

    echo "<h2>Your score: $score / " . count($answers) . "</h2>";
    echo '<p><a href="dashboard.php">Back to Dashboard</a></p>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Quiz: <?= htmlspecialchars($quiz['Title']) ?></title>
    <link rel="stylesheet" href="css/start_quiz.css">
</head>
<body>
<?php include 'header.php'; ?>

<h1>Quiz: <?= htmlspecialchars($quiz['Title']) ?></h1>
<form method="POST">
    <?php
    $qNum = 1;
    while ($question = $questionsRes->fetch_assoc()) {
        echo '<fieldset><legend>Question ' . $qNum++ . '</legend>';
        echo '<p>' . htmlspecialchars($question['Question_Text']) . '</p>';

        $optionsRes = $connection->query("SELECT * FROM Quiz_Option WHERE Question_Id = " . intval($question['Question_Id']));
        while ($opt = $optionsRes->fetch_assoc()) {
            $optId = $opt['Option_Id'];
            $qId = $question['Question_Id'];
            echo '<label><input type="radio" name="answers[' . $qId . ']" value="' . $optId . '" required> ' . htmlspecialchars($opt['Option_Text']) . '</label><br>';
        }
        echo '</fieldset><br>';
    }
    ?>
    <button type="submit">Submit Quiz</button>
</form>

<?php include 'footer.php'; ?>
</body>
</html>
