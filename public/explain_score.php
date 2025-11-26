<?php
require_once "../src/db.php";
require_once "../src/auth.php";

if (!isset($_GET['id'])) exit("Invalid request");

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT explanation FROM applications WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || empty($row['explanation'])) {
    echo "<p>No explanation available.</p>";
    exit;
}

$exp = json_decode($row['explanation'], true);

echo "<h3>Score Breakdown</h3>";
echo "<ul>";
echo "<li><strong>Skill Match:</strong> " . $exp['skill'] . "</li>";
echo "<li><strong>Experience:</strong> " . $exp['experience'] . "</li>";
echo "<li><strong>Potential:</strong> " . $exp['potential'] . "</li>";
echo "<li><strong>Keywords:</strong> " . implode(', ', $exp['keywords']) . "</li>";
echo "</ul>";
