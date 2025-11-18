<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/jobs.php';
if (session_status() == PHP_SESSION_NONE) session_start();
require_login();
if (current_user_role() != 2) { echo "Access denied. Recruiter role required."; exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $requirements = trim($_POST['requirements'] ?? '');
  if (!$title || !$description) $errors[] = 'Title and description required.';
  if (empty($errors)) {
    create_job($pdo, current_user_id(), $title, $description, $requirements);
    header('Location: recruiter_dashboard.php');
    exit;
  }
}
?><!doctype html><html><head><meta charset="utf-8"><title>Create Job</title><link rel="stylesheet" href="style.css"></head><body><div class="container">
<h1>Create Job</h1>
<?php if ($errors) echo '<div class="flash">'.implode('<br>',array_map('htmlspecialchars',$errors)).'</div>'; ?>
<form method="post">
  <label>Title</label><br><input name="title" required><br>
  <label>Description</label><br><textarea name="description" rows="6" required></textarea><br>
  <label>Requirements (optional)</label><br><textarea name="requirements" rows="3"></textarea><br><br>
  <button type="submit">Create Job</button>
</form>
<p><a href="recruiter_dashboard.php">Back to dashboard</a></p>
</div></body></html>