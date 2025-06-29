<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['error_message'] = 'Please log in to edit student records.';
    header('Location: ' . SITE_URL);
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid student ID.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

$id = intval($_GET['id']);

$db = new Database();
$db->query('SELECT * FROM students WHERE id = :id');
$db->bind(':id', $id);
$student = $db->single();

if (!$student) {
    $_SESSION['error_message'] = 'Student not found.';
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

require_once __DIR__ . '../../includes/header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Student Record</h1>
        <a href="<?php echo SITE_URL; ?>/students/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    
    <div class="registration-form">
        <form id="editForm" action="<?php echo SITE_URL; ?>/students/update.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id" value="<?php echo $student->id; ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="roll_no" class="form-label">Roll Number *</label>
                    <input type="text" class="form-control" id="roll_no" name="roll_no" value="<?php echo htmlspecialchars($student->roll_no); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student->name); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student->email); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student->phone); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="year_of_study" class="form-label">Year of Study *</label>
                    <select class="form-select" id="year_of_study" name="year_of_study" required>
                        <option value="1" <?php echo $student->year_of_study == 1 ? 'selected' : ''; ?>>First Year</option>
                        <option value="2" <?php echo $student->year_of_study == 2 ? 'selected' : ''; ?>>Second Year</option>
                        <option value="3" <?php echo $student->year_of_study == 3 ? 'selected' : ''; ?>>Third Year</option>
                        <option value="4" <?php echo $student->year_of_study == 4 ? 'selected' : ''; ?>>Fourth Year</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="degree" class="form-label">Degree Program *</label>
                    <select class="form-select" id="degree" name="degree" required>
                        <option value="BSc" <?php echo $student->degree == 'BSc' ? 'selected' : ''; ?>>Bachelor of Science (BSc)</option>
                        <option value="BCA" <?php echo $student->degree == 'BCA' ? 'selected' : ''; ?>>Bachelor of Computer Applications (BCA)</option>
                        <option value="BCom" <?php echo $student->degree == 'BCom' ? 'selected' : ''; ?>>Bachelor of Commerce (BCom)</option>
                        <option value="BA" <?php echo $student->degree == 'BA' ? 'selected' : ''; ?>>Bachelor of Arts (BA)</option>
                        <option value="BBA" <?php echo $student->degree == 'BBA' ? 'selected' : ''; ?>>Bachelor of Business Administration (BBA)</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="department" class="form-label">Department *</label>
                    <select class="form-select" id="department" name="department" required>
                        <option value="Computer Science" <?php echo $student->department == 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                        <option value="Mathematics" <?php echo $student->department == 'Mathematics' ? 'selected' : ''; ?>>Mathematics</option>
                        <option value="Physics" <?php echo $student->department == 'Physics' ? 'selected' : ''; ?>>Physics</option>
                        <option value="Chemistry" <?php echo $student->department == 'Chemistry' ? 'selected' : ''; ?>>Chemistry</option>
                        <option value="Commerce" <?php echo $student->department == 'Commerce' ? 'selected' : ''; ?>>Commerce</option>
                        <option value="Arts" <?php echo $student->department == 'Arts' ? 'selected' : ''; ?>>Arts</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="passing_year" class="form-label">Expected Year of Passing *</label>
                    <input type="number" class="form-control" id="passing_year" name="passing_year" value="<?php echo $student->passing_year; ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Interested in Placements?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="interested_in_placements" id="placement_yes" value="1" <?php echo $student->interested_in_placements ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="placement_yes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="interested_in_placements" id="placement_no" value="0" <?php echo !$student->interested_in_placements ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="placement_no">No</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="dob" class="form-label">Date of Birth *</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $student->dob; ?>" required>
                    <small class="text-muted">Current age: <?php echo date_diff(date_create($student->dob), date_create('today'))->y; ?> years</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="skills" class="form-label">Skills</label>
                <textarea class="form-control" id="skills" name="skills" rows="3"><?php echo htmlspecialchars($student->skills); ?></textarea>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">Update Record</button>
                <a href="<?php echo SITE_URL; ?>/students/" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>