<?php
session_start();
require_once "../../src/db.php";
require_once "../../src/auth.php";

// Ensure only admins can access
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../unauthorized.php");
    exit;
}

// Log viewing of admin dashboard
add_log($pdo, $_SESSION['user_id'], "Viewed admin dashboard");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard — EquiRecruit</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>Welcome, Administrator</h1>

    <p>Use the admin tools to manage system operations.</p>

    <div class="cards">
        <div class="card">
            <h3>User Management</h3>
            <a href="users.php" class="btn">Open</a>
        </div>

        <div class="card">
            <h3>Job Posts</h3>
            <a href="jobs.php" class="btn">Open</a>
        </div>

        <div class="card">
            <h3>Audit Logs</h3>
            <a href="logs.php" class="btn">Open</a>
        </div>

        <div class="card">
            <h3>Fairness Metrics</h3>
            <a href="fairness.php" class="btn">Open</a>
        </div>

        <div class="card">
            <h3>System Settings</h3>
            <a href="settings.php" class="btn">Open</a>
        </div>
    </div>

</div>

</body>
</html>
