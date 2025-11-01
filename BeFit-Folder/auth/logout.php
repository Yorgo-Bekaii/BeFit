<?php
require_once __DIR__ . '/config.php';  // Use absolute path
session_start();  // Add this - crucial for session handling

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page with correct path
header("Location: ../index.php");  
exit();
?>