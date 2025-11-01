<?php
// Set response type early
header('Content-Type: application/json');

// Autoloader logic
$rootDir = dirname(__DIR__);
$autoloader = $rootDir . '/vendor/autoload.php';

if (file_exists($autoloader)) {
    require $autoloader;
} else {
    $fixer = $rootDir . '/fix_autoloader.php';
    if (file_exists($fixer)) {
        require $fixer;
        if (function_exists('repairAutoloader')) {
            repairAutoloader();
            if (file_exists($autoloader)) {
                require $autoloader;
            } else {
                echo json_encode(['error' => 'Autoloader repair failed']);
                exit;
            }
        }
    }
    echo json_encode(['error' => 'Vendor dependencies missing. Run "composer install"']);
    exit;
}

require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/gemini_client.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Try/Catch wrapper for everything else
try {
    // Decode request
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['message'])) {
        throw new Exception('No message provided');
    }

    // Append user message to chat history
    $_SESSION['chat_history'][] = [
        'role' => 'user',
        'content' => $input['message']
    ];

    $gemini = new GeminiWorkoutClient();

    // Build workout context
    $workoutContext = "Current workout plan:\n" . 
        json_encode($_SESSION['workout_plan'], JSON_PRETTY_PRINT) . "\n\n" .
        "Conversation history:\n";

    foreach ($_SESSION['chat_history'] as $message) {
        $workoutContext .= "{$message['role']}: {$message['content']}\n";
    }

    // Call AI
    $response = $gemini->chatAboutWorkout($_SESSION['chat_history']);

    if (isset($response['error'])) {
        throw new Exception($response['error']);
    }

    // Return proper JSON (ensure only this runs)
    echo json_encode(['response' => $response['response'] ?? $response]);
    exit;

} catch (Throwable $e) {
    // Log and return error safely
    error_log("Chat error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to process your message: ' . $e->getMessage()
    ]);
    exit;
}
?>
