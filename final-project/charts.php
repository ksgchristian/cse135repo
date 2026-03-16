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
$query = "
SELECT reason, COUNT(*) AS total
FROM collector_events
GROUP BY reason
ORDER BY total DESC
";

$result = $mysqli->query($query);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['reason'];
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Analytics Charts</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body {
    font-family: Arial, sans-serif;
    padding: 20px;
}

canvas {
    max-width: 700px;
}

</style>

</head>

<body>

<h1>Analytics Event Types</h1>

<canvas id="chart"></canvas>

<br><br>

<a href="dashboard.php">Back to Dashboard</a>

<script>

const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Event Count',
            data: <?php echo json_encode($data); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

</script>

</body>
</html>
