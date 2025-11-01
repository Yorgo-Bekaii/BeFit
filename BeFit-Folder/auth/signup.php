<?php
session_start(); 
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/../auth/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords don't match!";
    } 
if (strlen($password) < PASSWORD_MIN_LENGTH) {
    $error = "Password must be at least ".PASSWORD_MIN_LENGTH." characters!";
} elseif (PASSWORD_NEEDS_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
    $error = "Password needs at least one uppercase letter!";
} elseif (PASSWORD_NEEDS_NUMBER && !preg_match('/[0-9]/', $password)) {
    $error = "Password needs at least one number!";
}
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
        if ($stmt->execute([$name, $email, $hashed_password])) {
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_email'] = $email;
            $_SESSION['verification_code'] = $verification_code;
            
            // Send email
            if (sendVerificationEmail($email, $verification_code)) {
                // Store in session before redirect
                $_SESSION['verification_sent'] = true;
                header("Location: verify_email.php?email=".urlencode($email));
                exit();
            } else {
                $error = "Failed to send verification email. Please try again.";
            }
        } else {
                            $error = "Registration failed!";
                        }
                    }
                }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeFit - Sign Up</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <style>
    html, body {
        height: 100%;
        overflow-y: hidden;
    }

    .signin-container, .signup-container {
        position: relative;
        z-index: 1;
    }

    .signup-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 1rem 5%;
        gap: 2rem;
        max-width: 1100px;
        margin: 0 auto;
    }

    .benefits-column, .form-container {
        flex: 1;
        max-width: 450px;
        padding: 2rem;
        height: fit-content;
    }

    .benefits-column {
        color: white;
        background: rgba(0, 0, 0, 0.4);
        border-radius: 15px;
        backdrop-filter: blur(5px);
    }

    .benefit-item {
        margin-bottom: 2rem;
    }

    .benefit-item h3 {
        font-size: 1.3rem;
        margin-bottom: 0.6rem;
        color: #4A90E2;
    }

    .benefit-item p {
        font-size: 1rem;
        opacity: 0.9;
        line-height: 1.5;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    input {
        width: 100%;
        padding: 12px 15px;
        border: none;
        border-bottom: 2px solid #ddd;
        border-radius: 0;
        font-size: 1rem;
        background: transparent;
        transition: all 0.3s ease;
    }

    input:focus {
        border-bottom-color: #4A90E2;
        outline: none;
        box-shadow: 0 5px 15px rgba(74, 144, 226, 0.1);
    }

    .terms-checkbox {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
        font-size: 0.9rem;
        color: #666;
    }

    .terms-checkbox input {
        width: auto;
        margin-right: 0.8rem;
    }

    .terms-checkbox a {
        color: #4A90E2;
        text-decoration: none;
    }

    .terms-checkbox a:hover {
        text-decoration: underline;
    }

    .signup-btn {
        width: 100%;
        padding: 14px;
        background-color: #4A90E2;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .signup-btn:hover {
        background-color: #357ABD;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
    }

    .account-section {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    .signin-btn {
        display: inline-block;
        padding: 10px 25px;
        background-color: transparent;
        color: #4A90E2;
        border: 2px solid #4A90E2;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: 1rem;
        font-size: 0.9rem;
    }

    .signin-btn:hover {
        background-color: #4A90E2;
        color: white;
    }

    .error-message {
        color: #d32f2f;
        padding: 12px;
        margin-bottom: 20px;
        background-color: #ffebee;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .signup-container {
            flex-direction: column;
            padding: 1rem;
            overflow-y: auto;
        }

        .benefits-column, .form-container {
            width: 100%;
            max-width: none;
        }

        html, body {
            overflow-y: auto;
        }
    }
</style>
<body class="shared-bg">
    <div class="signup-container">
        
        <div class="benefits-column">
            <h2 style="font-size: 2rem; margin-bottom: 1.5rem;">Why Join BeFit?</h2>
            
            <div class="benefit-item">
                <h3>Free AI-Powered Workouts</h3>
                <p>Personalized training plans tailored to your goals and equipment.</p>
            </div>

            <div class="benefit-item">
                <h3>Exclusive Discounts</h3>
                <p>Member-only deals on supplements and gear.</p>
            </div>

            <div class="benefit-item">
                <h3>Smart Progress Tracking</h3>
                <p>Smart monitoring of workouts and nutrition.</p>
            </div>
        </div>

        
            <div class="form-container">
                <?php if(isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <h2 style="margin-bottom: 1.5rem; color: #333; text-align: center; font-size: 1.8rem;">Create Account</h2>
                
                <form class="signup-form" method="POST" action="signup.php">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Create Password" required>
                </div>

                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">I agree to the <a href="../terms.php">Terms & Conditions</a></label>
                </div>

                <button type="submit" class="signup-btn">Sign Up</button>
                </form>

                <div class="account-section">
                    <p style="color: #666; font-size: 0.9rem;">Already have an account?</p>
                    <a href="signin.php" class="signin-btn">Sign In Now</a>
                </div>
        </div>
    </div>
<script>
    // Simple form submission handler
    document.querySelector('.signup-form')?.addEventListener('submit', function() {
    const btn = document.querySelector('.signup-btn');
    btn.disabled = true;
    btn.textContent = 'Creating Account...';
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>
</body>
</html>
