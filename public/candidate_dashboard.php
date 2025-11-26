<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

if (session_status() == PHP_SESSION_NONE) session_start();
require_login();

$uid = current_user_id();

// Fetch candidate applications + job titles
$stmt = $pdo->prepare("
    SELECT a.*, j.title 
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.candidate_id = ?
    ORDER BY a.uploaded_at DESC
");
$stmt->execute([$uid]);
$apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalApps = count($apps);

// Helper function for score color
function scoreColor($value) {
    return $value >= 60 ? "green" : ($value >= 40 ? "orange" : "red");
}
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
    <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>

    <!-- Dashboard Stats -->
    <div class="dashboard-cards">
      <div class="card">
        <h3>Total Applications</h3>
        <p><?= $totalApps ?></p>
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
            <th>Skill Match</th>
            <th>Experience</th>
            <th>Potential</th>
            <th>Total Score</th>
            <th>Top Keywords</th>
            <th>Explain</th>
          </tr>
        </thead>

        <tbody>
        <?php foreach ($apps as $a): ?>

          <?php
            // Prevent null errors (default 0 if missing)
            $skill = number_format($a['skill_score'] ?? 0, 2);
            $exp = number_format($a['experience_score'] ?? 0, 2);
            $potential = number_format($a['potential_score'] ?? 0, 2);
            $total = number_format($a['total_score'] ?? 0, 2);
            $keywords = !empty($a['top_keywords']) ? htmlspecialchars($a['top_keywords']) : "-";
          ?>

          <tr>
            <td><?= htmlspecialchars($a['title']); ?></td>
            <td><?= $a['uploaded_at']; ?></td>
            <td><?= htmlspecialchars($a['status']); ?></td>

            <td style="color:<?= scoreColor($skill) ?>; font-weight:bold;"><?= $skill ?></td>
            <td style="color:<?= scoreColor($exp) ?>; font-weight:bold;"><?= $exp ?></td>
            <td style="color:<?= scoreColor($potential) ?>; font-weight:bold;"><?= $potential ?></td>
            <td style="color:<?= scoreColor($total) ?>; font-weight:bold;"><?= $total ?></td>

            <td><?= $keywords ?></td>
            <td>
    <button class="btn-explain" onclick="showExplain(<?= $a['id'] ?>)">Explain</button>
</td>

          </tr>

        <?php endforeach; ?>
        </tbody>
      </table>

    <?php endif; ?>
  </div>
<!-- Explanation Modal -->
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 120px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
}

.modal-content {
    background: white;
    margin: auto;
    padding: 20px;
    width: 60%;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
}
</style>

<div id="explainModal" class="modal">
    <div class="modal-content">
        <span onclick="closeExplain()" class="close">&times;</span>
        <div id="explainText">Loading...</div>
    </div>
</div>
<script>
function showExplain(id) {
    fetch("explain_score.php?id=" + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById("explainText").innerHTML = html;
            document.getElementById("explainModal").style.display = "block";
        });
}

function closeExplain() {
    document.getElementById("explainModal").style.display = "none";
}
</script>

</body>
</html>
