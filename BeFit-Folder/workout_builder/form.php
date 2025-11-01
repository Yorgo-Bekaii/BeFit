<?php
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/includes/workout_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /BeFit-Folder/auth/signin.php");
    exit;
}

$userData = getUserWorkoutData($pdo, $_SESSION['user_id']);
$hasExistingPlan = !empty($userData);
$forceUpdate = isset($_GET['force_update']) && $_GET['force_update'] == 1;

if ($hasExistingPlan && !$forceUpdate) {
    header("Location: view_workout.php");
    exit;
}
// Set default values for the form
$defaultData = [
    'weight' => $userData['weight'] ?? '',
    'height' => $userData['height'] ?? '',
    'age' => $userData['age'] ?? '',
    'gender' => $userData['gender'] ?? 'male',
    'fitness_level' => $userData['fitness_level'] ?? 'beginner',
    'goal' => $userData['goal'] ?? 'build_muscle',
    'training_days' => $userData['training_days'] ?? 3,
    'equipment' => $userData['equipment'] ?? 'dumbbells,resistance_bands',
    'medical_conditions' => $userData['medical_conditions'] ?? '',
    'preferences' => $userData['preferences'] ?? ''
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - Workout Builder</title>
    <link rel="stylesheet" href="/BeFit-Folder/public/css/styles1.css">
    <link rel="stylesheet" href="/BeFit-Folder/workout_builder/assets/css/workout_builder.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main class="workout-builder-container">
        <div class="workout-header">
            <h1><?= $hasExistingPlan ? 'Update Your Workout Plan' : 'Create Your Workout Plan' ?></h1>
            <p><?= $hasExistingPlan ? 'Modify your details to generate an updated plan' : 'Get a personalized workout plan tailored to your goals and equipment' ?></p>
            <?php if ($hasExistingPlan): ?>
            <div class="update-notice">
                <i class="fas fa-info-circle"></i> Updating will generate a new plan based on your changes
            </div>
            <?php endif; ?>
            <div class="progress-bar">
                <div class="progress" style="width: 0%"></div>
            </div>
        </div>
        
        
        <form id="workoutForm" action="process_form.php" method="post" class="workout-form">
            <div class="form-section">
                <h2>Basic Information</h2>
                
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" id="weight" name="weight" value="<?= htmlspecialchars($defaultData['weight']) ?>" required min="30" max="200">
                </div>
                
                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <input type="number" id="height" name="height" value="<?= htmlspecialchars($defaultData['height']) ?>" required min="100" max="250">
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($defaultData['age']) ?>" required min="12" max="100">
                </div>
                
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="male" <?= $defaultData['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $defaultData['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= $defaultData['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Fitness Details</h2>
                
                <div class="form-group">
                    <label for="fitness_level">Current Fitness Level</label>
                    <select id="fitness_level" name="fitness_level" required>
                        <option value="beginner" <?= $defaultData['fitness_level'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="intermediate" <?= $defaultData['fitness_level'] === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="advanced" <?= $defaultData['fitness_level'] === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="goal">Primary Goal</label>
                    <select id="goal" name="goal" required>
                        <option value="build_muscle" <?= $defaultData['goal'] === 'build_muscle' ? 'selected' : '' ?>>Build Muscle</option>
                        <option value="lose_weight" <?= $defaultData['goal'] === 'lose_weight' ? 'selected' : '' ?>>Lose Weight</option>
                        <option value="strength" <?= $defaultData['goal'] === 'strength' ? 'selected' : '' ?>>Increase Strength</option>
                        <option value="endurance" <?= $defaultData['goal'] === 'endurance' ? 'selected' : '' ?>>Improve Endurance</option>
                        <option value="tone" <?= $defaultData['goal'] === 'tone' ? 'selected' : '' ?>>Tone Body</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="training_days">Days per Week You Can Train</label>
                    <select id="training_days" name="training_days" required>
                        <?php for ($i = 2; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>" <?= $defaultData['training_days'] == $i ? 'selected' : '' ?>><?= $i ?> days</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Equipment & Preferences</h2>
                
                <div class="form-group">
                    <label>Available Equipment</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="equipment[]" value="dumbbells" <?= strpos($defaultData['equipment'], 'dumbbells') !== false ? 'checked' : '' ?>> Dumbbells</label>
                        <label><input type="checkbox" name="equipment[]" value="resistance_bands" <?= strpos($defaultData['equipment'], 'resistance_bands') !== false ? 'checked' : '' ?>> Resistance Bands</label>
                        <label><input type="checkbox" name="equipment[]" value="pullup_bar" <?= strpos($defaultData['equipment'], 'pullup_bar') !== false ? 'checked' : '' ?>> Pull-up Bar</label>
                        <label><input type="checkbox" name="equipment[]" value="weight_bench" <?= strpos($defaultData['equipment'], 'weight_bench') !== false ? 'checked' : '' ?>> Weight Bench</label>
                        <label><input type="checkbox" name="equipment[]" value="none" <?= strpos($defaultData['equipment'], 'none') !== false ? 'checked' : '' ?>> No Equipment</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="medical_conditions">Medical Conditions (if any)</label>
                    <textarea id="medical_conditions" name="medical_conditions"><?= htmlspecialchars($defaultData['medical_conditions']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="preferences">Workout Preferences (e.g., avoid squats, prefer morning workouts)</label>
                    <textarea id="preferences" name="preferences"><?= htmlspecialchars($defaultData['preferences']) ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="cta-button" id="generateButton">
                    <span class="button-text"><?= $hasExistingPlan ? 'Update My Plan' : 'Generate My Workout Plan' ?></span>
                </button>
                <?php if ($hasExistingPlan): ?>
                <a href="history.php" class="secondary-button">View Workout History</a>
                <?php endif; ?>
            </div>
        </form>
    </main>
    
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    
    <script src="/BeFit-Folder/workout_builder/assets/js/workout_builder.js"></script>
    <script>
        // Update progress bar as user fills form
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', updateProgress);
            el.addEventListener('change', updateProgress);
        });

        function updateProgress() {
            const fields = document.querySelectorAll('input[required], select[required], textarea[required]');
            const filled = [...fields].filter(f => f.value.trim() !== '').length;
            const progress = (filled / fields.length) * 100;
            document.querySelector('.progress').style.width = `${progress}%`;
        }
    </script>
    <script>
        // Add update mode class if editing existing plan
        <?php if ($hasExistingPlan): ?>
        document.querySelector('.workout-form').classList.add('update-mode');
        <?php endif; ?>
    </script>
    <script>
    document.getElementById('workoutForm').addEventListener('submit', function(e) {
    const button = document.getElementById('generateButton');
    const textSpan = button.querySelector('.button-text');
    const existingLoader = button.querySelector('.loader');
    if (existingLoader) {
        existingLoader.remove();
    }
    
    const originalText = textSpan.textContent;
    
    // Prevent double submission
    if (button.classList.contains('loading')) {
        e.preventDefault();
        return;
    }
    
    button.classList.add('loading');
    button.disabled = true;
    textSpan.textContent = 'Generating Your Plan';
    
    // Optional: Revert if submission fails (you'll need to handle this in your process_form.php)
    setTimeout(() => {
        if (!document.getElementById('workoutForm').checkValidity()) {
            button.classList.remove('loading');
            button.disabled = false;
            textSpan.textContent = originalText;
        }
    }, 3000);
});
    </script>
</body>
</html>