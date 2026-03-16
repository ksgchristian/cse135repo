<?php
require_once 'auth_check.php';
requireLogin();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        ul {
            line-height: 2;
        }
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></p>
    <p>Role: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

    <ul>
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <li><a href="admin.php">Manage Users</a></li>
            <li><a href="page_efficiency_report.php">Page Efficiency Report</a></li>
            <li><a href="product_engagement_report.php">Product Engagement Report</a></li>
            <li><a href="session_activity_report.php">Session Activity Report</a></li>
            <li><a href="table.php">Analytics Table</a></li>
            <li><a href="charts.php">Event Activity Report</a></li>
            <li><a href="saved_reports.php">Saved Reports</a></li>

        <?php elseif ($_SESSION['role'] === 'analyst'): ?>
            <li><a href="page_efficiency_report.php">Page Efficiency Report</a></li>
            <li><a href="product_engagement_report.php">Product Engagement Report</a></li>
            <li><a href="session_activity_report.php">Session Activity Report</a></li>
            <li><a href="table.php">Analytics Table</a></li>
            <li><a href="charts.php">Event Activity Report</a></li>
            <li><a href="saved_reports.php">Saved Reports</a></li>

        <?php elseif ($_SESSION['role'] === 'viewer'): ?>
            <li><a href="saved_reports.php">Saved Reports</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
