<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();

if (current_user_role() != 1) { 
    echo "Unauthorized"; 
    exit; 
}

// Add audit log for viewing this page
add_log($pdo, $_SESSION['user_id'], "Viewed user management page");

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Manage Users</h1>

    <table class="table">
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Created</th>
        </tr>

        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <?= $u['role_id'] == 1 ? "Admin" : ($u['role_id'] == 2 ? "Recruiter" : "Candidate") ?>
            </td>
            <td><?= htmlspecialchars($u['phone']) ?></td>
            <td><?= $u['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
