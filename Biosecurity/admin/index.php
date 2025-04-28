<?php
session_start();
include_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Admin Dashboard - Biosecurity System";
include_once 'includes/admin_header.php';

// Get registration statistics
$totalQuery = "SELECT COUNT(*) as total FROM registrations";
$pendingQuery = "SELECT COUNT(*) as pending FROM registrations WHERE status = 'pending'";
$approvedQuery = "SELECT COUNT(*) as approved FROM registrations WHERE status = 'approved'";
$rejectedQuery = "SELECT COUNT(*) as rejected FROM registrations WHERE status = 'rejected'";

$totalResult = $conn->query($totalQuery);
$pendingResult = $conn->query($pendingQuery);
$approvedResult = $conn->query($approvedQuery);
$rejectedResult = $conn->query($rejectedQuery);

$totalCount = $totalResult->fetch_assoc()['total'];
$pendingCount = $pendingResult->fetch_assoc()['pending'];
$approvedCount = $approvedResult->fetch_assoc()['approved'];
$rejectedCount = $rejectedResult->fetch_assoc()['rejected'];

// Get recent registrations
$recentQuery = "SELECT * FROM registrations ORDER BY created_at DESC LIMIT 5";
$recentResult = $conn->query($recentQuery);
?>

<div class="admin-container">
    <?php include_once 'includes/admin_sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-actions">
                <a href="registrations.php" class="btn btn-primary">View All Registrations</a>
            </div>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-value"><?php echo $totalCount; ?></div>
                <div class="stat-label">Total Registrations</div>
            </div>
            
            <div class="stat-card pending">
                <div class="stat-value"><?php echo $pendingCount; ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            
            <div class="stat-card approved">
                <div class="stat-value"><?php echo $approvedCount; ?></div>
                <div class="stat-label">Approved</div>
            </div>
            
            <div class="stat-card rejected">
                <div class="stat-value"><?php echo $rejectedCount; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
        
        <div class="dashboard-section">
            <h2>Recent Registrations</h2>
            
            <?php if ($recentResult->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Applicant</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $recentResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['goods_type']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-records">No recent registrations found.</p>
            <?php endif; ?>
            
            <div class="view-all">
                <a href="registrations.php" class="btn btn-secondary">View All Registrations</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>