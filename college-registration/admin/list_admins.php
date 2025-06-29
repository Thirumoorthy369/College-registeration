<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db.php';

// At the top of admin pages
if ($_SESSION['admin_role'] !== ADMIN_ROLE_SUPER) {
    header('Location: /admin/');
    exit;
}
// Only admins can view this page
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Get all admins (only super admins see all)
$db = new Database();
if ($_SESSION['admin_role'] === ADMIN_ROLE_SUPER) {
    $db->query("SELECT * FROM admins ORDER BY username");
} else {
    $db->query("SELECT * FROM admins WHERE id = :id ORDER BY username");
    $db->bind(':id', $_SESSION['admin_id']);
}
$admins = $db->resultSet();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <h2>Admin Accounts</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="mb-3">
        <?php if ($_SESSION['admin_role'] === ADMIN_ROLE_SUPER): ?>
            <a href="create_admin.php" class="btn btn-primary">Create New Admin</a>
        <?php endif; ?>
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?= htmlspecialchars($admin->username) ?></td>
                    <td><?= htmlspecialchars($admin->email) ?></td>
                    <td>
                        <?= $admin->role === ADMIN_ROLE_SUPER ? 'Super Admin' : 'Standard Admin' ?>
                        <?= $admin->id === $_SESSION['admin_id'] ? '(You)' : '' ?>
                    </td>
                    <td>
                        <?php if ($_SESSION['admin_role'] === ADMIN_ROLE_SUPER || $admin->id === $_SESSION['admin_id']): ?>
                            <a href="edit_admin.php?id=<?= $admin->id ?>" class="btn btn-sm btn-warning">Edit</a>
                        <?php endif; ?>
                        <?php if ($_SESSION['admin_role'] === ADMIN_ROLE_SUPER && $admin->id !== $_SESSION['admin_id']): ?>
                            <a href="delete_admin.php?id=<?= $admin->id ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>