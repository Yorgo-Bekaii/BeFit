<?php
session_start();
require_once __DIR__ . '/auth/config.php';
require_once __DIR__ . '/auth/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Send email (you'll need to configure your mailer)
        $to = "befitcompany.contact@gmail.com";
        $subject = "Support Request from $name";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $emailBody = "Name: $name\n";
        $emailBody .= "Email: $email\n\n";
        $emailBody .= "Message:\n$message";
        
        if (sendSupportEmail(
    "befitcompany.contact@gmail.com",  // Your support email address
    $_POST['email'],     // Customer's email from form
    $_POST['name'],      // Customer's name from form
    $_POST['message']    // Customer's message from form
)) {
    $success = "Your message has been sent! We'll respond within 24 hours.";
} else {
    $error = "Failed to send message. Please try again later.";
}
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support - BeFit</title>
    <link rel="stylesheet" href="../public/css/styles1.css">
    <style>
        .support-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .support-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #4A90E2;
            text-align: center;
        }
        
        .support-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .support-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .support-form input,
        .support-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .support-form textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background: #4A90E2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            background: #357ABD;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="shared-bg">
    <?php include __DIR__ . '/includes/header.php';?>
    
    <div class="support-container">
        <h1 class="support-title">Contact Support</h1>
        
        <?php if(isset($success)): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form class="support-form" method="POST">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required
                       value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="message">How can we help?</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
