<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/jobs.php';
require_once __DIR__ . '/../src/apply.php';
require_once __DIR__ . '/../src/auth.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$job_id = intval($_GET['id'] ?? 0);
$job = $job_id ? get_job($pdo, $job_id) : null;
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
  $candidate_id = current_user_id() ?: null;
  if (!$candidate_id) {
    $flash = 'You must be logged in as an applicant to apply. Please 
              <a href="auth_login.php">login</a> or 
              <a href="auth_register.php">register</a>.';
  } else {
    $res = submit_application($pdo, $job_id, $candidate_id, $_FILES['resume']);
    if (isset($res['error'])) {
      $flash = 'Upload error: ' . htmlspecialchars($res['error']);
    } else {
      header('Location: job.php?id=' . $job_id . '&ok=1');
      exit;
    }
  }
}

if (isset($_GET['ok'])) {
  $flash = '✅ Application submitted successfully.';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Job Details — EquiRecruit</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

  <?php if ($flash): ?>
    <div class="flash"><?php echo $flash; ?></div>
  <?php endif; ?>

  <?php if (!$job): ?>
    <p>❌ Job not found.</p>
  <?php else: ?>
    <h1><?php echo htmlspecialchars($job['title']); ?></h1>

    <!-- Job details card -->
    <div class="card mb-20">
      <h3>Description</h3>
      <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>

      <?php if (!empty($job['requirements'])): ?>
        <h3>Requirements</h3>
        <p><?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
      <?php endif; ?>

      <p><small>Posted by: <?php echo htmlspecialchars($job['recruiter']); ?></small></p>
    </div>

    <?php if (current_user_role() == 3): // Applicant ?>
      <div class="card">
        <h2>Apply Now</h2>
        <form method="post" enctype="multipart/form-data">
          <label for="resume">Upload Resume (PDF/DOCX/TXT)</label>
          <input type="file" name="resume" id="resume" required>
          <button type="submit" class="btn mt-20">Submit Application</button>
        </form>
      </div>
    <?php else: ?>
      <p><em>Login as an Applicant to apply.</em></p>
    <?php endif; ?>
  <?php endif; ?>
</div>

</body>
</html>
