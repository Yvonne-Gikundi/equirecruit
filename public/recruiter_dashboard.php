<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/jobs.php';
require_once __DIR__ . '/../src/matching.php';

if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
if (current_user_role() != 2) { echo 'Access denied. Recruiter only.'; exit; }

$recruiter_id = current_user_id();
$jobs_stmt = $pdo->prepare("SELECT * FROM jobs WHERE recruiter_id = ? ORDER BY created_at DESC");
$jobs_stmt->execute([$recruiter_id]);
$jobs = $jobs_stmt->fetchAll();

$selected_job_id = intval($_GET['job'] ?? ($jobs[0]['id'] ?? 0));
$selected_job = $selected_job_id ? get_job($pdo, $selected_job_id) : null;

$appStmt = $pdo->prepare("SELECT a.*, u.name as candidate_name FROM applications a 
                          JOIN users u ON a.candidate_id = u.id 
                          WHERE a.job_id = ?");
$appStmt->execute([$selected_job_id]);
$applications = $appStmt->fetchAll();

$all_texts = [];
foreach ($applications as $ap) $all_texts[] = $ap['resume_text'] ?: '';

$matches = [];
if ($selected_job) {
  $jobText = $selected_job['description'] . ' ' . ($selected_job['requirements'] ?? '');
  foreach ($applications as $ap) {
    $res = match_job_and_resume($jobText, $ap['resume_text'] ?: '', $all_texts);
    $matches[$ap['id']] = $res;
    $upd = $pdo->prepare("UPDATE applications SET match_score = ?, explanation = ? WHERE id = ?");
    $upd->execute([$res['score'], json_encode($res['explanation']), $ap['id']]);
  }
}

// Count totals
$totalJobs = count($jobs);
$totalApplications = count($applications);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Recruiter Dashboard — EquiRecruit</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navigation Bar -->
  <header>
    <div class="nav">
      <h2>EquiRecruit</h2>
      <nav>
        <a href="index.php">Home</a>
        <a href="create_job.php">Create Job</a>
        <a href="recruiter_dashboard.php">Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container">
    <h1>Recruiter Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>

    <!-- Dashboard Stats -->
    <div class="dashboard-cards">
      <div class="card">
        <h3>Jobs Posted</h3>
        <p><?php echo $totalJobs; ?></p>
      </div>
      <div class="card">
        <h3>Applications (Current Job)</h3>
        <p><?php echo $totalApplications; ?></p>
      </div>
    </div>

    <!-- Job Selector -->
    <h2 class="mt-20">Your Jobs</h2>
    <?php if ($totalJobs === 0): ?>
      <p>You have not created any jobs yet. <a href="create_job.php" class="btn">Create Job</a></p>
    <?php else: ?>
      <form method="get" class="mt-20">
        <label for="job">Select Job:</label>
        <select name="job" id="job" onchange="this.form.submit()">
          <?php foreach ($jobs as $j): ?>
            <option value="<?php echo $j['id']; ?>" <?php if($j['id']==$selected_job_id) echo 'selected'; ?>>
              <?php echo htmlspecialchars($j['title']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>

    <!-- Job Details & Applicants -->
    <?php if ($selected_job): ?>
      <h2 class="mt-20"><?php echo htmlspecialchars($selected_job['title']); ?></h2>
      <p><?php echo nl2br(htmlspecialchars(substr($selected_job['description'], 0, 600))); ?></p>

      <h3>Applicants (<?php echo count($applications); ?>)</h3>
      <?php if (count($applications) === 0): ?>
        <p>No applications yet.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Candidate</th>
              <th>Uploaded</th>
              <th>Score</th>
              <th>Top Words</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($applications as $ap): 
              $m = $matches[$ap['id']] ?? ['score'=>0,'explanation'=>[]]; ?>
            <tr>
              <td><?php echo htmlspecialchars($ap['candidate_name']); ?></td>
              <td><?php echo $ap['uploaded_at']; ?></td>
              <td>
                <?php 
                  $score = number_format($m['score'], 4);
                  // Add color-coded score
                  $color = $score >= 0.6 ? 'green' : ($score >= 0.3 ? 'orange' : 'red');
                  echo "<span style='color:$color;font-weight:bold;'>$score</span>";
                ?>
              </td>
              <td><?php echo htmlspecialchars(implode(', ', array_keys($m['explanation']))); ?></td>
              <td>
                <a href="view_application.php?id=<?php echo $ap['id']; ?>" class="btn btn-secondary">View</a>
                <a href="download.php?file=<?php echo urlencode(basename($ap['file_path'])); ?>" class="btn">Download</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    <?php endif; ?>
  </div>

</body>
</html>
