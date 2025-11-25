<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();

if (current_user_role() != 1) { 
    echo "Unauthorized"; 
    exit; 
}

// Add audit log for viewing job management page
add_log($pdo, $_SESSION['user_id'], "Viewed job management page");

// Fetch jobs
$jobs = $pdo->query("
    SELECT jobs.*, users.name AS recruiter_name 
    FROM jobs 
    LEFT JOIN users ON jobs.recruiter_id = users.id
    ORDER BY jobs.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Job Posts</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Manage Job Posts</h1>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Posted By</th>
            <th>Published?</th>
            <th>Created</th>
        </tr>

        <?php foreach ($jobs as $job): ?>
        <tr>
            <td><?= $job['id'] ?></td>
            <td><?= htmlspecialchars($job['title']) ?></td>
            <td><?= htmlspecialchars($job['recruiter_name'] ?? 'Unknown') ?></td>
            <td>
                <?= $job['is_published'] ? 'Published' : 'Unpublished' ?>
            </td>
            <td><?= $job['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
