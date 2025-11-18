<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// destroy session
session_unset();
session_destroy();

// redirect to homepage
header("Location: index.php");
exit;
