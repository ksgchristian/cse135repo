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

/* Query analytics data */
$query = "SELECT * FROM collector_events LIMIT 50";
$result = $mysqli->query($query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Analytics Data Table</title>

<style>

body {
    font-family: Arial, sans-serif;
    padding: 20px;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #ccc;
    padding: 6px;
    font-size: 12px;
}

th {
    background: #eee;
}

</style>

</head>

<body>

<h1>Collected Analytics Data</h1>

<table>

<tr>
<th>id</th>
<th>received_at</th>
<th>sid</th>
<th>page_id</th>
<th>page</th>
<th>path</th>
<th>reason</th>
<th>sent_at</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>
<td><?= htmlspecialchars($row['id']) ?></td>
<td><?= htmlspecialchars($row['received_at']) ?></td>
<td><?= htmlspecialchars($row['sid']) ?></td>
<td><?= htmlspecialchars($row['page_id']) ?></td>
<td><?= htmlspecialchars($row['page']) ?></td>
<td><?= htmlspecialchars($row['path']) ?></td>
<td><?= htmlspecialchars($row['reason']) ?></td>
<td><?= htmlspecialchars($row['sent_at']) ?></td>
</tr>

<?php endwhile; ?>

</table>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
