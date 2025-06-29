<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';

// Only super admins can access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_role'] !== ADMIN_ROLE_SUPER) {
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

// Get admin ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid admin ID.';
    header('Location: ' . SITE_URL . '/admin/list_admins.php');
    exit;
}
$admin_id = (int)$_GET['id'];

// Prevent super admin from editing themselves (role or username)
$self_edit = ($admin_id === (int)$_SESSION['admin_id']);

$db = new Database();
$db->query('SELECT * FROM admins WHERE id = :id');
$db->bind(':id', $admin_id);
$admin = $db->single();
if (!$admin) {
    $_SESSION['error'] = 'Admin not found.';
    header('Location: ' . SITE_URL . '/admin/list_admins.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $role = isset($_POST['role']) ? (int)$_POST['role'] : $admin->role;
    $password = $_POST['password'] ?? '';
    $errors = [];

    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if (!$self_edit && !in_array($role, [ADMIN_ROLE_SUPER, ADMIN_ROLE_STANDARD])) {
        $errors[] = 'Invalid role.';
    }
    // Prevent demoting self from super admin
    if ($self_edit && $admin->role == ADMIN_ROLE_SUPER && $role != ADMIN_ROLE_SUPER) {
        $errors[] = 'You cannot change your own role.';
    }
    // Check for username uniqueness
    $db->query('SELECT id FROM admins WHERE username = :username AND id != :id');
    $db->bind(':username', $username);
    $db->bind(':id', $admin_id);
    if ($db->single()) {
        $errors[] = 'Username already exists.';
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $db->query('UPDATE admins SET username = :username, password = :password, role = :role WHERE id = :id');
            $db->bind(':password', $hashed);
        } else {
            $db->query('UPDATE admins SET username = :username, role = :role WHERE id = :id');
        }
        $db->bind(':username', $username);
        $db->bind(':role', $role);
        $db->bind(':id', $admin_id);
        $db->execute();
        $_SESSION['success'] = 'Admin updated successfully.';
        header('Location: ' . SITE_URL . '/admin/list_admins.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container mt-4">
    <h2>Edit Admin</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin->username) ?>" required <?= $self_edit ? 'readonly' : '' ?>>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" <?= $self_edit ? 'disabled' : '' ?>>
                <option value="<?= ADMIN_ROLE_SUPER ?>" <?= $admin->role == ADMIN_ROLE_SUPER ? 'selected' : '' ?>>Super Admin</option>
                <option value="<?= ADMIN_ROLE_STANDARD ?>" <?= $admin->role == ADMIN_ROLE_STANDARD ? 'selected' : '' ?>>Standard Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Admin</button>
        <a href="list_admins.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
