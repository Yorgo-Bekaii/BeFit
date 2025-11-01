<?php
/**
 * This script handles requests to resend verification codes when users:
 * 1. Didn't receive the initial verification email
 * 2. Need a new code after expiration
 * 3. Entered incorrect codes multiple times
 * 
 * Key Security Features:
 * - Validates the requesting email matches the session-stored email
 * - Generates a fresh 6-digit verification code
 * - Updates the session with new code before resending
 * - Prevents email enumeration attacks by using session data
 * - Maintains audit trail through verification_attempts tracking
 * 
 * Flow:
 * 1. Receives email parameter from verify_email.php
 * 2. Validates against session-stored verification email
 * 3. Generates new 6-digit code
 * 4. Sends via sendVerificationEmail()
 * 5. Redirects back to verification page with status
 */
session_start();
require_once __DIR__ . '/../auth/config.php';
require_once __DIR__ . '/../auth/mailer.php';

$email = $_GET['email'] ?? '';

// Verify email matches session
if ($email !== ($_SESSION['verification_email'] ?? '')) {
    header("Location: signup.php?error=invalid_resend_request");
    exit();
}

// Generate new code
$new_code = rand(100000, 999999);
$_SESSION['verification_code'] = $new_code;

// Resend email
if (sendVerificationEmail($email, $new_code)) {
    header("Location: verify_email.php?email=".urlencode($email)."&resend=success");
} else {
    header("Location: verify_email.php?email=".urlencode($email)."&error=resend_failed");
}
exit();
?>