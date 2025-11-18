<?php
// create_admin.php — run once, then delete it for safety.

require_once __DIR__ . '/src/db.php';   // uses $pdo from your src/db.php
require_once __DIR__ . '/src/auth.php'; // optional: uses register_user if present

// Edit these details to whatever you want:
$name    = 'Admin User';
$email   = 'admin@example.com';
$password = 'Admin@2025';   // CHANGE THIS after first login
$role_id = 1;               // using 1 for admin (2=recruiter,3=candidate per your setup)
$phone   = '0000000000';

try {
    // ensure email not already used
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "User with email {$email} already exists. Remove or modify that account first.";
        exit;
    }

    // create hashed password and insert
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare("INSERT INTO users (role_id, name, email, password_hash, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $done = $ins->execute([$role_id, $name, $email, $hash, $phone]);

    if ($done) {
        echo "Admin user created successfully.<br>";
        echo "Email: {$email}<br>Password: {$password}<br>";
        echo "<strong>IMPORTANT:</strong> delete this file (create_admin.php) now for security.";
    } else {
        echo "Failed to create admin. Check DB permissions and table structure.";
    }
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
