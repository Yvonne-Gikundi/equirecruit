<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

add_log($pdo, $_SESSION['user_id'], "Viewed audit & security report");

// Total logs
$totalLogs = $pdo->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();

// Distinct users with actions
$activeUsers = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM audit_logs")->fetchColumn();

// Latest 20 actions
$recent = $pdo->query("
    SELECT a.*, u.name AS user_name
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.id DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit & Security Report</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Audit & Security Report</h1>

    <h2>Overview</h2>
    <table class="table">
        <tr><th>Total Audit Entries</th><th>Active Users (with actions)</th></tr>
        <tr>
            <td><?= $totalLogs ?></td>
            <td><?= $activeUsers ?></td>
        </tr>
    </table>

    <h3>Recent Activity (last 20 actions)</h3>
    <table class="table">
        <tr><th>ID</th><th>User</th><th>Action</th><th>Date</th></tr>
        <?php foreach ($recent as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['user_name'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($r['action']) ?></td>
            <td><?= $r['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
