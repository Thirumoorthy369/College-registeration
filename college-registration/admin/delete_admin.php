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

// Prevent super admin from deleting themselves
if ($admin_id === (int)$_SESSION['admin_id']) {
    $_SESSION['error'] = 'You cannot delete your own account.';
    header('Location: ' . SITE_URL . '/admin/list_admins.php');
    exit;
}

$db = new Database();
$db->query('SELECT * FROM admins WHERE id = :id');
$db->bind(':id', $admin_id);
$admin = $db->single();
if (!$admin) {
    $_SESSION['error'] = 'Admin not found.';
    header('Location: ' . SITE_URL . '/admin/list_admins.php');
    exit;
}

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $db->query('DELETE FROM admins WHERE id = :id');
        $db->bind(':id', $admin_id);
        $db->execute();
        $_SESSION['success'] = 'Admin deleted successfully.';
    } else {
        $_SESSION['info'] = 'Deletion cancelled.';
    }
    header('Location: ' . SITE_URL . '/admin/list_admins.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container mt-4">
    <h2>Delete Admin</h2>
    <div class="alert alert-warning">
        <strong>Warning:</strong> You are about to delete the admin <b><?= htmlspecialchars($admin->username) ?></b>.<br>
        This action cannot be undone. Are you sure?
    </div>
    <form method="post">
        <button type="submit" name="confirm" value="yes" class="btn btn-danger">Yes, Delete</button>
        <a href="list_admins.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
