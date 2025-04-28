<?php
session_start();
include_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if registration ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: registrations.php");
    exit;
}

$registrationId = intval($_GET['id']);
$success = false;
$error = '';

// Get registration details
$query = "SELECT * FROM registrations WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $registrationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: registrations.php");
    exit;
}

$registration = $result->fetch_assoc();
$stmt->close();

// Only allow processing pending registrations
if ($registration['status'] !== 'pending') {
    header("Location: view_registration.php?id=$registrationId");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    
    // Validate input
    if (empty($status) || !in_array($status, ['approved', 'rejected', 'additional_info'])) {
        $error = "Please select a valid status.";
    } elseif ($status !== 'approved' && empty($adminNotes)) {
        $error = "Admin notes are required when rejecting or requesting additional information.";
    } else {
        // Update registration status
        $updateQuery = "UPDATE registrations SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssi", $status, $adminNotes, $registrationId);
        
        if ($updateStmt->execute()) {
            // Log the status change
            $admin = $_SESSION['admin_username'];
            $logQuery = "INSERT INTO registration_logs (registration_id, action, status, user, notes, timestamp) VALUES (?, ?, ?, ?, ?, NOW())";
            $logStmt = $conn->prepare($logQuery);
            $action = "status_change";
            $logStmt->bind_param("issss", $registrationId, $action, $status, $admin, $adminNotes);
            $logStmt->execute();
            $logStmt->close();
            
            // Send email notification to applicant
            $to = $registration['email'];
            $subject = "Biosecurity Registration Update - " . $registration['reference_number'];
            
            $message = "
            <html>
            <head>
                <title>Biosecurity Registration Update</title>
            </head>
            <body>
                <h2>Biosecurity Registration Update</h2>
                <p>Dear " . htmlspecialchars($registration['applicant_name']) . ",</p>
                <p>Your biosecurity registration application with reference number <strong>" . $registration['reference_number'] . "</strong> has been reviewed.</p>
            ";
            
            if ($status === 'approved') {
                $message .= "
                <p>We are pleased to inform you that your application has been <strong>approved</strong>.</p>
                <p>You may now proceed with the importation or transportation of your goods in accordance with biosecurity regulations.</p>
                ";
            } elseif ($status === 'rejected') {
                $message .= "
                <p>We regret to inform you that your application has been <strong>rejected</strong> for the following reason:</p>
                <p><em>" . nl2br(htmlspecialchars($adminNotes)) . "</em></p>
                <p>If you have any questions or wish to submit a revised application, please contact our support team.</p>
                ";
            } elseif ($status === 'additional_info') {
                $message .= "
                <p>Your application requires <strong>additional information</strong> before we can complete the review process:</p>
                <p><em>" . nl2br(htmlspecialchars($adminNotes)) . "</em></p>
                <p>Please provide the requested information by replying to this email or submitting it through our portal.</p>
                ";
            }
            
            $message .= "
                <p>You can check the status of your application on our website using your reference number.</p>
                <p>Thank you for your cooperation.</p>
                <p>Regards,<br>Biosecurity Registration Team</p>
            </body>
            </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@biosecurity.org" . "\r\n";
            
            mail($to, $subject, $message, $headers);
            
            $success = true;
        } else {
            $error = "Database error: " . $updateStmt->error;
        }
        
        $updateStmt->close();
    }
}

$pageTitle = "Process Registration - Admin";
include_once 'includes/admin_header.php';
?>

<div class="admin-container">
    <?php include_once 'includes/admin_sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Process Registration</h1>
            <div class="admin-actions">
                <a href="view_registration.php?id=<?php echo $registrationId; ?>" class="btn btn-secondary">Back to Details</a>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="success-message">
                <p>Registration has been processed successfully.</p>
                <div class="action-buttons">
                    <a href="view_registration.php?id=<?php echo $registrationId; ?>" class="btn btn-primary">View Details</a>
                    <a href="registrations.php" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="process-form">
                <div class="registration-summary">
                    <h3>Registration Summary</h3>
                    
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="label">Reference:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['reference_number']); ?></span>
                        </div>
                        
                        <div class="summary-item">
                            <span class="label">Applicant:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['applicant_name']); ?></span>
                        </div>
                        
                        <div class="summary-item">
                            <span class="label">Goods Type:</span>
                            <span class="value"><?php echo htmlspecialchars(ucfirst($registration['goods_type'])); ?></span>
                        </div>
                        
                        <div class="summary-item">
                            <span class="label">Date Submitted:</span>
                            <span class="value"><?php echo date('F j, Y', strtotime($registration['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="summary-description">
                        <span class="label">Description:</span>
                        <div class="value">
                            <?php echo nl2br(htmlspecialchars($registration['goods_description'])); ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($registration['supporting_document'])): ?>
                        <div class="summary-document">
                            <a href="<?php echo '../' . htmlspecialchars($registration['supporting_document']); ?>" target="_blank" class="btn btn-sm btn-secondary">
                                View Supporting Document
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form action="process_registration.php?id=<?php echo $registrationId; ?>" method="post" class="approval-form">
                    <h3>Process Decision</h3>
                    
                    <div class="form-group">
                        <label>Decision: <span class="required">*</span></label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="status_approved" name="status" value="approved" required>
                                <label for="status_approved">Approve</label>
                            </div>
                            
                            <div class="radio-item">
                                <input type="radio" id="status_rejected" name="status" value="rejected">
                                <label for="status_rejected">Reject</label>
                            </div>
                            
                            <div class="radio-item">
                                <input type="radio" id="status_additional_info" name="status" value="additional_info">
                                <label for="status_additional_info">Request Additional Info</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_notes">Notes <span class="notes-required">(required for rejection or additional info)</span></label>
                        <textarea id="admin_notes" name="admin_notes" class="form-control" rows="5"></textarea>
                        <div class="form-help">
                            Provide detailed reasons for rejection or specific information required from the applicant.
                            These notes will be included in the email sent to the applicant.
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Submit Decision</button>
                        <a href="view_registration.php?id=<?php echo $registrationId; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>