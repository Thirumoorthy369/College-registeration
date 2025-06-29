<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: ' . SITE_URL . '/students/');
    exit;
}

// Initialize variables
$error = '';
$username = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        // Database verification
        $db = new Database();
        $db->query("SELECT * FROM admins WHERE username = :username");
        $db->bind(':username', $username);
        $admin = $db->single();

        if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin->id;
            $_SESSION['admin_username'] = $admin->username;
            $_SESSION['admin_role'] = $admin->role;
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            // Set cookie for "remember me" functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + 60 * 60 * 24 * 30; // 30 days

                // Store token in database
                $db->query("UPDATE admins SET remember_token = :token, token_expiry = :expiry WHERE id = :id");
                $db->bind(':token', $token);
                $db->bind(':expiry', date('Y-m-d H:i:s', $expiry));
                $db->bind(':id', $admin->id);
                $db->execute();

                // Set cookie
                setcookie('remember_token', $token, $expiry, '/');
            }

            // Redirect to intended page or dashboard
            $redirect_url = $_SESSION['redirect_url'] ?? SITE_URL . '/students/';
            unset($_SESSION['redirect_url']);
            header('Location: ' . $redirect_url);
            exit;
        } else {
            $error = "Invalid username or password";
            // Log failed login attempt
            error_log("Failed login attempt for username: $username");
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Admin Login</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <a href="forgot_password.php">Forgot password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>