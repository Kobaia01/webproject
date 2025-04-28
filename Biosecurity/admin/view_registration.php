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

// Get audit logs for this registration
$logsQuery = "SELECT * FROM registration_logs WHERE registration_id = ? ORDER BY timestamp DESC";
$logsStmt = $conn->prepare($logsQuery);
$logsStmt->bind_param("i", $registrationId);
$logsStmt->execute();
$logsResult = $logsStmt->get_result();
$logs = [];

while ($log = $logsResult->fetch_assoc()) {
    $logs[] = $log;
}

$logsStmt->close();

$pageTitle = "View Registration - Admin";
include_once 'includes/admin_header.php';
?>

<div class="admin-container">
    <?php include_once 'includes/admin_sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Registration Details</h1>
            <div class="admin-actions">
                <a href="registrations.php" class="btn btn-secondary">Back to List</a>
                <?php if ($registration['status'] === 'pending'): ?>
                    <a href="process_registration.php?id=<?php echo $registrationId; ?>" class="btn btn-primary">Process Registration</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="registration-details">
            <div class="detail-header">
                <div class="detail-reference">
                    <span class="label">Reference:</span>
                    <span class="value"><?php echo htmlspecialchars($registration['reference_number']); ?></span>
                </div>
                
                <div class="detail-status">
                    <span class="label">Status:</span>
                    <span class="status-badge status-<?php echo $registration['status']; ?>">
                        <?php echo ucfirst($registration['status']); ?>
                    </span>
                </div>
            </div>
            
            <div class="detail-sections">
                <div class="detail-section">
                    <h3>Applicant Information</h3>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="label">Name:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['applicant_name']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="label">Email:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['email']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="label">Phone:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['phone']); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="label">Date Submitted:</span>
                            <span class="value"><?php echo date('F j, Y, g:i a', strtotime($registration['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h3>Goods Information</h3>
                    
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="label">Type:</span>
                            <span class="value"><?php echo htmlspecialchars(ucfirst($registration['goods_type'])); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="label">Origin:</span>
                            <span class="value"><?php echo htmlspecialchars($registration['goods_origin']); ?></span>
                        </div>
                        
                        <div class="detail-item full-width">
                            <span class="label">Description:</span>
                            <div class="value description">
                                <?php echo nl2br(htmlspecialchars($registration['goods_description'])); ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($registration['supporting_document'])): ?>
                            <div class="detail-item full-width">
                                <span class="label">Supporting Document:</span>
                                <div class="value">
                                    <a href="<?php echo '../' . htmlspecialchars($registration['supporting_document']); ?>" target="_blank" class="document-link">
                                        View Document
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!empty($registration['admin_notes'])): ?>
                    <div class="detail-section">
                        <h3>Admin Notes</h3>
                        
                        <div class="detail-item full-width">
                            <div class="value admin-notes">
                                <?php echo nl2br(htmlspecialchars($registration['admin_notes'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="detail-section">
                    <h3>Activity Log</h3>
                    
                    <?php if (count($logs) > 0): ?>
                        <div class="activity-log">
                            <?php foreach ($logs as $log): ?>
                                <div class="log-entry">
                                    <div class="log-icon status-<?php echo $log['status']; ?>"></div>
                                    <div class="log-content">
                                        <div class="log-action">
                                            <?php 
                                            $actionText = '';
                                            switch ($log['action']) {
                                                case 'status_change':
                                                    $actionText = "Status changed to " . ucfirst($log['status']);
                                                    break;
                                                case 'created':
                                                    $actionText = "Registration submitted";
                                                    break;
                                                case 'note_added':
                                                    $actionText = "Admin note added";
                                                    break;
                                                default:
                                                    $actionText = ucfirst(str_replace('_', ' ', $log['action']));
                                            }
                                            echo $actionText;
                                            ?>
                                        </div>
                                        <div class="log-meta">
                                            <span class="log-user"><?php echo htmlspecialchars($log['user']); ?></span>
                                            <span class="log-time"><?php echo date('M d, Y, g:i a', strtotime($log['timestamp'])); ?></span>
                                        </div>
                                        <?php if (!empty($log['notes'])): ?>
                                            <div class="log-notes">
                                                <?php echo nl2br(htmlspecialchars($log['notes'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-records">No activity logs found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>