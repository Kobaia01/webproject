<?php
session_start();
include_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Pending Review - Admin";
include_once 'includes/admin_header.php';

// Get pending registrations
$query = "SELECT * FROM registrations WHERE status = 'pending' ORDER BY created_at ASC";
$result = $conn->query($query);
?>

<div class="admin-container">
    <?php include_once 'includes/admin_sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Pending Review</h1>
            <div class="admin-actions">
                <a href="registrations.php" class="btn btn-secondary">View All Registrations</a>
            </div>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="pending-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="pending-card">
                        <div class="pending-header">
                            <div class="pending-ref"><?php echo htmlspecialchars($row['reference_number']); ?></div>
                            <div class="pending-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                        </div>
                        
                        <div class="pending-body">
                            <div class="pending-info">
                                <strong>Applicant:</strong> <?php echo htmlspecialchars($row['applicant_name']); ?>
                            </div>
                            <div class="pending-info">
                                <strong>Type:</strong> <?php echo htmlspecialchars(ucfirst($row['goods_type'])); ?>
                            </div>
                            <div class="pending-info">
                                <strong>Origin:</strong> <?php echo htmlspecialchars($row['goods_origin']); ?>
                            </div>
                            <div class="pending-description">
                                <strong>Description:</strong>
                                <p><?php echo nl2br(htmlspecialchars($row['goods_description'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="pending-actions">
                            <a href="view_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary">View Details</a>
                            <a href="process_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Process</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-records">
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <h3>No Pending Reviews</h3>
                    <p>All registrations have been processed. Check back later for new submissions.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>