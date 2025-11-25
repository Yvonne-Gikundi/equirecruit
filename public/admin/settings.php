<?php
require_once "../../src/db.php";
require_once "../../src/auth.php";
require_login();

if (current_user_role() != 1) {
    echo "Unauthorized";
    exit;
}

// Log viewing settings
add_log($pdo, $_SESSION['user_id'], "Viewed system settings");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $id => $value) {
        $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE id = ?");
        $stmt->execute([$value, $id]);

        add_log($pdo, $_SESSION['user_id'], "Updated system setting ID $id to '$value'");
    }

    $message = "Settings updated successfully!";
}

$settings = $pdo->query("SELECT * FROM system_settings ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>System Settings</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        .msg {
            padding: 10px;
            background: #d4edda;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .input-field {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .save-btn {
            padding: 12px 18px;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }
        .save-btn:hover {
            background: #1a242f;
        }
    </style>
</head>

<body>

<?php include "sidebar.php"; ?>

<div class="main">
    <h1>System Settings</h1>

    <?php if (!empty($message)): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <table class="table">
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>

            <?php foreach ($settings as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['setting_key']) ?></td>
                <td>
                    <input 
                        class="input-field"
                        type="text" 
                        name="settings[<?= $s['id'] ?>]" 
                        value="<?= htmlspecialchars($s['setting_value']) ?>"
                    >
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <button type="submit" class="save-btn">Save Changes</button>
    </form>
</div>

</body>
</html>
