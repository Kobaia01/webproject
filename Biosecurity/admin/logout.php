<?php
session_start();

// Log the logout action if admin is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    include_once '../includes/config.php';
    
    $username = $_SESSION['admin_username'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $timestamp = date('Y-m-d H:i:s');
    $logAction = "logout";
    
    $logQuery = "INSERT INTO admin_logs (username, action, ip_address, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($logQuery);
    $stmt->bind_param("ssss", $username, $logAction, $ipAddress, $timestamp);
    $stmt->execute();
    $stmt->close();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>