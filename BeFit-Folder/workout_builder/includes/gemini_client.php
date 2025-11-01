<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../auth/config.php';

use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Resources\Content;
use GeminiAPI\Requests\GenerateContentRequest;
use GeminiAPI\Enums\Role;

class GeminiWorkoutClient {
    private $client;
    private $model = 'gemini-2.0-flash';

    public function __construct() {
        $this->client = new GeminiAPI\Client(GEMINI_API_KEY);
    }

    public function generateWorkoutPlan(array $userData): array {
        $promptText = $this->buildWorkoutPrompt($userData);

        try {
            $textPart = new TextPart($promptText, 'text/plain');
            $content = new Content([$textPart], Role::User);
            $request = new GenerateContentRequest($this->model, [$content]);
            
            $response = $this->client->generateContent($request);

            $responseText = '';
            foreach ($response->candidates as $candidate) {
                if (isset($candidate->content->parts[0]->text)) {
                    $responseText .= $candidate->content->parts[0]->text;
                }
            }

            return $this->parseWorkoutResponse($responseText);
        } catch (Exception $e) {
            return [
                'error' => 'API Error: ' . $e->getMessage(),
                'api_response' => $e->getMessage()
            ];
        }
    }
    

    public function chatAboutWorkout(array $conversationHistory): array {
    $promptText = "You are a professional fitness trainer. Continue this conversation about workout plans:\n\n";
    foreach ($conversationHistory as $message) {
        $promptText .= "{$message['role']}: {$message['content']}\n";
    }

    try {
        $textPart = new TextPart($promptText, 'text/plain');
        $content = new Content([$textPart], Role::User);
        $request = new GenerateContentRequest($this->model, [$content]);

        $response = $this->client->generateContent($request);

        $responseText = '';
        foreach ($response->candidates as $candidate) {
            if (isset($candidate->content->parts[0]->text)) {
                $responseText .= $candidate->content->parts[0]->text;
            }
        }

        if (trim($responseText) === '') {
            return ['response' => null, 'error' => 'Empty response from AI.'];
        }

        return ['response' => $responseText, 'error' => null];

    } catch (Exception $e) {
        error_log("Gemini Chat Error: " . $e->getMessage());
        return ['response' => null, 'error' => 'Failed to process your message. Please try again.'];
    }
}


    private function buildWorkoutPrompt(array $userData): string {
        $equipmentList = explode(',', $userData['equipment']);
        $equipmentText = implode(', ', $equipmentList);
        
        return sprintf(
            "Create a personalized %s-day workout plan for a %s year old %s, %scm tall, weighing %skg. " .
            "Fitness level: %s. Goal: %s. Available equipment: %s. " .
            "Medical considerations: %s. Preferences: %s. " .
            "Provide a detailed weekly plan with exercises, sets, reps, rest periods, and notes for each day. " .
            "Format the response as valid JSON with these exact keys: " .
            "'weekly_plan' (array of days), 'supplement_recommendations' (array with 'name' and 'reason'), " .
            "and 'general_advice' (string). Ensure the JSON is properly formatted and can be decoded.",
            $userData['training_days'],
            $userData['age'],
            $userData['gender'],
            $userData['height'],
            $userData['weight'],
            $userData['fitness_level'],
            $userData['goal'],
            $equipmentText,
            $userData['medical_conditions'] ?? 'none',
            $userData['preferences'] ?? 'none'
        );
    }

    private function parseWorkoutResponse(string $response): array {
        // First try direct JSON decode
        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // If that fails, try to extract JSON from the response
            preg_match('/\{.*\}/s', $response, $matches);
            if ($matches) {
                $decoded = json_decode($matches[0], true);
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE || !$decoded) {
            echo "<h3>JSON Parse Error:</h3>";
            echo "<p>Error: " . json_last_error_msg() . "</p>";
            echo "<p>Response that failed to parse:</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
            return ['error' => 'Could not parse the workout plan response.'];
        }

        return $decoded;
    }
}