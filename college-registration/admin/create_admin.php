<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

// At the top of admin pages
if ($_SESSION['admin_role'] !== ADMIN_ROLE_SUPER) {
    header('Location: /admin/');
    exit;
}
// Only super admins can create accounts
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== ADMIN_ROLE_SUPER) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = (int)$_POST['role'];
    
    // Validate inputs
    $errors = [];
    if (empty($username)) $errors[] = "Username required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required";
    if (strlen($password) < 8) $errors[] = "Password must be 8+ characters";
    if (!in_array($role, [ADMIN_ROLE_SUPER, ADMIN_ROLE_STANDARD])) $errors[] = "Invalid role";
    
    if (empty($errors)) {
        // Check if username/email exists
        $db = new Database();
        $db->query("SELECT id FROM admins WHERE username = :username OR email = :email");
        $db->bind(':username', $username);
        $db->bind(':email', $email);
        
        if ($db->single()) {
            $errors[] = "Username or email already exists";
        } else {
            // Create new admin
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $db->query("INSERT INTO admins (username, password_hash, email, role) 
                        VALUES (:username, :password, :email, :role)");
            $db->bind(':username', $username);
            $db->bind(':password', $hashedPassword);
            $db->bind(':email', $email);
            $db->bind(':role', $role);
            
            if ($db->execute()) {
                $_SESSION['success'] = "Admin created successfully";
                header('Location: list_admins.php');
                exit;
            } else {
                $errors[] = "Failed to create admin";
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Create New Admin</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required minlength="8">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="<?= ADMIN_ROLE_STANDARD ?>">Standard Admin</option>
                <option value="<?= ADMIN_ROLE_SUPER ?>">Super Admin</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Admin</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>