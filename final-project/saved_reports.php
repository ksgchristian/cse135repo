<?php
require_once 'auth_check.php';
requireRole(['super_admin','analyst','viewer']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved Reports</title>
</head>
<body>
    <h1>Saved Reports</h1>

    <ul>
        <li><a href="page_efficiency_report.php">Page Efficiency / Exit Pressure Report</a></li>
        <li><a href="charts.php">Event Activity Report</a></li>
        <li><a href="table.php">Raw Analytics Table</a></li>
    </ul>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
