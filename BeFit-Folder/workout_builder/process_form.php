<?php
/**
 * Process Workout Form Submission
 * 
 * 1. Validates user input and sanitizes data
 * 2. Generates workout plan via Gemini API
 * 3. Saves plan to database and session
 * 
 * Debug Mode:
 * - Set DEBUG=true to show processing steps
 * - Shows raw data, API calls, and results
 * - Disables redirects for testing
 * - Set DEBUG=false for production (enables redirects)
 */

session_start();
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/gemini_client.php';
require_once __DIR__ . '/includes/workout_functions.php';

// Debug mode
define('DEBUG', false);

unset($_SESSION['error']);

// Function to show debug output
function debug_output($data, $title = '') {
    if (!defined('DEBUG') || !DEBUG) return;
    if ($title) echo "<h3>$title</h3>";
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Debug: Show all POST data
debug_output($_POST, 'Raw POST Data');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    debug_output(['Error' => 'Not authenticated'], 'Authentication Error');
    if (!DEBUG) header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_output(['Error' => 'Invalid request method'], 'Request Method Error');
    if (!DEBUG) header("Location: form.php");
    exit;
}

// Initialize filtered data array
$filteredData = [];

// Validate numeric inputs
$numericFields = [
    'weight' => ['min' => 30, 'max' => 200],
    'height' => ['min' => 100, 'max' => 250],
    'age' => ['min' => 12, 'max' => 100],
    'training_days' => ['min' => 2, 'max' => 6]
];

foreach ($numericFields as $field => $limits) {
    $value = filter_input(INPUT_POST, $field, FILTER_VALIDATE_FLOAT);
    if ($value === false || $value === null || $value < $limits['min'] || $value > $limits['max']) {
        debug_output([
            'Error' => "Invalid $field",
            'Value' => $_POST[$field] ?? 'NULL',
            'Expected' => "Between {$limits['min']} and {$limits['max']}"
        ], 'Validation Error');
        if (!DEBUG) {
            $_SESSION['error'] = "Invalid value for $field";
            header("Location: form.php");
        }
        exit;
    }
    $filteredData[$field] = $value;
}

// Validate select inputs
$selectFields = [
    'gender' => ['male', 'female', 'other'],
    'fitness_level' => ['beginner', 'intermediate', 'advanced'],
    'goal' => ['build_muscle', 'lose_weight', 'strength', 'endurance', 'tone']
];

foreach ($selectFields as $field => $allowedValues) {
    $value = $_POST[$field] ?? '';
    if (!in_array($value, $allowedValues)) {
        debug_output([
            'Error' => "Invalid $field",
            'Value' => $value,
            'Allowed' => $allowedValues
        ], 'Validation Error');
        if (!DEBUG) {
            $_SESSION['error'] = "Invalid value for $field";
            header("Location: form.php");
        }
        exit;
    }
    $filteredData[$field] = $value;
}

// Process equipment
$allowedEquipment = ['dumbbells', 'resistance_bands', 'pullup_bar', 'weight_bench', 'none'];
$equipment = [];

if (isset($_POST['equipment'])) {
    if (is_array($_POST['equipment'])) {
        foreach ($_POST['equipment'] as $item) {
            if (in_array($item, $allowedEquipment)) {
                $equipment[] = $item;
            }
        }
    }
}

$filteredData['equipment'] = !empty($equipment) ? implode(',', $equipment) : 'none';

// Sanitize text inputs
$textFields = ['medical_conditions', 'preferences'];
foreach ($textFields as $field) {
    $filteredData[$field] = isset($_POST[$field]) 
        ? htmlspecialchars(strip_tags(trim($_POST[$field])), ENT_QUOTES, 'UTF-8')
        : '';
}

// Debug: Show filtered data
debug_output($filteredData, 'Filtered Form Data');

// Save user data
if (!saveUserWorkoutData($pdo, $_SESSION['user_id'], $filteredData)) {
    debug_output(['Error' => 'Failed to save user data'], 'Database Error');
    if (!DEBUG) {
        $_SESSION['error'] = "Failed to save user data";
        header("Location: form.php");
    }
    exit;
}

// Generate workout plan
try {
    $gemini = new GeminiWorkoutClient();
    $workoutPlan = $gemini->generateWorkoutPlan($filteredData);
    
    if (isset($workoutPlan['error'])) {
        throw new Exception($workoutPlan['error']);
    }
    
    if (empty($workoutPlan['weekly_plan'])) {
        throw new Exception("Invalid workout plan structure received");
    }
    
    debug_output($workoutPlan, 'Generated Workout Plan');
    
} catch (Exception $e) {
    debug_output([
        'Error' => 'Workout generation failed',
        'Message' => $e->getMessage(),
        'Trace' => $e->getTraceAsString()
    ], 'Workout Generation Error');
    if (!DEBUG) {
        $_SESSION['error'] = "Workout generation failed: " . $e->getMessage();
        header("Location: form.php");
    }
    exit;
}

// Save the generated workout plan
if (!saveWorkoutPlan($pdo, $_SESSION['user_id'], $workoutPlan)) {
    debug_output(['Error' => 'Failed to save workout plan'], 'Database Error');
    if (!DEBUG) {
        $_SESSION['error'] = "Failed to save workout plan";
        header("Location: form.php");
    }
    exit;
}

// Store supplement recommendations
if (isset($workoutPlan['supplement_recommendations'])) {
    saveSupplementRecommendations($pdo, $_SESSION['user_id'], $workoutPlan['supplement_recommendations']);
}

// Store in session
$_SESSION['workout_plan'] = $workoutPlan;
$_SESSION['workout_data'] = $filteredData;

debug_output($_SESSION, 'Session Data After Processing');

// In debug mode, show success but don't redirect
if (!DEBUG) {
    header("Location: view_workout.php");
    exit;
}

echo "<h2>Workout Plan Generated Successfully!</h2>";
echo "<p>In production mode, this would redirect to view_workout.php</p>";