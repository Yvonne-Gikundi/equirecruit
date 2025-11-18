<?php

// src/jobs.php
require_once __DIR__ . '/db.php';

function create_job($pdo, $recruiter_id, $title, $description, $requirements='') {
  $stmt = $pdo->prepare("INSERT INTO jobs (recruiter_id, title, description, requirements, is_published) VALUES (?, ?, ?, ?, 1)");
  $stmt->execute([$recruiter_id, $title, $description, $requirements]);
  return $pdo->lastInsertId();
}

function list_jobs($pdo, $only_published = true) {
  if ($only_published) {
    $stmt = $pdo->query("SELECT j.*, u.name as recruiter FROM jobs j JOIN users u ON j.recruiter_id = u.id WHERE j.is_published = 1 ORDER BY j.created_at DESC");
  } else {
    $stmt = $pdo->query("SELECT j.*, u.name as recruiter FROM jobs j JOIN users u ON j.recruiter_id = u.id ORDER BY j.created_at DESC");
  }
  return $stmt->fetchAll();
}

function get_job($pdo, $job_id) {
  $stmt = $pdo->prepare("SELECT j.*, u.name as recruiter FROM jobs j JOIN users u ON j.recruiter_id = u.id WHERE j.id = ?");
  $stmt->execute([$job_id]);
  return $stmt->fetch();
}
?>