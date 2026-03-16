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
$totalSessionsResult = $mysqli->query("
    SELECT COUNT(DISTINCT sid) AS total_sessions
    FROM collector_events
");
$totalSessions = $totalSessionsResult->fetch_assoc()['total_sessions'] ?? 0;

$avgEventsResult = $mysqli->query("
    SELECT ROUND(AVG(session_events), 2) AS avg_events_per_session
    FROM (
        SELECT sid, COUNT(*) AS session_events
        FROM collector_events
        GROUP BY sid
    ) t
");
$avgEventsPerSession = $avgEventsResult->fetch_assoc()['avg_events_per_session'] ?? 0;

$topSessionResult = $mysqli->query("
    SELECT sid, COUNT(*) AS total_events
    FROM collector_events
    GROUP BY sid
    ORDER BY total_events DESC
    LIMIT 1
");
$topSessionRow = $topSessionResult->fetch_assoc();
$topSession = $topSessionRow['sid'] ?? 'N/A';

/* Main session report query */
$query = "
SELECT 
    sid,
    COUNT(*) AS total_events,
    COUNT(DISTINCT SUBSTRING_INDEX(path, '?', 1)) AS unique_pages,
    SUM(CASE WHEN reason = 'load' THEN 1 ELSE 0 END) AS loads,
    SUM(CASE WHEN reason = 'beforeunload' THEN 1 ELSE 0 END) AS exits
FROM collector_events
GROUP BY sid
ORDER BY total_events DESC
LIMIT 15
";

$result = $mysqli->query($query);

if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

$rows = [];
$chartLabels = [];
$chartData = [];

while ($row = $result->fetch_assoc()) {
    $sessionId = $row['sid'];
    $shortSessionId = strlen($sessionId) > 8 ? substr($sessionId, 0, 8) . '...' : $sessionId;

    $row['short_sid'] = $shortSessionId;
    $rows[] = $row;

    $chartLabels[] = $shortSessionId;
    $chartData[] = (int)$row['total_events'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Session Activity Report</title>
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

<h1>Session Activity Report</h1>
<p>
This report analyzes session-level engagement by comparing total event volume, the number of distinct pages visited,
and load/exit behavior across sessions.
</p>

<div class="actions">
    <button class="button" onclick="window.print()">Export Report (PDF)</button>
    <a class="button" href="dashboard.php">Back to Dashboard</a>
</div>

<div class="cards">
    <div class="card">
        <div class="label">Total Sessions</div>
        <div class="value"><?= htmlspecialchars((string)$totalSessions) ?></div>
    </div>

    <div class="card">
        <div class="label">Avg Events / Session</div>
        <div class="value"><?= htmlspecialchars((string)$avgEventsPerSession) ?></div>
    </div>

    <div class="card">
        <div class="label">Most Active Session</div>
        <div class="value" style="font-size:16px;"><?= htmlspecialchars((string)$topSession) ?></div>
    </div>
</div>

<div class="section">
    <h2>Top Sessions by Event Volume</h2>
    <p>
        Sessions with higher event counts are stronger candidates for deeper engagement, repeat interactions,
        or longer browsing duration.
    </p>
    <canvas id="sessionChart" height="120"></canvas>
</div>

<div class="section">
    <h2>Session Detail Table</h2>
    <table>
        <tr>
            <th>Session ID</th>
            <th>Total Events</th>
            <th>Unique Pages</th>
            <th>Loads</th>
            <th>Exits</th>
        </tr>

        <?php foreach ($rows as $row): ?>
            <tr>
                <td title="<?= htmlspecialchars((string)$row['sid']) ?>">
                    <?= htmlspecialchars((string)$row['short_sid']) ?>
                </td>
                <td><?= htmlspecialchars((string)$row['total_events']) ?></td>
                <td><?= htmlspecialchars((string)$row['unique_pages']) ?></td>
                <td><?= htmlspecialchars((string)$row['loads']) ?></td>
                <td><?= htmlspecialchars((string)$row['exits']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h2>Analyst Notes</h2>
    <p>
        Sessions with higher total event counts and more unique pages suggest deeper browsing behavior and stronger engagement.
        Sessions with low event counts and low page diversity may reflect quick exits, shallow browsing, or test behavior.
    </p>
    <p>
        Comparing load and beforeunload events also helps identify whether sessions are balanced or skewed toward early exits.
    </p>
</div>

<script>
const labels = <?= json_encode($chartLabels) ?>;
const data = <?= json_encode($chartData) ?>;

new Chart(document.getElementById('sessionChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Events',
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
