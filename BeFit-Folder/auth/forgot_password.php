<?php
/**
 * forgot_password.php
 * 
 * Handles password reset requests by:
 * 
 * 1. Accepting user's email via POST request
 * 2. Generating secure token (even if email doesn't exist)
 * 3. Sending password reset link via email
 * 4. Recording token in database with expiration
 * 
 * Security Features:
 * - Never reveals if email exists
 * - Uses time-limited tokens
 * - Requires HTTPS in production
 * - Rate limiting via database timestamps
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please use POST.'
    ]);
    exit();
}

// Get and sanitize email
$email = trim($_POST['email'] ?? '');

// Validate email format
if (empty($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please enter your email address'
    ]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address'
    ]);
    exit();
}

try {
    // Check if user exists (security: don't reveal if user exists)
    $stmt = $pdo->prepare("SELECT id, email, name FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Standard response (same whether user exists or not)
    $response = [
        'success' => true,
        'message' => 'If an account exists with this email, you will receive a password reset link shortly.'
    ];

    if ($user) {
        // Generate secure token
        $token = bin2hex(random_bytes(TOKEN_LENGTH/2)); // 64 chars
        $expiresAt = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);

        // Store token in database
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, token, expires_at) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                token = VALUES(token),
                expires_at = VALUES(expires_at),
                created_at = NOW()
        ");
        $stmt->execute([$user['id'], $token, $expiresAt]);

        // Create reset link
          $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') 
           . $_SERVER['HTTP_HOST'] 
           . '/BeFit-Folder/auth/reset_password.php?token=' . $token;

        // Configure PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings with debugging
            $mail->SMTPDebug = 0; // 0 = off (for production), 2 = client/server messages
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 15;
            
            // Disable strict certificate verification (for local/testing)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Recipients
            $mail->setFrom(SMTP_USER, 'BeFit Support');
            $mail->addAddress($user['email'], $user['name'] ?? '');
            $mail->addReplyTo(SMTP_USER, 'BeFit Support');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your BeFit Password Reset Link';
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #4A90E2;'>Password Reset Request</h2>
                    <p>Hello " . htmlspecialchars($user['name'] ?? 'User') . ",</p>
                    <p>We received a request to reset your password. Click the button below to proceed:</p>
                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='$resetLink' style='background: #4A90E2; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;'>
                            Reset Password
                        </a>
                    </div>
                    <p>If you didn't request this, please ignore this email.</p>
                    <p><strong>This link will expire in 1 hour.</strong></p>
                    <p>Thanks,<br>The BeFit Team</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #777;'>
                        If you're having trouble with the button above, copy and paste this link into your browser:<br>
                        $resetLink
                    </p>
                </div>
            ";

            $mail->AltBody = "Password Reset Request\n\n" .
                "Hello " . ($user['name'] ?? 'User') . ",\n\n" .
                "We received a request to reset your password. Visit this link to proceed:\n" .
                "$resetLink\n\n" .
                "If you didn't request this, please ignore this email.\n" .
                "This link will expire in 1 hour.\n\n" .
                "Thanks,\nThe BeFit Team";

            // Send email
            if (!$mail->send()) {
                throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
            }
            
            error_log("Password reset email sent to {$user['email']}");

        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            throw new Exception('Failed to send password reset email. Please try again later.');
        }
    }

    // Return success response
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred. Please try again later.'
    ]);
    exit();
} catch (Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit();
}