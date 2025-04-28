<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Biosecurity Registration System'; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">ALD Biosecurity</a>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li><a href="register.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : ''; ?>">Register</a></li>
                        <li><a href="status.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'status.php' ? 'active' : ''; ?>">Check Status</a></li>
                        <li><a href="#" class="<?php echo basename($_SERVER['PHP_SELF']) === 'help.php' ? 'active' : ''; ?>">Help</a></li>
                    </ul>
                </nav>
                
                <div class="admin-link">
                    <a href="admin/login.php">Admin Login</a>
                </div>
            </div>
        </div>
    </header>