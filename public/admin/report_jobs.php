<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

add_log($pdo, $_SESSION['user_id'], "Viewed job & recruiter report");

// Job summary
$summary = $pdo->query("
    SELECT 
        COUNT(*) AS total_jobs,
        SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published_jobs,
        SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) AS unpublished_jobs
    FROM jobs
")->fetch(PDO::FETCH_ASSOC);

// Jobs per recruiter
$byRecruiter = $pdo->query("
    SELECT u.name AS recruiter_name, COUNT(j.id) AS job_count
    FROM users u
    LEFT JOIN jobs j ON j.recruiter_id = u.id
    WHERE u.role_id = 2
    GROUP BY u.id
    ORDER BY job_count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Jobs per month
$jobsMonthly = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS cnt
    FROM jobs
    GROUP BY month
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_ASSOC);

$months = array_column($jobsMonthly, 'month');
$jobCounts = array_column($jobsMonthly, 'cnt');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Job & Recruiter Report</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Job & Recruiter Report</h1>

    <h2>Job Overview</h2>
    <table class="table">
        <tr><th>Total Jobs</th><th>Published</th><th>Unpublished</th></tr>
        <tr>
            <td><?= $summary['total_jobs'] ?></td>
            <td><?= $summary['published_jobs'] ?></td>
            <td><?= $summary['unpublished_jobs'] ?></td>
        </tr>
    </table>

    <h3>Jobs per Recruiter</h3>
    <table class="table">
        <tr><th>Recruiter</th><th>Jobs Posted</th></tr>
        <?php foreach ($byRecruiter as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['recruiter_name'] ?? 'Unknown') ?></td>
            <td><?= $r['job_count'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Jobs Posted per Month</h3>
    <div style="width:70%; margin:20px auto;">
        <canvas id="jobsChart"></canvas>
    </div>
</div>

<script>
const ctxJobs = document.getElementById('jobsChart').getContext('2d');

new Chart(ctxJobs, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Jobs Posted',
            data: <?= json_encode($jobCounts) ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
