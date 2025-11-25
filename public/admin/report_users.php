<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

add_log($pdo, $_SESSION['user_id'], "Viewed user report");

// Total users
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Role breakdown
$roleRows = $pdo->query("
    SELECT role_id, COUNT(*) AS cnt 
    FROM users 
    GROUP BY role_id
")->fetchAll(PDO::FETCH_ASSOC);

// Monthly registrations
$monthly = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS cnt
    FROM users
    GROUP BY month
    ORDER BY month ASC
")->fetchAll(PDO::FETCH_ASSOC);

function roleName($role_id) {
    if ($role_id == 1) return "Admin";
    if ($role_id == 2) return "Recruiter";
    return "Candidate";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Report</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>User Report</h1>

    <h2>Overview</h2>
    <p><strong>Total users:</strong> <?= $totalUsers ?></p>

    <h3>Users by Role</h3>
    <table class="table">
        <tr><th>Role</th><th>Count</th></tr>
        <?php foreach ($roleRows as $r): ?>
            <tr>
                <td><?= roleName($r['role_id']) ?></td>
                <td><?= $r['cnt'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php
    $months = array_column($monthly, 'month');
    $counts = array_column($monthly, 'cnt');
    ?>

    <h3>Monthly Registrations</h3>
    <div style="width:70%; margin:20px auto;">
        <canvas id="userChart"></canvas>
    </div>
</div>

<script>
const ctxUsers = document.getElementById('userChart').getContext('2d');

new Chart(ctxUsers, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Registrations',
            data: <?= json_encode($counts) ?>,
            borderColor: '#3498db',
            borderWidth: 2,
            tension: 0.2
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
