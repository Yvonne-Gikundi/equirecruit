<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();

if (current_user_role() != 1) exit("Unauthorized");

// Log that admin viewed the fairness metrics page
add_log($pdo, $_SESSION['user_id'], "Viewed fairness metrics");

$metrics = $pdo->query("SELECT * FROM fairness_metrics ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Prepare chart arrays
$labels = [];
$scores = [];

foreach ($metrics as $m) {
    $labels[] = $m['metric_name'];
    $scores[] = $m['score'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fairness Metrics</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Fairness Metrics</h1>

    <p>Below is a visual representation of fairness across the recruitment platform.</p>

    <div style="width: 70%; margin: 20px auto;">
        <canvas id="fairnessChart"></canvas>
    </div>

    <h2>Raw Data</h2>
    <table class="table">
        <tr>
            <th>ID</th><th>Metric</th><th>Score</th><th>Updated At</th>
        </tr>

        <?php foreach ($metrics as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['metric_name']) ?></td>
            <td><?= htmlspecialchars($m['score']) ?></td>
            <td><?= $m['updated_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

<script>
const ctx = document.getElementById('fairnessChart').getContext('2d');

const fairnessChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Fairness Score',
            data: <?= json_encode($scores) ?>,
            backgroundColor: [
                '#1abc9c',
                '#3498db',
                '#9b59b6',
                '#e67e22',
                '#e74c3c'
            ],
            borderColor: '#2c3e50',
            borderWidth: 2,
            hoverBackgroundColor: '#34495e'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: {
                display: true,
                text: 'Fairness Metrics Overview',
                font: { size: 18 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>

</body>
</html>
