<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

add_log($pdo, $_SESSION['user_id'], "Viewed admin summary report");

// Users
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$byRole = $pdo->query("
    SELECT role_id, COUNT(*) AS cnt 
    FROM users 
    GROUP BY role_id
")->fetchAll(PDO::FETCH_ASSOC);

// Jobs
$jobSummary = $pdo->query("
    SELECT 
        COUNT(*) AS total_jobs,
        SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published_jobs,
        SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) AS unpublished_jobs
    FROM jobs
")->fetch(PDO::FETCH_ASSOC);

// Fairness
$avgFairness = $pdo->query("
    SELECT AVG(score) FROM fairness_metrics
")->fetchColumn();

// Logs
$totalLogs = $pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
$lastLog = $pdo->query("
    SELECT a.*, u.name AS user_name
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.id DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

function roleName($role_id) {
    if ($role_id == 1) return "Admin";
    if ($role_id == 2) return "Recruiter";
    return "Candidate";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Summary Report</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Admin Summary Report</h1>

    <h2>Users</h2>
    <p><strong>Total users:</strong> <?= $totalUsers ?></p>
    <table class="table">
        <tr><th>Role</th><th>Count</th></tr>
        <?php foreach ($byRole as $r): ?>
        <tr>
            <td><?= roleName($r['role_id']) ?></td>
            <td><?= $r['cnt'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Jobs</h2>
    <table class="table">
        <tr><th>Total Jobs</th><th>Published</th><th>Unpublished</th></tr>
        <tr>
            <td><?= $jobSummary['total_jobs'] ?></td>
            <td><?= $jobSummary['published_jobs'] ?></td>
            <td><?= $jobSummary['unpublished_jobs'] ?></td>
        </tr>
    </table>

    <h2>Fairness</h2>
    <p><strong>Average Fairness Score:</strong> <?= $avgFairness ? round($avgFairness, 2) : 'N/A' ?> / 100</p>

    <h2>System Activity</h2>
    <p><strong>Total Audit Log Entries:</strong> <?= $totalLogs ?></p>
    <?php if ($lastLog): ?>
        <p><strong>Last Action:</strong> 
            <?= htmlspecialchars($lastLog['action']) ?> 
            by <?= htmlspecialchars($lastLog['user_name'] ?? 'Unknown') ?> 
            at <?= $lastLog['created_at'] ?>
        </p>
    <?php endif; ?>

</div>

</body>
</html>
