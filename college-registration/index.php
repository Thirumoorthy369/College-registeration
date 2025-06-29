<?php
require_once __DIR__ . '/includes/header.php';

// Check for success message
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['success_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success_message']);
}

// Check for error message
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            ' . $_SESSION['error_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['error_message']);
}
?>

<div class="registration-form">
    <div class="form-header">
        <h2>Student Registration</h2>
        <p class="text-muted">Fill in the form below to register as a student</p>
    </div>
    
    <form id="registrationForm" action="<?php echo SITE_URL; ?>/process/register.php" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="roll_no" class="form-label">Roll Number *</label>
                <input type="text" class="form-control" id="roll_no" name="roll_no" required>
                <div class="invalid-feedback">Please provide a valid roll number.</div>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" class="form-control" id="name" name="name" required>
                <div class="invalid-feedback">Please provide your full name.</div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Please provide a valid email address.</div>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number *</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
                <div class="invalid-feedback">Please provide a valid phone number.</div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dob" class="form-label">Date of Birth *</label>
                <input type="date" class="form-control" id="dob" name="dob" required>
                <div class="invalid-feedback">Please provide your date of birth.</div>
                <small class="text-muted">You must be between 10 and 30 years old to register.</small>
            </div>
            <div class="col-md-6">
                <label for="year_of_study" class="form-label">Year of Study *</label>
                <select class="form-select" id="year_of_study" name="year_of_study" required>
                    <option value="" selected disabled>Select year</option>
                    <option value="1">First Year</option>
                    <option value="2">Second Year</option>
                    <option value="3">Third Year</option>
                    <option value="4">Fourth Year</option>
                </select>
                <div class="invalid-feedback">Please select your year of study.</div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="degree" class="form-label">Degree Program *</label>
                <select class="form-select" id="degree" name="degree" required>
                    <option value="" selected disabled>Select degree</option>
                    <option value="BSc">Bachelor of Science (BSc)</option>
                    <option value="BCA">Bachelor of Computer Applications (BCA)</option>
                    <option value="BCom">Bachelor of Commerce (BCom)</option>
                    <option value="BA">Bachelor of Arts (BA)</option>
                    <option value="BBA">Bachelor of Business Administration (BBA)</option>
                </select>
                <div class="invalid-feedback">Please select your degree program.</div>
            </div>
            <div class="col-md-6">
                <label for="department" class="form-label">Department *</label>
                <select class="form-select" id="department" name="department" required>
                    <option value="" selected disabled>Select department</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                    <option value="Commerce">Commerce</option>
                    <option value="Arts">Arts</option>
                </select>
                <div class="invalid-feedback">Please select your department.</div>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="passing_year" class="form-label">Expected Year of Passing *</label>
                <input type="number" class="form-control" id="passing_year" name="passing_year" min="<?php echo date('Y'); ?>" max="<?php echo date('Y') + 4; ?>" required>
                <div class="invalid-feedback">Please provide a valid passing year.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Interested in Placements?</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interested_in_placements" id="placement_yes" value="1" checked>
                    <label class="form-check-label" for="placement_yes">Yes</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="interested_in_placements" id="placement_no" value="0">
                    <label class="form-check-label" for="placement_no">No</label>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="skills" class="form-label">Skills (Optional)</label>
            <textarea class="form-control" id="skills" name="skills" rows="3" placeholder="List your skills separated by commas"></textarea>
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Register</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>