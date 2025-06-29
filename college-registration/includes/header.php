<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/styles.css">
    <link rel="stylesheet" id="theme-style" href="assets/css/themes/light.css">
    
    <!-- Theme CSS -->
    <link rel="stylesheet" id="theme-style" href="<?php echo SITE_URL; ?>/assets/css/themes/light.css">
</head>
<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>/assets/images/college-logo.png" alt="College Logo" height="50">
                    <?php echo SITE_NAME; ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['admin_logged_in'])): ?>
                    <li class="nav-item">
                        <!-- <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span> -->
                     </li>
                        <li class="nav-item">
                           <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">Logout</a>
                         </li>
                     <?php else: ?>
                    <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                        </li>
                     <li class="nav-item">
            <a class="nav-link" href="<?php echo SITE_URL; ?>/login.php">Login</a>
                    </li>
              <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/students/">Students</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="themeDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-palette"></i> Theme
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item theme-option" href="#" data-theme="light">Light</a></li>
                                <li><a class="dropdown-item theme-option" href="#" data-theme="dark">Dark</a></li>
                                <li><a class="dropdown-item theme-option" href="#" data-theme="colorful">Colorful</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container my-5">