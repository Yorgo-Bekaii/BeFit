<?php
session_start();
require_once __DIR__ . '/../auth/config.php';

// Redirect if no verification session exists
if (!isset($_SESSION['verification_email']) || !isset($_SESSION['verification_code'])) {
    header("Location: signup.php?error=verification_expired");
    exit();
}


$email = $_SESSION['verification_email'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_code = $_POST['digit1'] . $_POST['digit2'] . $_POST['digit3'] . 
                 $_POST['digit4'] . $_POST['digit5'] . $_POST['digit6'];
    
    if ($user_code == $_SESSION['verification_code']) {
        // Update verification status and get user data
        $stmt = $pdo->prepare("UPDATE users SET verified=1 WHERE email=?");
        $stmt->execute([$email]);
        
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['loggedin'] = true;
        
        // Clear verification session
        unset($_SESSION['verification_email']);
        unset($_SESSION['verification_code']);
        
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Invalid verification code. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | BeFit</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <style>
        .verify-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 3rem;
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .digit-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 1.8rem;
            margin: 0 8px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .digit-input:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74,144,226,0.2);
            transform: translateY(-2px);
        }
        .verify-btn {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74,144,226,0.3);
        }
        .verify-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(74,144,226,0.4);
        }
        .resend-link {
            display: block;
            margin-top: 1.5rem;
            color: #4A90E2;
            text-decoration: none;
        }
    </style>
</head>
<body class="shared-bg">
    <div class="verify-container">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">Verify Your Email</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem;">We've sent a 6-digit code to <strong><?= htmlspecialchars($email) ?></strong></p>
        
        <?php if ($error): ?>
            <div class="error-message" style="color: #ff4444; margin-bottom: 1.5rem; padding: 10px; background: #ffebee; border-radius: 4px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="verificationForm">
            <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" name="digit<?= $i ?>" class="digit-input" maxlength="1" required 
                           pattern="[0-9]" inputmode="numeric" autocomplete="one-time-code">
                <?php endfor; ?>
            </div>
            
            <button type="submit" class="verify-btn">Verify & Continue</button>
            
            <a href="resend_code.php?email=<?= urlencode($email) ?>" class="resend-link">
                Didn't receive a code? Resend it
            </a>
        </form>
    </div>

    <script>
    // Enhanced input handling
    const inputs = document.querySelectorAll('.digit-input');
    const form = document.getElementById('verificationForm');
    
    inputs.forEach((input, index) => {
        // Auto-advance and validate
        input.addEventListener('input', (e) => {
            if (e.target.value.match(/[0-9]/)) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    form.submit();
                }
            } else {
                e.target.value = '';
            }
        });
        
        // Handle backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
    
    // Focus first input on load
    inputs[0].focus();
    </script>
</body>
</html>