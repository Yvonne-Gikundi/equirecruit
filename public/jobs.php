<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/jobs.php';

$q = trim($_GET['q'] ?? '');
if ($q) {
  $stmt = $pdo->prepare("SELECT j.*, u.name as recruiter 
                         FROM jobs j 
                         JOIN users u ON j.recruiter_id = u.id 
                         WHERE j.is_published = 1 
                         AND (j.title LIKE ? OR j.description LIKE ?) 
                         ORDER BY j.created_at DESC");
  $stmt->execute(["%$q%", "%$q%"]);
  $jobs = $stmt->fetchAll();
} else {
  $jobs = list_jobs($pdo);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>EquiRecruit — Job Listings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Main Content -->
  <div class="container">
    <h1>Job Listings</h1>

    <!-- Search Bar -->
    <form method="get" class="mb-20">
      <input type="text" name="q" placeholder="Search jobs (e.g. PHP, Finance, Remote)" 
             value="<?php echo htmlspecialchars($q); ?>">
      <button type="submit" class="btn">Search</button>
    </form>

    <?php if (count($jobs) === 0): ?>
      <p>No jobs found<?php echo $q ? " for '<strong>".htmlspecialchars($q)."</strong>'" : ""; ?>.</p>
    <?php else: ?>
      <ul class="jobs">
        <?php foreach($jobs as $j): ?>
        <li>
          <h3><?php echo htmlspecialchars($j['title']); ?></h3>
          <p><?php echo nl2br(htmlspecialchars(substr($j['description'],0,300))); ?>...</p>
          <small>Posted by: <?php echo htmlspecialchars($j['recruiter']); ?> on <?php echo $j['created_at']; ?></small>
          <p><a href="job.php?id=<?php echo $j['id']; ?>" class="btn">View & Apply</a></p>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

</body>
</html>
