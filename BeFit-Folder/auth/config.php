<?php
// Set timezone for Lebanon (Asia/Beirut)
date_default_timezone_set('Asia/Beirut');

if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', 'AIzaSyAKA9hwIB0VYN7ltMbZF3NJbAyVOtejNME');
}

if (!defined('ALLOW_ANALYTICS')) {
    $cookiePath = '/BeFit-Folder/'; // Match your site structure
    $cookieName = 'cookie_consent';
    
    // Check for cookie in the correct path
    $cookieValue = $_COOKIE[$cookieName] ?? '';
    define('ALLOW_ANALYTICS', $cookieValue === 'accepted');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (ALLOW_ANALYTICS && !defined('GA_TRACKING_ID')) {
    define('GA_TRACKING_ID', 'UA-XXXXX-Y'); // Replace with your actual ID
}

// Your existing database configuration...
$dbHost = 'localhost';
$dbName = 'befit_db';
$dbUser = 'root';
$dbPass = '';

if (!defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
    define('PASSWORD_NEEDS_UPPERCASE', true);
    define('PASSWORD_NEEDS_NUMBER', true);
    define('PASSWORD_RESET_EXPIRY', 3600); // 1 hour in seconds
    define('TOKEN_LENGTH', 64);
}

if (!defined('SMTP_CONFIG_SET')) {
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_USER', 'befitcompany.contact@gmail.com');
    define('SMTP_PASS', 'yzqa psik rafu sszd');
    define('SMTP_PORT', 587);
    define('SMTP_SECURE', 'tls');
    define('SMTP_CONFIG_SET', true);
}

try {
    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET time_zone = '+03:00'"); //Lebanon time
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    $pdo->exec("USE `$dbName`");
    $pdo->exec("
         CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");$pdo->exec("
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        UNIQUE KEY (token)
    )
"); // Order status management
if (!function_exists('updateOrderStatuses')) {
    function updateOrderStatuses($pdo) {
        try {
            $stmt = $pdo->prepare("
                UPDATE orders 
                SET status = 'completed', 
                    status_updated_at = NOW() 
                WHERE status = 'pending' 
                AND created_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            ");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Order status update failed: " . $e->getMessage());
            return false;
        }
    }
    
    // Call it once when config is loaded
    updateOrderStatuses($pdo);
}
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
