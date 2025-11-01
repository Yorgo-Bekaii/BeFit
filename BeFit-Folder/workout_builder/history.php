<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}

$workoutHistory = getWorkoutHistory($pdo, $_SESSION['user_id']);
$currentPlan = getUserWorkoutData($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - Workout History</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="/BeFit-Folder/workout_builder/assets/css/workout_builder.css">
    <style>
        .history-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .history-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .history-grid {
            display: grid;
            gap: 1.5rem;
        }
        .history-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .history-date {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .history-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .no-history {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="history-container">
        <div class="history-header">
            <h1>Your Workout History</h1>
            <p>Track your progress and revisit past workouts</p>
            
            <div class="header-actions" style="text-align: center; margin-top: 1.5rem;">
                <a href="form.php" class="cta-button" style="padding: 12px 30px; font-size: 1.1rem;">
                    <i class="fas fa-plus"></i> Create New Plan
                </a>
            </div>
        </div>
        
        <div class="history-grid">
            <?php if (!empty($workoutHistory)): ?>
                <?php foreach ($workoutHistory as $workout): 
                    $workoutData = json_decode($workout['workout_data'], true);
                    $workoutDate = date('F j, Y', strtotime($workout['workout_date']));
                ?>
                    <div class="history-card">
                        <div class="history-date">
                            <?= htmlspecialchars($workoutDate) ?>
                            <?php if ($workout['completed']): ?>
                                <span class="completed-badge">✓ Completed</span>
                            <?php else: ?>
                                <span class="incomplete-badge">⏱ In Progress</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($workoutData['notes'])): ?>
                            <div class="history-notes">
                                <p><?= nl2br(htmlspecialchars($workoutData['notes'])) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="history-actions">
                            <a href="view_workout.php?history_id=<?= $workout['id'] ?>" class="secondary-button">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if (!$workout['completed']): ?>
                                <form action="log_completion.php" method="post" style="display: inline;">
                                    <input type="hidden" name="workout_id" value="<?= $workout['id'] ?>">
                                    <button type="submit" class="cta-button small">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-history">
                    <h3>No Workout History Yet</h3>
                    <p>Your completed workouts will appear here.</p>
                    <a href="form.php" class="cta-button">Create Your First Workout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>