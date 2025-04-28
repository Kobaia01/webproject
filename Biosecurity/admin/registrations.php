<?php
session_start();
include_once '../includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pageTitle = "All Registrations - Admin";
include_once 'includes/admin_header.php';

// Set default filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query based on filters
$query = "SELECT * FROM registrations WHERE 1=1";

if ($statusFilter !== 'all') {
    $query .= " AND status = '$statusFilter'";
}

if (!empty($searchTerm)) {
    $query .= " AND (reference_number LIKE '%$searchTerm%' OR applicant_name LIKE '%$searchTerm%' OR email LIKE '%$searchTerm%')";
}

$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
?>

<div class="admin-container">
    <?php include_once 'includes/admin_sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>All Registrations</h1>
            <div class="admin-actions">
                <a href="export.php" class="btn btn-secondary">Export Data</a>
            </div>
        </div>
        
        <div class="filter-section">
            <form action="registrations.php" method="get" class="filter-form">
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="additional_info" <?php echo $statusFilter === 'additional_info' ? 'selected' : ''; ?>>Additional Info</option>
                    </select>
                </div>
                
                <div class="search-group">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search reference, name, or email" class="form-control">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <?php if ($result->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Goods Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($row['goods_type'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <a href="view_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a href="process_registration.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-secondary">Process</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-records">
                    <p>No registrations found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'includes/admin_footer.php'; ?>