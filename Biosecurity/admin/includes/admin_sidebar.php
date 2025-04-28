<aside class="admin-sidebar">
    <nav class="admin-nav">
        <ul>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                    <span class="icon-dashboard"></span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="registrations.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'registrations.php' ? 'active' : ''; ?>">
                    <span class="icon-registrations"></span>
                    <span>Registrations</span>
                </a>
            </li>
            <li>
                <a href="pending.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'pending.php' ? 'active' : ''; ?>">
                    <span class="icon-pending"></span>
                    <span>Pending Review</span>
                </a>
            </li>
            <li>
                <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                    <span class="icon-reports"></span>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <span class="icon-settings"></span>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>