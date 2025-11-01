<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

//debug
error_log("Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}
// Initialize chat history if it doesn't exist
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [
        [
            'role' => 'system',
            'content' => 'You are a professional fitness trainer helping with workout plans.'
        ],
        [
            'role' => 'ai',
            'content' => "Hello! I'm your AI fitness trainer. Ask me anything about your workout plan."
        ]
    ];
}

// Ensure workout data exists
// Ensure workout data exists
$userData = getUserWorkoutData($pdo, $_SESSION['user_id']);

if (!empty($userData['workout_plan'])) {
    $workoutPlan = json_decode($userData['workout_plan'], true);
    $_SESSION['workout_plan'] = $workoutPlan;
} else {
    $_SESSION['error'] = 'Please generate a workout plan first';
    header("Location: form.php");
    exit;
}

if (empty($workoutPlan)) {
    header("Location: form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - AI Workout Chat</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="/BeFit-Folder/workout_builder/assets/css/workout_builder.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main class="chat-container">
        <div class="chat-header">
            <h1>AI Workout Trainer</h1>
            <p>Ask questions about your workout plan, get modifications, or advice</p>
            
            <div class="chat-actions">
                <a href="view_workout.php" class="secondary-button">View My Plan</a>
                <a href="form.php" class="secondary-button">Update My Plan</a>
            </div>
        </div>
        
        <div class="chat-box" id="chatBox">
            <div class="chat-message ai-message">
                <div class="message-content">
                    <strong>AI Trainer:</strong> Hello! I'm your AI fitness trainer. You can ask me anything about your workout plan. 
                    Would you like modifications, explanations of exercises, or advice on nutrition?
                </div>
            </div>
            
            <!-- Chat messages will be added here dynamically -->
        </div>
        
        <div class="chat-input">
            <form id="chatForm">
                <input type="text" id="userMessage" placeholder="Type your message here..." required>
                <button type="submit" class="cta-button">Send</button>
            </form>
            
            <div class="suggested-questions">
                <p>Try asking:</p>
                <button class="suggestion-btn">How can I make this workout harder?</button>
                <button class="suggestion-btn">Explain the proper form for squats</button>
                <button class="suggestion-btn">Suggest a modification for back pain</button>
            </div>
        </div>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script src="/BeFit-Folder/workout_builder/assets/js/workout_builder.js"></script>
    <script>
        const chatForm = document.getElementById('chatForm');
        const chatBox = document.getElementById('chatBox');
        const userMessage = document.getElementById('userMessage');
        
        // Load conversation history from session if available
        let conversationHistory = <?= json_encode($_SESSION['chat_history'] ?? [
            ['role' => 'system', 'content' => 'You are a professional fitness trainer helping a user with their workout plan.'],
            ['role' => 'ai', 'content' => 'Hello! I\'m your AI fitness trainer. You can ask me anything about your workout plan.']
        ]) ?>;
        
        // Display initial messages
        conversationHistory.forEach(msg => {
            if (msg.role === 'ai') {
                addAiMessage(msg.content);
            } else if (msg.role === 'user') {
                addUserMessage(msg.content);
            }
        });
        
        // Handle form submission
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = userMessage.value.trim();
            if (!message) return;
            console.log("User message submitted:", message);  //debug
            // Add user message to chat
            addUserMessage(message);
            userMessage.value = '';
            
            // Add to conversation history
            conversationHistory.push({role: 'user', content: message});
            
            // Show loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'chat-message ai-message loading';
            loadingDiv.innerHTML = '<div class="message-content"><strong>AI Trainer:</strong> Thinking...</div>';
            chatBox.appendChild(loadingDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
            
            try {
                // Send to server for processing
                const response = await fetch('process_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        history: conversationHistory,
                        workoutPlan: <?= json_encode($workoutPlan) ?>,
                        userData: <?= json_encode($userData) ?>
                    })
                });
                
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    const text = await response.text();
                    console.error("Server returned non-JSON response:", text);
                    throw new Error("Invalid JSON from server.");
                }
                
                // Remove loading indicator
                chatBox.removeChild(loadingDiv);
                
                if (data.error) {
                    addAiMessage("Sorry, I encountered an error: " + data.error);
                } else {
                    addAiMessage(data.response);
                    conversationHistory.push({role: 'ai', content: data.response});
                    
                    // Update session history (simplified for example)
                    fetch('update_chat_history.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({history: conversationHistory})
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                chatBox.removeChild(loadingDiv);
                addAiMessage("Sorry, I'm having trouble responding right now. Please try again later.");
            }
        });
        
        // Handle suggestion buttons
        document.querySelectorAll('.suggestion-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                userMessage.value = this.textContent;
                userMessage.focus();
            });
        });
        
        function addUserMessage(content) {
            const div = document.createElement('div');
            div.className = 'chat-message user-message';
            div.innerHTML = `<div class="message-content"><strong>You:</strong> ${content}</div>`;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        
        function addAiMessage(content) {
            const div = document.createElement('div');
            div.className = 'chat-message ai-message';
            div.innerHTML = `<div class="message-content"><strong>AI Trainer:</strong> ${content}</div>`;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>