<?php
require_once __DIR__ . '/config/config.php';

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: ' . SITE_URL . '/login.php');
exit;
?>