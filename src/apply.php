<?php
// src/apply.php
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
}
?>