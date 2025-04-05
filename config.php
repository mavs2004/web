<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'gymlife_db');

// Create database connection
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Site-wide settings
define('SITE_NAME', 'GymLife');
define('SITE_URL', 'http://localhost/gymlife');

// GCash API configuration (replace with actual credentials when available)
define('GCASH_API_KEY', 'your_api_key_here');
define('GCASH_API_SECRET', 'your_api_secret_here');
define('GCASH_API_URL', 'https://api.gcash.com/v1'); // Example URL

/**
 * Validates a GCash reference number
 * 
 * @param string $reference_number The reference number to validate
 * @return array ['valid' => bool, 'message' => string]
 */
function validateGCashReference($reference_number) {
    // Basic format validation
    if (!preg_match('/^[0-9]{13}$/', $reference_number)) {
        return [
            'valid' => false,
            'message' => 'Invalid reference number format. Must be 13 digits.'
        ];
    }

    // Check first digit (should be 1-9)
    if ($reference_number[0] === '0') {
        return [
            'valid' => false,
            'message' => 'Reference number cannot start with 0.'
        ];
    }

    // Validate checksum (Luhn algorithm)
    $digits = str_split($reference_number);
    $sum = 0;
    $length = count($digits);
    $parity = $length % 2;

    for ($i = $length - 1; $i >= 0; $i--) {
        $digit = intval($digits[$i]);
        if ($i % 2 === $parity) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }

    if ($sum % 10 !== 0) {
        return [
            'valid' => false,
            'message' => 'Invalid reference number checksum.'
        ];
    }

    // Additional validation rules
    $timestamp = substr($reference_number, 1, 10);
    $current_time = time();
    $ref_time = intval($timestamp);
    
    // Check if reference number timestamp is within last 24 hours
    if ($ref_time > $current_time || ($current_time - $ref_time) > 86400) {
        return [
            'valid' => false,
            'message' => 'Reference number has expired or is invalid.'
        ];
    }

    return [
        'valid' => true,
        'message' => 'Reference number is valid.'
    ];
}

// Admin authentication functions (updated for plain text passwords)
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: ../adminlogin.php");
        exit();
    }
}

function adminLogout() {
    session_unset();
    session_destroy();
    header("Location: adminlogin.php");
    exit();
}

// Flash message function
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name.'_class'] = $class;
        } elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name.'_class']) ? $_SESSION[$name.'_class'] : $class;
            echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name.'_class']);
        }
    }
}
?>