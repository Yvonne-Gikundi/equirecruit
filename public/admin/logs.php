<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();
if (current_user_role() != 1) exit("Unauthorized");

$logs = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Audit Logs</h1>

    <table class="table">
        <tr>
            <th>ID</th><th>User</th><th>Action</th><th>Date</th>
        </tr>

        <?php foreach ($logs as $l): ?>
        <tr>
            <td><?= $l['id'] ?></td>
            <td><?= $l['user_id'] ?></td>
            <td><?= $l['action'] ?></td>
            <td><?= $l['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
