<?php
session_start();
include_once '../includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = "Both username and password are required.";
    } else {
        // In a real application, you would check against a database
        // For this example, we'll use a hardcoded admin user
        $adminUsername = "admin";
        $adminPassword = "!tm3l@d2o25"; // This is from the user's requirements
        
        if ($username === $adminUsername && $password === $adminPassword) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            
            // Log successful login
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $timestamp = date('Y-m-d H:i:s');
            $logQuery = "INSERT INTO admin_logs (username, action, ip_address, timestamp) VALUES (?, ?, ?, ?)";
            $logAction = "login";
            
            $stmt = $conn->prepare($logQuery);
            $stmt->bind_param("ssss", $username, $logAction, $ipAddress, $timestamp);
            $stmt->execute();
            $stmt->close();
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username or password.";
            
            // Log failed login attempt
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $timestamp = date('Y-m-d H:i:s');
            $logQuery = "INSERT INTO admin_logs (username, action, ip_address, timestamp) VALUES (?, ?, ?, ?)";
            $logAction = "failed_login";
            
            $stmt = $conn->prepare($logQuery);
            $stmt->bind_param("ssss", $username, $logAction, $ipAddress, $timestamp);
            $stmt->execute();
            $stmt->close();
        }
    }
}

$pageTitle = "Admin Login - Biosecurity System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <h1>Biosecurity Admin</h1>
                <p>Login to access the administration dashboard</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="post" class="admin-login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                </div>
            </form>
            
            <div class="admin-login-footer">
                <a href="../index.php">Return to Main Site</a>
            </div>
        </div>
    </div>
    
    <script src="../js/script.js"></script>
</body>
</html>