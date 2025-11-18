<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if (login_user($pdo, $email, $password)) {
    // redirect based on role
    if (current_user_role() == 2) header('Location: recruiter_dashboard.php');
    else header('Location: candidate_dashboard.php');
    exit;
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
    /* Extra styling just for login/register pages */
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
  </style>
</head>
<body>

  <!-- Navbar -->
  <header>
    <div class="nav">
      <h2>EquiRecruit</h2>
      <nav>
        <a href="index.php">Home</a>
        <a href="auth_register.php">Register</a>
      </nav>
    </div>
  </header>

  <!-- Login Form -->
  <div class="auth-card">
    <h1>Login</h1>

    <?php if ($registered): ?>
      <div class="flash">✅ Registration successful. Please login.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="flash"><?php echo implode('<br>', array_map('htmlspecialchars',$errors)); ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit" class="btn mt-20">Login</button>
    </form>

    <p>No account? <a href="auth_register.php">Register</a></p>
  </div>

</body>
</html>
