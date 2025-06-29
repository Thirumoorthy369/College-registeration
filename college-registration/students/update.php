<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error_message'] = 'Please log in to update student records.';
    header('Location: ' . SITE_URL);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

// Validate ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    $_SESSION['error_message'] = 'Invalid student ID.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

$id = intval($_POST['id']);

// Sanitize and validate input data
$roll_no = trim($_POST['roll_no'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$dob = $_POST['dob'] ?? '';
$year_of_study = intval($_POST['year_of_study'] ?? 0);
$degree = trim($_POST['degree'] ?? '');
$department = trim($_POST['department'] ?? '');
$passing_year = intval($_POST['passing_year'] ?? 0);
$interested_in_placements = isset($_POST['interested_in_placements']) ? (int)$_POST['interested_in_placements'] : 0;
$skills = trim($_POST['skills'] ?? '');

// Validate required fields
if (empty($roll_no) || empty($name) || empty($email) || empty($phone) || empty($dob) || 
    $year_of_study < 1 || $year_of_study > 4 || empty($degree) || empty($department) || 
    $passing_year < date('Y') || $passing_year > (date('Y') + 4)) {
    $_SESSION['error_message'] = 'Please fill in all required fields with valid data.';
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = 'Please provide a valid email address.';
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}

// Validate phone number
if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    $_SESSION['error_message'] = 'Please provide a valid phone number.';
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}

// Validate date of birth and calculate age
$dob_date = new DateTime($dob);
$today = new DateTime();
$age = $today->diff($dob_date)->y;

if ($age > MAX_AGE) {
    $_SESSION['error_message'] = 'Student age cannot be more than ' . MAX_AGE . ' years.';
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}

$db = new Database();

// Check if roll number or email already exists for another student
$db->query('SELECT id FROM students WHERE (roll_no = :roll_no OR email = :email) AND id != :id');
$db->bind(':roll_no', $roll_no);
$db->bind(':email', $email);
$db->bind(':id', $id);

if ($db->single()) {
    $_SESSION['error_message'] = 'Another student with this roll number or email already exists.';
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}

// Update student record
try {
    $db->query('UPDATE students SET 
                roll_no = :roll_no, 
                name = :name, 
                email = :email, 
                phone = :phone, 
                dob = :dob, 
                year_of_study = :year_of_study, 
                degree = :degree, 
                department = :department, 
                passing_year = :passing_year, 
                interested_in_placements = :interested_in_placements, 
                skills = :skills 
                WHERE id = :id');
    
    $db->bind(':roll_no', $roll_no);
    $db->bind(':name', $name);
    $db->bind(':email', $email);
    $db->bind(':phone', $phone);
    $db->bind(':dob', $dob);
    $db->bind(':year_of_study', $year_of_study);
    $db->bind(':degree', $degree);
    $db->bind(':department', $department);
    $db->bind(':passing_year', $passing_year);
    $db->bind(':interested_in_placements', $interested_in_placements);
    $db->bind(':skills', $skills);
    $db->bind(':id', $id);
    
    $db->execute();
    
    $_SESSION['success_message'] = 'Student record updated successfully!';
    header('Location: ' . SITE_URL . '/students/');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    header('Location: ' . SITE_URL . '/students/edit.php?id=' . $id);
    exit;
}
?>