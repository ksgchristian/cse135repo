<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'auth_check.php';
requireRole(['super_admin', 'analyst']);

$mysqli = new mysqli("localhost", "analytics_user", "America2007#", "cse135_hw3");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

/* Summary stats */
$totalViewsResult = $mysqli->query("
    SELECT COUNT(*) AS total_views
    FROM collector_events
    WHERE reason = 'load'
      AND path LIKE '%product-detail%'
");
$totalViews = $totalViewsResult->fetch_assoc()['total_views'] ?? 0;

$uniqueProductsResult = $mysqli->query("
    SELECT COUNT(DISTINCT path) AS unique_products
    FROM collector_events
    WHERE reason = 'load'
      AND path LIKE '%product-detail%'
");
$uniqueProducts = $uniqueProductsResult->fetch_assoc()['unique_products'] ?? 0;

/* Main query */
$query = "
SELECT 
    path,
    COUNT(*) AS views
FROM collector_events
WHERE reason = 'load'
  AND path LIKE '%product-detail%'
GROUP BY path
ORDER BY views DESC
";

$result = $mysqli->query($query);

if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

$rows = [];
$chartLabels = [];
$chartData = [];

while ($row = $result->fetch_assoc()) {
    $productPath = $row['path'];
    $views = (int)$row['views'];

    preg_match('/id=(\d+)/', $productPath, $matches);
    $productId = $matches[1] ?? 'Unknown';
    $productLabel = "Product " . $productId;

    $row['product_label'] = $productLabel;
    $rows[] = $row;

    $chartLabels[] = $productLabel;
    $chartData[] = $views;
}

$topProduct = count($rows) > 0 ? $rows[0]['product_label'] : 'No product data';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Engagement Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 24px;
            background: #f8f9fb;
            color: #222;
        }

        h1, h2 {
            margin-bottom: 10px;
        }

        .cards {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 16px;
            min-width: 200px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .card .label {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
        }

        .card .value {
            font-size: 22px;
            font-weight: bold;
        }

        .section {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f0f2f5;
        }

        .actions {
            margin: 16px 0;
        }

        .button {
            display: inline-block;
            padding: 10px 14px;
            border: 1px solid #aaa;
            border-radius: 8px;
            text-decoration: none;
            color: #222;
            background: #fff;
            margin-right: 10px;
            cursor: pointer;
        }

        @media print {
            .actions {
                display: none;
            }
            body {
                background: white;
            }
            .section, .card {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<h1>Product Engagement Report</h1>
<p>
This report analyzes which product pages receive the most attention based on page load events.
Higher view counts indicate stronger product interest.
</p>

<div class="actions">
    <button class="button" onclick="window.print()">Export Report (PDF)</button>
    <a class="button" href="dashboard.php">Back to Dashboard</a>
</div>

<div class="cards">
    <div class="card">
        <div class="label">Total Product Views</div>
        <div class="value"><?= htmlspecialchars((string)$totalViews) ?></div>
    </div>

    <div class="card">
        <div class="label">Unique Product Pages</div>
        <div class="value"><?= htmlspecialchars((string)$uniqueProducts) ?></div>
    </div>

    <div class="card">
        <div class="label">Top Product</div>
        <div class="value" style="font-size:16px;"><?= htmlspecialchars((string)$topProduct) ?></div>
    </div>
</div>

<div class="section">
    <h2>Most Viewed Products</h2>
    <p>
        This chart shows which product detail pages attracted the most visits.
    </p>
    <canvas id="productChart" height="120"></canvas>
</div>

<div class="section">
    <h2>Product View Table</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Original Path</th>
            <th>Views</th>
        </tr>

        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars((string)$row['product_label']) ?></td>
                <td><?= htmlspecialchars((string)$row['path']) ?></td>
                <td><?= htmlspecialchars((string)$row['views']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h2>Analyst Notes</h2>
    <p>
        This report highlights which product pages generate the most user interest. Products with higher view counts may indicate stronger demand,
        better discoverability, or more successful placement in site navigation. Lower-view products may need improved visibility or promotion.
    </p>
</div>

<script>
const labels = <?= json_encode($chartLabels) ?>;
const data = <?= json_encode($chartData) ?>;

new Chart(document.getElementById('productChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Product Views',
            data: data,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
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
