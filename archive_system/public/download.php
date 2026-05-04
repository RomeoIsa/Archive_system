<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$institution_id = $_SESSION['institution_id'] ?? 0;

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$file_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->bind_param("i", $file_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found");
}

$file = $result->fetch_assoc();

/* ACCESS CONTROL */
$allowed = false;

if ($file['user_id'] == $user_id) {
    $allowed = true;
} elseif ($file['visibility'] === 'public') {
    $allowed = true;
} elseif ($file['visibility'] === 'institution' && $file['institution_id'] == $institution_id) {
    $allowed = true;
}

if (!$allowed) {
    die("Access denied");
}

/* LOG DOWNLOAD (NEW PART) */
$log = $conn->prepare("
    INSERT INTO downloads (user_id, upload_id, institution_id)
    VALUES (?, ?, ?)
");
$log->bind_param("iii", $user_id, $file_id, $institution_id);
$log->execute();

/* FILE DOWNLOAD */
$filePath = "../uploads/" . $file['file_name'];

if (!file_exists($filePath)) {
    die("File missing");
}

header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($filePath) . "\"");

readfile($filePath);
exit();