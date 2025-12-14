<?php
session_start();
require 'config.php';

if (!isset($_SESSION['userData'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['userData']['Registered_User_Id'];
$quizId = intval($_POST['quiz_id'] ?? 0);
$answers = $_POST['answers'] ?? [];

if (!$quizId || !is_array($answers)) {
    die("Invalid submission.");
}

// Fetch questions for the quiz
$questionsRes = $connection->query("SELECT * FROM Quiz_Question WHERE Quiz_Id = $quizId");
$totalQuestions = $questionsRes->num_rows;
$correctCount = 0;

while ($question = $questionsRes->fetch_assoc()) {
    $qid = $question['Question_Id'];
    $correctOption = $question['Correct_Option'];
    $userAnswer = intval($answers[$qid] ?? 0);

    if ($userAnswer === intval($correctOption)) {
        $correctCount++;
    }
}

// Save or update quiz result
// Check if user already took quiz
$res = $connection->query("SELECT * FROM Quiz_Result WHERE Registered_User_Id = $userId AND Quiz_Id = $quizId");
if ($res->num_rows > 0) {
    $connection->query("UPDATE Quiz_Result SET Score = $correctCount, Total_Questions = $totalQuestions, Taken_At = NOW() WHERE Registered_User_Id = $userId AND Quiz_Id = $quizId");
} else {
    $connection->query("INSERT INTO Quiz_Result (Registered_User_Id, Quiz_Id, Score, Total_Questions) VALUES ($userId, $quizId, $correctCount, $totalQuestions)");
}

// Redirect to user dashboard or quiz results page
header("Location: /educaster/courses/dashboard.php?quiz_id=$quizId");
exit();
