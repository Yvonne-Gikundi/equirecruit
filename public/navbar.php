<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../src/auth.php';
?>
<header>
  <div class="nav">
    <h2>EquiRecruit</h2>
    <nav>
      <a href="index.php">Home</a>
      <a href="jobs.php">Jobs</a>

      <?php if (isset($_SESSION['user'])): ?>
        <?php if (function_exists('current_user_role') && current_user_role() == 2): ?>
          <!-- Recruiter links -->
          <a href="recruiter_dashboard.php">Dashboard</a>
          <a href="create_job.php">Create Job</a>
        <?php else: ?>
          <!-- Candidate links -->
          <a href="candidate_dashboard.php">Dashboard</a>
        <?php endif; ?>
        <!-- Logout at the end -->
        <a href="logout.php" class="btn btn-danger">Logout</a>
      <?php else: ?>
        <!-- If not logged in -->
        <a href="auth_login.php">Login</a>
        <a href="auth_register.php">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
