<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer classes
require_once __DIR__ . '/../vendor/autoload.php';

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    
    try {
        // Debug settings (must be set AFTER instantiating PHPMailer)
        $mail->SMTPDebug = 3; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            file_put_contents(__DIR__.'/smtp_debug.log', 
                date('Y-m-d H:i:s')." [$level] $str\n", 
                FILE_APPEND
            );
        };

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 30; // Increase timeout

        // Recipients
        $mail->setFrom(SMTP_USER, 'BeFit AI');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your BeFit Verification Code';
        $mail->Body    = "Your verification code is: <b>$code</b>";
        $mail->AltBody = "Your verification code is: $code";

        // Security settings
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $e->getMessage());
        return false;
    }
} // <-- THIS WAS MISSING! (Closing brace for sendVerificationEmail)

function sendSupportEmail($to, $from, $name, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Use the same SMTP settings as your verification email
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 30;

        // Debug settings (optional)
        $mail->SMTPDebug = 3; 
        $mail->Debugoutput = function($str, $level) {
            file_put_contents(__DIR__.'/smtp_debug.log', 
                date('Y-m-d H:i:s')." [$level] $str\n", 
                FILE_APPEND
            );
        };

        // Recipients
        $mail->setFrom(SMTP_USER, 'BeFit Support');
        $mail->addAddress($to);
        $mail->addReplyTo($from, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Support Request: ' . substr($message, 0, 30) . '...';
        $mail->Body    = "
            <h2>New Support Request</h2>
            <p><strong>From:</strong> {$name} ({$from})</p>
            <p><strong>Message:</strong></p>
            <div style='background:#f5f5f5; padding:15px; border-radius:5px;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            <p style='margin-top:20px;'>
                <a href='mailto:{$from}?subject=Re: Your Support Request' 
                   style='background:#4A90E2; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;'>
                   Reply to Customer
                </a>
            </p>
        ";
        $mail->AltBody = "New Support Request\nFrom: {$name} ({$from})\n\nMessage:\n{$message}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Support email failed: " . $e->getMessage());
        return false;
    }
}