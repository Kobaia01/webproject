<?php
session_start();
include_once 'includes/config.php';

if (!isset($_SESSION['registration_reference'])) {
    header("Location: register.php");
    exit;
}

$reference = $_SESSION['registration_reference'];
$pageTitle = "Registration Success - Biosecurity System";
include_once 'includes/header.php';
?>

<div class="container">
    <div class="success-page">
        <div class="success-icon">
            <i class="icon-success"></i>
        </div>
        
        <h1>Registration Successful</h1>
        
        <div class="success-message">
            <p>Your biosecurity registration has been submitted successfully.</p>
            <div class="reference-number">
                <span>Reference Number:</span>
                <strong><?php echo htmlspecialchars($reference); ?></strong>
            </div>
            
            <div class="success-details">
                <p>Please save your reference number for tracking your application status.</p>
                <p>A confirmation email has been sent to your registered email address.</p>
                <p>Our team will review your application and notify you of any updates.</p>
            </div>
        </div>
        
        <div class="next-steps">
            <h3>What happens next?</h3>
            <ol>
                <li>Our biosecurity team will review your application.</li>
                <li>You may be contacted if additional information is required.</li>
                <li>Upon approval, you will receive an email notification with further instructions.</li>
            </ol>
        </div>
        
        <div class="action-buttons">
            <a href="status.php" class="btn btn-primary">Check Application Status</a>
            <a href="index.php" class="btn btn-secondary">Return to Home</a>
        </div>
    </div>
</div>

<?php
// Clear the registration reference from session after displaying
unset($_SESSION['registration_reference']);

include_once 'includes/footer.php';
?>