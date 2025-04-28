<?php
session_start();
include_once 'includes/config.php';
$pageTitle = "Check Status - Biosecurity System";
include_once 'includes/header.php';

$status = null;
$registration = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $referenceNumber = trim($_POST['reference_number']);
    
    if (empty($referenceNumber)) {
        $error = "Please enter a reference number.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM registrations WHERE reference_number = ?");
        $stmt->bind_param("s", $referenceNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $registration = $result->fetch_assoc();
            $status = $registration['status'];
        } else {
            $error = "No registration found with this reference number. Please check and try again.";
        }
        
        $stmt->close();
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1>Check Application Status</h1>
        <p>Enter your reference number to check the status of your biosecurity registration</p>
    </div>
    
    <div class="status-checker">
        <?php if ($registration === null): ?>
            <form action="status.php" method="post" class="status-form">
                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="reference_number">Reference Number</label>
                    <input type="text" id="reference_number" name="reference_number" class="form-control" placeholder="e.g. BIO-ABC12345" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Check Status</button>
                </div>
            </form>
        <?php else: ?>
            <div class="status-result">
                <h2>Application Details</h2>
                
                <div class="result-section">
                    <div class="result-row">
                        <span class="label">Reference Number:</span>
                        <span class="value"><?php echo htmlspecialchars($registration['reference_number']); ?></span>
                    </div>
                    
                    <div class="result-row">
                        <span class="label">Applicant:</span>
                        <span class="value"><?php echo htmlspecialchars($registration['applicant_name']); ?></span>
                    </div>
                    
                    <div class="result-row">
                        <span class="label">Date Submitted:</span>
                        <span class="value"><?php echo date('F j, Y', strtotime($registration['created_at'])); ?></span>
                    </div>
                    
                    <div class="result-row">
                        <span class="label">Status:</span>
                        <span class="value status-badge status-<?php echo $registration['status']; ?>">
                            <?php echo ucfirst($registration['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="status-details">
                    <?php if ($registration['status'] === 'pending'): ?>
                        <div class="status-message pending">
                            <p>Your application is currently under review by our biosecurity team.</p>
                            <p>The standard processing time is 5-7 business days.</p>
                        </div>
                    <?php elseif ($registration['status'] === 'approved'): ?>
                        <div class="status-message approved">
                            <p>Your application has been approved.</p>
                            <p>The approval notification has been sent to your registered email address.</p>
                            <p>If you have any questions, please contact our support team.</p>
                        </div>
                    <?php elseif ($registration['status'] === 'rejected'): ?>
                        <div class="status-message rejected">
                            <p>Your application has been rejected.</p>
                            <p>Reason: <?php echo htmlspecialchars($registration['admin_notes'] ?? 'Not specified'); ?></p>
                            <p>Please contact our support team for further assistance.</p>
                        </div>
                    <?php elseif ($registration['status'] === 'additional_info'): ?>
                        <div class="status-message additional-info">
                            <p>Additional information is required for your application.</p>
                            <p>Details have been sent to your registered email address.</p>
                            <p>Please respond promptly to avoid delays in processing.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="status-actions">
                    <a href="status.php" class="btn btn-secondary">Check Another Application</a>
                    <a href="index.php" class="btn btn-primary">Return to Home</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>