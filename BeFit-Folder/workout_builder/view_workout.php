<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}
$userData = getUserWorkoutData($pdo, $_SESSION['user_id']);

if (!empty($userData['workout_plan'])) {
    $workoutPlan = json_decode($userData['workout_plan'], true);
    $_SESSION['workout_plan'] = $workoutPlan; // update session
} else {
    $_SESSION['error'] = "Please generate a workout plan first";
    header("Location: form.php");
    exit;
}
$isHistoricalView = isset($_GET['history_id']);
$workoutData = [];

if ($isHistoricalView) {
    // Load historical workout
    $historyId = (int)$_GET['history_id'];
    $stmt = $pdo->prepare("SELECT * FROM user_workout_history 
                          WHERE id = ? AND user_id = ?");
    $stmt->execute([$historyId, $_SESSION['user_id']]);
    $workoutData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$workoutData) {
        $_SESSION['error'] = "Workout not found";
        header("Location: history.php");
        exit;
    }
    
    $workoutPlan = json_decode($workoutData['workout_data'], true);
    
    // Get user data - first try from the workout plan, then fallback to current data
    $userData = $workoutPlan['user_data'] ?? [];
    
    // If user_data is empty or incomplete, get current data
    if (empty($userData) || !isset($userData['age'])) {
        $currentData = getUserWorkoutData($pdo, $_SESSION['user_id']);
        $userData = array_merge([
            'age' => $currentData['age'] ?? null,
            'height' => $currentData['height'] ?? null,
            'weight' => $currentData['weight'] ?? null,
            'fitness_level' => $currentData['fitness_level'] ?? null,
            'goal' => $currentData['goal'] ?? null,
            'training_days' => $currentData['training_days'] ?? null
        ], $userData);
    }
}


if (empty($workoutPlan)) {
    header("Location: form.php");
    exit;
}

$recommendedSupplements = [];
$stmt = $pdo->prepare("SELECT rs.*, p.name, p.price, p.image_url 
    FROM recommended_supplements rs
    JOIN products p ON rs.product_id = p.id
    WHERE rs.user_id = ? AND rs.purchased = 0");
$stmt->execute([$_SESSION['user_id']]);
$recommendedSupplements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - Your Workout Plan</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="/BeFit-Folder/workout_builder/assets/css/workout_builder.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main class="workout-plan-container">
    <div class="workout-header">
        <h1>
            <?= $isHistoricalView ? 
                'Workout from '.htmlspecialchars(date('F j, Y', strtotime($workoutData['workout_date']))) : 
                'Your Personalized Workout Plan' ?>
        </h1>
        
        <p>
            <?= $isHistoricalView ? 
                'Your past workout session' : 
                'Generated specifically for your goals and fitness level' ?>
        </p>
        
        <div class="plan-actions">
            <?php if (!$isHistoricalView): ?>
                <a href="chat.php" class="cta-button" id="chatButton">
                    <i class="fas fa-comment-dots"></i> Chat with AI Trainer
                </a>
            <?php endif; ?>
            <a href="history.php" class="secondary-button">
                <i class="fas fa-history"></i> <?= $isHistoricalView ? 'Back to History' : 'View History' ?>
            </a>
        </div>
        
        <?php if ($isHistoricalView && isset($workoutData['notes'])): ?>
            <div class="workout-notes">
                <h3>Your Notes:</h3>
                <p><?= nl2br(htmlspecialchars($workoutData['notes'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
        
        <div class="plan-details">
            <div class="user-stats">
                <h2>Your Stats</h2>
                <ul>
                    <li><strong>Age:</strong> <?= htmlspecialchars($userData['age']) ?></li>
                    <li><strong>Height:</strong> <?= htmlspecialchars($userData['height']) ?> cm</li>
                    <li><strong>Weight:</strong> <?= htmlspecialchars($userData['weight']) ?> kg</li>
                    <li><strong>Fitness Level:</strong> <?= ucfirst(htmlspecialchars($userData['fitness_level'])) ?></li>
                    <li><strong>Goal:</strong> <?= formatGoal(htmlspecialchars($userData['goal'])) ?></li>
                    <li><strong>Training Days:</strong> <?= htmlspecialchars($userData['training_days']) ?> per week</li>
                </ul>
            </div>
            
            <div class="general-advice">
                <h2>General Advice</h2>
                <p><?= nl2br(htmlspecialchars($workoutPlan['general_advice'] ?? 'No general advice provided.')) ?></p>
            </div>
        </div>
        
        <div class="weekly-plan">
            <h2>Weekly Workout Schedule</h2>
            
            <?php foreach ($workoutPlan['weekly_plan'] as $day => $workout): ?>
            <div class="workout-day">
                <h3><?= htmlspecialchars(ucfirst($day)) ?></h3>
                
                <?php if (empty($workout['exercises'])): ?>
                    <p>Rest day or no exercises specified.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Exercise</th>
                                <th>Sets</th>
                                <th>Reps</th>
                                <th>Rest</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workout['exercises'] as $exercise): ?>
                            <tr>
                                <td><?= htmlspecialchars($exercise['name']) ?></td>
                                <td><?= htmlspecialchars($exercise['sets'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($exercise['reps'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($exercise['rest'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($exercise['notes'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($recommendedSupplements)): ?>
        <div class="supplement-recommendations">
            <h2>Recommended Supplements</h2>
            <p>Based on your goals, these supplements may help you achieve better results:</p>
            
            <div class="supplement-grid">
                <?php foreach ($recommendedSupplements as $supplement): ?>
                <div class="supplement-card">
    <img src="/BeFit-Folder/public/<?= htmlspecialchars($supplement['image_url']) ?>" alt="<?= htmlspecialchars($supplement['name']) ?>">
    <div class="supplement-content">
        <?php 
        // Extract brand and product name if formatted as "Brand - Product"
        $nameParts = explode(' - ', htmlspecialchars($supplement['name']), 2);
        $brand = count($nameParts) > 1 ? trim($nameParts[0]) : '';
        $productName = count($nameParts) > 1 ? trim($nameParts[1]) : htmlspecialchars($supplement['name']);
        ?>
        
        <?php if($brand): ?>
            <p class="supplement-brand"><?= strtoupper($brand) ?></p>
        <?php endif; ?>
        
        <h3 class="supplement-name"><?= $productName ?></h3>
        
        <?php if(!empty($supplement['description'])): ?>
            <p class="supplement-subtitle"><?= htmlspecialchars($supplement['description']) ?></p>
        <?php endif; ?>
        
        <p class="supplement-price">$<?= number_format($supplement['price'], 2) ?></p>
        <p class="supplement-description"><?= htmlspecialchars($supplement['reason']) ?></p>
        
        <div class="supplement-actions">
                    <a href="/BeFit-Folder/ecommerce/shop.php?add_to_cart=<?= $supplement['product_id'] ?>" class="buy-button">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Add to Cart
                    </a>
                </div>
            </div>
        </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="plan-actions-bottom">
            <a href="form.php?force_update=1" class="secondary-button">Update My Plan</a>
            <button id="printPlan" class="secondary-button">Print This Plan</button>
        </div>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script src="/BeFit-Folder/workout_builder/assets/js/workout_builder.js"></script>
    <script>
        document.getElementById('printPlan').addEventListener('click', function() {
            window.print();
        });
    </script>
    <script>
        document.getElementById('chatButton').addEventListener('click', function(e) {
            // Check if workout data exists
            if (!<?= isset($_SESSION['workout_plan']) ? 'true' : 'false' ?>) {
                e.preventDefault();
                alert('Please generate a workout plan first');
                window.location.href = 'form.php';
            }
            // Otherwise proceed normally
        });
    </script>
</body>
</html>

<?php
function formatGoal(string $goal): string {
    $goals = [
        'build_muscle' => 'Build Muscle',
        'lose_weight' => 'Lose Weight',
        'strength' => 'Increase Strength',
        'endurance' => 'Improve Endurance',
        'tone' => 'Tone Body'
    ];
    
    return $goals[$goal] ?? ucfirst(str_replace('_', ' ', $goal));
}
?>