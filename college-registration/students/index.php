<?php
// Define the correct base path (points to college-registration folder)
define('BASE_PATH', dirname(__DIR__, 2) . '/college-registration');

// Verify the base path is correct
if (!is_dir(BASE_PATH)) {
    die('Base path not found: ' . BASE_PATH);
}

// Load configuration
$configPath = BASE_PATH . '/config/config.php';
if (!file_exists($configPath)) {
    die("Configuration file not found at: " . $configPath);
}
require_once $configPath;

// Load database connection
$dbPath = BASE_PATH . '/includes/db.php';
if (!file_exists($dbPath)) {
    die("Database file not found at: " . $dbPath);
}
require_once $dbPath;
// Check if user is logged in (basic authentication)
// In a real application, you would implement proper authentication
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
$db = new Database();

// Get all students grouped by age
$db->query('SELECT *, TIMESTAMPDIFF(YEAR, dob, CURDATE()) AS age FROM students ORDER BY name');
$students = $db->resultSet();

// Group students by age
$young_students = [];
$regular_students = [];

foreach ($students as $student) {
    if ($student->age <= MIN_AGE) {
        $young_students[] = $student;
    } elseif ($student->age <= MAX_AGE) {
        $regular_students[] = $student;
    }
    // Students older than MAX_AGE are ignored as per registration requirements
}
require_once __DIR__ . '/../config/config.php';

// Verify config loaded
if (!defined('BASE_DIR')) {
    die('Configuration not loaded properly');
}

// Set header path
$headerPath = BASE_DIR . '/includes/header.php';
if (!file_exists($headerPath)) {
    die("Required header file missing at: " . $headerPath);
}

require_once $headerPath;
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Student Records</h1>
        <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Registration
        </a>
    </div>
    
    <?php if (empty($regular_students) && empty($young_students)): ?>
        <div class="alert alert-info">No student records found.</div>
    <?php else: ?>
        <!-- Regular Students (11-30 years) -->
        <?php if (!empty($regular_students)): ?>
            <div class="age-group">
                <h3>Students (11-<?php echo MAX_AGE; ?> years)</h3>
                <div class="table-responsive">
                    <table class="student-table table table-hover">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Degree</th>
                                <th>Year</th>
                                <th>Passing Year</th>
                                <th>Placements</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($regular_students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student->roll_no); ?></td>
                                    <td><?php echo htmlspecialchars($student->name); ?></td>
                                    <td><?php echo $student->age; ?></td>
                                    <td><?php echo htmlspecialchars($student->degree); ?></td>
                                    <td><?php echo $student->year_of_study; ?></td>
                                    <td><?php echo $student->passing_year; ?></td>
                                    <td>
                                        <?php if ($student->interested_in_placements): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/students/edit.php?id=<?php echo $student->id; ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/students/delete.php?id=<?php echo $student->id; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Young Students (10 years or younger) -->
        <?php if (!empty($young_students)): ?>
            <div class="age-group">
                <h3>Young Students (<?php echo MIN_AGE; ?> years or younger)</h3>
                <div class="table-responsive">
                    <table class="student-table table table-hover">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Degree</th>
                                <th>Year</th>
                                <th>Passing Year</th>
                                <th>Placements</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($young_students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student->roll_no); ?></td>
                                    <td><?php echo htmlspecialchars($student->name); ?></td>
                                    <td><?php echo $student->age; ?></td>
                                    <td><?php echo htmlspecialchars($student->degree); ?></td>
                                    <td><?php echo $student->year_of_study; ?></td>
                                    <td><?php echo $student->passing_year; ?></td>
                                    <td>
                                        <?php if ($student->interested_in_placements): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/students/edit.php?id=<?php echo $student->id; ?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/students/delete.php?id=<?php echo $student->id; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>