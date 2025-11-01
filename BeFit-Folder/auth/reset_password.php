<?php
/**
 * reset_password.php
 * 
 * Handles password reset functionality for users who have requested a reset link.
 * 
 * 1. Validates the reset token from the URL
 * 2. Shows a form to enter new password
 * 3. Updates password in database after validation
 * 4. Deletes used token to prevent reuse
 * 
 * Security Features:
 * - Token expiration (1 hour)
 * - Complex password requirements
 * - CSRF protection via unique tokens
 */
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    session_regenerate_id(true); // Optional security measure
}
session_unset();

// Validate token
$token = $_GET['token'] ?? '';
if (empty($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $_SESSION['reset_error'] = 'Invalid or expired reset link';
    header("Location: signin.php");
    exit();
}

try {
    // Verify token exists and is valid
    $stmt = $pdo->prepare("
    SELECT u.id, u.email, pr.expires_at 
    FROM password_resets pr
    JOIN users u ON pr.user_id = u.id
    WHERE pr.token = ? 
    AND pr.expires_at > NOW()
    LIMIT 1
");
    $stmt->execute([$token]);
    $resetData = $stmt->fetch();

    if (!$resetData) {
        $_SESSION['reset_error'] = 'Invalid or expired reset link';
        header("Location: signin.php");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Validate inputs
        $errors = [];
        
        if (empty($password) || empty($confirm_password)) {
            $errors[] = 'Both password fields are required';
        } elseif ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } elseif (PASSWORD_NEEDS_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } elseif (PASSWORD_NEEDS_NUMBER && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (empty($errors)) {
            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $resetData['id']]);
            
            // Delete the used token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
            
            // Set success message
            $_SESSION['reset_success'] = 'Your password has been reset successfully. Please sign in.';
            header("Location: signin.php");
            exit();
        }
    }

} catch (PDOException $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['reset_error'] = 'A database error occurred. Please try again.';
    header("Location: signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | BeFit</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <style>
        .reset-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .reset-header h2 {
            color: #4A90E2;
            margin-bottom: 0.5rem;
        }
        
        .reset-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #444;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            border-color: #4A90E2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }
        
        .btn-reset {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }
        
        .error-message {
            background: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        
        .error-message p {
            margin: 0;
        }
    </style>
</head>
<body class="shared-bg">
    <div class="reset-container">
        <div class="reset-header">
            <h2>Reset Your Password</h2>
            <p>Enter a new password for <?= htmlspecialchars($resetData['email']) ?></p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-reset">Reset Password</button>
        </form>
    </div>
</body>
</html>
