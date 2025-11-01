<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || !isset($_POST['workout_id'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: history.php");
    exit;
}

$workoutId = (int)$_POST['workout_id'];
$userId = (int)$_SESSION['user_id'];

if (logWorkoutCompletion($pdo, $workoutId, $userId)) {
    $_SESSION['success'] = "Workout marked as completed!";
} else {
    $_SESSION['error'] = "Failed to update workout";
}

header("Location: history.php");
exit;
?>