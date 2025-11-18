<?php
$fname = basename($_GET['file'] ?? '');
$path = __DIR__ . '/uploads/' . $fname;
if (!$fname || !file_exists($path)) { header('HTTP/1.1 404 Not Found'); echo 'File not found.'; exit; }
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($path).'"');
readfile($path);
exit;
?>