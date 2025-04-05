<?php
require_once 'config.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set logout message
$_SESSION['logout_success'] = 'You have been logged out successfully';

// Redirect to home page
header('location: index.php');
exit();
?>