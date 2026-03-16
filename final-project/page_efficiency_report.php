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
$totalEventsResult = $mysqli->query("SELECT COUNT(*) AS total_events FROM collector_events");
$totalEvents = $totalEventsResult->fetch_assoc()['total_events'] ?? 0;

$uniqueVisitorsResult = $mysqli->query("SELECT COUNT(DISTINCT sid) AS unique_visitors FROM collector_events");
$uniqueVisitors = $uniqueVisitorsResult->fetch_assoc()['unique_visitors'] ?? 0;

/* Main report query */
$query = "
SELECT 
    SUBSTRING_INDEX(path, '?', 1) AS base_path,
    SUM(CASE WHEN reason = 'load' THEN 1 ELSE 0 END) AS loads,
    SUM(CASE WHEN reason = 'beforeunload' THEN 1 ELSE 0 END) AS exits
FROM collector_events
GROUP BY base_path
HAVING loads > 0
ORDER BY loads DESC
";

$result = $mysqli->query($query);

if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

$rows = [];
$chartLabels = [];
$loadData = [];
$exitData = [];

while ($row = $result->fetch_assoc()) {
    $loads = (int)$row['loads'];
    $exits = (int)$row['exits'];

$exitPressure = $loads > 0 ? ($exits / $loads) : 0;
    $exitPressurePercent = round($exitPressure * 100, 2);
    $retentionPercent = round((1 - $exitPressure) * 100, 2);

    $row['exit_pressure_percent'] = $exitPressurePercent;
    $row['retention_percent'] = $retentionPercent;

    $rows[] = $row;

    $label = $row['base_path'];
    if ($label == '/' || $label == '') {
        $label = 'Homepage';
    }

    $chartLabels[] = $label;
    $loadData[] = $loads;
    $exitData[] = $exits;
}

$topPage = 'N/A';
if (count($rows) > 0) {
    $topPage = $rows[0]['base_path'];
    if ($topPage == '/' || $topPage == '') {
        $topPage = 'Homepage';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page Efficiency Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 24px;
            background: #f8f9fb;
            color: #222;
        }

        h1, h2, h3 {
            margin-bottom: 10px;
        }

        .topbar {
            margin-bottom: 20px;
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
            min-width: 180px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .card .label {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
        }

        .card .value {
            font-size: 24px;
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
            .actions, .navlinks {
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

<div class="topbar">
    <h1>Page Efficiency / Exit Pressure Report</h1>
    <p>
        This report estimates which pages are more likely to be exit points by comparing page load events to beforeunload events.
        Query parameters are normalized so similar pages are grouped together.
    </p>
</div>

<div class="actions">
    <button class="button" onclick="window.print()">Export Report (PDF)</button>
    <a class="button navlinks" href="dashboard.php">Back to Dashboard</a>
</div>

<div class="cards">
    <div class="card">
        <div class="label">Total Events</div>
        <div class="value"><?= htmlspecialchars((string)$totalEvents) ?></div>
    </div>

    <div class="card">
        <div class="label">Unique Visitors</div>
        <div class="value"><?= htmlspecialchars((string)$uniqueVisitors) ?></div>
    </div>

    <div class="card">
        <div class="label">Top Traffic Page</div>
        <div class="value" style="font-size:18px;"><?= htmlspecialchars((string)$topPage) ?></div>
    </div>
</div>

<div class="section">
    <h2>User Navigation Efficiency by Page</h2>
    <p>
        This visualization compares page load events to exit events. Pages where exits closely match loads are stronger
        candidates for user drop-off. Pages where loads exceed exits suggest deeper navigation behavior.
    </p>
    <canvas id="exitPressureChart" height="120"></canvas>
</div>

<div class="section">
    <h2>Page Efficiency Table</h2>
    <table>
        <tr>
            <th>Page</th>
            <th>Loads</th>
            <th>Exits</th>
            <th>Exit Pressure</th>
            <th>Retention Signal</th>
        </tr>

        <?php foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?php
                    $page = $row['base_path'];
                    if ($page == '/' || $page == '') {
                        echo "Homepage";
                    } else {
                        echo htmlspecialchars($page);
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars((string)$row['loads']) ?></td>
                <td><?= htmlspecialchars((string)$row['exits']) ?></td>
                <td><?= htmlspecialchars((string)$row['exit_pressure_percent']) ?>%</td>
                <td><?= htmlspecialchars((string)$row['retention_percent']) ?>%</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h2>Analyst Notes</h2>
    <p>
        Pages with higher exit pressure are stronger candidates for user drop-off. This does not necessarily mean those pages are poor-performing pages;
        in some cases, users may simply be finishing their task there. Pages with lower exit pressure suggest stronger continuation into other areas of the site.
    </p>
    <p>
        Because beforeunload events may batch or repeat, exit pressure should be interpreted as a behavioral signal rather than an exact bounce rate.
    </p>
</div>

<script>
const labels = <?= json_encode($chartLabels) ?>;
const loadData = <?= json_encode($loadData) ?>;
const exitData = <?= json_encode($exitData) ?>;

new Chart(document.getElementById('exitPressureChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Loads',
                data: loadData,
                borderWidth: 1
            },
            {
                label: 'Exits',
                data: exitData,
                borderWidth: 1
            }
        ]
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
