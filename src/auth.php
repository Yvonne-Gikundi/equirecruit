<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

/**
 * Register a new user
 */
function register_user($pdo, $name, $email, $password, $role_id, $phone = null) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role_id, phone) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $email, $hashed, $role_id, $phone]);
}

/**
 * Log in an existing user
 */
function login_user($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role_id'] = $user['role_id'];
        return true;
    }
    return false;
}

/**
 * Protect pages (force login)
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth_login.php');
        exit;
    }
}

/**
 * Getters for user info
 */
function current_user_id() { return $_SESSION['user_id'] ?? null; }
function current_user_role() { return $_SESSION['role_id'] ?? null; }

/**
 * Logout and redirect
 */
function logout_user() {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
