<?php
// config/config.php
// Define root directory path
define('ROOT_PATH', dirname(__DIR__));

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'admin123');
define('DB_NAME', 'college_registration');

// Site configuration
define('SITE_NAME', 'College Student Registration');
define('SITE_URL', 'http://localhost/college-registration');
define('BASE_DIR', dirname(__DIR__)); // Points to college-registration folder

// Security settings
define('MAX_AGE', 30); // Maximum allowed age for registration
define('MIN_AGE', 10); // Minimum age for separate listing

// Add these to your existing config
define('ADMIN_USERNAME', 'admin'); // Change in production!
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT)); // Change in production!

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS
ini_set('session.use_strict_mode', 1);


// Admin roles
define('ADMIN_ROLE_SUPER', 1);
define('ADMIN_ROLE_STANDARD', 2);

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// CSRF protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>