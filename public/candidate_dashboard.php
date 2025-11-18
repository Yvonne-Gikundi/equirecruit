<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();
require_login();

$uid = current_user_id();
$stmt = $pdo->prepare("SELECT a.*, j.title 
                       FROM applications a 
                       JOIN jobs j ON a.job_id = j.id 
                       WHERE a.candidate_id = ? 
                       ORDER BY a.uploaded_at DESC");
$stmt->execute([$uid]);
$apps = $stmt->fetchAll();
$totalApps = count($apps);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Candidate Dashboard — EquiRecruit</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <header>
    <div class="nav">
      <h2>EquiRecruit</h2>
      <nav>
        <a href="index.php">Home</a>
        <a href="candidate_dashboard.php">Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container">
    <h1>Candidate Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

    <!-- Dashboard Stats -->
    <div class="dashboard-cards">
      <div class="card">
        <h3>Total Applications</h3>
        <p><?php echo $totalApps; ?></p>
      </div>
    </div>

    <!-- Applications Table -->
    <h2 class="mt-20">My Applications</h2>
    <?php if ($totalApps === 0): ?>
      <p>You have not applied to any jobs yet. Browse <a href="index.php">available jobs</a>.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Job</th>
            <th>Applied</th>
            <th>Status</th>
            <th>Score</th>
            <th>Top Keywords</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($apps as $a): 
            $score = number_format($a['match_score'], 4);
            $color = $score >= 0.6 ? 'green' : ($score >= 0.3 ? 'orange' : 'red');
            $ex = $a['explanation'] ? json_decode($a['explanation'], true) : null;
        ?>
          <tr>
            <td><?php echo htmlspecialchars($a['title']); ?></td>
            <td><?php echo $a['uploaded_at']; ?></td>
            <td><?php echo htmlspecialchars($a['status']); ?></td>
            <td><span style="color:<?php echo $color; ?>;font-weight:bold;"><?php echo $score; ?></span></td>
            <td><?php echo $ex ? htmlspecialchars(implode(', ', array_keys($ex))) : '-'; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</body>
</html>
