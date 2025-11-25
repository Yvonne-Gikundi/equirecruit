<?php
require_once __DIR__ . '/db.php';

// Update metric function
function update_metric($pdo, $name, $score) {
    $stmt = $pdo->prepare("
        INSERT INTO fairness_metrics (metric_name, score)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE score = VALUES(score), updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$name, $score]);
}

/* ------------------------------
   1. Gender Balance Score
--------------------------------*/
function compute_gender_balance($pdo) {
    $stmt = $pdo->query("
        SELECT gender, COUNT(*) AS cnt
        FROM applicants
        GROUP BY gender
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($data) < 2) return 50; // Only one gender present → low fairness

    $counts = array_column($data, 'cnt', 'gender');

    $male = $counts['male'] ?? 0;
    $female = $counts['female'] ?? 0;

    if ($male + $female == 0) return 50;

    $ratio = min($male, $female) / max($male, $female);
    return round($ratio * 100, 2);
}

/* ------------------------------
   2. Recruiter Bias Index
--------------------------------*/
function compute_recruiter_bias($pdo) {
    // Count how many times each recruiter views user profiles
    $stmt = $pdo->query("
        SELECT users.id, users.name, COUNT(audit_logs.id) AS views
        FROM users
        LEFT JOIN audit_logs ON audit_logs.user_id = users.id
        WHERE role_id = 2
        GROUP BY users.id
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) return 50;

    $views = array_column($data, 'views');
    $max = max($views);
    $min = min($views);

    if ($max == 0) return 50;

    $ratio = $min / $max;
    return round($ratio * 100, 2);
}

/* ------------------------------
   3. Equal Opportunity Score
--------------------------------*/
function compute_equal_opportunity($pdo) {
    $stmt = $pdo->query("
        SELECT gender, COUNT(*) AS interviewed
        FROM applicants
        WHERE status = 'interviewed'
        GROUP BY gender
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($data) < 2) return 50;

    $counts = array_column($data, 'interviewed', 'gender');
    $male = $counts['male'] ?? 0;
    $female = $counts['female'] ?? 0;

    if ($male + $female == 0) return 50;

    $ratio = min($male, $female) / max($male, $female);
    return round($ratio * 100, 2);
}

/* ------------------------------
   4. Algorithmic Fairness Score
--------------------------------*/
function compute_algorithmic_fairness($pdo) {
    // Dummy logic: you can replace with your scoring system
    return rand(80, 100);
}

/* ------------------------------
   5. Application Distribution Score
--------------------------------*/
function compute_application_distribution($pdo) {
    $stmt = $pdo->query("
        SELECT job_id, COUNT(*) AS total
        FROM applicants
        GROUP BY job_id
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) return 50;

    $totals = array_column($data, 'total');
    $max = max($totals);
    $min = min($totals);

    if ($max == 0) return 50;

    return round(($min / $max) * 100, 2);
}

/* ------------------------------
   RUN ALL FAIRNESS METRICS
--------------------------------*/

$genderScore = compute_gender_balance($pdo);
update_metric($pdo, 'Gender Balance Score', $genderScore);

$biasScore = compute_recruiter_bias($pdo);
update_metric($pdo, 'Recruiter Bias Index', $biasScore);

$equalScore = compute_equal_opportunity($pdo);
update_metric($pdo, 'Equal Opportunity Score', $equalScore);

$algoScore = compute_algorithmic_fairness($pdo);
update_metric($pdo, 'Algorithmic Fairness Score', $algoScore);

$distributionScore = compute_application_distribution($pdo);
update_metric($pdo, 'Application Distribution Score', $distributionScore);

echo "Fairness metrics updated successfully!";
