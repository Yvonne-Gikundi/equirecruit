<?php
// src/apply.php
require_once __DIR__ . '/../src/score_engine.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/parser.php';

function save_uploaded_resume($file, $candidate_id) {
  $uploadDir = __DIR__ . '/../public/uploads/';
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
  $safeName = time() . '_' . preg_replace('/[^a-z0-9\._-]/i','_', $file['name']);
  $target = $uploadDir . $safeName;
  if (!move_uploaded_file($file['tmp_name'], $target)) {
    return ['error' => 'Failed to move uploaded file'];
  }
  $text = extract_text_from_file($target);
  return ['path'=>$target, 'text'=>$text, 'filename'=>$safeName];
}

function submit_application($pdo, $job_id, $candidate_id, $file) {
  $res = save_uploaded_resume($file, $candidate_id);
  if (isset($res['error'])) return $res;
  $stmt = $pdo->prepare("INSERT INTO applications (job_id, candidate_id, resume_text, original_filename, file_path) VALUES (?, ?, ?, ?, ?)"); 
  $stmt->execute([$job_id, $candidate_id, $res['text'], $res['filename'], $res['path']]);
  return ['ok'=>true, 'application_id'=>$pdo->lastInsertId()];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $candidate_id = $_SESSION['user_id'];

    // 1. Insert base application entry
    $stmt = $pdo->prepare("INSERT INTO applications (job_id, candidate_id, status) VALUES (?, ?, 'submitted')");
    $stmt->execute([$job_id, $candidate_id]);

    // 2. Get application ID
    $application_id = $pdo->lastInsertId();

    // 3. Load scoring engine
    require_once __DIR__ . '/../src/score_engine.php';

    // 4. Fetch job description
    $job = $pdo->prepare("SELECT description FROM jobs WHERE id = ?");
    $job->execute([$job_id]);
    $jobDescription = $job->fetchColumn();

    // 5. Fetch candidate CV text (depends on your setup)
    // Example: stored in users table
    $cvStmt = $pdo->prepare("SELECT cv_text FROM users WHERE id = ?");
    $cvStmt->execute([$candidate_id]);
    $cvText = $cvStmt->fetchColumn();

    // 6. Calculate all scores
    $skill = skill_match_score($jobDescription, $cvText);
    $exp = experience_score($cvText);
    $potential = inferred_potential_score($cvText);
    $keywords = top_matching_keywords($jobDescription, $cvText);
    $total = calculate_total_score($skill, $exp, $potential);

    // 7. Update application with the new scores
    $stmt = $pdo->prepare("
        UPDATE applications 
        SET skill_score=?, experience_score=?, potential_score=?, total_score=?, top_keywords=?
        WHERE id=?
    ");
    $stmt->execute([$skill, $exp, $potential, $total, $keywords, $application_id]);

    // 8. Redirect
    header("Location: candidate_dashboard.php");
    exit;
}

}
?>