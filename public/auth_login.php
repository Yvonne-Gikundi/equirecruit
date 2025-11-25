<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_user($pdo, $email, $password)) {

        $role = current_user_role();

        // Correct role-based redirect
        if ($role == 1) {
            // Admin → folder with index.php inside
            header('Location: admin/');
            exit;
        } elseif ($role == 2) {
            // Recruiter
            header('Location: recruiter_dashboard.php');
            exit;
        } else {
            // Candidate
            header('Location: candidate_dashboard.php');
            exit;
        }

    } else {
        $errors[] = 'Invalid credentials.';
    }
}

$registered = isset($_GET['registered']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login — EquiRecruit</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .auth-card {
      max-width: 400px;
      margin: 60px auto;
      padding: 30px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .auth-card h1 {
      margin-top: 0;
      text-align: center;
      color: #2c3e50;
    }
    .auth-card p {
      text-align: center;
      margin-top: 12px;
    }
    .flash {
      background: #f8d7da;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      color: #721c24;
    }
    .auth-card input {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: #2c3e50;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn:hover {
      background: #1a252f;
    }
  </style>
</head>
<body>

<header>
    <div class="nav">
        <h2>EquiRecruit</h2>
        <nav>
          <a href="index.php">Home</a>
          <a href="auth_register.php">Register</a>
        </nav>
    </div>
</header>

<div class="auth-card">
    <h1>Login</h1>

    <?php if ($registered): ?>
      <div class="flash">Registration successful. Please login.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="flash"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit" class="btn">Login</button>
    </form>

    <p>No account? <a href="auth_register.php">Register</a></p>
</div>

</body>
</html>
