<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard - Biosecurity System'; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="admin-page">
    <header class="admin-top-header">
        <div class="admin-logo">
            <a href="index.php">Biosecurity Admin</a>
        </div>
        
        <div class="admin-user-menu">
            <span class="username"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="logout-link">Log Out</a>
        </div>
    </header>