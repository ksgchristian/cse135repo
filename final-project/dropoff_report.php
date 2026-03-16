<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth_check.php';
requireRole(['super_admin', 'analyst']);

/* Connect to database */
$mysqli = new mysqli("localhost", "analytics_user", "America2007#", "cse135_hw3");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

/*
Compute loads, exits, and drop-off rate
*/
$query = "
SELECT 
    path,
    SUM(CASE WHEN reason = 'load' THEN 1 ELSE 0 END) AS loads,
    SUM(CASE WHEN reason = 'beforeunload' THEN 1 ELSE 0 END) AS exits
FROM collector_events
GROUP BY path
ORDER BY loads DESC
";

$result = $mysqli->query($query);

if (!$result) {
    die('Query failed: ' . $mysqli->error);
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Page Drop-off Analysis</title>

<style>

body {
    font-family: Arial;
    padding: 20px;
}

table {
    border-collapse: collapse;
    width: 70%;
}

th, td {
    border: 1px solid #ccc;
    padding: 8px;
}

th {
    background: #eee;
}

</style>

</head>

<body>

<h1>Page Drop-off Analysis</h1>

<p>
Drop-off rate estimates where users leave the site.  
Calculated as: exits ÷ loads.
</p>

<table>

<tr>
<th>Page</th>
<th>Loads</th>
<th>Exits</th>
<th>Drop-off Rate</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<?php
$loads = $row['loads'];
$exits = $row['exits'];

$dropoff = 0;

if ($loads > 0) {
    $dropoff = round((min($exits / $loads,1)) * 100, 2);
}
?>

<tr>

<td><?= htmlspecialchars($row['path']) ?></td>
<td><?= $loads ?></td>
<td><?= $exits ?></td>
<td><?= $dropoff ?>%</td>

</tr>

<?php endwhile; ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
