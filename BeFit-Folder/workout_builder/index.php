<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}

$userData = getUserWorkoutData($pdo, $_SESSION['user_id']);

if (empty($userData) || empty($userData['workout_plan'])) {
    header("Location: form.php");
} else {
    // Store plan in session and redirect properly
    $_SESSION['workout_plan'] = json_decode($userData['workout_plan'], true);
    $_SESSION['workout_data'] = $userData; // Store all user data
    header("Location: view_workout.php");
}
exit;
?>