<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $role_id = $_POST['role_id'] ?? 3; // Default: candidate

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        $errors[] = "Please fill in all required fields.";
    } else {
        try {
            if (register_user($pdo, $name, $email, $password, $role_id, $phone)) {
                header('Location: auth_login.php?registered=1');
                exit;
            } else {
                $errors[] = "Failed to register user.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register — EquiRecruit</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-card {max-width: 450px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);}
    .auth-card h1 {text-align: center; color: #2c3e50;}
    label {display: block; margin-top: 10px;}
    input, select {width: 100%; padding: 8px; margin-top: 5px;}
    button {margin-top: 20px; padding: 10px 20px;}
    .flash {background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px;}
  </style>
</head>
<body>

<header>
  <div class="nav">
    <h2>EquiRecruit</h2>
    <nav>
      <a href="index.php">Home</a>
      <a href="auth_login.php">Login</a>
    </nav>
  </div>
</header>

<div class="auth-card">
  <h1>Register</h1>

  <?php if ($errors): ?>
    <div class="flash"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
  <?php endif; ?>

  <form method="post">
    <label for="name">Full Name</label>
    <input id="name" name="name" required>

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required>

    <label for="phone">Phone</label>
    <input id="phone" name="phone" type="text">

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>

    <label for="confirm">Confirm Password</label>
    <input id="confirm" name="confirm" type="password" required>

    <label for="role_id">Register as:</label>
    <select id="role_id" name="role_id">
      <option value="3">Candidate</option>
      <option value="2">Recruiter</option>
    </select>

    <button type="submit">Register</button>
  </form>
</div>

</body>
</html>
