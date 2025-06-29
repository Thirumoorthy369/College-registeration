<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL);
    exit;
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Invalid CSRF token.';
    header('Location: ' . SITE_URL);
    exit;
}

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
    header('Location: ' . SITE_URL);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_message'] = 'Please provide a valid email address.';
    header('Location: ' . SITE_URL);
    exit;
}

// Validate phone number (basic validation)
if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
    $_SESSION['error_message'] = 'Please provide a valid phone number.';
    header('Location: ' . SITE_URL);
    exit;
}

// Validate date of birth and calculate age
$dob_date = new DateTime($dob);
$today = new DateTime();
$age = $today->diff($dob_date)->y;

if ($age > MAX_AGE) {
    $_SESSION['error_message'] = 'Sorry, registration is only allowed for students aged ' . MAX_AGE . ' or younger.';
    header('Location: ' . SITE_URL);
    exit;
}

// Check if roll number or email already exists
$db = new Database();
$db->query('SELECT id FROM students WHERE roll_no = :roll_no OR email = :email');
$db->bind(':roll_no', $roll_no);
$db->bind(':email', $email);

if ($db->single()) {
    $_SESSION['error_message'] = 'A student with this roll number or email already exists.';
    header('Location: ' . SITE_URL);
    exit;
}

// Insert student into database
try {
    $db->query('INSERT INTO students (roll_no, name, email, phone, dob, year_of_study, degree, department, passing_year, interested_in_placements, skills) 
                VALUES (:roll_no, :name, :email, :phone, :dob, :year_of_study, :degree, :department, :passing_year, :interested_in_placements, :skills)');
    
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
    
    $db->execute();
    
    $_SESSION['success_message'] = 'Registration successful! Your details have been saved.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    header('Location: ' . SITE_URL);
    exit;
}
?>