<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) { echo 'Login required.'; exit; }
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT a.*, u.name as candidate_name, j.title FROM applications a JOIN users u ON a.candidate_id = u.id JOIN jobs j ON a.job_id = j.id WHERE a.id = ?");
$stmt->execute([$id]);
$ap = $stmt->fetch();
if (!$ap) { echo 'Application not found.'; exit; }
?><!doctype html><html><head><meta charset="utf-8"><title>View application</title><link rel="stylesheet" href="style.css"></head><body><div class="container">
<header><p><a href="recruiter_dashboard.php">← Back</a></p></header>
<h1>Application for <?php echo htmlspecialchars($ap['title']); ?></h1>
<h3>Candidate: <?php echo htmlspecialchars($ap['candidate_name']); ?></h3>
<p><strong>Uploaded:</strong> <?php echo $ap['uploaded_at']; ?></p>
<h4>Resume text (extracted)</h4>
<pre style="white-space:pre-wrap;"><?php echo htmlspecialchars($ap['resume_text']); ?></pre>
<h4>Explanation (top tokens)</h4>
<?php $ex = $ap['explanation'] ? json_decode($ap['explanation'], true) : []; if ($ex) { echo '<ul>'; foreach ($ex as $w=>$v) echo '<li>'.htmlspecialchars($w).' — contribution: '.number_format($v,6).'</li>'; echo '</ul>'; } else echo '<p>No explanation stored.</p>'; ?>
<p><a href="download.php?file=<?php echo urlencode(basename($ap['file_path'])); ?>">Download original file</a></p>
</div></body></html>