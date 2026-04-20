<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['file'])) {
    die("No file specified");
}

$file = basename($_GET['file']); // security fix
$path = "../uploads/" . $file;

if (!file_exists($path)) {
    die("File not found");
}

// force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));

readfile($path);
exit();