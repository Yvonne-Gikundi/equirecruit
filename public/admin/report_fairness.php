<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

add_log($pdo, $_SESSION['user_id'], "Viewed fairness report");

$metrics = $pdo->query("
    SELECT * 
    FROM fairness_metrics
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fairness Report</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Fairness Report</h1>

    <p>This report summarises the fairness metrics across your recruitment platform. Scores are out of 100, where higher is fairer.</p>

    <table class="table">
        <tr>
            <th>Metric</th>
            <th>Score</th>
            <th>Last Updated</th>
        </tr>
        <?php foreach ($metrics as $m): ?>
        <tr>
            <td><?= htmlspecialchars($m['metric_name']) ?></td>
            <td><?= htmlspecialchars($m['score']) ?></td>
            <td><?= $m['updated_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p style="margin-top:20px;">
        <strong>Interpretation:</strong><br>
        - Scores below 60 may indicate potential fairness issues that require investigation.<br>
        - Scores between 60–80 are acceptable but should be monitored regularly.<br>
        - Scores above 80 indicate strong fairness and balance across the metric being measured.
    </p>
</div>

</body>
</html>
