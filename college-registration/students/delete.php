<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error_message'] = 'Please log in to delete student records.';
    header('Location: ' . SITE_URL);
    exit;
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid student ID.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

$id = intval($_GET['id']);

$db = new Database();

// Check if student exists
$db->query('SELECT id FROM students WHERE id = :id');
$db->bind(':id', $id);

if (!$db->single()) {
    $_SESSION['error_message'] = 'Student not found.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

// Delete student
try {
    $db->query('DELETE FROM students WHERE id = :id');
    $db->bind(':id', $id);
    $db->execute();
    
    $_SESSION['success_message'] = 'Student record deleted successfully!';
    header('Location: ' . SITE_URL . '/students/');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    header('Location: ' . SITE_URL . '/students/');
    exit;
}
?>